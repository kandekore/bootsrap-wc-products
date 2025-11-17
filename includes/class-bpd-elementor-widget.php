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
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        // WooCommerce categories
        $categories = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ]);

        $options = [];
        if (!empty($categories) && !is_wp_error($categories)) {
            foreach ($categories as $cat) {
                $options[$cat->slug] = $cat->name;
            }
        }

        $this->add_control('category', [
            'label'       => __('Category', 'bpd'),
            'type'        => Controls_Manager::SELECT2,
            'options'     => $options,
            'multiple'    => true,
            'description' => 'Select product categories to display',
        ]);

        $this->add_control('columns', [
            'label'   => __('Columns', 'bpd'),
            'type'    => Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 6,
            'default' => 3,
        ]);

        $this->add_control('limit', [
            'label'   => __('Number of Products', 'bpd'),
            'type'    => Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 50,
            'default' => 9,
        ]);

        $this->add_control('icon_class', [
            'label'       => __('Icon CSS Class (Font Awesome)', 'bpd'),
            'type'        => Controls_Manager::TEXT,
            'default'     => '',
            'placeholder' => 'e.g. fa-solid fa-users',
            'description' => 'Uses Font Awesome 6. Leave empty for no icon.',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $category = '';
        if (!empty($settings['category'])) {
            $category = is_array($settings['category'])
                ? implode(',', $settings['category'])
                : $settings['category'];
        }

        echo bpd_render_products(
            $category,
            $settings['columns'],
            $settings['limit'],
            isset($settings['icon_class']) ? $settings['icon_class'] : ''
        );
    }
}
