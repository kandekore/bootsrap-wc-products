<?php
if (!defined('ABSPATH')) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class BPD_Elementor_Widget extends Widget_Base {

    public function get_name() {
        return 'bpd_products';
    }

    public function get_title() {
        return 'Bootstrap Product Display';
    }

    public function get_icon() {
        return 'eicon-products';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section('content_section', [
            'label' => __('Product Display Settings', 'bpd'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('category', [
            'label' => __('Category ID(s)', 'bpd'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'description' => 'Enter comma-separated WooCommerce category IDs',
        ]);

        $this->add_control('columns', [
            'label' => __('Columns', 'bpd'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'max' => 6,
            'default' => 3,
        ]);

        $this->add_control('limit', [
            'label' => __('Number of Products', 'bpd'),
            'type' => Controls_Manager::NUMBER,
            'min' => 1,
            'max' => 50,
            'default' => 9,
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        echo bpd_render_products($settings['category'], $settings['columns'], $settings['limit']);
    }
}

// Register widget with Elementor
add_action('elementor/widgets/register', function($widgets_manager) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    if (is_plugin_active('woocommerce/woocommerce.php')) {
        $widgets_manager->register(new \BPD_Elementor_Widget());
    }
});
