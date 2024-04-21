<?php

namespace Drupal\iiif_image_focalpoint\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\iiif_image_handling\IiifImageHandlingProcessor;

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
      'iiif_fp_preview_image_style_size' => 150,
      'iiif_fp_offsets' => '50,50',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['iiif_fp_preview_image_style_size'] = [
      '#title' => $this->t('Preview image style size (in pixels)'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('iiif_fp_preview_image_style_size'),
      '#description' => $this->t('The preview image will be shown while editing the content. What is its max size.'),
      '#weight' => 16,
    ];

    $form['iiif_fp_offsets'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default focal point value'),
      '#default_value' => $this->getSetting('iiif_fp_offsets'),
      '#description' => $this->t('Specify the default focal point of this widget in the form "leftoffset,topoffset" where iiif_fp_offsets are in percentages. Ex: 25,75.'),
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

    $iiif_fp_offsets = $this->getSetting('iiif_fp_offsets');
    $summary[] = $this->t('Default focal point: @iiif_fp_offsets', ['@iiif_fp_offsets' => $iiif_fp_offsets]);

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

    $element = IiifImageHandlingProcessor::buildElementFocalPoint($element, $form_state, $context);

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

}
