<?php

/**
 * @file
 * Contains \Drupal\location\Element\LocationSettingsEntity.
 */

namespace Drupal\location\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element; // TODO: Remove this once confirmed it isn't necessary.
use Drupal\Core\Render\Element\FormElement;

/**
 * @FormElement("location_settings")
 */
class LocationSettingsEntity extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return array(
      '#input' => TRUE,
      '#return_value' => 1,
      '#process' => array(
        array($class, 'processLocationSettings'),
        /*array($class, 'processAjaxForm'),
        array($class, 'processGroup'),*/
      ),
      /*'#pre_render' => array(
        array($class, 'preRenderCheckbox'),
        array($class, 'preRenderGroup'),
      ),*/
      //'#theme' => 'location_settings',
      //'#theme_wrappers' => array('form_element'),
      //'#title_display' => 'after',
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {}

  /**
   * Prepares a #type 'checkbox' render element for input.html.twig.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   *   Properties used: #title, #value, #return_value, #description, #required,
   *   #attributes, #checked.
   *
   * @return array
   *   The $element with prepared variables ready for input.html.twig.
   */
  /*public static function preRenderCheckbox($element) {
    $element['#attributes']['type'] = 'checkbox';
    Element::setAttributes($element, array('id', 'name', '#return_value' => 'value'));

    // Unchecked checkbox has #value of integer 0.
    if (!empty($element['#checked'])) {
      $element['#attributes']['checked'] = 'checked';
    }
    static::setAttributes($element, array('form-checkbox'));

    return $element;
  }*/

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
  
  
  

  /*public function getInfo() {
    $class = get_class($this);
    return array(
      '#input' => TRUE,
      '#process' => array(
        $class, 'processLocationSettings'
      ),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#tree' => TRUE,
    );
  }*/

  /*public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    dsm($element['#default_value']);
  }*/
  
  /**
   * Process location settings.
   */
  /*public static function processLocationSettings(&$element, FormStateInterface $form_state, &$complete_form) {
    dsm('Here');
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
    dsm($fields);
  
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
    
    dsm($element);
  
    return $element;
  }

  public static function validateLocation(&$element, FormStateInterface $form_state, &$complete_form) {
    // ...
  }*/

}
      