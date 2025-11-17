<?php
/**
 * Plugin Name: Bootstrap Product Display
 * Description: Display WooCommerce products in Bootstrap cards using shortcode or Elementor widget.
 * Version: 1.7
 * Author: D Kandekore
 */

if (!defined('ABSPATH')) exit;

define('BPD_PLUGIN_DIR', plugin_dir_path(__FILE__));

/**
 * --------------------------------------------------------
 * ADMIN SETTINGS
 * --------------------------------------------------------
 */
add_action('admin_menu', 'bpd_add_admin_menu');
add_action('admin_init', 'bpd_register_settings');

function bpd_add_admin_menu() {
    add_options_page(
        'Bootstrap Product Display',
        'Product Display',
        'manage_options',
        'bootstrap-product-display',
        'bpd_options_page'
    );
}

function bpd_register_settings() {
    register_setting('bpd_settings_group', 'bpd_border_color');
    register_setting('bpd_settings_group', 'bpd_divider_color');
    register_setting('bpd_settings_group', 'bpd_bg_color');
    register_setting('bpd_settings_group', 'bpd_desc_length');
    register_setting('bpd_settings_group', 'bpd_button_text');
    register_setting('bpd_settings_group', 'bpd_default_image');
    register_setting('bpd_settings_group', 'bpd_button_color');
    register_setting('bpd_settings_group', 'bpd_text_color');
}

function bpd_options_page() { ?>
    <div class="wrap">
        <h1>Bootstrap Product Display Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('bpd_settings_group'); ?>
            <?php do_settings_sections('bpd_settings_group'); ?>
            <table class="form-table">
                <tr>
                    <th>Border Color</th>
                    <td><input type="text" name="bpd_border_color" value="<?php echo esc_attr(get_option('bpd_border_color', '#cccccc')); ?>" /></td>
                </tr>
                <tr>
                    <th>Divider Color</th>
                    <td><input type="text" name="bpd_divider_color" value="<?php echo esc_attr(get_option('bpd_divider_color', '#eeeeee')); ?>" /></td>
                </tr>
                <tr>
                    <th>Background Color</th>
                    <td><input type="text" name="bpd_bg_color" value="<?php echo esc_attr(get_option('bpd_bg_color', '#ffffff')); ?>" /></td>
                </tr>
                <tr>
                    <th>Text Color</th>
                    <td><input type="text" name="bpd_text_color" value="<?php echo esc_attr(get_option('bpd_text_color', '#333333')); ?>" /></td>
                </tr>
                <tr>
                    <th>Button Color</th>
                    <td><input type="text" name="bpd_button_color" value="<?php echo esc_attr(get_option('bpd_button_color', '#007bff')); ?>" /></td>
                </tr>
                <tr>
                    <th>Description Limit (characters)</th>
                    <td><input type="number" name="bpd_desc_length" value="<?php echo esc_attr(get_option('bpd_desc_length', 300)); ?>" min="50" max="2000" /></td>
                </tr>
                <tr>
                    <th>Button Text</th>
                    <td><input type="text" name="bpd_button_text" value="<?php echo esc_attr(get_option('bpd_button_text', 'Find out more')); ?>" /></td>
                </tr>
                <tr>
                    <th>Default Image (URL)</th>
                    <td>
                        <input type="text" name="bpd_default_image" value="<?php echo esc_attr(get_option('bpd_default_image', '')); ?>" style="width:80%;" placeholder="https://example.com/default.jpg" />
                        <p><em>Paste a Media Library image URL or external image URL.</em></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }

/**
 * --------------------------------------------------------
 * SHORTCODE
 * --------------------------------------------------------
 */
add_shortcode('bootstrap_products', 'bpd_display_products');

function bpd_display_products($atts) {
    $atts = shortcode_atts([
        'category'   => '',
        'columns'    => '3',
        'limit'      => '12',
        'icon_class' => '', // e.g. "fa-solid fa-users"
    ], $atts, 'bootstrap_products');

    return bpd_render_products(
        $atts['category'],
        $atts['columns'],
        $atts['limit'],
        $atts['icon_class']
    );
}

/**
 * --------------------------------------------------------
 * RENDER FUNCTION (shared by shortcode & Elementor)
 * --------------------------------------------------------
 */
function bpd_render_products($category = '', $columns = 3, $limit = 12, $icon_class = '') {
    $border_color  = get_option('bpd_border_color', '#cccccc');
    $divider_color = get_option('bpd_divider_color', '#eeeeee');
    $bg_color      = get_option('bpd_bg_color', '#ffffff');
    $text_color    = get_option('bpd_text_color', '#333333');
    $button_color  = get_option('bpd_button_color', '#007bff');
    $desc_length   = intval(get_option('bpd_desc_length', 300));
    $button_text   = esc_html(get_option('bpd_button_text', 'Find out more'));
    $default_image = trim(get_option('bpd_default_image', ''));

    $args = [
        'post_type'      => 'product',
        'posts_per_page' => intval($limit),
        'tax_query'      => [],
    ];

    if (!empty($category)) {
        $args['tax_query'][] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => explode(',', $category),
        ];
    }

    $products = new WP_Query($args);
    if (!$products->have_posts()) {
        return '<p>No products found.</p>';
    }

    // Bootstrap
    wp_enqueue_style(
        'bpd-bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
        [],
        '5.3.2'
    );

    // Font Awesome (only if icon requested)
    if (!empty($icon_class)) {
        wp_enqueue_style(
            'bpd-fontawesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
            [],
            '6.5.2'
        );
    }

    $col_class = 'col-md-' . (12 / max(1, intval($columns)));

    ob_start(); ?>
    <style>
        .bpd-card-body{
            display:flex;
            flex-direction:column;
        }
        .bpd-section-divider{
            width:75%;
            margin:0.75rem auto;
            border:0;
            border-top:1px solid var(--bpd-divider-color, #cccccc);
            align-self:center;
        }
      .bpd-image-divider-wrapper {
    position: relative;
    text-align:center;
    margin: 0;
    padding: 0px 0 30px;
    border-top: 10px solid var(--bpd-divider-color, #cccccc); 
}

.bpd-image-divider-line {
    border: none;
    border-top: 10px solid #ccc; /* overridden inline */
    margin: 0 auto;
    width: 100%;
}

.bpd-image-divider-icon {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    background: var(--bpd-divider-color, #fff)!important; 
    z-index: 3;
        font-size: x-large;
}


        
    </style>

    <div class="container my-4">
        <div class="row g-4">
            <?php while ($products->have_posts()) : $products->the_post();
                global $product;

                $short_desc = wp_strip_all_tags($product->get_short_description());
                $long_desc  = wp_strip_all_tags($product->get_description());
                if (strlen($long_desc) > $desc_length) {
                    $long_desc = substr($long_desc, 0, $desc_length) . '...';
                }

                // Default image logic
          $image_url = '';

if (has_post_thumbnail()) {
    $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
}

if (!$image_url || $image_url === false) {
    $fallback = trim(get_option('bpd_default_image', ''));
    if (!empty($fallback)) {
        $image_url = esc_url($fallback);
    }
}

if (!$image_url || $image_url === false) {
    // final fallback that ALWAYS works
    $image_url = 'https://via.placeholder.com/800x500?text=No+Image';
}

                ?>
                <div class="<?php echo esc_attr($col_class); ?>">
                    <div class="card h-100 shadow-sm"
                         style="border:2px solid <?php echo esc_attr($border_color); ?>;
                                background-color:<?php echo esc_attr($bg_color); ?>;">
                        <?php if ($image_url): ?>
                            <img src="<?php echo esc_url($image_url); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>

                        <!-- Straight divider + icon between image and content -->
                        <div class="bpd-image-divider-wrapper"
                             style="--bpd-divider-color:<?php echo esc_attr($divider_color); ?>;">
<hr class="bpd-image-divider-line" style="border-top:1px solid <?php echo esc_attr($divider_color); ?>;">
                            <?php if (!empty($icon_class)): ?>
                                <span class="bpd-image-divider-icon"
                                      style="background-color:<?php echo esc_attr($bg_color); ?>;">
                                    <i class="<?php echo esc_attr($icon_class); ?>"
                                       style="color:<?php echo esc_attr($bg_color); ?>;"></i>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="card-body bpd-card-body"
                             style="color:<?php echo esc_attr($text_color); ?>;
                                    --bpd-divider-color:<?php echo esc_attr($divider_color); ?>;">
                            <!-- Title -->
                            <h5 class="card-title text-center mb-2"><?php the_title(); ?></h5>
                            <hr class="bpd-section-divider">

                            <!-- Short Description -->
                            <?php if (!empty($short_desc)): ?>
                                <p class="small text-center mb-2"><?php echo esc_html($short_desc); ?></p>
                                <hr class="bpd-section-divider">
                            <?php endif; ?>

                            <!-- Long Description (trimmed) -->
                            <?php if (!empty($long_desc)): ?>
                                <p class="small text-center flex-grow-1 mb-3"><?php echo esc_html($long_desc); ?></p>
                                <hr class="bpd-section-divider">
                            <?php endif; ?>

                            <!-- Price -->
                            <p class="card-text fw-bold text-center mb-3">
                                <?php echo $product->get_price_html(); ?>
                            </p>

                            <!-- Button -->
                            <a href="<?php the_permalink(); ?>"
                               class="btn w-100"
                               style="background-color:<?php echo esc_attr($button_color); ?>; color:#fff;">
                                <?php echo esc_html($button_text); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}

/**
 * --------------------------------------------------------
 * ELEMENTOR WIDGET REGISTER
 * --------------------------------------------------------
 */
function bpd_register_elementor_widget($widgets_manager) {
    if (!class_exists('WooCommerce')) return;
    require_once BPD_PLUGIN_DIR . 'includes/class-bpd-elementor-widget.php';
    $widgets_manager->register(new \BPD_Elementor_Widget());
}
add_action('elementor/widgets/register', 'bpd_register_elementor_widget');
