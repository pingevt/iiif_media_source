<?php

namespace Drupal\iiif_media_source_focalpoint\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\crop\Entity\Crop;

/**
 * IIIF Image Widget. (Focal Point)
 *
 * @FieldWidget(
 *   id = "iiif_image_focal_point_widget",
 *   label = @Translation("IIIF Image Widget (Focal Point)"),
 *   field_types = {
 *     "iiif_id"
 *   }
 * )
 */
class IiifImageFocalPointWidget extends StringTextfieldWidget implements ContainerFactoryPluginInterface {

  const PREVIEW_TOKEN_NAME = 'focal_point_preview';

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'preview_image_style' => 'thumbnail',
      'preview_image_style_size' => 150,
      'offsets' => '50,50',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    // We need a preview image for this widget.
    $form['preview_image_style']['#required'] = TRUE;
    unset($form['preview_image_style']['#empty_option']);
    // @todo Implement https://www.drupal.org/node/2872960
    //   The preview image should not be generated using a focal point effect
    //   and should maintain the aspect ratio of the original image.
    $form['preview_image_style'] = [
      '#title' => $this->t('Preview image style'),
      '#type' => 'select',
      '#options' => image_style_options(FALSE),
      '#empty_option' => '<' . $this->t('no preview') . '>',
      '#default_value' => $this->getSetting('preview_image_style'),
      '#description' => $this->t('The preview image will be shown while editing the content.'),
      '#weight' => 15,
    ];

    $form['preview_image_style']['#description'] = t(
      $form['preview_image_style']['#description']->getUntranslatedString() . "<br/>Do not choose an image style that alters the aspect ratio of the original image nor an image style that uses a focal point effect.",
      $form['preview_image_style']['#description']->getArguments(),
      $form['preview_image_style']['#description']->getOptions()
    );

    $form['preview_image_style_size'] = [
      '#title' => $this->t('Preview image style size (in pixels)'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('preview_image_style_size'),
      '#description' => $this->t('The preview image will be shown while editing the content. What is its max size.'),
      '#weight' => 16,
    ];

    $form['offsets'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default focal point value'),
      '#default_value' => $this->getSetting('offsets'),
      '#description' => $this->t('Specify the default focal point of this widget in the form "leftoffset,topoffset" where offsets are in percentages. Ex: 25,75.'),
      '#size' => 7,
      '#maxlength' => 7,
      '#element_validate' => [[$this, 'validateFocalPointWidget']],
      '#required' => TRUE,
      '#weight' => 35,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $offsets = $this->getSetting('offsets');
    $summary[] = $this->t('Default focal point: @offsets', ['@offsets' => $offsets]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $field_name = $this->fieldDefinition->getName();
    $element['#field_name'] = $field_name;
    $element['#process'] = [[static::class, 'process']];

    $element['#item'] = $items[$delta]->getValue();

    if (!$items[$delta]->isEmpty()) {
      // ksm($items[$delta]);

      $img = $items[$delta]->getImg($items[$delta]->getValue());
      $element['#item']['full_url'] = $img->getFullUrl();
      $element['#item']['width'] = $img->getwidth();
      $element['#item']['height'] = $img->getHeight();

      $scaled_url = $img->getScaledUrl($this->getSetting('preview_image_style_size'), $this->getSetting('preview_image_style_size'));

      $element['preview'] = [
        '#theme' => 'image',
        '#uri' => $scaled_url,
        '#weight' => 1,
        '#attributes' => [
          'style' => "margin: 0 1rem 3rem 0",
        ],
      ];

    }

    $element['#focal_point'] = [
      'offsets' => $this->getSetting('offsets'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   *
   * Processes an image_focal_point field Widget.
   *
   * Expands the image_focal_point Widget to include the focal_point field.
   * This method is assigned as a #process callback in formElement() method.
   *
   * @todo Implement https://www.drupal.org/node/2657592
   *   Convert focal point selector tool into a standalone form element.
   * @todo Implement https://www.drupal.org/node/2848511
   *   Focal Point offsets not accessible by keyboard.
   */
  public static function process($element, FormStateInterface $form_state, $form) {
    $item = $element['#item'];
    // ksm($element);

    $element_selectors = [
      'focal_point' => 'focal-point-' . implode('-', $element['#parents']),
    ];

    if (!isset($item['focal_point']) && isset($item['full_url'])) {
      // $url = $item['_image']->getFullUrl();
      // todo; should we make our own crop type?
      $crop_type = \Drupal::config('focal_point.settings')->get('crop_type');
// ksm($url, $crop_type);
      $crop = Crop::findCrop($item['full_url'], $crop_type);

      if ($crop) {
        $anchor = \Drupal::service('focal_point.manager')->absoluteToRelative($crop->x->value, $crop->y->value, $item['width'], $item['height']);
        $item['focal_point'] = "{$anchor['x']},{$anchor['y']}";
      }
    }

    $default_focal_point_value = $item['focal_point'] ?? $element['#focal_point']['offsets'];

    // Add the focal point indicator to preview.
    if (isset($element['preview'])) {
      $preview = [
        'indicator' => self::createFocalPointIndicator($element['#delta'], $element_selectors),
        'thumbnail' => $element['preview'],
      ];

      // Use the existing preview weight value so that the focal point indicator
      // and thumbnail appear in the correct order.
      $preview['#weight'] = $element['preview']['#weight'] ?? 0;
      unset($preview['thumbnail']['#weight']);

      $element['preview'] = $preview;
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
    if (empty($element['#value']) || (FALSE === \Drupal::service('focal_point.manager')->validateFocalPoint($element['#value']))) {
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
        'class' => ['focal-point-wrapper'],
      ],
      '#attached' => [
        'library' => ['focal_point/drupal.focal_point'],
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
        'class' => ['focal-point-indicator'],
        'data-selector' => $element_selectors['focal_point'],
        'data-delta' => $delta,
      ],
    ];

    return $indicator;
  }

}
