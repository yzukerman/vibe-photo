<?php
if (!defined('ABSPATH')) { exit; }

function vibe_photo_reverse_geocode($lat, $lon, $attachment_id = null) {
	// Get API key from settings
	$api_key = get_option('vibe_photo_google_api_key', '');
    if (!is_numeric($lat) || !is_numeric($lon) || abs((float) $lat) > 90 || abs((float) $lon) > 180 || !is_string($api_key)) return null;

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
		rawurlencode($api_key)
	);

	// Make API request
	$response = wp_remote_get($url, array(
		'timeout' => 10,
        'limit_response_size' => 262144,
		'sslverify' => true
	));

	// Check for errors
	if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
		return null;
	}

	// Parse response
	$body = wp_remote_retrieve_body($response);
	$data = json_decode($body, true);

	// Check if we got results
	if (empty($data['results']) || $data['status'] !== 'OK') {
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

/** Reject missing files and symlinks escaping the real uploads directory. */
function vibe_photo_galleries_safe_file($path) {
    $uploads = wp_get_upload_dir();
    $base = realpath($uploads['basedir']);
    $file = is_string($path) ? realpath($path) : false;
    if (!$base || !$file || !is_file($file) || !is_readable($file) || strpos(wp_normalize_path($file), trailingslashit(wp_normalize_path($base))) !== 0) return false;
    return $file;
}

function vibe_photo_get_image_exif() {
	if (!isset($_POST['nonce']) || !is_string($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'vibe_photo_nonce')) {
        wp_send_json_error(__('Invalid request.', 'vibe-photo-galleries'));
        return;
    }
    if (!isset($_POST['image_url']) || !is_string($_POST['image_url'])) {
        wp_send_json_error(__('Invalid image URL.', 'vibe-photo-galleries'));
        return;
    }
    $image_url = esc_url_raw(wp_unslash($_POST['image_url']));
    $uploads = wp_get_upload_dir();
    $base_url = trailingslashit($uploads['baseurl']);
    if (strpos($image_url, $base_url) !== 0 || strpos(rawurldecode($image_url), '..') !== false) {
        wp_send_json_error(__('Only local media-library images are supported.', 'vibe-photo-galleries'));
        return;
    }
    // Resolve exact upload-relative filenames; never search by a partial basename.
    $path = wp_parse_url($image_url, PHP_URL_PATH);
    $base_path = trailingslashit(wp_parse_url($base_url, PHP_URL_PATH));
    $relative = rawurldecode(substr($path, strlen($base_path)));
    $candidates = array($relative, preg_replace('/-\d+x\d+(\.[^.]+)$/', '$1', $relative));
    $candidates[] = preg_replace('/(\.[^.]+)$/', '-scaled$1', $candidates[1]);
    global $wpdb;
    $attachment_id = 0;
    foreach (array_unique($candidates) as $candidate) {
        $attachment_id = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT p.ID FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id WHERE p.post_type = 'attachment' AND pm.meta_key = '_wp_attached_file' AND pm.meta_value = %s LIMIT 1",
            $candidate
        ));
        if ($attachment_id) break;
    }
    $post = get_post($attachment_id);
    $parent = $post && $post->post_parent ? get_post($post->post_parent) : null;
    if (!$post || !wp_attachment_is_image($attachment_id) || $post->post_status === 'trash' || post_password_required($post) ||
        ($post->post_status !== 'inherit' && $post->post_status !== 'publish' && !current_user_can('read_post', $attachment_id)) ||
        ($parent && ((!is_post_publicly_viewable($parent) && !current_user_can('read_post', $parent->ID)) || post_password_required($parent)))) {
        wp_send_json_error(__('Image metadata is unavailable.', 'vibe-photo-galleries'));
        return;
    }
    $file_path = vibe_photo_galleries_safe_file(get_attached_file($attachment_id));
    if (!$file_path) {
        wp_send_json_error(__('Image metadata is unavailable.', 'vibe-photo-galleries'));
        return;
    }
    $metadata = wp_get_attachment_metadata($attachment_id);
    $exif_data = array();
    foreach (array('title' => $post->post_title, 'caption' => $post->post_excerpt, 'alt_text' => get_post_meta($attachment_id, '_wp_attachment_image_alt', true)) as $key => $value) {
        if ($value !== '') $exif_data[$key] = wp_strip_all_tags($value);
    }
    if (isset($metadata['width'], $metadata['height'])) {
        $exif_data['size'] = absint($metadata['width']) . ' × ' . absint($metadata['height']) . ' pixels';
    }
    $exif_data['file_size'] = size_format(filesize($file_path));

	// Try to get EXIF data if available
	// Note: exif_read_data() only works with JPEG and TIFF formats, not WebP
	$file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
	$supported_formats = array('jpg', 'jpeg', 'tif', 'tiff');


	if (function_exists('exif_read_data') && file_exists($file_path) && in_array($file_extension, $supported_formats)) {
		// WordPress may have created a scaled version - try original first
		$original_path = $file_path;
		if (strpos($file_path, '-scaled.') !== false) {
			$original_path = str_replace('-scaled.', '.', $file_path);
			if (vibe_photo_galleries_safe_file($original_path)) {
				$file_path = vibe_photo_galleries_safe_file($original_path);
			}
		}

		$exif = @exif_read_data($file_path);

		// Add to debug info

		// Debug logging for troubleshooting

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
					0 => __('No Flash', 'vibe-photo-galleries'),
					1 => __('Flash Fired', 'vibe-photo-galleries'),
					5 => __('Flash Fired, Return not detected', 'vibe-photo-galleries'),
					7 => __('Flash Fired, Return detected', 'vibe-photo-galleries'),
					8 => __('On, Did not fire', 'vibe-photo-galleries'),
					9 => __('On, Fired', 'vibe-photo-galleries'),
					13 => __('On, Return not detected', 'vibe-photo-galleries'),
					15 => __('On, Return detected', 'vibe-photo-galleries'),
					16 => __('Off, Did not fire', 'vibe-photo-galleries'),
					24 => __('Auto, Did not fire', 'vibe-photo-galleries'),
					25 => __('Auto, Fired', 'vibe-photo-galleries'),
					29 => __('Auto, Fired, Return not detected', 'vibe-photo-galleries'),
					31 => __('Auto, Fired, Return detected', 'vibe-photo-galleries'),
					32 => __('No flash function', 'vibe-photo-galleries'),
					65 => __('Red-eye reduction, Fired', 'vibe-photo-galleries'),
					69 => __('Red-eye reduction, Fired, Return not detected', 'vibe-photo-galleries'),
					71 => __('Red-eye reduction, Fired, Return detected', 'vibe-photo-galleries'),
					73 => __('Red-eye reduction, On, Fired', 'vibe-photo-galleries'),
					77 => __('Red-eye reduction, On, Fired, Return not detected', 'vibe-photo-galleries'),
					79 => __('Red-eye reduction, On, Fired, Return detected', 'vibe-photo-galleries'),
					89 => __('Red-eye reduction, Auto, Fired', 'vibe-photo-galleries'),
					93 => __('Red-eye reduction, Auto, Fired, Return not detected', 'vibe-photo-galleries'),
					95 => __('Red-eye reduction, Auto, Fired, Return detected', 'vibe-photo-galleries')
				);
				$exif_data['flash'] = isset($flash_descriptions[$flash_value]) ? $flash_descriptions[$flash_value] : __('Unknown', 'vibe-photo-galleries');
			}

			// White balance
			if (isset($exif['WhiteBalance'])) {
				$wb_values = array(0 => __('Auto', 'vibe-photo-galleries'), 1 => __('Manual', 'vibe-photo-galleries'));
				$exif_data['white_balance'] = isset($wb_values[$exif['WhiteBalance']]) ? $wb_values[$exif['WhiteBalance']] : __('Unknown', 'vibe-photo-galleries');
			}

			// Exposure mode
			if (isset($exif['ExposureMode'])) {
				$exposure_modes = array(0 => __('Auto', 'vibe-photo-galleries'), 1 => __('Manual', 'vibe-photo-galleries'), 2 => __('Auto bracket', 'vibe-photo-galleries'));
				$exif_data['exposure_mode'] = isset($exposure_modes[$exif['ExposureMode']]) ? $exposure_modes[$exif['ExposureMode']] : __('Unknown', 'vibe-photo-galleries');
			}

			// Metering mode
			if (isset($exif['MeteringMode'])) {
				$metering_modes = array(
					0 => __('Unknown', 'vibe-photo-galleries'),
					1 => __('Average', 'vibe-photo-galleries'),
					2 => __('Center-weighted average', 'vibe-photo-galleries'),
					3 => __('Spot', 'vibe-photo-galleries'),
					4 => __('Multi-spot', 'vibe-photo-galleries'),
					5 => __('Pattern', 'vibe-photo-galleries'),
					6 => __('Partial', 'vibe-photo-galleries')
				);
				$exif_data['metering_mode'] = isset($metering_modes[$exif['MeteringMode']]) ? $metering_modes[$exif['MeteringMode']] : __('Unknown', 'vibe-photo-galleries');
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
				$color_spaces = array(1 => __('sRGB', 'vibe-photo-galleries'), 65535 => __('Uncalibrated', 'vibe-photo-galleries'));
				$exif_data['color_space'] = isset($color_spaces[$exif['ColorSpace']]) ? $color_spaces[$exif['ColorSpace']] : __('Unknown', 'vibe-photo-galleries');
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
