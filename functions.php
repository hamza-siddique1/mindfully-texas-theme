<?php

function elementor_child_enqueue_styles() {
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );

    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style'),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'elementor_child_enqueue_styles');

function th_cta_link() {
    return "/request/";
}
add_shortcode('th_cta_link', 'th_cta_link');

function th_cta_text() {
    return "Book Your First Session";
}
add_shortcode('th_cta_text', 'th_cta_text');
