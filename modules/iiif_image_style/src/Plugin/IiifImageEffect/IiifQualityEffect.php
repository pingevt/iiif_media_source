<?php

namespace Drupal\iiif_image_style\Plugin\IiifImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\iiif_image_style\Attribute\IiifImageEffect;
use Drupal\iiif_image_style\IiifConfigurableImageEffectBase;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

/**
 *
 */
#[IiifImageEffect(
  id: "iiif_image_quality",
  label: new TranslatableMarkup("Quality Param"),
  description: new TranslatableMarkup("Just a settings form for the basic IIIF Quality Param Setting")
)]
class IiifQualityEffect extends IiifConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params, array $context = NULL) {
    $params->quality = $this->configuration['quality'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = [
      '#theme' => 'iiif_image_quality_summary',
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
      'quality' => 'default',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    // Quality.
    $form['quality'] = [
      '#type' => 'select',
      '#title' => $this->t('Quality'),
      '#options' => IiifImageUrlParams::getQualityOptions(),
      '#default_value' => $this->configuration['quality'],
      '#attributes' => [
        'data-states' => 'quality',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['quality'] = $form_state->getValue('quality');
  }

}
