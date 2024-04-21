<?php

namespace Drupal\iiif_image_crop\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\iiif_image_handling\IiifImageHandlingProcessor;

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
      'iiif_crop_preview_image_style_size' => 150,
      'iiif_crop_offsets' => '0,0,100,100',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    // We need a preview image for this widget.
    $form['iiif_crop_preview_image_style_size'] = [
      '#title' => $this->t('Preview image style size (in pixels)'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('iiif_crop_preview_image_style_size'),
      '#description' => $this->t('The preview image will be shown while editing the content. What is its max size.'),
      '#weight' => 16,
    ];

    $form['iiif_crop_offsets'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default crop value'),
      '#default_value' => $this->getSetting('iiif_crop_offsets'),
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

    $iiif_crop_offsets = $this->getSetting('iiif_crop_offsets');
    $summary[] = $this->t('Default crop: @iiif_crop_offsets', ['@iiif_crop_offsets' => $iiif_crop_offsets]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $field_name = $this->fieldDefinition->getName();
    $element['#field_name'] = $field_name;

    $context = [
      'form' => $form,
      'widget' => $this,
      'items' => $items,
      'delta' => $delta,
      'default' => FALSE,
    ];

    $element = IiifImageHandlingProcessor::buildElementCrop($element, $form_state, $context);

    return $element;
  }

  /**
   * {@inheritdoc}
   *
   * Validation Callback; Crop process field.
   */
  public static function validateCrop($element, FormStateInterface $form_state) {
    // @todo VALIDATE!
    // if (empty($element['#value']) || (FALSE === \Drupal::service('iiif_image_crop.crop_manager')->validateCrop($element['#value']))) {
    //   $replacements = ['@title' => strtolower($element['#title'])];
    //   $form_state->setError($element, new TranslatableMarkup('The @title field should be in the form "leftoffset,topoffset" where offsets are in percentages. Ex: 25,75.', $replacements));
    // }.
  }

  /**
   * {@inheritdoc}
   *
   * Validation Callback; Crop widget setting.
   */
  public function validateCropWidget(array &$element, FormStateInterface $form_state) {
    static::validateCrop($element, $form_state);
  }

}
