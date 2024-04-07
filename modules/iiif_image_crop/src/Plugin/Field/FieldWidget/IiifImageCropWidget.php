<?php

namespace Drupal\iiif_image_crop\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\crop\Entity\Crop;

/**
 * IIIF Image Widget. (Crop)
 *
 * @FieldWidget(
 *   id = "iiif_image_crop_widget",
 *   label = @Translation("IIIF Image Widget (Crop)"),
 *   field_types = {
 *     "iiif_id"
 *   }
 * )
 */
class IiifImageCropWidget extends StringTextfieldWidget implements ContainerFactoryPluginInterface {

  const PREVIEW_TOKEN_NAME = 'crop_preview';

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'preview_image_style' => 'thumbnail',
      'preview_image_style_size' => 150,
      'offsets' => '0,0,100,100',
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
    //   The preview image should not be generated using a crop effect
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
      $form['preview_image_style']['#description']->getUntranslatedString() . "<br/>Do not choose an image style that alters the aspect ratio of the original image nor an image style that uses a crop effect.",
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
      '#title' => $this->t('Default crop value'),
      '#default_value' => $this->getSetting('offsets'),
      '#description' => $this->t('Specify the crop of this image in the form "leftoffset,topoffset,width,height" where all numbers are in percents. Ex: 25,75,50,5'),
      '#size' => 13,
      '#maxlength' => 13,
      '#element_validate' => [[$this, 'validateCropWidget']],
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
    $summary[] = $this->t('Default crop: @offsets', ['@offsets' => $offsets]);

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
        '#prefix' => "<div class='cropper-image'>",
        '#suffix' => "</div>",
      ];
    }

    $element['#crop'] = [
      'offsets' => $this->getSetting('offsets'),
    ];

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
  public static function process($element, FormStateInterface $form_state, $form) {
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
    if (isset($element['preview'])) {
      $preview = [
        // 'indicator' => self::createCropIndicator($element['#delta'], $element_selectors),
        'thumbnail' => $element['preview'],
      ];

      // Use the existing preview weight value so that the crop indicator
      // and thumbnail appear in the correct order.
      $preview['#weight'] = $element['preview']['#weight'] ?? 0;
      unset($preview['thumbnail']['#weight']);

      $element['preview'] = $preview;
    }

    // Add the crop field.
    $element['crop'] = self::createCropField($element['#field_name'], $element_selectors, $default_crop_value);

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

  /**
   * Create the crop form element.
   *
   * @param int $delta
   *   The delta of the image field widget.
   * @param array $element_selectors
   *   The element selectors to ultimately be used by javascript.
   *
   * @return array
   *   The crop field form element.
   */
  // private static function createCropIndicator($delta, array $element_selectors) {
  //   $indicator = [
  //     '#type' => 'html_tag',
  //     '#tag' => 'div',
  //     '#attributes' => [
  //       'class' => ['crop-indicator'],
  //       'data-selector' => $element_selectors['crop'],
  //       'data-delta' => $delta,
  //     ],
  //   ];

  //   return $indicator;
  // }

}
