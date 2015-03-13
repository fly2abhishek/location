<?php

/**
 * @file
 * Contains \Drupal\location\Form\AdminConfigContentLocationMapLinkingForm.
 */

namespace Drupal\location\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings page for map links.
 */
class AdminConfigContentLocationMapLinkingForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'location_config_content_location_maplinking_form';
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
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the config.
    $config = \Drupal::config('location.variables');

    $form['countries'] = array(
      '#type' => 'markup',
      '#markup' => ''
    );
  
    foreach (_location_supported_countries() as $country_iso => $country_name) {
      location_load_country($country_iso);
  
      $form['countries'][$country_iso] = array(
        '#type' => 'markup',
        '#markup' => ''
      );
  
      $form['countries'][$country_iso]['label_' . $country_iso] = array(
        '#type' => 'markup',
        '#markup' => $country_name
      );
  
      // Set up '#options' array for mapping providers for the current country.
      $mapping_options = array();
      $provider_function = 'location_map_link_' . $country_iso . '_providers';
      $default_provider_function = 'location_map_link_' . $country_iso . '_default_providers';
  
      // Default providers will be taken from the country specific default providers
      // function if it exists, otherwise it will use the global function.
      $checked = $config->get('location_map_link_' . $country_iso);
      if (is_null($checked)) {
        $checked = function_exists($default_provider_function) ? $default_provider_function() : location_map_link_default_providers();
      }
  
      // Merge the global providers with country specific ones so that countries
      // can add to or override the defaults.
      $providers = function_exists($provider_function) ? array_merge(
        location_map_link_providers(),
        $provider_function()
      ) : location_map_link_providers();
      foreach ($providers as $name => $details) {
        $mapping_options[$name] = '<a href="' . $details['url'] . '">' . $details['name'] . '</a> (<a href="' . $details['tos'] . '">Terms of Use</a>)';
      }
  
      if (count($mapping_options)) {
        $form['countries'][$country_iso]['location_map_link_' . $country_iso] = array(
          '#title' => '',
          '#type' => 'checkboxes',
          '#default_value' => $checked,
          '#options' => $mapping_options,
        );
      }
      else {
        $form['countries'][$country_iso]['location_map_link_' . $country_iso] = array(
          '#type' => 'markup',
          '#markup' => t('None supported.'),
        );
      }
    }

    $form['countries']['#theme'] = 'location_map_link_options';
  
    return parent::buildForm($form, $form_state);
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->config('location.variables');
    
    // Loop through all supported countries and save the config value for each.
    foreach (_location_supported_countries() as $country_iso => $country_name) {
      $config->set('location_map_link_' . $country_iso, $form_state->getValue('location_map_link_' . $country_iso));
    }
    
    // Save the config.
    $config->save();
  }

}
