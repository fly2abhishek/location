<?php

/**
 * @file
 * Contains \Drupal\location\Element\LocationElementEntity.
 */

namespace Drupal\location\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

/**
 * @FormElement("location_element")
 */
class LocationElementEntity extends FormElement {

  public function getInfo() {
    $class = get_class($this);
    return array(
      '#input' => TRUE,
      '#process' => array(
        array($class, 'processLocation'),
      ),
      '#tree' => TRUE,
      '#location_settings' => array(),
      '#required' => FALSE,
      '#attributes' => array('class' => array('location')),
      '#element_validate' => array('location_element_validate'),
    );
  }
  
  public static function processLocation(&$element, FormStateInterface $form_state, &$complete_form) {
    
  }
  
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    // ...
  }

  public static function preRenderTextfield($element) {
    // ...
  }

}
      