<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Plugin\IiifImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\iiif_image_style\Attribute\IiifImageEffect;
use Drupal\iiif_image_style\IiifConfigurableImageEffectBase;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

/**
 * Iiif Image Effect to change the image format.
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
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params, ?array $context = NULL): bool {
    $params->format = $this->configuration['format'];

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary(): array {
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
  public function defaultConfiguration(): array {
    return [
      'format' => 'jpg',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {

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
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['format'] = $form_state->getValue('format');
  }

}
