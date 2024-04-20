<?php

namespace Drupal\iiif_image_focalpoint\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\crop\Entity\Crop;
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

}
