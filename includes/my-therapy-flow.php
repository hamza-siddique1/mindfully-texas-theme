<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

add_action('template_redirect', function () {
  global $wp;

  if (!isset($_GET['form'])) {
    return;
  }

  $utm_source = 'website';
  if (isset($_GET['utm_source'])) {
    $utm_source = $_GET['utm_source'];
  } elseif (isset($_GET['google_paid'])) {
    $utm_source = $_GET['google_paid'];
  }

  $data = [
    'form_submission' => array(
      'referrer' => $utm_source,
      'referring_url' => home_url(),
      'landing_page_url' => home_url($wp->request),
      'form_url' => home_url($wp->request),
      'form_data' => [
          'source' => $_ENV['SOURCE'],
        ],
    ),
  ];

  $data['form_submission']['form_data'] = array_merge($data['form_submission']['form_data'], $_GET);


  $formData = $data['form_submission']['form_data'] ?? [];

  $name  = $formData['name']  ?? null;
  $email = $formData['email'] ?? null;

  if (empty($name) && empty($email)) {
      return;
  }

  $response = wp_remote_post(
    $_ENV['WEBHOOK_URL'],
    [
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'body' => wp_json_encode($data),
      'blocking' => false,
    ]
  );

  wp_remote_retrieve_body($response);

});
