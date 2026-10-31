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

add_filter( 'jet-smart-filters/query/final-query', function( $query ) {

    if ( empty( $query['jet_smart_filters'] ) || $query['jet_smart_filters'] !== 'jet-engine/cares-filter' ) {
        return $query;
    }

    if ( empty( $query['meta_query'] ) ) {
        return $query;
    }

    foreach ( $query['meta_query'] as $index => $meta_query ) {

        if ( isset( $meta_query['key'] ) && $meta_query['key'] === 'age_filter' ) {

            $age = (int) $meta_query['value'];

            $query['meta_query'][$index] = [
                'relation' => 'AND',
                [
                    'key'     => 'mf-cares-min-age',
                    'value'   => $age,
                    'compare' => '<=',
                    'type'    => 'NUMERIC'
                ],
                [
                    'key'     => 'mf-cares-max-age',
                    'value'   => $age,
                    'compare' => '>=',
                    'type'    => 'NUMERIC'
                ],
            ];

            break;
        }
    }

    return $query;
} );

//
