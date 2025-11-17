<?php
/**
 * Plugin Name: Bootstrap Product Display
 * Description: Display WooCommerce products in Bootstrap cards using shortcode or Elementor widget.
 * Version: 1.2
 * Author: Darren Kandekore
 */

if (!defined('ABSPATH')) exit;

// Define plugin path
define('BPD_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include Elementor widget file
add_action('plugins_loaded', function() {
    if (did_action('elementor/loaded')) {
        require_once BPD_PLUGIN_DIR . 'includes/class-bpd-elementor-widget.php';
    }
});

// --------------------
// ADMIN SETTINGS
// --------------------

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
    register_setting('bpd_settings_group', 'bpd_desc_length');
    register_setting('bpd_settings_group', 'bpd_button_text');
}

function bpd_options_page() { ?>
    <div class="wrap">
        <h1>Bootstrap Product Display Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('bpd_settings_group'); ?>
            <?php do_settings_sections('bpd_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Border Color (HEX)</th>
                    <td><input type="text" name="bpd_border_color" value="<?php echo esc_attr(get_option('bpd_border_color', '#cccccc')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Divider Color (HEX)</th>
                    <td><input type="text" name="bpd_divider_color" value="<?php echo esc_attr(get_option('bpd_divider_color', '#eeeeee')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Description Length (characters)</th>
                    <td><input type="number" name="bpd_desc_length" value="<?php echo esc_attr(get_option('bpd_desc_length', 120)); ?>" min="20" max="500" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Button Text</th>
                    <td><input type="text" name="bpd_button_text" value="<?php echo esc_attr(get_option('bpd_button_text', 'Find out more')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }

// --------------------
// SHORTCODE
// --------------------

add_shortcode('bootstrap_products', 'bpd_display_products');

function bpd_display_products($atts) {
    $atts = shortcode_atts(array(
        'category' => '',
        'columns'  => '3',
        'limit'    => '12',
    ), $atts, 'bootstrap_products');

    return bpd_render_products($atts['category'], $atts['columns'], $atts['limit']);
}

// --------------------
// RENDER FUNCTION (shared by shortcode & Elementor)
// --------------------

function bpd_render_products($category = '', $columns = 3, $limit = 12) {
    $border_color = get_option('bpd_border_color', '#cccccc');
    $divider_color = get_option('bpd_divider_color', '#eeeeee');
    $desc_length = intval(get_option('bpd_desc_length', 120));
    $button_text = esc_html(get_option('bpd_button_text', 'Find out more'));

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => intval($limit),
        'tax_query' => array()
    );

    if (!empty($category)) {
        $args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => explode(',', $category),
        );
    }

    $products = new WP_Query($args);

    if (!$products->have_posts()) {
        return '<p>No products found.</p>';
    }

    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');

    $col_class = 'col-md-' . (12 / max(1, intval($columns)));

    ob_start(); ?>
    <div class="container my-4">
        <div class="row g-4">
            <?php while ($products->have_posts()) : $products->the_post();
                global $product;
                $desc = strip_tags(get_the_excerpt());
                if (strlen($desc) > $desc_length) {
                    $desc = substr($desc, 0, $desc_length) . '...';
                } ?>
                <div class="<?php echo esc_attr($col_class); ?>">
                    <div class="card h-100" style="border: 2px solid <?php echo esc_attr($border_color); ?>;">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>
                        <hr style="border: 1px solid <?php echo esc_attr($divider_color); ?>; margin:0;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php the_title(); ?></h5>
                            <p class="card-text text-muted small flex-grow-1"><?php echo esc_html($desc); ?></p>
                            <p class="card-text fw-bold mb-3"><?php echo $product->get_price_html(); ?></p>
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary w-100"><?php echo esc_html($button_text); ?></a>
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
