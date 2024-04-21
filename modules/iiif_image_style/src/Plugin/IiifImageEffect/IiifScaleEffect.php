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
  id: "iiif_image_scale",
  label: new TranslatableMarkup("Scale"),
  description: new TranslatableMarkup("Simple scaler")
)]
class IiifScaleEffect extends IiifConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params, array $context = NULL) {

    $params->size = 'pct:n';
    $params->size_n = $this->configuration['size_n'];

  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = [
      '#theme' => 'iiif_image_scale_summary',
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
      'size_n' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $params = $this->configuration;

    $form['size_n'] = [
      '#title' => $this->t('n'),
      '#type' => 'number',
      '#default_value' => $params['size_n'] ?? NULL,
      '#description' => $this->t(''),
      '#required' => TRUE,
      '#min' => 0,
      '#max' => 100,
      '#step' => 0.1,
      '#states' => [
        'visible' => [
          ':input[data-states="size"]' => [
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

    $this->configuration['size_n'] = $form_state->getValue('size_n');
  }

}
