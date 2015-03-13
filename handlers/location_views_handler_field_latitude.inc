<?php

/**
 * @file
 * Latitude field handler.
 */
 
use Drupal\Component\Utility\String;

// @codingStandardsIgnoreStart
class location_views_handler_field_latitude extends views_handler_field {

  /**
   * {@inheritdoc}
   */
  public function option_definition() {
    $options = parent::option_definition();
    $options['style'] = array('default' => 'dms');

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
      '#options' => array(
        'dd' => t('Decimal degrees'),
        'dms' => t('Degrees, minutes, seconds'),
      ),
      '#default_value' => $this->options['style'],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render($values) {
    if ($this->options['style'] == 'dd') {
      return String::checkPlain($values->{$this->field_alias});
    }
    else {
      $render = array(
        '#theme' => 'location_latitude_dms',
        '#latitude' => $values->{$this->field_alias},
      );
      return render($render);
    }
  }
}
// @codingStandardsIgnoreEnd
