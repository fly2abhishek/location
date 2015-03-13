<?php

/**
 * @file
 * Country field handler.
 */
 
use Drupal\Component\Utility\String;

// @codingStandardsIgnoreStart
class location_handler_field_location_country extends views_handler_field {

  /**
   * {@inheritdoc}
   */
  public function option_definition() {
    $options = parent::option_definition();
    $options['style'] = array('default' => 'name');

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['style'] = array(
      '#title' => t('Display style'),
      '#type' => 'select',
      '#options' => array('name' => t('Country name'), 'code' => t('Country code')),
      '#default_value' => $this->options['style'],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render($values) {
    if ($this->options['style'] == 'name') {
      return String::checkPlain(location_country_name($values->{$this->field_alias}));
    }
    else {
      return String::checkPlain(strtoupper($values->{$this->field_alias}));
    }
  }
}
// @codingStandardsIgnoreEnd
