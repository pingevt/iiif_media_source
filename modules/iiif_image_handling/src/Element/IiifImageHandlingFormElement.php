<?php

namespace Drupal\iiif_image_handling\Element;

use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a colour picker field form element.
 *
 * Properties:
//  * - #colour_values: A comma separated list of hex codes.
 *
 * Usage example:
 * @code
 * $form['colour'] = [
 *   '#type' => 'iiif_image_handler',
 *   '#title' => $this->t('Image'),
 *   '#default_value' => '',
 *   '#required' => TRUE,
 * ];
 * @endcode
 *
 * @FormElement("iiif_image_handler")
 */
class IiifImageHandlingFormElement extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      // '#input' => TRUE,
      // '#size' => 7,
      // '#maxlength' => 7,
      // '#pattern' => '#([A-Fa-f0-9]{6})',
      // '#process' => [
      //   [$class, 'processAjaxForm'],
      //   [$class, 'processPattern'],
      // ],
      // '#pre_render' => [
      //   [$class, 'preRenderColorPicker'],
      // ],
      '#theme' => 'iiif_image_handler2',
      '#theme_wrappers' => ['form_element'],
      '#attached' => [
        // 'library' => ['color_picker/color_picker'],
      ],
    ];
  }

  /**
   * Prepares a 'iiif_image_handler' render element for iiif-image-handler.html.twig.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   *
   * @return array
   *   The $element with prepared variables ready for input.html.twig.
   */
  // public static function preRenderColorPicker(array $element) {
  //   $element['#attributes']['type'] = 'text';
  //   Element::setAttributes($element, [
  //     'id',
  //     'name',
  //     'value',
  //     'size',
  //     'maxlength',
  //     'color_values',
  //   ]);
  //   static::setAttributes($element, ['form-color-picker']);

  //   return $element;
  // }

}
