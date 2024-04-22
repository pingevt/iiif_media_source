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
  id: "iiif_image_resize",
  label: new TranslatableMarkup("Resize"),
  description: new TranslatableMarkup("Simple resizer")
)]
class IiifResizeEffect extends IiifConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params, array $context = NULL) {
    $params->size = "w,h";
    $params->size_w = $this->configuration["size_w"];
    $params->size_h = $this->configuration["size_h"];

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = [
      '#theme' => 'iiif_image_resize_summary',
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
      'size_w' => NULL,
      'size_h' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $params = $this->configuration;

    $form['size_w'] = [
      '#title' => $this->t('w'),
      '#type' => 'number',
      '#default_value' => $params['size_w'] ?? NULL,
      '#description' => $this->t(''),
      '#states' => [
        'invisible' => [
          ':input[data-states="size"]' => [
            ['value' => 'max'],
            'or',
            ['value' => '^max'],
            'or',
            ['value' => 'full'],
            'or',
            ['value' => '^full'],
            'or',
            ['value' => 'pct:n'],
            'or',
            ['value' => '^pct:n'],
          ],
        ],
      ],
    ];
    $form['size_h'] = [
      '#title' => $this->t('h'),
      '#type' => 'number',
      '#default_value' => $params['size_h'] ?? NULL,
      '#description' => $this->t(''),
      '#states' => [
        'invisible' => [
          ':input[data-states="size"]' => [
            ['value' => 'max'],
            'or',
            ['value' => '^max'],
            'or',
            ['value' => 'full'],
            'or',
            ['value' => '^full'],
            'or',
            ['value' => 'pct:n'],
            'or',
            ['value' => '^pct:n'],
          ],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['size_w'] = $form_state->getValue('size_w');
    $this->configuration['size_h'] = $form_state->getValue('size_h');
    // $this->configuration['size_n'] = $form_state->getValue('size_n');
  }

}
