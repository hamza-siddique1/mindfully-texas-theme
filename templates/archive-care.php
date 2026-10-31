<?php

global $wp;
?>

<style>
  .container {
    margin: 80px 0px;
  }

  .sm-padding {
    padding: 30px;
  }

  .sm-radius {
    border-radius: 8px;
  }

  ul {
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .btn {
    font-size: 18px;
    width: auto;
    border-radius: 100px;
    padding: 8px 40px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .btn-primary {
    color: #fff;
    background: #411aa8;
    border: none;
  }

  .btn-primary:hover {
    color: #fff;
    background: #7253cc;
  }

  .btn-secondary {
    color: #411aa8;
    background: #fff;
    border: 2px solid #411aa8;
    padding: 6px 38px;
  }

  .btn-secondary:hover {
    color: #7253cc;
    border-color: #7253cc;
  }

  .flex-horizontal {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
  }

  .flex-vertical {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
  }

  .sec-shadow {
    box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.05);
  }

  .sec-border {
    border: 1px solid rgba(17, 18, 26, 0.05);
  }

  #form-care {
    display: flex;
    flex-direction: row;
    align-items: end;
    gap: 20px;
    font-size: 18px;
  }

  #form-care input,
  #form-care select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border-color 0.3s ease;
  }

  .badge {
    position: absolute;
    top: 0;
    right: 0;
    background-color: #411AA8;
    color: #fff;
    padding: 0px 10px;
    border-radius: 10px;
    transform: translate(-20%, 10%);
  }

  .pagination {
    margin-top: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
  }

  .pagination>li>a {
    border-radius: 0px;
    padding: 5px 10px;
  }

  @media only screen and (max-width: 1024px) {
    .flex-horizontal {
      flex-wrap: wrap;
    }

    .flex-vertical {
      text-align: center;
    }

    .btn {
      font-size: 16px;
    }

    .form-actions {
      flex-direction: column;
    }

    #form-care .form-actions [type="submit"],
    #form-care .form-actions [type="reset"],
    #form-care>div.form-actions>a {
      width: 100%;
      text-align: center;
    }

    #form-care {
      flex-direction: column;
      align-items: stretch;
    }
  }

  @media only screen and (max-width: 767px) {
    .btn {
      font-size: 14px;
    }
  }
</style>

<div class="container">
  <div class="sm-padding sec-shadow sec-border sm-radius">
    <form action="" method="GET" id="form-care" class="flex-horizontal" style="flex-wrap: nowrap;">
      <div>
        <label for="care_staff_name">Search for care ...</label>
        <input type="text" id="care_staff_name" name="staff_name" value="<?php if (isset ($_GET['staff_name'])) {
          echo $_GET['staff_name'];
        } ?>" id="filter-name" placeholder="e.g. name, specialty, email, services, etc.">
      </div>

      <?php
      global $post;
      $post_slug = $post->post_name;

      if ($post_slug != 'colorado-telehealth-counseling') {
        ?>
        <div>
          <label for="care_zipcode">Zip Code</label>
          <input type="text" name="zipcode" value="<?php if (isset ($_GET['zipcode'])) {
            echo $_GET['zipcode'];
          } ?>" id="care_zipcode">
        </div>
      <?php } ?>

      <div>
        <label for="care_speciality">Looking for help with ...</label><br>
        <select name="speciality" id="care_speciality">
          <option value="">Select a speciality</option>
          <?php
          $response = wp_remote_get(
            sprintf(
              'https://cdn.contentful.com/spaces/%s/entries?content_type=%s&order=fields.name&limit=1000',
              CONTENTFUL_SPACE_ID,
              'specialties',
            ),
            [
              'headers' => [
                'Authorization' => sprintf('Bearer %s', CONTENTFUL_ACCESS_TOKEN)
              ],
            ]
          );

          $body = wp_remote_retrieve_body($response);
          $data = json_decode($body, true);

          foreach ($data['items'] as $item) {
            if (empty ($item) || empty ($item['fields']['name'])) {
              continue;
            }

            $selected = '';
            if (isset ($_GET['speciality'])) {
              if ($_GET['speciality'] == $item['sys']['id']) {
                $selected = 'selected';
              }
            }

            echo sprintf('<option value="%s" %s>%s</option>', $item['sys']['id'], $selected, $item['fields']['name']);
          }
          ?>
        </select>
      </div>

      <div class="form-actions" style="display: flex; align-items: end; gap: 20px;">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="<?php echo home_url($wp->request); ?>" class="btn btn-secondary">Clear</a>
      </div>
    </form>
  </div>

  <div id="listing-items" style="margin-top: 50px;">
    <ul class="flex-horizontal">
      <?php
      $zipcodes = [];
      $nearest_location = [];
      $page_size = 50;
      $skip = 0;

      if (isset ($_GET['skip'])) {
        $skip = (int) $_GET['skip'];
      }

      // get content from contentful
      $url = sprintf(
        $url_template,
        CONTENTFUL_SPACE_ID,
        $skip,
        $state,
      );

      // apply zip code filter
      $location_distances = [];
      $valid_nearest_locations = [];

      if (isset ($_GET['zipcode']) && !empty ($_GET['zipcode'])) {
        $zipcode = $_GET['zipcode'];

        $zipcodes = get_contentful_office_locations();

        $state_name = '';
        if ($state == '6puDUIuTRkQGKzMofl5mu') {
          $state_name = 'Ohio';
        } else if ($state == '4GSyTbBQO7HelU5GbjaRMB') {
          $state_name = 'Colorado';
        }

        $nearest_locations = get_contentful_nearset_zipcode($zipcode, $zipcodes, $state_name);

        foreach ($nearest_locations as $zipcode => $distance) {
          if (kilometers_to_miles($distance) <= 30) {
            $valid_nearest_locations[] = $zipcodes[$zipcode]['id'];
          }
        }

        if (!empty ($valid_nearest_locations)) {
          foreach ($zipcodes as $key => $value) {
            $location_distances[$value['id']] = $nearest_locations[$key] ?? '';
          }
        }

        // add non office location as well
        $valid_nearest_locations[] = '1ycNSL3tuSuw1pNTgaoJAX';

        $url = sprintf('%s&fields.officeLocation.sys.id[in]=%s', $url, implode(',', $valid_nearest_locations));
        $page_size = 1000;
      }

      // apply search for care filter
      if (isset ($_GET['staff_name']) && !empty ($_GET['staff_name'])) {
        $url = sprintf('%s&query=%s', $url, $_GET['staff_name']);
      }

      // apply specialty filter
      if (isset ($_GET['speciality']) && !empty ($_GET['speciality'])) {
        $url = sprintf('%s&fields.specialties.sys.id=%s', $url, $_GET['speciality']);
      }

      // apply page size
      $url = sprintf('%s&limit=%s', $url, $page_size);

      $response = wp_remote_get($url, [
        'headers' => [
          'Authorization' => sprintf('Bearer %s', CONTENTFUL_ACCESS_TOKEN)
        ],
      ]);

      $body = wp_remote_retrieve_body($response);
      $data = json_decode($body, true);

      $items = $data['items'];

      // get total items
      $total_items = $data['total'];
      $total_pages = ceil($total_items / $page_size);

      // get all the images
      $assets = [];
      foreach ($data['includes']['Asset'] as $asset) {
        $id = $asset['sys']['id'];
        $image_url = $asset['fields']['file']['url'];
        $assets[$id] = $image_url;
      }

      // loop through each item
      $staffs = [];
      foreach ($data['items'] as $key => $item) {
        $id = $item['sys']['id'];
        $slug = $item['fields']['slug'] ?? '';
        $name = $item['fields']['name'] ?? '';
        $image_id = $item['fields']['image']['sys']['id'] ?? '';
        $waiting_room_link = $item['fields']['waitingRoomLink'] ?? '';

        if (!empty ($image_id)) {
          $image = $assets[$image_id];
        } else {
          $image = 'https://images.ctfassets.net/e3w7r8ejfead/27Gcsk7qvn4qTg83rkvOO0/c5ddbe7a58241c3821bb0e6d0e522b1b/mindfully-lotus-logo.png';
        }

        if (!empty ($image)) {
          $image = sprintf('%s?w=200&h=200&fm=webp&fit=thumb&r=max', $image);
        }

        // check office location
        $distance = 'default';
        foreach ($item['fields']['officeLocation'] as $location) {
          $location_id = $location['sys']['id'] ?? '';
          $location_dis = $location_distances[$location_id] ?? '';

          if (in_array($location_id, $valid_nearest_locations)) {
            if ($location_id != '1ycNSL3tuSuw1pNTgaoJAX') {
              if ($location_distances[$location_id] == 0) {
                $distance = kilometers_to_miles($location_distances[$location_id]);
              }

              if (!empty ($location_dis)) {
                $distance = kilometers_to_miles($location_distances[$location_id]);
              }
            }
          }
        }

        $staffs[] = [
          'slug' => $slug,
          'name' => $name,
          'image' => $image,
          'distance' => $distance,
          'waiting_room_link' => $waiting_room_link
        ];
      }

      if (isset ($_GET['zipcode']) && !empty ($_GET['zipcode'])) {
        usort($staffs, function ($a, $b) {
          if ($a['distance'] === 'default') {
            return 1;
          } elseif ($b['distance'] === 'default') {
            return -1;
          }

          return $a['distance'] - $b['distance'];
        });
      }

      $virtual_waiting_room = '';
      foreach ($staffs as $staff) {
        $slug = $staff['slug'];
        $name = $staff['name'];
        $image = $staff['image'];
        $distance = $staff['distance'];
        $waiting_room_link = $staff['waiting_room_link'] ?? '';

        $url = sprintf('%s/care/%s', home_url(), $slug);
        if ($virtual_waiting_room) {
            $url = $waiting_room_link;
        }
        ?>
        <li>
          <a class="flex-vertical" href="<?php echo $url; ?>" target="_blank">
            <div style="position: relative;">
              <img src="<?php echo $image; ?>" alt="<?php echo sprintf('%s profile picture', $name); ?>">

              <?php if ($distance != 'default') { ?>
                <span class="badge">
                  <?php echo $distance . ' mi'; ?>
                </span>
              <?php } ?>
            </div>

            <?php echo $name; ?>
          </a>
        </li>
      <?php } ?>
    </ul>

    <?php
    if (empty ($data['items'])) {
      echo '<p style="text-align: center; font-size: 24px; font-weight: bold;">No results found!</p>';
    }
    ?>
  </div>

  <div class="pagination md-margin">
    <ul class="pagination">
      <?php
      $baseUrl = strtok($_SERVER["REQUEST_URI"], '?');
      $queryString = $_SERVER["QUERY_STRING"];

      parse_str($queryString, $queryParams);

      if ($skip > 0) {
        $queryParams['skip'] = max(0, $skip - $page_size);
        $prevUrl = $baseUrl . '?' . http_build_query($queryParams);
        echo sprintf('<li><a href="%s" class="btn">Previous</a></li>', $prevUrl);
      }

      for ($i = 1; $i <= $total_pages; $i++) {
        $queryParams['skip'] = ($i - 1) * $page_size;
        $pageUrl = $baseUrl . '?' . http_build_query($queryParams);

        if ($i == $skip / $page_size + 1) {
          echo sprintf('<li><a href="%s" class="btn btn-primary">%s</a></li>', $pageUrl, $i);
        } else {
          echo sprintf('<li><a href="%s" class="btn">%s</a></li>', $pageUrl, $i);
        }
      }

      if ($skip < ($total_pages - 1) * $page_size) {
        $queryParams['skip'] = $skip + $page_size;
        $nextUrl = $baseUrl . '?' . http_build_query($queryParams);
        echo sprintf('<li><a href="%s" class="btn">Next</a></li>', $nextUrl);
      }
      ?>
    </ul>
  </div>
</div>

<script>
  <?php if (isset ($_GET['staff_name']) || isset ($_GET['staff_specialty']) || isset ($_GET['staff_zipcode']) || isset ($_GET['skip'])) { ?>
    jQuery(document).ready(function ($) {
      $('html, body').animate({
        scrollTop: $('#form-care').offset().top - 180
      }, 0);
    });
  <?php } ?>
</script>
