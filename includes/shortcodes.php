<?php

function mf_get_first_sentence($text) {
    $text = strip_tags(trim($text));
    if (empty($text)) {
        return '';
    }
    preg_match('/^.*?[.!?](?:\s|$)/u', $text, $matches);
    return isset($matches[0]) ? trim($matches[0]) : $text;
}

function mf_seo_first_sentence($atts) {
    $atts = shortcode_atts(array(
        'field'   => 'rank_math_description',
        'post_id' => get_the_ID(),
    ), $atts);

    if (empty($atts['field'])) {
        return '';
    }

    $meta_value = get_post_meta($atts['post_id'], $atts['field'], true);

    if (empty($meta_value)) {
        return '';
    }

    return mf_get_first_sentence($meta_value);
}
add_shortcode('seo_first_sentence', 'mf_seo_first_sentence');
