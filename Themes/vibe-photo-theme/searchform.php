<form role="search" method="get" class="portfolio-search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label>
        <span class="show-for-sr"><?php esc_html_e('Search the site', 'vibe-photo'); ?></span>
        <input type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php esc_attr_e('Search photographs and stories…', 'vibe-photo'); ?>" required>
    </label>
    <button type="submit"><?php esc_html_e('Search', 'vibe-photo'); ?></button>
</form>
