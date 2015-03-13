<?php

/**
 * @file
 * Coordinates field handler.
 */
 
use Drupal\Component\Utility\String;

// @codingStandardsIgnoreStart
class location_views_handler_field_coordinates extends location_views_handler_field_latitude {
  /**
   * {@inheritdoc}
   */
  public function construct() {
    parent::construct();
    $this->additional_fields['longitude'] = 'longitude';
  }

  /**
   * {@inheritdoc}
   */
  public function render($values) {
    if ($this->options['style'] == 'dms') {
      $render = array(
        '#theme' => 'location_latitude_dms',
        '#latitude' => $values->{$this->field_alias},
      );
      $return = render($render);
      $render = array(
        '#theme' => 'location_longitude_dms',
        '#longitude' => $values->{$this->aliases['longitude']},
      );
      $return .= render($render);
      return $return;
    }
    else {
      return String::checkPlain($values->{$this->field_alias}) . ', ' . String::checkPlain($values->{$this->aliases['longitude']});
    }
  }
}
// @codingStandardsIgnoreEnd
