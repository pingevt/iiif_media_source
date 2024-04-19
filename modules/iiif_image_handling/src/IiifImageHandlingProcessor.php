<?php

namespace Drupal\iiif_image_handling;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\crop\Entity\Crop;

class IiifImageHandlingProcessor {
  /**
   *
   */
  public static function buildElementCrop(array $element, FormStateInterface $form_state, $context) {
    // ksm($element, $form_state, $context);

    $items = $context['items'];
    $delta = $context['delta'];
    $widget = $context['widget'];

    // $element['#field_name'] = $field_name;
    $element['#process'][] = [static::class, 'processCrop'];
    if (!in_array([static::class, 'process'], $element['#process'])) {
      $element['#process'][] = [static::class, 'process'];
    }

    $element['#item'] = $items[$delta]->getValue();

    if (!$items[$delta]->isEmpty()) {
      // ksm($items[$delta]);

      $img = $items[$delta]->getImg($items[$delta]->getValue());
      $element['#item']['full_url'] = $img->getFullUrl();
      $element['#item']['width'] = $img->getwidth();
      $element['#item']['height'] = $img->getHeight();

      $crop_size = $widget->getThirdPartySetting('iiif_image_crop', 'crop_preview_image_style_size') ?? 500;
      $scaled_url = $img->getScaledUrl($crop_size, $crop_size);

      $element['crop_preview'] = [
        '#theme' => 'image',
        '#uri' => $scaled_url,
        '#weight' => 1,
        '#attributes' => [
          'style' => "margin: 0 1rem 3rem 0",
        ],
        '#prefix' => "<div class='cropper-image'>",
        '#suffix' => "</div>",
      ];
    }

    $element['#crop'] = [
      'offsets' => $widget->getSetting('offsets'),
    ];

    return $element;
  }

  /**
   *
   */
  public static function buildElementFocalPoint(array $element, FormStateInterface $form_state, $context) {
    $items = $context['items'];
    $delta = $context['delta'];
    $widget = $context['widget'];

    unset($element['thumbnail']);

    $element['#process'][] = [static::class, 'processFocalPoint'];
    if (!in_array([static::class, 'process'], $element['#process'])) {
      $element['#process'][] = [static::class, 'process'];
    }
    else {
      unset($element['#process'][array_search([static::class, 'process'], $element['#process'])]);
      $element['#process'][] = [static::class, 'process'];
    }

    $element['#item'] = $items[$delta]->getValue();

    if (!$items[$delta]->isEmpty()) {
      // ksm($items[$delta]);

      $img = $items[$delta]->getImg($items[$delta]->getValue());
      $element['#item']['full_url'] = $img->getFullUrl();
      $element['#item']['width'] = $img->getwidth();
      $element['#item']['height'] = $img->getHeight();

      // ksm($widget->getSettings());
      // ksm($widget->getThirdPartySettings());

      $crop_size = $widget->getThirdPartySetting('iiif_image_focalpoint', 'focal_point_preview_image_style_size') ?? 500;
      $scaled_url = $img->getScaledUrl($crop_size, $crop_size);

      $element['fp_preview'] = [
        '#theme' => 'image',
        '#uri' => $scaled_url,
        '#weight' => 1,
        '#attributes' => [
          // 'style' => "margin: 0 1rem 3rem 0",
        ],
      ];

    }

    $element['#focal_point'] = [
      'offsets' => $widget->getSetting('offsets'),
    ];


    return $element;
  }

  /**
   *
   */
  public static function process($element, FormStateInterface $form_state, $form) {

    if (isset($element['crop_preview']) && isset($element['fp_preview'])) {
      $element['additional_settings'] = array(
        '#type' => 'vertical_tabs',
        '#title' => 'Vertical Tabs',
        '#weight' => 99,
      );

      if (isset($element['crop_preview']) && !isset($element['crop_preview']['#group'])) {

        $element['preview_group_crop'] = [
          '#type' => 'details',
          '#title' => t('Crop'),
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
          '#group' => $element['#field_name'] . '][0][additional_settings',
        ];

        $element['crop_preview']['#group'] = $element['#field_name'] . '][' . $element['#delta'] . '][preview_group_crop';
        $element['crop_preview']['#process'][] = ['Drupal\Core\Render\Element\RenderElement', 'processGroup'];
        $element['crop_preview']['#pre_render'][] = ['Drupal\Core\Render\Element\RenderElement', 'preRenderGroup'];

      }

      if (isset($element['fp_preview']) && !isset($element['fp_preview']['#group'])) {
        $element['preview_group_focal_point'] = [
          '#type' => 'details',
          '#title' => t('Focal Point'),
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
          '#group' => $element['#field_name'] . '][0][additional_settings',
        ];

        $element['fp_preview']['#group'] = $element['#field_name'] . '][' . $element['#delta'] . '][preview_group_focal_point';
        $element['fp_preview']['#process'][] = ['Drupal\Core\Render\Element\RenderElement', 'processGroup'];
        $element['fp_preview']['#pre_render'][] = ['Drupal\Core\Render\Element\RenderElement', 'preRenderGroup'];
      }
    }

    return $element;
  }


  /**
   * {@inheritdoc}
   *
   * Processes an image_crop field Widget.
   *
   * Expands the image_crop Widget to include the crop field.
   * This method is assigned as a #process callback in formElement() method.
   *
   * @todo Implement https://www.drupal.org/node/2657592
   *   Convert crop selector tool into a standalone form element.
   * @todo Implement https://www.drupal.org/node/2848511
   *   Crop offsets not accessible by keyboard.
   */
  public static function processCrop($element, FormStateInterface $form_state, $form) {
    $item = $element['#item'];
    // ksm($element);

    $element_selectors = [
      'crop' => 'crop-' . implode('-', $element['#parents']),
    ];

    if (!isset($item['crop']) && isset($item['full_url'])) {
      // $url = $item['_image']->getFullUrl();
      // todo; should we make our own crop type?
      $crop_type = \Drupal::config('iiif_image_crop.settings')->get('crop_type');
      // ksm($url, $crop_type);

      // ksm($item['full_url'], $crop_type);
      $crop = Crop::findCrop($item['full_url'], $crop_type);

      if ($crop) {
        $anchor = \Drupal::service('iiif_image_crop.crop_manager')->absoluteToRelative($crop->x->value, $crop->y->value, $crop->width->value, $crop->height->value, $item['width'], $item['height']);
        $item['crop'] = implode(',', [...$anchor]);
        // ksm($anchor, $item);
      }
    }

    $default_crop_value = $item['crop'] ?? $element['#crop']['offsets'];

    // Add the crop indicator to preview.
    if (isset($element['crop_preview'])) {
      $preview = [
        'thumbnail' => $element['crop_preview'],
      ];

      // Use the existing preview weight value so that the crop indicator
      // and thumbnail appear in the correct order.
      $preview['#weight'] = $element['crop_preview']['#weight'] ?? 0;
      unset($preview['thumbnail']['#weight']);

      $element['crop_preview'] = $preview;
    }

    // Add the crop field.
    $element['crop'] = self::createCropField($element['#field_name'], $element_selectors, $default_crop_value);

    // ksm($element);

    return $element;
  }

  /**
   * {@inheritdoc}
   *
   * Validation Callback; Crop process field.
   */
  public static function validateCrop($element, FormStateInterface $form_state) {
    // todo: VALIDATE!
    // if (empty($element['#value']) || (FALSE === \Drupal::service('iiif_image_crop.crop_manager')->validateCrop($element['#value']))) {
    //   $replacements = ['@title' => strtolower($element['#title'])];
    //   $form_state->setError($element, new TranslatableMarkup('The @title field should be in the form "leftoffset,topoffset" where offsets are in percentages. Ex: 25,75.', $replacements));
    // }
  }

  /**
   * {@inheritdoc}
   *
   * Validation Callback; Crop widget setting.
   */
  public function validateCropWidget(array &$element, FormStateInterface $form_state) {
    static::validateCrop($element, $form_state);
  }

  /**
   * Create the crop form element.
   *
   * @param string $field_name
   *   The name of the field element for the image field.
   * @param array $element_selectors
   *   The element selectors to ultimately be used by javascript.
   * @param string $default_crop_value
   *   The default crop value in the form x,y.
   *
   * @return array
   *   The preview link form element.
   */
  private static function createCropField($field_name, array $element_selectors, $default_crop_value) {
    // ksm($field_name, $element_selectors, $default_crop_value);
    $field = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Crop'),
      '#description' => new TranslatableMarkup('Specify the crop of this image in the form "leftoffset,topoffset,width,height" where all numbers are in percents. Ex: 25,75,50,5'),
      '#default_value' => $default_crop_value,
      '#element_validate' => [[static::class, 'validateCrop']],
      '#attributes' => [
        'class' => ['crop', $element_selectors['crop']],
        'data-selector' => $element_selectors['crop'],
        'data-field-name' => $field_name,
      ],
      '#wrapper_attributes' => [
        'class' => ['crop-wrapper'],
      ],
      '#attached' => [
        'library' => ['iiif_image_crop/iiif_image_crop.crop'],
      ],
    ];

    return $field;
  }























  public static function processFocalPoint($element, FormStateInterface $form_state, $form) {
    // ksm($element, $form_state, $form);


    $item = $element['#item'];

    // $img = $items[$delta]->getImg($items[$delta]->getValue());

    $element_selectors = [
      'focal_point' => 'focal-point-' . implode('-', $element['#parents']),
    ];

    if (!isset($item['focal_point']) && isset($item['full_url'])) {
      $crop_type = \Drupal::config('iiif_image_focalpoint.settings')->get('crop_type');
      $crop = Crop::findCrop($item['full_url'], $crop_type);
      if ($crop) {
        $anchor = \Drupal::service('iiif_image_focalpoint.focal_point_manager')->absoluteToRelative($crop->x->value, $crop->y->value, $item['width'], $item['height']);
        $item['focal_point'] = "{$anchor['x']},{$anchor['y']}";
      }
    }

    $default_focal_point_value = $item['focal_point'] ?? $element['#focal_point']['offsets'];

    // Add the focal point indicator to preview.
    if (isset($element['fp_preview'])) {
      $preview = [
        'indicator' => self::createFocalPointIndicator($element['#delta'], $element_selectors),
        'thumbnail' => $element['fp_preview'],
        // '#group' => $element['fp_preview']['#group'],
      ];

      // $preview['thumbnail']['indicator'] = self::createFocalPointIndicator($element['#delta'], $element_selectors);

      // unset($preview['thumbnail']['#group']);
      // $preview['indicator']['#group'] = $preview['thumbnail']['#group'];
      // $preview['indicator']['#process'][] = ['Drupal\Core\Render\Element\RenderElement', 'processGroup'];
      // $preview['indicator']['#pre_render'][] = ['Drupal\Core\Render\Element\RenderElement', 'preRenderGroup'];

      // Use the existing preview weight value so that the focal point indicator
      // and thumbnail appear in the correct order.
      $preview['#weight'] = $element['fp_preview']['#weight'] ?? 0;
      // unset($preview['thumbnail']['#weight']);

      $element['fp_preview'] = $preview;
    }

    // Add the focal point field.
    $element['focal_point'] = self::createFocalPointField($element['#field_name'], $element_selectors, $default_focal_point_value);

    return $element;
  }

  /**
   * {@inheritdoc}
   *
   * Validation Callback; Focal Point process field.
   */
  public static function validateFocalPoint($element, FormStateInterface $form_state) {
    if (empty($element['#value']) || (FALSE === \Drupal::service('iiif_image_focalpoint.focal_point_manager')->validateFocalPoint($element['#value']))) {
      $replacements = ['@title' => strtolower($element['#title'])];
      $form_state->setError($element, new TranslatableMarkup('The @title field should be in the form "leftoffset,topoffset" where offsets are in percentages. Ex: 25,75.', $replacements));
    }
  }

  /**
   * {@inheritdoc}
   *
   * Validation Callback; Focal Point widget setting.
   */
  public function validateFocalPointWidget(array &$element, FormStateInterface $form_state) {
    static::validateFocalPoint($element, $form_state);
  }

  /**
   * Create the focal point form element.
   *
   * @param string $field_name
   *   The name of the field element for the image field.
   * @param array $element_selectors
   *   The element selectors to ultimately be used by javascript.
   * @param string $default_focal_point_value
   *   The default focal point value in the form x,y.
   *
   * @return array
   *   The preview link form element.
   */
  private static function createFocalPointField($field_name, array $element_selectors, $default_focal_point_value) {

    $field = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Focal point'),
      '#description' => new TranslatableMarkup('Specify the focus of this image in the form "leftoffset,topoffset" where offsets are in percents. Ex: 25,75'),
      '#default_value' => $default_focal_point_value,
      '#element_validate' => [[static::class, 'validateFocalPoint']],
      '#attributes' => [
        'class' => ['focal-point', $element_selectors['focal_point']],
        'data-selector' => $element_selectors['focal_point'],
        'data-field-name' => $field_name,
      ],
      '#wrapper_attributes' => [
        'class' => ['iiif-focal-point-wrapper'],
      ],
      '#attached' => [
        // 'library' => ['focal_point/drupal.focal_point'],
        'library' => ['iiif_image_focalpoint/iiif_image_focalpoint.focalpoint'],
      ],
    ];

    return $field;
  }

  /**
   * Create the focal point form element.
   *
   * @param int $delta
   *   The delta of the image field widget.
   * @param array $element_selectors
   *   The element selectors to ultimately be used by javascript.
   *
   * @return array
   *   The focal point field form element.
   */
  private static function createFocalPointIndicator($delta, array $element_selectors) {
    $indicator = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ['iiif-focal-point-indicator'],
        'data-selector' => $element_selectors['focal_point'],
        'data-delta' => $delta,
      ],
      '#weight' => -1,
    ];

    return $indicator;
  }

}
