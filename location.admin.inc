<?php

/**
 * @file
 * Admin forms for Location.
 */




/**
 * Theme callback.
 */
function theme_location_map_link_options($variables) {
  $form = $variables['form'];

  $header = array(
    array('align' => 'center', 'data' => '<center>' . t('Country') . '</center>'),
    array('align' => 'center', 'data' => '<center>' . t('Options') . '</center>')
  );
  $rows = array();
  foreach (element_children($form['countries']) as $country_iso) {
    $row = array();
    $row[] = array(
      'data' => drupal_render($form['countries'][$country_iso]['label_' . $country_iso]),
    );
    $row[] = array(
      'data' => drupal_render($form['countries'][$country_iso]['location_map_link_' . $country_iso]),
    );
    $rows[] = $row;
  }
  $output = theme('table', array('header' => $header, 'rows' => $rows));
  $output .= drupal_render_children($form);

  return $output;
}

/**
 * Theme callback.
 */
function theme_location_geocoding_options($variables) {
  $form = $variables['form'];

  $output = drupal_render($form['location_geocode_google_minimum_accuracy']);
  $header = array(
    array('align' => 'center', 'data' => '<center>' . t('Country') . '</center>'),
    array('align' => 'center', 'data' => '<center>' . t('Options') . '</center>'),
    array('align' => 'center', 'data' => '<center>' . t('Configure') . '</center>')
  );
  $rows = array();
  foreach (element_children($form['countries']) as $country_iso) {
    $row = array();
    $row[] = array(
      'data' => drupal_render($form['countries'][$country_iso]['label_' . $country_iso])
    );
    $row[] = array(
      'data' => drupal_render($form['countries'][$country_iso]['location_geocode_' . $country_iso])
    );
    $row[] = array(
      'data' => drupal_render($form['countries'][$country_iso]['location_geocode_config_link_' . $country_iso])
    );
    $rows[] = $row;
  }
  $output .= theme('table', array('header' => $header, 'rows' => $rows));
  $output .= drupal_render_children($form);

  return $output;
}

/**
 * Location utilities form: Clear province cache.
 */
function location_util_form_clear_province_cache_submit() {
  
}

/**
 * Location utilities form: Clear supported countries cache.
 */
function location_util_form_clear_supported_countries_submit() {
  
}
