<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

add_action('wp_enqueue_scripts', function () {
  if (is_page(3540)) {

    wp_enqueue_style(
      'mf-fantawsome',
      'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
      array(),
      '1.0.0'
    );

    wp_enqueue_style(
      'mf-style',
        get_stylesheet_directory_uri() . '/assets/css/style.css',
      array('elementor-frontend'),
      filemtime(get_stylesheet_directory() . '/assets/css/style.css'),
      'all'
    );

    wp_enqueue_script(
      'mf-vue',
      get_stylesheet_directory_uri() . '/assets/js/vue.global.js',
      array(),
      '1.0.0',
      true
    );

    wp_enqueue_script(
      'mf-vue-prime',
       get_stylesheet_directory_uri() . '/assets/js/primevue.js',
      array(),
      '1.0.0',
      true
    );

    wp_enqueue_script(
      'mf-aura',
       get_stylesheet_directory_uri() . '/assets/js/aura.js',
      array(),
      '1.0.0',
      true
    );

    wp_enqueue_script(
      'mf-map',
      sprintf(
        'https://maps.googleapis.com/maps/api/js?key=%s&loading=async',
         $_ENV['GOOGLE_MAP_API_KEY']
      ),
      array(),
      null,
      true
    );

    wp_enqueue_script(
      'mf-request-form',
      get_stylesheet_directory_uri() . '/assets/js/request-form.js',
      array('mf-vue'),
      filemtime(get_stylesheet_directory() . '/assets/js/request-form.js'),
      true
    );

    wp_localize_script('mf-request-form', 'mf_data', [
        'site_url' => get_site_url(),
        'assets_url' => get_stylesheet_directory_uri() . '/assets/js/',
        'orgId' => $_ENV['HEALTHY_ORGANIZATION_ID'],
    ]);
  }
}, 9999);

add_action('rest_api_init', function () {
    register_rest_route('api/v1', '/proxy', [
        'methods' => 'POST',
        'callback' => 'mf_proxy_handler',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('api/v1', '/thumbnails', [
        'methods'  => 'POST',
        'callback' => 'mf_thumbnails_handler',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('api/v1', '/log', [
        'methods'  => 'POST',
        'callback' => 'mf_api_log',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('api/v1', '/search-log', [
        'methods'  => 'POST',
        'callback' => 'mf_search_log',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('api/v1', '/generate-pdf', [
        'methods' => 'POST',
        'callback' => 'mf_generate_pdf',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('api/v1', '/form-progress', [
        'methods'  => 'POST',
        'callback' => 'save_form_progress',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('api/v1', '/form-submitted', [
        'methods'  => 'POST',
        'callback' => 'save_form_submitted_progress',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('api/v1', '/locations', [
        'methods'             => 'GET',
        'callback'            => 'mf_get_locations',
        'permission_callback' => '__return_true',
    ]);

    //my therapyflow webhook
    register_rest_route('api/v1', '/crm-lead', [
        'methods'             => 'POST',
        'callback'            => 'mf_send_lead_to_crm',
        'permission_callback' => '__return_true',
    ]);

});

function mf_send_lead_to_crm(WP_REST_Request $request) {
    $respond = function (bool $ok, string $reason = '') {
        $body = ['ok' => $ok];
        if (!$ok && $reason !== '') {
            $body['reason'] = $reason;
        }
        return new WP_REST_Response($body, 200);
    };

    try {
        $webhook = $_ENV['WEBHOOK_URL'] ?? '';
        if ($webhook === '') {
            error_log('CRM Healthy: WEBHOOK_URL is not configured.');
            return $respond(false, 'not_configured');
        }

        $p = $request->get_json_params();
        if (!is_array($p)) {
            return $respond(false, 'invalid_body');
        }

        $params = [];
        foreach ($p as $k => $v) {
            if (is_string($v) || is_numeric($v)) {
                $params[sanitize_key($k)] = sanitize_text_field((string) $v);
            }
        }

        $email = sanitize_email($params['email'] ?? '');
        if (!is_email($email)) {
            return $respond(false, 'invalid_email');
        }
        $params['email'] = $email;

        $utm_source = 'website';
        if (!empty($params['utm_source'])) {
            $utm_source = $params['utm_source'];
        } elseif (!empty($params['google_paid'])) {
            $utm_source = $params['google_paid'];
        }

        $page_url = esc_url_raw($p['page_url'] ?? home_url());
        unset($params['page_url']);

        $data = [
            'form_submission' => [
                'referrer'         => $utm_source,
                'referring_url'    => home_url(),
                'landing_page_url' => $page_url,
                'form_url'         => $page_url,
                'form_data'        => array_merge($params, ['source' => $_ENV['SOURCE'] ?? '']),
            ],
        ];

        $res = wp_remote_post($webhook, [
            'timeout' => 15,
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => wp_json_encode($data),
        ]);

        if (is_wp_error($res)) {
            error_log('CRM Healthy webhook request failed: ' . $res->get_error_message());
            return $respond(false, 'webhook_unreachable');
        }

        $code = wp_remote_retrieve_response_code($res);
        if ($code < 200 || $code >= 300) {
            error_log('CRM Healthy webhook returned HTTP ' . $code);
            return $respond(false, 'webhook_rejected');
        }

        return $respond(true);

    } catch (\Throwable $e) {
        error_log('CRM Healthy unexpected error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        return $respond(false, 'unexpected_error');
    }
}

function mf_fetch_locations_from_healthy_graphql() {
    $org_id = $_ENV['HEALTHY_ORGANIZATION_ID'];

    $query = 'query Locations {
        locations(organizationId: ' . wp_json_encode($org_id) . ') {
            healthieLocationId
            coordinates { latitude longitude }
            address { city postalCode }
        }
    }';

    $response = wp_remote_post($_ENV['HEALTHY_GRAPHQL_URL'], [
        'timeout' => 15,
        'headers' => [
            'Content-Type'  => 'application/json'
        ],
        'body' => wp_json_encode(['query' => $query]),
    ]);

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        return null;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    $locations = $body['data']['locations'] ?? null;

    if (!is_array($locations) || !empty($body['errors'])) {
        return null;
    }

    $locations = array_values(array_filter($locations, function ($l) {
        return !empty($l['coordinates']['latitude']) && !empty($l['coordinates']['longitude']);
    }));

    return ['data' => ['locations' => $locations]];
}

function mf_get_locations() {
    $fresh_key = 'mf_locations_fresh2';
    $stale_key = 'mf_locations_stale2';

    $cached = get_transient($fresh_key);
    if ($cached !== false) {
        return rest_ensure_response($cached);
    }

    $data = mf_fetch_locations_from_healthy_graphql();

    if ($data) {
        set_transient($fresh_key, $data, DAY_IN_SECONDS);
        set_transient($stale_key, $data, 7 * DAY_IN_SECONDS);
        return rest_ensure_response($data);
    }

    $stale = get_transient($stale_key);
    if ($stale !== false) {
        return rest_ensure_response($stale);
    }

    return new WP_Error('locations_unavailable', 'Could not load locations.', ['status' => 502]);
}

function mf_proxy_handler(WP_REST_Request $request) {

    $params = $request->get_json_params();

    $key = $params['key'] ?? null;
    $payload = $params['payload'] ?? [];

    if (!$key) {
        return new WP_REST_Response(['error' => 'Missing key'], 400);
    }

    try {
        $response = call_healthy_api($key, $payload);
        if (is_array($response)) {
            $provider_ids = array_unique(array_column($response, 'healthieProviderId'));

            $clinicians   = mf_get_clinicians_by_provider_ids($provider_ids);

            return new WP_REST_Response(
                mf_group_slots_with_clinicians($response, $clinicians),
                200
            );
        }

        // Other keys keep the original response shape
        return new WP_REST_Response($response, 200);
    } catch (Exception $e) {
        return new WP_REST_Response(['error' => $e->getMessage()], 500);
    }
}

function mf_group_slots_with_clinicians(array $slots, array $clinicians): array {
    $groups = [];

    foreach ($slots as $slot) {
        $hid = $slot['healthieProviderId'] ?? null;
        if (!$hid) {
            continue;
        }

        // Skip providers that have no matching WordPress post
        if (!isset($clinicians[$hid])) {
            continue;
        }

        if (!isset($groups[$hid])) {
            $groups[$hid] = [
                'clinician' => array_merge(
                    [
                        'id'                 => $slot['providerId'],
                    ],
                    $clinicians[$hid]
                ),
                'slots' => [],
            ];
        }

        $slot['id'] = $slot['providerId'] . '|' . $slot['start'];
        $groups[$hid]['slots'][] = $slot;
    }

    return array_values($groups);
}

function call_healthy_api($key, $payload = [], $method = 'POST') {

    $endpoints = [
        'fetch_slots' => $_ENV['HEALTHY_BASE_URL'] . '/matching/slots'
    ];

    if (!isset($endpoints[$key])) {
        throw new Exception("Invalid API key: $key");
    }

    $payload_hash = md5(json_encode($payload));
    $cache_key = 'healthy_response_' . substr($payload_hash, 0, 30);

    $cached_data = get_transient($cache_key);
    if ($cached_data !== false) {
        return $cached_data;
    }

    $url = $endpoints[$key];
    $args = [
        'method'  => $method,
        'timeout' => 20,
        'headers' => [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'x-functions-key'    => $_ENV['HEALTHY_API_KEY'],
        ],
        'body' => json_encode($payload),
    ];

    $res = wp_remote_post($url, $args);

    if (is_wp_error($res)) {
        throw new Exception($res->get_error_message());
    }

    $body = json_decode(wp_remote_retrieve_body($res), true);

    set_transient($cache_key, $body, DAY_IN_SECONDS);

    return $body;
}

function mf_get_clinicians_by_provider_ids(array $provider_ids, bool $include_payrate = false): array {
    $provider_ids = array_values(array_unique(array_filter(
        array_map('sanitize_text_field', $provider_ids)
    )));

    if (empty($provider_ids)) {
        return [];
    }

    $meta_key = 'mf-cares-healthierproviderid';

    $query = new WP_Query([
        'post_type'      => 'mf-cares',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'no_found_rows'  => true,
        'meta_query'     => [[
            'key'     => $meta_key,
            'value'   => $provider_ids,
            'compare' => 'IN',
        ]],
    ]);

    $fallback_thumbnail = home_url() . '/wp-content/uploads/2024/08/mindfully-lotus-logo-1.webp';

    $rate_map = [
        'CT' => 60, 'SWT' => 60,
        'LPC' => 100, 'LSW' => 100, 'LCS' => 100,
        'LISW' => 140, 'LPCC' => 140, 'LISW-S' => 140, 'LPCC-S' => 140,
    ];

    $results = [];

    foreach ($query->posts as $post) {
        $post_id     = $post->ID;
        $provider_id = get_post_meta($post_id, $meta_key, true);
        $slug        = $post->post_name;

        $description = get_post_meta($post_id, 'rank_math_description', true) ?: '';

        $thumbnail_url = has_post_thumbnail($post_id)
            ? get_the_post_thumbnail_url($post_id, 'thumbnail')
            : $fallback_thumbnail;

        $top_raw   = get_post_meta($post_id, 'mf-cares-top-specialities', true);
        $top_array = array_filter(array_map('trim', explode(',', (string) $top_raw)));

        if (empty($top_array)) {
            $specialities = [];
            $terms = get_the_terms($post_id, 'mf-cares-specialties');
            if (!is_wp_error($terms) && !empty($terms)) {
                shuffle($terms);
                $specialities = wp_list_pluck(array_slice($terms, 0, 3), 'name');
            }
        } else {
            $specialities = array_values($top_array);
        }

        $private_pay_rate = 0;
        if ($include_payrate) {
            $credentials = get_the_terms($post_id, 'mf-cares-credentials');
            if (!is_wp_error($credentials) && !empty($credentials)) {
                foreach (wp_list_pluck($credentials, 'name') as $cred) {
                    $cred = trim($cred);
                    if (isset($rate_map[$cred])) {
                        $private_pay_rate = max($private_pay_rate, $rate_map[$cred]);
                    }
                }
            }
        }

        $results[$provider_id] = [
            'name'             => get_the_title($post_id),
            'slug'             => $slug,
            'thumbnail_url'    => $thumbnail_url,
            'profile_url'      => home_url() . '/care/' . $slug,
            'description'      => $description,
            'specialities'     => $specialities,
            'private_pay_rate' => $private_pay_rate,
            'boosted'          => get_post_meta($post_id, 'mf-cares-boosted', true),
        ];
    }

    return $results;
}

function mf_api_log(WP_REST_Request $request) {
    $endpoint = sanitize_text_field($request->get_param('endpoint'));
    $payload  = $request->get_param('payload');
    $response = $request->get_param('response');

    $date     = current_time('Y-m-d');
    $time     = current_time('H:i:s');
    $log_dir  = ABSPATH . 'logs/form';
    $log_file = $log_dir . "/{$date}.json";

    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $log_entries = file_exists($log_file)
        ? json_decode(file_get_contents($log_file), true)
        : [];

    $log_entries[] = [
        'time'     => $time,
        'endpoint' => $endpoint,
        'payload'  => $payload,
        'response' => $response
    ];

    file_put_contents($log_file, json_encode($log_entries, JSON_PRETTY_PRINT));

    return new WP_REST_Response(['message' => 'Log saved successfully.'], 200);
}

function mf_search_log(WP_REST_Request $request) {

    $params = $request->get_json_params();

    $date     = current_time('Y-m-d');
    $log_dir  = ABSPATH . 'logs/search';
    $log_file = $log_dir . "/{$date}.json";

    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $log_entries = file_exists($log_file)
        ? json_decode(file_get_contents($log_file), true)
        : [];

    $log_entries[] = [
        'time'    => current_time('Y-m-d H:i:s'),
        $params,
    ];

    file_put_contents($log_file, json_encode($log_entries, JSON_PRETTY_PRINT));

    return new WP_REST_Response(['message' => 'Log saved successfully.'], 200);
}

function mf_generate_pdf($request) {
    $data = $request->get_json_params();

    $htmlTemplate = file_get_contents(__DIR__ . '/appointment-template.html');

    if ($data['type'] === 'teletherapy') {
        $htmlTemplate = preg_replace('/<!-- BEGIN_LOCATION -->(.*?)<!-- END_LOCATION -->/is', '', $htmlTemplate);
    }
    else{
        $htmlTemplate = preg_replace('/<!-- BEGIN_TELETHERAPY_WORDING -->(.*?)<!-- END_TELETHERAPY_WORDING -->/is', '', $htmlTemplate);
    }

    if ($data['reminder'] != true) {
        $htmlTemplate = preg_replace('/<!-- BEGIN_REMINDER -->(.*?)<!-- END_REMINDER -->/is', '', $htmlTemplate);
    }

    $replacements = [
        '{{appointment_date}}' => $data['appointment_date'],
        '{{appointment_time}}' => $data['appointment_time'],
        '{{therapist}}' => $data['therapist'],
        '{{location}}' => $data['location'],
        '{{client_name}}' => $data['client'],
    ];

    $finalHtml = strtr($htmlTemplate, $replacements);

    $options = new Dompdf\Options();
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf\Dompdf($options);
    $dompdf->loadHtml($finalHtml);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfOutput = $dompdf->output();

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="appointment-summary.pdf"');
    echo $pdfOutput;
    exit;
}

function save_form_progress(WP_REST_Request $request) {
    global $wpdb;

    $table = $wpdb->prefix . 'th_form_progress';
    $params  = $request->get_json_params();
    $session_id = $params['session_id'];

    $update_pairs = [];
    foreach ($params as $column => $value) {
        if ($column === 'session_id') continue;

        if ($column === 'max_step') {
            $update_pairs[] = "max_step = GREATEST(max_step, " . intval($value) . ")";
        } else {
            $update_pairs[] = "$column = " . $wpdb->prepare("%s", $value);
        }
    }

    $update_string = implode(', ', $update_pairs);

    $wpdb->query("
        INSERT INTO $table (session_id, max_step)
        VALUES ('$session_id', 1)
        ON DUPLICATE KEY UPDATE $update_string
    ");

    return new WP_REST_Response(['success' => true], 200);
}

function save_form_submitted_progress( $request ) {
    global $wpdb;

    $table = $wpdb->prefix . 'th_form_progress';
    $session_id = sanitize_text_field( $request->get_param('session_id') );
    $status     = sanitize_text_field( $request->get_param('status') );

    if ( empty( $session_id ) ) {
        return new WP_Error( 'missing_session', 'Session ID is required', array( 'status' => 400 ) );
    }

    $last_step = 6;

    $row = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM $table WHERE session_id = %s",
        $session_id
    ) );

    if ( !$row ) return array( 'success' => false );

    $max_step = max( $row->max_step, $last_step );
    $wpdb->update(
        $table,
        array(
            'max_step'          => $max_step,
            'submission_status' => $status,
            'submitted_at'        => current_time( 'mysql' ),
        ),
        array( 'session_id' => $session_id ),
        array( '%d', '%s', '%s' ),
        array( '%s' )
    );

    return array( 'success' => true );
}

add_action('wp_ajax_nopriv_elementor_pro_forms_send_form', 'intercept_schedule_popup_submission', 5);
add_action('wp_ajax_elementor_pro_forms_send_form', 'intercept_schedule_popup_submission', 5);

function intercept_schedule_popup_submission() {

  $data = array(
    'name' => sanitize_text_field($_POST['form_fields']['name']),
    'email' => sanitize_email($_POST['form_fields']['email']),
    'phone' => sanitize_text_field($_POST['form_fields']['phone'])
  );

  $response = wp_remote_post(
    MY_THERAPY_FLOW_WEBHOOK,
    [
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'body' => wp_json_encode($data),
      'blocking' => false,
    ]
  );

  if (is_wp_error($response)) {
    error_log('Third-party API Error: ' . $response->get_error_message());
    return false;
  }

  $status_code = wp_remote_retrieve_response_code($response);
  $body = wp_remote_retrieve_body($response);

  error_log('Third-party API Response: ' . $status_code . ' - ' . $body);
}
