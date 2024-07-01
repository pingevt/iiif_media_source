<?php

namespace Drupal\iiif_image_style\Plugin\IiifImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\iiif_image_style\Attribute\IiifImageEffect;
use Drupal\iiif_image_style\IiifConfigurableImageEffectBase;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

/**
 * Iiif Image Effect to change the image rotation.
 */
#[IiifImageEffect(
  id: "iiif_image_rotation",
  label: new TranslatableMarkup("Rotation Param"),
  description: new TranslatableMarkup("Just a settings form for the basic IIIF Rotation Param Setting")
)]
class IiifRotationEffect extends IiifConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params, array $context = NULL) {
    $params->rotation = $this->configuration['rotation'];

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = [
      '#theme' => 'iiif_image_rotation_summary',
      '#data' => $this->configuration,
    ];
    $summary += parent::getSummary();

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'rotation' => 'default',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // @todo add in mirroring.
    // Rotation.
    $form['rotation'] = [
      '#title' => $this->t('Rotation'),
      '#type' => 'number',
      '#default_value' => $this->configuration['rotation'],
      '#description' => $this->t('The degrees of clockwise rotation from 0 up to 360.'),
      '#min' => 0,
      '#max' => 360,
      '#step' => 0.1,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['rotation'] = $form_state->getValue('rotation');
  }

}
