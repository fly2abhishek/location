<?php

/**
 * @file
 * Contains \Drupal\location\Element\LocationSettings.
 */

namespace Drupal\location\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;

/**
 * @FormElement("location_settings")
 */
class LocationSettings extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return array(
      '#input' => TRUE,
      '#process' => array(
        array($class, 'processLocationSettings'),
      ),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#tree' => TRUE,
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {}

  /**
   * Sets the #checked property of a checkbox element.
   */
  public static function processLocationSettings(&$element, FormStateInterface $form_state, &$complete_form) {
    //$element['#attributes']['type'] = 'location_settings';

    // Set a value for the fieldset that doesn't interfere with rendering and doesn't generate a warning.
    $element['#tree'] = TRUE;
    $element['#theme'] = 'location_settings';
  
    if (!isset($element['#title'])) {
      $element['#title'] = t('Location Fields');
    }
    if (!isset($element['#default_value']) || $element['#default_value'] == 0) {
      $element['#default_value'] = array();
    }
  
    // Force #tree on.
    $element['#tree'] = TRUE;
  
    $defaults = $element['#default_value'];
    if (!isset($defaults) || !is_array($defaults)) {
      $defaults = array();
    }
    $temp = location_invoke_locationapi($element, 'defaults');
    foreach ($temp as $k => $v) {
      if (!isset($defaults[$k])) {
        $defaults[$k] = array();
      }
      $defaults[$k] = array_merge($v, $defaults[$k]);
    }
  
    $fields = location_field_names();
  
    // Options for fields.
    $options = array(
      0 => t('Do not collect'),
      1 => t('Allow'),
      2 => t('Require'),
      // Need to consider the new "defaults" when saving.
      4 => t('Force Default'),
    );
  
    foreach ($fields as $field => $title) {
      $element[$field] = array(
        '#type' => 'fieldset',
        '#tree' => TRUE,
      );
      $element[$field]['name'] = array(
        '#type' => 'item',
        '#markup' => $title,
      );
      $element[$field]['collect'] = array(
        '#type' => 'select',
        '#default_value' => $defaults[$field]['collect'],
        '#options' => $options,
      );
  
      $dummy = array();
      $widgets = location_invoke_locationapi($dummy, 'widget', $field);
      if (!empty($widgets)) {
        $element[$field]['widget'] = array(
          '#type' => 'select',
          '#default_value' => $defaults[$field]['widget'],
          '#options' => $widgets,
        );
      }
  
      $temp = $defaults[$field]['default'];
      $element[$field]['default'] = location_invoke_locationapi($temp, 'field_expand', $field, 1, $defaults);
      $defaults[$field]['default'] = $temp;
  
      $element[$field]['weight'] = array(
        '#type' => 'weight',
        '#delta' => 100,
        '#default_value' => $defaults[$field]['weight'],
      );
    }
  
    // 'Street Additional' field should depend on 'Street' setting.
    // It should never be required and should only display when the street field is 'allowed' or 'required'.
    // @todo Alter here.
  
    return $element;
  }

}
