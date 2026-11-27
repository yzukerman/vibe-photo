<?php
/** Comments and reply form, rendered according to WordPress discussion settings. */
if (post_password_required()) {
    return;
}
?>
<section id="comments" class="comments-area" aria-label="<?php esc_attr_e('Comments', 'vibe-photo'); ?>">
    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $vibe_photo_comment_count = get_comments_number();
            printf(
                esc_html(_n('%s comment', '%s comments', $vibe_photo_comment_count, 'vibe-photo')),
                esc_html(number_format_i18n($vibe_photo_comment_count))
            );
            ?>
        </h2>
        <ol class="comment-list">
            <?php wp_list_comments(array(
                'style' => 'ol',
                'avatar_size' => 48,
                'short_ping' => true,
            )); ?>
        </ol>
        <?php the_comments_pagination(array(
            'prev_text' => __('Previous comments', 'vibe-photo'),
            'next_text' => __('Next comments', 'vibe-photo'),
        )); ?>
        <?php if (!comments_open()) : ?>
            <p class="no-comments"><?php esc_html_e('Comments are closed.', 'vibe-photo'); ?></p>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (comments_open()) { comment_form(); } ?>
</section>
