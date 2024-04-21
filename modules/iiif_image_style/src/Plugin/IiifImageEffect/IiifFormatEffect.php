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
  id: "iiif_image_format",
  label: new TranslatableMarkup("Format Param"),
  description: new TranslatableMarkup("Just a settings form for the basic IIIF Format Param Setting")
)]
class IiifFormatEffect extends IiifConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params, array $context = NULL) {
    $params->format = $this->configuration['format'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = [
      '#theme' => 'iiif_image_format_summary',
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
      'format' => 'jpg',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    // Format.
    $form['format'] = [
      '#type' => 'select',
      '#title' => $this->t('Format'),
      '#options' => IiifImageUrlParams::getFormatOptions(),
      '#default_value' => $this->configuration['format'],
      '#attributes' => [
        'data-states' => 'format',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['format'] = $form_state->getValue('format');
  }

}
