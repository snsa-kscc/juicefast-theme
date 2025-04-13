<?php

/**
 * @snippet		Fixed Reviews Container (Iva Sviben, Andrea...)
 */
function jf_swiper_slider_shortcode() {
    ob_start();
    ?>
    <div class="jf-fixed-reviews-container">
    <div class="swiper-button-next jf-fixed-reviews-next"></div>
    <div class="swiper-button-prev jf-fixed-reviews-prev"></div>
    <h2>Backed by nutritionists. Trusted by professional athletes.</h2>
        <div class="swiper fixed-reviews-swiper">
            <div class="swiper-wrapper">
                <!-- Slide 1 - Kristina -->
                <div class="swiper-slide">
                    <img src="/wp-content/uploads/Kristina-Dudek-Juicefast-tim-2.jpg">
                    <div class="slide-content">
                        <p>"I try to eat as varied and healthy as possible, adjusting my meals to my workouts and schedule.
							If I don’t have time for a proper meal, I always have a BITE ME bar with me!
							It saves the day every time!"</p>
						<div class="fixed-reviews-author">
							<span class="rev-name">Kristina Dudek</span>
							<span>Track athlete 400/800m</span>
						</div>
                    </div>
                </div>
                <!-- Slide 2 - Bojan Grbić -->
                <div class="swiper-slide">
                    <img src="/wp-content/uploads/Bojan-Grbic-Juicefast-tim-min.jpg">
                    <div class="slide-content">
                        <p>“The first bars that don’t mess with my digestion – even during races. BITE ME is always by my side.”</p>
						<div class="fixed-reviews-author">
							<span class="rev-name">Bojan Grbić</span>
							<span>Spartan World Champion 2018</span>
						</div>
                    </div>
                </div>
                <!-- Slide 3 - Martina Linarić -->
                <div class="swiper-slide">
                    <img src="/wp-content/uploads/Martina-Linaric-Juicefast-tim.jpg">
                    <div class="slide-content">
                        <p>“I love Juicefast products because they make everyday life easier for my clients.
							The detox programs provide a great head start for anyone looking to cleanse their body and kickstart effective weight loss.
							Plus, for those who don’t have time to cook and prepare nutrient-rich meals,
							Juicefast meals offer a high-quality, healthy option ready in just 2 minutes.”</p>
						<div class="fixed-reviews-author">
							<span class="rev-name">Martina Linarić</span>
							<span>Certified Nutritionist, MSc</span>
						</div>
                    </div>
                </div>
				 <!-- Slide 4 - Andrea Bilandzija -->
                <div class="swiper-slide">
                    <img src="/wp-content/uploads/Martina-Linaric-Juicefast-tim.jpg">
                    <div class="slide-content">
                        <p>“Juicefast has helped my clients on their journey to feeling good. Their detox programs are great for jumpstarting weight loss.
							After the detox, I recommend a balanced diet, combining Juicefast juices with nutritious meals to maintain their goals.”</p>
						<div class="fixed-reviews-author">
							<span class="rev-name">Andrea Bilandžija</span>
							<span>mag. nutr.</span>
						</div>
                    </div>
                </div>
				<!-- Slide 5 - Ivan Furdin -->
                <div class="swiper-slide">
                    <img src="/wp-content/uploads/Martina-Linaric-Juicefast-tim.jpg">
                    <div class="slide-content">
                        <p>"I love Juicefast products because they make everyday life easier for my clients.
							The detox products provide a great head start — they give a boost to anyone whose
							initial goal is to cleanse the body and start effectively losing accumulated weight.
							In addition, for those who don’t have time to cook and prepare nutritionally rich meals,
							Juicefast meals offer a high-quality and healthy option in just 2 minutes."</p>
						<div class="fixed-reviews-author">
							<span class="rev-name">Ivan Furdin</span>
							<span>mag. cin.</span>
						</div>
                    </div>
                </div>
				<!-- Slide 6 - Maja Čović -->
                <div class="swiper-slide">
                    <img src="/wp-content/uploads/Martina-Linaric-Juicefast-tim.jpg">
                    <div class="slide-content">
                        <p>"As a yoga teacher, I strive for balance in everyday life and nutrition.
							Juicefast juices and ready-made meals help me achieve better nutritional balance
							and leave more time for practicing yoga. A warm recommendation and namaste!"</p>
						<div class="fixed-reviews-author">
							<span class="rev-name">Maja Čović</span>
							<span>Yoga Instructor</span>
						</div>
                    </div>
                </div>
            </div>
            <!-- Pagination (dots) -->
            <div class="swiper-pagination jf-fixed-reviews-pagination"></div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('jf_fixed_reviews_slider', 'jf_swiper_slider_shortcode');

/**
 * @snippet		WooCommerce Default Reviews display as slider shortcode with Swiper.js
 */
function jf_woocommerce_reviews_slider($atts) {
    ob_start();

    // Shortcode Attributes
    $atts = shortcode_atts([
        'limit' => 5,  // Number of reviews to display
        'product_id' => '',  // Specific product ID (optional)
    ], $atts);

    $args = [
        'status'      => 'approve',
        'number'      => $atts['limit'],
        'post_id'     => !empty($atts['product_id']) ? $atts['product_id'] : '',
        'type'        => 'review', // Ensure only WooCommerce reviews are fetched
    ];

    $reviews = get_comments($args);

    if ($reviews) : ?>
        <div class="swiper-button-next jf-reviews-next"></div>
        <div class="swiper-button-prev jf-reviews-prev"></div>
        <div class="swiper jf-reviews-slider">
            <div class="swiper-wrapper">
                <?php foreach ($reviews as $review) : 
                    $rating = get_comment_meta($review->comment_ID, 'rating', true);
                    $review_date = date_i18n(get_option('date_format'), strtotime($review->comment_date));
                    $verified_icon = "/wp-content/uploads/2025/02/checklist.svg";
                ?>
                    <div class="swiper-slide">
                        <div class="jf-review-card">
                            
                            <!-- Comment at the top -->
                            <p class="jf-review-content">
                                <?php echo wp_trim_words(esc_html($review->comment_content), 45, '...'); ?>
                            </p>

                            <!-- Name, Stars, Date, and Verified Buyer -->
                            <div class="jf-review-footer">
                                <div class="jf-verified">
                                    <span class="jf-review-author"><?php echo esc_html($review->comment_author); ?></span>
                                    <img src="<?php echo esc_url($verified_icon); ?>" alt="Verified Buyer" width="14" height="14">
                                    <span>PROVJERENO</span>
                                </div>
                                <div class="jf-review-meta">
                                    <span class="jf-review-date"><?php echo esc_html($review_date); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination jf-reviews-pagination"></div>
        </div>
    <?php endif;

    return ob_get_clean();
}

add_shortcode('jf_reviews_slider', 'jf_woocommerce_reviews_slider');

/**
 * @snippet		WooCommerce Default Reviews display as grid shortcode
 */
function jf_woocommerce_reviews_grid_shortcode( $atts ) {
    ob_start();

    $atts = shortcode_atts([
        'number' => 6, // how many reviews to show
    ], $atts);

    $args = [
        'number' => $atts['number'],
        'status' => 'approve',
        'post_status' => 'publish',
        'post_type' => 'product',
    ];

    $comments = get_comments($args);

    if ($comments) {
        echo '<div class="jf-review-grid">';
        foreach ($comments as $comment) {
            $product = wc_get_product($comment->comment_post_ID);
            $rating = floatval(get_comment_meta($comment->comment_ID, 'rating', true));

            echo '<div class="jf-review-item">';

            // ⭐ Star rating
            echo '<div class="jf-review-stars">';
            for ($i = 1; $i <= 5; $i++) {
                if ($rating >= $i) {
                    echo '<img src="/wp-content/uploads/green-star-full.svg" alt="star" class="jf-star">';
                } elseif ($rating >= ($i - 0.5)) {
                    echo '<img src="/wp-content/uploads/green-half-star.svg" alt="half star" class="jf-star">';
                } else {
                    echo '<img src="/wp-content/uploads/empty-star.svg" alt="empty star" class="jf-star">';
                }
            }
            echo '</div>';

            // ✍️ Review content
            echo '<div class="jf-review-text">' . wp_kses_post(wpautop($comment->comment_content)) . '</div>';
			echo '<p class="jf-review-author">' . esc_html($comment->comment_author) . '</p>';

            if ($product) {
                echo '<p class="jf-review-product">For: <a href="' . get_permalink($product->get_id()) . '">' . esc_html($product->get_name()) . '</a></p>';
            }

            echo '</div>';
        }
        echo '</div>';
    }

    return ob_get_clean();
}
add_shortcode('jf_product_reviews', 'jf_woocommerce_reviews_grid_shortcode');

/**
 * @snippet		ACF FAQs repeater fields shortcode
 */
function jf_faq_shortcode() {
    if( !function_exists('get_field') ) return '<p>ACF plugin is required.</p>';

    $faqs = get_field('faq-acf-repeater');

    if( empty($faqs) ) return '<p>No FAQs available.</p>';

    ob_start();
    ?>
    <div class="jf-faq">
        <?php foreach($faqs as $index => $faq) : ?>
            <div class="jf-faq-item">
                <button class="jf-faq-question" aria-expanded="false" aria-controls="faq-<?php echo $index; ?>">
                    <h3 class="jf-faq-text"><?php echo esc_html($faq['faq_question']); ?></h3>
                    <span class="jf-faq-icon">+</span>
                </button>
                <div id="faq-<?php echo $index; ?>" class="jf-faq-answer">
                    <p><?php echo esc_html($faq['faq_answer']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('jf_faq', 'jf_faq_shortcode');

/**
 * @snippet Display WooCommerce product categories with reviews
 */
function display_product_categories_shortcode($atts) {
    $atts = shortcode_atts([
        'ids' => ''
    ], $atts, 'display_product_categories');

    if (empty($atts['ids'])) return '';

    $category_ids = explode(',', $atts['ids']);
    $output = '<div class="jf-category-boxes">';

    // Custom check icon URL
    $check_icon_url = '/wp-content/uploads/green-check-icon-bold.svg';

    foreach ($category_ids as $category_id) {
        $category = get_term($category_id, 'product_cat');
        if (!$category || is_wp_error($category)) continue;

        $category_link = get_term_link($category);
        $category_image_id = get_term_meta($category_id, 'thumbnail_id', true);
        $category_image = $category_image_id ? wp_get_attachment_image($category_image_id, 'medium') : '';

        // Fetch ACF Main Benefits
        $benefits = get_field('main_benefits', 'product_cat_' . $category_id);
        $benefits_list = '';
        if (!empty($benefits)) {
            $benefits_list .= '<ul class="jf-category-benefits">';
            foreach ($benefits as $benefit) {
                $benefits_list .= '<li><img src="' . esc_url($check_icon_url) . '" alt="Check" class="jf-benefit-icon"> ' . esc_html($benefit['benefit']) . '</li>';
            }
            $benefits_list .= '</ul>';
        }

        // Fetch products in category
        $args = [
            'post_type' => 'product',
            'posts_per_page' => -1,
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'id',
                    'terms' => $category_id,
                    'include_children' => true,
                ]
            ]
        ];
        $query = new WP_Query($args);
        $total_reviews = 0;
        $total_rating = 0;
        $lowest_price = null;

        while ($query->have_posts()) {
            $query->the_post();
            $product = wc_get_product(get_the_ID());
            if ($product) {
                // Reviews
                $total_reviews += $product->get_review_count();
                $total_rating += $product->get_average_rating() * $product->get_review_count();

                // Lowest price
                if ($product->is_purchasable() && !$product->is_type('grouped')) {
                    $price = floatval($product->get_price());
                    if (is_null($lowest_price) || $price < $lowest_price) {
                        $lowest_price = $price;
                    }
                }
            }
        }
        wp_reset_postdata();

        // Calculate average rating
        $average_rating = ($total_reviews > 0) ? $total_rating / $total_reviews : 0;

        // Format review output
        $review_text = '';
        if ($total_reviews > 0) {
            $review_text = '<div class="jf-category-reviews">';
            $review_text .= '<span class="jf-review-stars">' . display_stars($average_rating) . '</span>';
            $review_text .= '<span class="jf-review-count">' . number_format($average_rating, 1) . '/' . $total_reviews . ' recenzija</span>';
            $review_text .= '</div>';
        }

        // Format lowest price
        $lowest_price_html = '';
        if (!is_null($lowest_price)) {
            $lowest_price_html = '<div class="jf-category-price">From ' . wc_price($lowest_price) . '<span>/per package</span></div>';
        }

        // Output HTML
        $output .= '<div class="jf-category-box">';

        $output .= '<div class="jf-category-top-row">';

        // Left: Image
        if ($category_image) {
            $output .= '<div class="jf-category-left">';
            $output .= '<a href="' . esc_url($category_link) . '" class="jf-category-link">';
            $output .= '<div class="jf-category-image">' . $category_image . '</div>';
            $output .= '</a>';
            $output .= '</div>';
        }

        // Right: Title, Benefits, Price
        $output .= '<div class="jf-category-right">';
        $output .= '<a href="' . esc_url($category_link) . '" class="jf-category-link">';
        $output .= '<h3 class="jf-category-title">' . esc_html($category->name) . '</h3>';
        $output .= '</a>';
        $output .= '<div class="jf-review-mobile">' . $review_text . '</div>'; // ← MOBILE REVIEW PLACEMENT
        $output .= $benefits_list;
        $output .= $lowest_price_html;
        $output .= '</div>';

        $output .= '</div>'; // .jf-category-top-row

        // Bottom row: Button + Desktop Reviews
        $output .= '<div class="jf-category-bottom-row">';
        $output .= '<a href="' . esc_url($category_link) . '" class="jf-category-button">Order now</a>';
        $output .= '<div class="jf-review-desktop">' . $review_text . '</div>';
        $output .= '</div>';

        $output .= '</div>'; // .jf-category-box
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('display_product_categories', 'display_product_categories_shortcode');

/**
 * @snippet		Function to display stars based on average rating
 */
function display_stars($rating) {
    $full_stars = floor($rating); // Number of full stars
    $half_star = ($rating - $full_stars) >= 0.5 ? 1 : 0; // Check for half star
    $empty_stars = 5 - $full_stars - $half_star; // Number of empty stars

    $stars = '';

    // Full stars (SVG)
    for ($i = 0; $i < $full_stars; $i++) {
        $stars .= '<img src="/wp-content/uploads/green-star-full.svg" alt="Full Star" class="star full" />';
    }

    // Half star (SVG - You can modify this with a custom half-star SVG)
    if ($half_star) {
        $stars .= '<img src="/wp-content/uploads/green-half-star.svg" alt="Half Star" class="star half" />';
    }

    // Empty stars (You can either create an empty star SVG or use a transparent image for now)
    for ($i = 0; $i < $empty_stars; $i++) {
        $stars .= '<img src="/wp-content/uploads/empty-star.svg" alt="Empty Star" class="star empty" />';
    }

    return $stars;
}

/**
 * @snippet		Display market brands - Where to find us as slides
 */
function jf_sellers_carousel_shortcode() {
    ob_start(); ?>

    <div class="jf-sellers-wrapper">
        <div class="jf-sellers-track">
            <!-- Original Slides -->
            <div class="jf-sellers-slide"><img src="/wp-content/uploads/2025/02/Bipa-logo-black.png"></div>
            <div class="jf-sellers-slide"><img src="/wp-content/uploads/2025/02/DM-logo-black.png"></div>
            <div class="jf-sellers-slide"><img src="/wp-content/uploads/2025/02/Muller-logo-black.png"></div>
            <div class="jf-sellers-slide"><img src="/wp-content/uploads/2025/02/Rewe-Group-logo-black.png"></div>
            <div class="jf-sellers-slide"><img src="/wp-content/uploads/2025/02/Kaufland-logo-black.png"></div>
            <div class="jf-sellers-slide"><img src="/wp-content/uploads/2025/02/Billa-logo-black.png"></div>

            <!-- Duplicate Slides for Seamless Loop -->
            <div class="jf-sellers-slide"><img src="/wp-content/uploads/2025/02/Bipa-logo-black.png"></div>
            <div class="jf-sellers-slide"><img src="/wp-content/uploads/2025/02/DM-logo-black.png"></div>
            <div class="jf-sellers-slide"><img src="/wp-content/uploads/2025/02/Muller-logo-black.png"></div>
            <div class="jf-sellers-slide"><img src="/wp-content/uploads/2025/02/Rewe-Group-logo-black.png"></div>
            <div class="jf-sellers-slide"><img src="/wp-content/uploads/2025/02/Kaufland-logo-black.png"></div>
            <div class="jf-sellers-slide"><img src="/wp-content/uploads/2025/02/Billa-logo-black.png"></div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let track = document.querySelector('.jf-sellers-track');
            let slides = document.querySelectorAll('.jf-sellers-slide');

            // Duplicate slides dynamically for seamless loop
            for (let i = 0; i < slides.length; i++) {
                let clone = slides[i].cloneNode(true);
                track.appendChild(clone);
            }
        });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('jf_sellers', 'jf_sellers_carousel_shortcode');

/**
 * @snippet		Display fixed hero-banner.php with ACF dynamic content for elementor pages
 */
function jf_hero_banner_shortcode($atts) {
    ob_start(); // Start output buffering

    // Extract attributes (optional, allows for customization)
    $atts = shortcode_atts([
        'id' => get_the_ID() // Default to the current page ID if not provided
    ], $atts);

    $page_id = intval($atts['id']); // Ensure it's an integer

    // Determine the title based on context
    if (is_home()) { 
        // Blog page title (if set in Settings -> Reading)
        $title = get_the_title(get_option('page_for_posts'));
    } elseif (is_archive()) { 
        // Archive title (category, tag, author, etc.)
        if (is_category()) {
    $title = single_cat_title('', false);
		} elseif (is_tag()) {
			$title = single_tag_title('', false);
		} elseif (is_author()) {
			$title = get_the_author();
		} elseif (is_post_type_archive()) {
			$title = post_type_archive_title('', false);
		} else {
			$title = get_the_title($page_id);
		}
    } elseif (is_singular('post')) { 
        // Single blog post title
        $title = get_the_title();
    } else { 
        // Default for pages
        $title = get_the_title($page_id);
    }

    // Get ACF fields
    if (is_category()) {
    $term = get_queried_object();
    $short_description = get_field('short_description', 'category_' . $term->term_id);
		} else {
			$short_description = get_field('short_description', $page_id);
		}
    $banner_button = get_field('banner_button', $page_id);
    ?>

    <div class="jf-hero-banner-wrapper">
        <div class="jf-hero-banner">
            <div class="jf-hero-banner-cont">
                <h1 class="jf-hero-title"><?php echo esc_html($title); ?></h1>
                
                <?php if (!empty($short_description)) : ?>
                    <p class="jf-hero-desc"><?php echo esc_html($short_description); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($banner_button['button_text']) && !empty($banner_button['button_url'])) : ?>
                <div class="jf-btn-black">
                    <button><a href="<?php echo esc_url($banner_button['button_url']); ?>"><?php echo esc_html($banner_button['button_text']); ?></a></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    return ob_get_clean(); // Return the output buffer
}
add_shortcode('jf_hero_banner', 'jf_hero_banner_shortcode');

/**
 * @snippet		Display additional product categories as horizontal menu
 */
function horizontal_scroll_menu() {
    ob_start();

    // Get the ID of the Shop page
    $shop_page_id = wc_get_page_id('shop');

    // Load the repeater field from the Shop page
    $repeater_field = get_field('product_category_additional_menu', $shop_page_id);

    if ($repeater_field && !empty($repeater_field)) {
        echo '<div class="horizontal-scroll-menu">';
        echo '<ul>';
        foreach ($repeater_field as $row) {
            $term_id = $row['product_category_menu'];
            $term_object = get_term($term_id);
            if ($term_object) {
                $term_link = get_term_link($term_object);
                echo '<li><a href="' . esc_url($term_link) . '">' . esc_html($term_object->name) . '</a></li>';
            }
        }
        echo '</ul>';
        echo '</div>';
    }

    return ob_get_clean();
}

add_shortcode('horizontal_scroll_menu', 'horizontal_scroll_menu');

/**
 * 
 * Display product ingredients
 * Page: single product, category
 * 
 **/
function jf_ing_desktop_shortcode($atts) {
    $terms = get_the_terms(get_the_ID(), 'ingredients');
    if (!$terms || is_wp_error($terms)) {
        return '<p>No ingredients available.</p>';
    }

    $fixed_texts = [
        "It’s what’s inside that counts",
        "Backed by science, powered by nature.",
        "1.5 kilos of fruit and vegetables per juice."
    ];

    ob_start();

    // Desktop layout
    echo '<div class="jf-ing-desktop">';
    $row = 0;
    $ingredient_index = 0;
    while ($ingredient_index < count($terms) || $row < 3) {
        echo '<div class="jf-ing-row">';
        for ($i = 0; $i < 3; $i++) {
            $fixed_positions = [0 => 0, 1 => 2, 2 => 1];
            if (array_key_exists($row, $fixed_positions) && $i === $fixed_positions[$row]) {
                echo '<div class="jf-ing-box fixed" style="background-color: #fff;">' . esc_html($fixed_texts[$row]) . '</div>';
            } elseif ($ingredient_index < count($terms)) {
                $term = $terms[$ingredient_index];
                $image = get_field('ingredient_image', 'ingredients_' . $term->term_id);
                $description = get_field('ingredient_description', 'ingredients_' . $term->term_id);

                echo '<div class="jf-ing-box ingredient">';
                if ($image) {
                    echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '" />';
                }
                echo '<h3>' . esc_html($term->name) . '</h3>';
                if (!empty($description)) {
                    echo '<div class="ingredient-description">' . wp_kses_post($description) . '</div>';
                }
                echo '</div>';
                $ingredient_index++;
            } else {
                echo '<div class="jf-ing-box filler"></div>';
            }
        }
        echo '</div>';
        $row++;
    }
    echo '</div>'; // end .jf-ing-desktop

    // Mobile Swiper layout
    echo '<div class="jf-ing-mobile-swiper swiper">';
    echo '<div class="swiper-wrapper">';
    foreach ($terms as $term) {
        $image = get_field('ingredient_image', 'ingredients_' . $term->term_id);
        $description = get_field('ingredient_description', 'ingredients_' . $term->term_id);

        echo '<div class="swiper-slide jf-ing-box ingredient">';
        if ($image) {
            echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '" />';
        }
        echo '<h3>' . esc_html($term->name) . '</h3>';
        if (!empty($description)) {
            echo '<div class="ingredient-description">' . wp_kses_post($description) . '</div>';
        }
        echo '</div>';
    }
    echo '</div>'; // .swiper-wrapper
    echo '<div class="swiper-pagination"></div>';
    echo '<div class="swiper-button-prev"></div>';
    echo '<div class="swiper-button-next"></div>';
    echo '</div>'; // .swiper

    return ob_get_clean();
}
add_shortcode('jf_product_ing', 'jf_ing_desktop_shortcode');