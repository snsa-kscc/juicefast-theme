<?php
// Get the page title
$title = get_the_title();

// Get the ACF field value
$short_description = get_field('short_description');
?>

<div class="jf-hero-banner-wrapper">
    <div class="jf-hero-banner">
        <div class="jf-hero-banner-cont">
            <h1 class="jf-hero-title">
                <?php 
                if ( is_shop() ) {
                    echo esc_html( get_the_title( wc_get_page_id( 'shop' ) ) ); // WooCommerce shop title
                } elseif ( is_product_category() ) {
                    echo single_cat_title( '', false ); // Product category title
                } elseif ( is_home() || get_queried_object_id() == get_option( 'page_for_posts' ) ) {
                    echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) ); // Blog page title
                } elseif ( is_archive() ) {
                    echo get_the_archive_title(); // Archive title (categories, tags, authors)
                } elseif ( is_single() ) {
                    echo esc_html( get_the_title() ); // Single post title
                } else {
                    echo esc_html( get_the_title() ); // Default title
                }
                ?>
            </h1>

            <?php 
            if ( ! empty( $short_description ) ) : ?>
                <p class="jf-hero-desc"><?php echo esc_html( $short_description ); ?></p>
            <?php endif; ?>
        </div>

        <?php 
        $banner_button = get_field('banner_button');
        if ( ! empty( $banner_button['button_text'] ) && ! empty( $banner_button['button_url'] ) ) : ?>
            <div class="jf-btn-black">
                <button><a href="<?php echo esc_url( $banner_button['button_url'] ); ?>"><?php echo esc_html( $banner_button['button_text'] ); ?></a></button>
            </div>
        <?php endif; ?>
    </div>
</div>