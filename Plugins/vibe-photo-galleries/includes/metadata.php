<?php
if (!defined('ABSPATH')) { exit; }

function vibe_photo_reverse_geocode($lat, $lon, $attachment_id = null) {
	// Get API key from settings
	$api_key = get_option('vibe_photo_google_api_key', '');

	// If no API key, return null
	if (empty($api_key)) {
		return null;
	}

	// Check if we have cached location for this attachment
	if ($attachment_id) {
		$cached_location = get_post_meta($attachment_id, '_vibe_photo_location', true);
		if (!empty($cached_location)) {
			return $cached_location;
		}
	}

	// Build API request URL
	$url = sprintf(
		'https://maps.googleapis.com/maps/api/geocode/json?latlng=%s,%s&key=%s',
		$lat,
		$lon,
		$api_key
	);

	// Make API request
	$response = wp_remote_get($url, array(
		'timeout' => 10,
		'sslverify' => true
	));

	// Check for errors
	if (is_wp_error($response)) {
		error_log('VIBE PHOTO: Geocoding API error: ' . $response->get_error_message());
		return null;
	}

	// Parse response
	$body = wp_remote_retrieve_body($response);
	$data = json_decode($body, true);

	// Check if we got results
	if (empty($data['results']) || $data['status'] !== 'OK') {
		error_log('VIBE PHOTO: Geocoding API returned no results. Status: ' . ($data['status'] ?? 'unknown'));
		return null;
	}

	// Extract location name
	// Try to get city, state/province, country format
	$location_parts = array();
	$address_components = $data['results'][0]['address_components'];

	$locality = null;
	$sublocality = null;
	$admin_area = null;
	$country = null;

	foreach ($address_components as $component) {
		// Skip Plus Code components
		if (in_array('plus_code', $component['types'])) {
			continue;
		}

		if (in_array('locality', $component['types'])) {
			$locality = $component['long_name'];
		}
		if (in_array('sublocality', $component['types']) || in_array('sublocality_level_1', $component['types'])) {
			$sublocality = $component['long_name'];
		}
		if (in_array('administrative_area_level_1', $component['types'])) {
			$admin_area = $component['short_name'];
		}
		if (in_array('administrative_area_level_2', $component['types']) && !$locality) {
			// Use level 2 admin area if no locality found
			if (!$locality) {
				$locality = $component['long_name'];
			}
		}
		if (in_array('country', $component['types'])) {
			$country = $component['long_name'];
		}
	}

	// Build location string
	if ($locality) {
		$location_parts[] = $locality;
	} elseif ($sublocality) {
		$location_parts[] = $sublocality;
	}
	if ($admin_area) {
		$location_parts[] = $admin_area;
	}
	if ($country) {
		$location_parts[] = $country;
	}

	// If we still have no parts, try to extract from formatted_address, but skip Plus Codes
	if (empty($location_parts)) {
		$formatted = $data['results'][0]['formatted_address'];
		// Check if it's a Plus Code (format: XXXX+XX)
		if (!preg_match('/^[A-Z0-9]{4,8}\+[A-Z0-9]{2,3}/', $formatted)) {
			$location_name = $formatted;
		} else {
			// If only Plus Code, try next result
			if (isset($data['results'][1])) {
				$formatted = $data['results'][1]['formatted_address'];
				if (!preg_match('/^[A-Z0-9]{4,8}\+[A-Z0-9]{2,3}/', $formatted)) {
					$location_name = $formatted;
				}
			}
		}
	} else {
		$location_name = implode(', ', $location_parts);
	}

	// If still empty, return null
	if (empty($location_name)) {
		error_log('VIBE PHOTO: Could not extract meaningful location from geocoding response');
		return null;
	}

	// Cache the result
	if ($attachment_id && !empty($location_name)) {
		update_post_meta($attachment_id, '_vibe_photo_location', $location_name);
	}

	return $location_name;
}

/**
 * AJAX handler for getting image EXIF data
 */
function vibe_photo_get_image_exif() {
	// Verify nonce
	if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'vibe_photo_nonce')) {
		wp_send_json_error('Invalid nonce');
		return;
	}

	$image_url = isset($_POST['image_url']) ? esc_url_raw(wp_unslash($_POST['image_url'])) : '';
	$uploads = wp_get_upload_dir();
	if (!$image_url || strpos($image_url, trailingslashit($uploads['baseurl'])) !== 0 || strpos(rawurldecode($image_url), '..') !== false) {
		wp_send_json_error('Only local media-library images are supported.');
		return;
	}

	// Fix protocol-relative URLs
	if (strpos($image_url, '//') === 0) {
		$image_url = 'http:' . $image_url;
	}

	// Convert resized image URL to original URL for attachment lookup
	$original_url = $image_url;
	// Remove size dimensions (e.g., -683x1024.jpeg -> .jpeg)
	$original_url = preg_replace('/-\d+x\d+(\.[^.]+)$/', '$1', $original_url);

	// Try to get attachment ID from original URL
	$attachment_id = attachment_url_to_postid($original_url);

	$exif_data = array();
	if (!$attachment_id) {
		// If attachment_url_to_postid failed, try alternative methods to find the attachment
		global $wpdb;

		// Try multiple variations of the filename including -scaled versions
		$filename = basename($original_url);
		$filename_no_ext = pathinfo($filename, PATHINFO_FILENAME);
		$filename_ext = pathinfo($filename, PATHINFO_EXTENSION);

		// Try multiple variations of the filename
		$search_patterns = array(
			'%' . $filename,                                    // exact: DSCF7743.jpeg
			'%' . $filename_no_ext . '-scaled.' . $filename_ext, // scaled: DSCF7743-scaled.jpeg
			'%' . $filename_no_ext . '%.' . $filename_ext        // any variation: DSCF7743-*.jpeg
		);

		foreach ($search_patterns as $pattern) {
			$attachments_with_filename = $wpdb->get_results($wpdb->prepare(
				"SELECT p.ID FROM {$wpdb->posts} p 
				 LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
				 WHERE p.post_type = 'attachment' AND pm.meta_key = '_wp_attached_file' 
				 AND pm.meta_value LIKE %s 
				 LIMIT 1",
				$pattern
			));

			if ($attachments_with_filename) {
				$attachment_id = $attachments_with_filename[0]->ID;
				break; // Found one, stop searching
			}
		}
	}

	if (!$attachment_id) {
		// Still no attachment ID, work with file directly
		// If no attachment ID, try to work with the URL directly
		error_log('VIBE PHOTO: No attachment ID found for: ' . $image_url);
		$upload_dir = wp_get_upload_dir();

		// Try multiple methods to convert URL to file path
		$file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $image_url);

		// If that didn't work, try extracting just the relative path
		if (!file_exists($file_path)) {
			// Extract the path after /wp-content/uploads/
			if (preg_match('/\/wp-content\/uploads\/(.+)$/', $image_url, $matches)) {
				$relative_path = $matches[1];
				$file_path = $upload_dir['basedir'] . '/' . $relative_path;
			}
		}

		if (file_exists($file_path)) {
			// Get basic file info without WordPress metadata
			$image_size = getimagesize($file_path);
			if ($image_size) {
				$exif_data['size'] = $image_size[0] . ' × ' . $image_size[1] . ' pixels';
			}

			$file_size = filesize($file_path);
			if ($file_size) {
				$exif_data['file_size'] = size_format($file_size);
			}
		} else {
			wp_send_json_error(array(
				'message' => 'Image file not found'
			));
			return;
		}
	} else {
		// Get image metadata from WordPress
		$metadata = wp_get_attachment_metadata($attachment_id);
		$file_path = get_attached_file($attachment_id);

		// Debug logging
		error_log('VIBE PHOTO: Attachment ID: ' . $attachment_id);
		error_log('VIBE PHOTO: File path: ' . $file_path);
		error_log('VIBE PHOTO: File exists: ' . (file_exists($file_path) ? 'yes' : 'no'));

		// Check if file exists
		if (!file_exists($file_path)) {
			wp_send_json_error(array(
				'message' => 'Image file not found on server',
				'debug' => array(
					'attachment_id' => $attachment_id,
					'file_path' => $file_path
				)
			));
			return;
		}

		// Get WordPress image title and caption
		$post = get_post($attachment_id);
		if ($post) {
			// Get title (post_title)
			if (!empty($post->post_title)) {
				$exif_data['title'] = $post->post_title;
			}

			// Get caption (post_excerpt)  
			if (!empty($post->post_excerpt)) {
				$exif_data['caption'] = $post->post_excerpt;
			}

			// Get alt text (stored in meta)
			$alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
			if (!empty($alt_text)) {
				$exif_data['alt_text'] = $alt_text;
			}
		}

		// Get basic file info
		if ($metadata) {
			$exif_data['size'] = $metadata['width'] . ' × ' . $metadata['height'] . ' pixels';

			// Get file size
			$file_size = filesize($file_path);
			$exif_data['file_size'] = size_format($file_size);
		}
	}

	// Try to get EXIF data if available
	// Note: exif_read_data() only works with JPEG and TIFF formats, not WebP
	$file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
	$supported_formats = array('jpg', 'jpeg', 'tif', 'tiff');

	// Add debug info to response
	$exif_data['debug'] = array(
		'file_extension' => $file_extension,
		'exif_function_exists' => function_exists('exif_read_data'),
		'file_path' => $file_path,
		'is_supported_format' => in_array($file_extension, $supported_formats)
	);

	if (function_exists('exif_read_data') && file_exists($file_path) && in_array($file_extension, $supported_formats)) {
		// WordPress may have created a scaled version - try original first
		$original_path = $file_path;
		if (strpos($file_path, '-scaled.') !== false) {
			$original_path = str_replace('-scaled.', '.', $file_path);
			if (file_exists($original_path)) {
				$exif_data['debug']['trying_original'] = true;
				$file_path = $original_path;
			}
		}

		$exif = @exif_read_data($file_path);

		// Add to debug info
		$exif_data['debug']['exif_read_success'] = ($exif !== false);
		$exif_data['debug']['exif_keys_found'] = $exif ? count($exif) : 0;
		if ($exif) {
			$exif_data['debug']['exif_keys'] = array_keys($exif);
		}

		// Debug logging for troubleshooting
		if (!$exif) {
			error_log('VIBE PHOTO: Failed to read EXIF from: ' . $file_path);
			error_log('VIBE PHOTO: File exists: ' . (file_exists($file_path) ? 'yes' : 'no'));
			error_log('VIBE PHOTO: File readable: ' . (is_readable($file_path) ? 'yes' : 'no'));
			error_log('VIBE PHOTO: File size: ' . (file_exists($file_path) ? filesize($file_path) : 'N/A'));
		}

		if ($exif) {
			// Camera info
			if (isset($exif['Make']) && isset($exif['Model'])) {
				$exif_data['camera'] = trim($exif['Make'] . ' ' . $exif['Model']);
			}

			// Lens info - check multiple possible fields
			if (isset($exif['UndefinedTag:0xA434'])) {
				$exif_data['lens'] = $exif['UndefinedTag:0xA434'];
			} elseif (isset($exif['LensModel'])) {
				$exif_data['lens'] = $exif['LensModel'];
			} elseif (isset($exif['LensInfo'])) {
				$exif_data['lens'] = $exif['LensInfo'];
			} elseif (isset($exif['LensMake'])) {
				$lens_make = $exif['LensMake'];
				$lens_model = isset($exif['LensModel']) ? $exif['LensModel'] : '';
				$exif_data['lens'] = trim($lens_make . ' ' . $lens_model);
			}

			// Aperture
			if (isset($exif['COMPUTED']['ApertureFNumber'])) {
				$exif_data['aperture'] = $exif['COMPUTED']['ApertureFNumber'];
			} elseif (isset($exif['FNumber'])) {
				$aperture = explode('/', $exif['FNumber']);
				if (count($aperture) == 2 && $aperture[1] != 0) {
					$exif_data['aperture'] = 'f/' . round($aperture[0] / $aperture[1], 1);
				}
			} elseif (isset($exif['MaxApertureValue'])) {
				$aperture = explode('/', $exif['MaxApertureValue']);
				if (count($aperture) == 2 && $aperture[1] != 0) {
					$f_stop = pow(2, ($aperture[0] / $aperture[1]) / 2);
					$exif_data['aperture'] = 'f/' . round($f_stop, 1);
				}
			}

			// Shutter speed
			if (isset($exif['ExposureTime'])) {
				$shutter = $exif['ExposureTime'];
				if (strpos($shutter, '/') !== false) {
					$parts = explode('/', $shutter);
					if (count($parts) == 2 && $parts[1] != 0) {
						$decimal = $parts[0] / $parts[1];
						if ($decimal >= 1) {
							$exif_data['shutter'] = round($decimal, 1) . 's';
						} else {
							$exif_data['shutter'] = $shutter . 's';
						}
					}
				} else {
					$exif_data['shutter'] = $shutter . 's';
				}
			} elseif (isset($exif['ShutterSpeedValue'])) {
				$shutter_speed = explode('/', $exif['ShutterSpeedValue']);
				if (count($shutter_speed) == 2 && $shutter_speed[1] != 0) {
					$speed = pow(2, $shutter_speed[0] / $shutter_speed[1]);
					if ($speed >= 1) {
						$exif_data['shutter'] = '1/' . round(1 / $speed) . 's';
					} else {
						$exif_data['shutter'] = round($speed, 1) . 's';
					}
				}
			}

			// ISO
			if (isset($exif['ISOSpeedRatings'])) {
				$exif_data['iso'] = 'ISO ' . $exif['ISOSpeedRatings'];
			} elseif (isset($exif['PhotographicSensitivity'])) {
				$exif_data['iso'] = 'ISO ' . $exif['PhotographicSensitivity'];
			}

			// Focal length
			if (isset($exif['FocalLength'])) {
				$focal = $exif['FocalLength'];
				if (strpos($focal, '/') !== false) {
					$parts = explode('/', $focal);
					if (count($parts) == 2 && $parts[1] != 0) {
						$exif_data['focal_length'] = round($parts[0] / $parts[1]) . 'mm';
					}
				} else {
					$exif_data['focal_length'] = $focal . 'mm';
				}
			}

			// 35mm equivalent focal length
			if (isset($exif['FocalLengthIn35mmFilm'])) {
				$exif_data['focal_35mm'] = $exif['FocalLengthIn35mmFilm'] . 'mm (35mm equiv.)';
			}

			// Date taken - try multiple date fields
			if (isset($exif['DateTimeOriginal'])) {
				$date = DateTime::createFromFormat('Y:m:d H:i:s', $exif['DateTimeOriginal']);
				if ($date) {
					$exif_data['date_taken'] = $date->format('F j, Y g:i A');
				}
			} elseif (isset($exif['DateTime'])) {
				$date = DateTime::createFromFormat('Y:m:d H:i:s', $exif['DateTime']);
				if ($date) {
					$exif_data['date_taken'] = $date->format('F j, Y g:i A');
				}
			} elseif (isset($exif['DateTimeDigitized'])) {
				$date = DateTime::createFromFormat('Y:m:d H:i:s', $exif['DateTimeDigitized']);
				if ($date) {
					$exif_data['date_taken'] = $date->format('F j, Y g:i A');
				}
			}

			// Flash information
			if (isset($exif['Flash'])) {
				$flash_value = intval($exif['Flash']);
				$flash_descriptions = array(
					0 => 'No Flash',
					1 => 'Flash Fired',
					5 => 'Flash Fired, Return not detected',
					7 => 'Flash Fired, Return detected',
					8 => 'On, Did not fire',
					9 => 'On, Fired',
					13 => 'On, Return not detected',
					15 => 'On, Return detected',
					16 => 'Off, Did not fire',
					24 => 'Auto, Did not fire',
					25 => 'Auto, Fired',
					29 => 'Auto, Fired, Return not detected',
					31 => 'Auto, Fired, Return detected',
					32 => 'No flash function',
					65 => 'Red-eye reduction, Fired',
					69 => 'Red-eye reduction, Fired, Return not detected',
					71 => 'Red-eye reduction, Fired, Return detected',
					73 => 'Red-eye reduction, On, Fired',
					77 => 'Red-eye reduction, On, Fired, Return not detected',
					79 => 'Red-eye reduction, On, Fired, Return detected',
					89 => 'Red-eye reduction, Auto, Fired',
					93 => 'Red-eye reduction, Auto, Fired, Return not detected',
					95 => 'Red-eye reduction, Auto, Fired, Return detected'
				);
				$exif_data['flash'] = isset($flash_descriptions[$flash_value]) ? $flash_descriptions[$flash_value] : 'Unknown';
			}

			// White balance
			if (isset($exif['WhiteBalance'])) {
				$wb_values = array(0 => 'Auto', 1 => 'Manual');
				$exif_data['white_balance'] = isset($wb_values[$exif['WhiteBalance']]) ? $wb_values[$exif['WhiteBalance']] : 'Unknown';
			}

			// Exposure mode
			if (isset($exif['ExposureMode'])) {
				$exposure_modes = array(0 => 'Auto', 1 => 'Manual', 2 => 'Auto bracket');
				$exif_data['exposure_mode'] = isset($exposure_modes[$exif['ExposureMode']]) ? $exposure_modes[$exif['ExposureMode']] : 'Unknown';
			}

			// Metering mode
			if (isset($exif['MeteringMode'])) {
				$metering_modes = array(
					0 => 'Unknown',
					1 => 'Average',
					2 => 'Center-weighted average',
					3 => 'Spot',
					4 => 'Multi-spot',
					5 => 'Pattern',
					6 => 'Partial'
				);
				$exif_data['metering_mode'] = isset($metering_modes[$exif['MeteringMode']]) ? $metering_modes[$exif['MeteringMode']] : 'Unknown';
			}

			// GPS coordinates if available
			if (isset($exif['GPSLatitude']) && isset($exif['GPSLongitude'])) {
				$lat = $exif['GPSLatitude'];
				$lon = $exif['GPSLongitude'];
				$lat_ref = isset($exif['GPSLatitudeRef']) ? $exif['GPSLatitudeRef'] : '';
				$lon_ref = isset($exif['GPSLongitudeRef']) ? $exif['GPSLongitudeRef'] : '';

				// Helper function to convert GPS rational number to decimal
				$gps_to_decimal = function ($coordinate) {
					if (is_array($coordinate) && count($coordinate) >= 3) {
						// Each component might be a rational number like "25/1"
						$degrees = $coordinate[0];
						$minutes = $coordinate[1];
						$seconds = $coordinate[2];

						// Evaluate fractions
						if (is_string($degrees) && strpos($degrees, '/') !== false) {
							$parts = explode('/', $degrees);
							$degrees = $parts[1] != 0 ? $parts[0] / $parts[1] : 0;
						}
						if (is_string($minutes) && strpos($minutes, '/') !== false) {
							$parts = explode('/', $minutes);
							$minutes = $parts[1] != 0 ? $parts[0] / $parts[1] : 0;
						}
						if (is_string($seconds) && strpos($seconds, '/') !== false) {
							$parts = explode('/', $seconds);
							$seconds = $parts[1] != 0 ? $parts[0] / $parts[1] : 0;
						}

						return $degrees + ($minutes / 60) + ($seconds / 3600);
					}
					return 0;
				};

				// Convert DMS to decimal
				if (is_array($lat) && count($lat) >= 3) {
					$lat_decimal = $gps_to_decimal($lat);
					if ($lat_ref == 'S') $lat_decimal = -$lat_decimal;

					$lon_decimal = $gps_to_decimal($lon);
					if ($lon_ref == 'W') $lon_decimal = -$lon_decimal;

					$exif_data['gps_coordinates'] = round($lat_decimal, 6) . ', ' . round($lon_decimal, 6);

					// Add reverse geocoding
					$location_name = vibe_photo_reverse_geocode($lat_decimal, $lon_decimal, $attachment_id);
					if ($location_name) {
						$exif_data['location'] = $location_name;
					}
				}
			}

			// Color space
			if (isset($exif['ColorSpace'])) {
				$color_spaces = array(1 => 'sRGB', 65535 => 'Uncalibrated');
				$exif_data['color_space'] = isset($color_spaces[$exif['ColorSpace']]) ? $color_spaces[$exif['ColorSpace']] : 'Unknown';
			}

			// Software/Camera firmware
			if (isset($exif['Software'])) {
				$exif_data['software'] = $exif['Software'];
			}
		}
	}

	wp_send_json_success($exif_data);
}

// Register AJAX handlers for both logged-in and non-logged-in users
add_action('wp_ajax_vibe_photo_get_image_exif', 'vibe_photo_get_image_exif');
add_action('wp_ajax_nopriv_vibe_photo_get_image_exif', 'vibe_photo_get_image_exif');
