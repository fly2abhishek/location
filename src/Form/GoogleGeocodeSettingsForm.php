<?php

/**
 * @file
 * Contains \Drupal\location\Form\GoogleGeocodeSettingsForm.
 */

namespace Drupal\location\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * General settings for this geocoder.
 */
class GoogleGeocodeSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'location_google_geocode_settings_form';
  }
  
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['location.variables'];
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $country_iso = NULL, $service = NULL) {
    // Get the config.
    $config = \Drupal::config('location.variables');

    $form['location_geocode_google_apikey'] = array(
      '#type' => 'textfield',
      '#title' => t('Google Geocoding API Server Key'),
      '#size' => 64,
      '#maxlength' => 128,
      '#default_value' => $config->get('location_geocode_google_apikey'),
      '#description' => t(
        'In order to use the Google Geocoding API web-service, you will need a Google Geocoding API Server Key.  You can obtain one at the !sign_up_link for the !google_maps_api.  Without a key daily requests from a single IP address will be automaticaly limited.  If you do not enter a key here this module will use the Google Maps API Key from gmap if one is present. NOTE: You will <em>not</em> have to re-enter your API key for each country for which you have selected Google Maps for geocoding.  This setting is global.',
        array(
          '!sign_up_link' => '<a href="http://console.developers.google.com/">sign-up page</a>',
          '!google_maps_api' => '<a href="http://developers.google.com/maps/documentation/geocoding/">Google Geocoding API</a>',
        )
      ),
    );
  
    $form['location_geocode_google_delay'] = array(
      '#type' => 'textfield',
      '#title' => t('Delay between geocoding requests (is milliseconds)'),
      '#description' => t(
        'To avoid a 620 error (denial of service) from Google, you can add a delay between geocoding requests. 200ms is recommended.'
      ),
      '#default_value' => $config->get('location_geocode_google_delay'),
      '#size' => 10,
    );
  
    $country = \Drupal::routeMatch()->getParameter('country_iso');
  
    if ($country) {
      $location_geocode = $config->get('location_geocode_' . $country . '_google_accuracy_code');
      if (is_null($location_geocode)) {
        $location_geocode = $config->get('location_geocode_google_minimum_accuracy');
      }
      $form['location_geocode_' . $country . '_google_accuracy_code'] = array(
        '#type' => 'select',
        '#title' => t('Google Maps Geocoding Accuracy for %country', array('%country' => $country)),
        '#default_value' => $location_geocode,
        '#options' => location_google_geocode_accuracy_codes(),
        '#description' => t('The minimum required accuracy for the geolocation data to be saved.'),
      );
    }
  
    return parent::buildForm($form, $form_state);
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    dsm($form_state);
    /*parent::submitForm($form, $form_state);
    $config = $this->config('location.variables');
    
    // Set up an array of config to check for values.
    $config_settings = array(
      'location_default_country',
      'location_display_location',
      'location_use_province_abbreviation',
      'location_usegmap',
      'location_locpick_macro',
      'location_jit_geocoding',
      'location_maplink_external',
      'location_maplink_external_method'
    );
    
    // Loop through the array and determine which config has values.
    foreach ($config_settings as $config_setting) {
      if ($form_state->hasValue($config_setting)) {
        $config->set($config_setting, $form_state->getvalue($config_setting));
      }
    }
    
    // Save the config.
    $config->save();*/
  }

}
