<?php

add_filter('jet-smart-filters/query/final-query', function ($query_args) {

    if (!empty($query_args['tax_query'])) {

        foreach ($query_args['tax_query'] as $index => $tax_query) {

            if (
                isset($tax_query['taxonomy']) &&
                $tax_query['taxonomy'] === 'mf-cares-locations' &&
                isset($tax_query['terms']) &&
                in_array(670, (array) $tax_query['terms'])
            ) {
                unset($query_args['tax_query'][$index]);
            }
        }

        $query_args['tax_query'] = array_values($query_args['tax_query']);
    }

    return $query_args;

}, 10, 1);
