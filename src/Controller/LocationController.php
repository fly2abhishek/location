<?php

/**
 * @file
 * Contains \Drupal\location\Controller\LocationController.
 */

namespace Drupal\location\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Component\Utility\Unicode;

/**
 * Returns responses for location module routes.
 */
class LocationController extends ControllerBase {

  /**
   * Create a list of states from a given country.
   *
   * @param string $country
   *   The country code
   *
   * @param string $string
   *   The state name typed by user
   *
   * @return array
   *   Javascript array. List of states
   */
  public function autocompletePage($country = NULL, $string = NULL) {
    // If no country or string is supplied, return a blank json response.
    if (!isset($country) && !isset($string)) {
      return new JsonResponse();
    }
    $counter = 0;
    $string = strtolower($string);
    $string = '/^' . preg_quote($string) . '/';
    $matches = array();
  
    if (strpos($country, ',') !== FALSE) {
      // Multiple countries specified.
      $provinces = array();
      $country = explode(',', $country);
      foreach ($country as $c) {
        $provinces = $provinces + self::getProvinces($c);
      }
    }
    else {
      $provinces = self::getProvinces($country);
    }
  
    if (!empty($provinces)) {
      while (list($code, $name) = each($provinces)) {
        if ($counter < 5) {
          if (preg_match($string, strtolower($name))) {
            $matches[] = $name;
            ++$counter;
          }
        }
      }
    }
    return new JsonResponse($matches);
  }
  
  /**
   * Page callback.
   */
  public function geocodingParametersPage($country_iso, $service) {
    // TODO: RE-implement drupal_set_title() functionality. See: https://www.drupal.org/node/2067859.
    // drupal_set_title(t('Configure parameters for %service geocoding', array('%service' => $service)), PASS_THROUGH);

    // TODO: Re-implement drupal_get_breadcrumb()/drupal_set_breadcrumb() functionality. See: https://www.drupal.org/node/1947536.
    /*$breadcrumbs = drupal_get_breadcrumb();
    $breadcrumbs[] = l(t('Location'), 'admin/config/content/location');
    $breadcrumbs[] = l(t('Geocoding'), 'admin/config/content/location/geocoding');
    $countries = location_get_iso3166_list();
    $breadcrumbs[] = l(
      $countries[$country_iso],
      'admin/config/content/location/geocoding',
      array('fragment' => $country_iso)
    );
    drupal_set_breadcrumb($breadcrumbs);*/
    
    location_load_country($country_iso);
    
    $geocode_settings_form_function_specific = 'location_geocode_' . $country_iso . '_' . $service . '_settings';
    // TODO: Unify this in a way that allows other users to create their own custom form class.
    /*if (function_exists($geocode_settings_form_function_specific)) {
      return parent::buildForm($geocode_settings_form_function_specific(), $form_state);
    }*/
    location_load_geocoder($service);
    $geocode_settings_form_function_general = Unicode::ucfirst($service . 'GeocodeSettings') . "Form";
    // TODO: Extend this to check other namespaces.
    if (class_exists("\Drupal\location\Form\\$geocode_settings_form_function_general")) {
      return \Drupal::formBuilder()->getForm('Drupal\location\Form\GoogleGeocodeSettingsForm');
    }
    else {
      return array('#markup' => 'No configuration parameters are necessary, or a form to take such parameters has not been implemented.');
    }
  }
  
  // @@@ New in 3.x, document.
  /**
   * Fetch the provinces for a country.
   */
  public function getProvinces($country = 'us') {
    $provinces = & drupal_static(__FUNCTION__, array());
    // Current language.
    $lang_code = $GLOBALS['language']->language;
  
    location_standardize_country_code($country);
    if (!isset($provinces[$country])) {
      if ($cache = \Drupal::cache('location')->get("provinces:$country:$lang_code")) {
        $provinces[$country] = $cache->data;
      }
      else {
        location_load_country($country);
        $func = 'location_province_list_' . $country;
        if (function_exists($func)) {
          $provinces[$country] = $func();
          \Drupal::cache('location')->set("provinces:$country:$lang_code", $provinces[$country]);
        }
      }
    }
  
    // Invoke hook_location_provinces_alter and
    // hook_location_provinces_COUNTRY_CODE_alter.
    \Drupal::moduleHandler()->alter(
      array('location_provinces', 'location_provinces_' . $country),
      $provinces,
      $country
    );
  
    return isset($provinces[$country]) ? $provinces[$country] : array();
  }

}
