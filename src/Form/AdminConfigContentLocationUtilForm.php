<?php

/**
 * @file
 * Contains \Drupal\location\Form\AdminConfigContentLocationUtilForm.
 */

namespace Drupal\location\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings for Location module.
 */
class AdminConfigContentLocationUtilForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'location_admin_config_content_location_util_form';
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
    $form['province_clear'] = array(
      '#type' => 'fieldset',
      '#title' => t('Clear province cache'),
      '#description' => t(
        'If you have modified location.xx.inc files, you will need to clear the province cache to get Location to recognize the modifications.'
      ),
    );
  
    $form['supported_countries_clear'] = array(
      '#type' => 'fieldset',
      '#title' => t('Clear supported country list'),
      '#description' => t(
        'If you have added support for a new country, you will need to clear the supported country list to get Location to recognize the modifications.'
      ),
    );
  
    $form['actions'] = array(
      '#type' => 'actions',
    );
    $form['actions']['province_clear_submit'] = array(
      '#type' => 'submit',
      '#value' => t('Clear province cache'),
    );
    $form['actions']['supported_countries_clear_submit'] = array(
      '#type' => 'submit',
      '#value' => t('Clear supported country list'),
    );
  
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $submitted_button = $form_state->getTriggeringElement();

    switch ($submitted_button['#id']) {
      case 'edit-province-clear-submit':
        drupal_set_message(t('Location province cache cleared.'));
        // TODO: Verify this in fact deletes all provinces:* options (i.e. the $wildcard param in D7's cache_clear_all() function).
        \Drupal::cache('location')->delete('provinces:');
        break;
      
      case 'edit-supported-countries-clear-submit':
        drupal_set_message(t('Location supported country list cleared.'));
        \Drupal::cache('location')->delete('location:supported-countries');
        break;
    }
  }

}
