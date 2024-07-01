<?php

namespace Drupal\iiif_image_style\Plugin\IiifImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\iiif_image_style\Attribute\IiifImageEffect;
use Drupal\iiif_image_style\IiifConfigurableImageEffectBase;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

/**
 * Iiif Image Effect to change the image size.
 */
#[IiifImageEffect(
  id: "iiif_image_size",
  label: new TranslatableMarkup("Size Param"),
  description: new TranslatableMarkup("Just a settings form for the basic IIIF Size Param Settings")
)]
class IiifSizeEffect extends IiifConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params, array $context = NULL) {
    $params->size = $this->configuration['size'];
    $params->size_w = $this->configuration['size_w'];
    $params->size_h = $this->configuration['size_h'];
    $params->size_n = $this->configuration['size_n'];

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = [
      '#theme' => 'iiif_image_size_summary',
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
      'size' => 'full',
      'size_w' => '',
      'size_h' => '',
      'size_n' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    // Size.
    $form['size'] = [
      '#type' => 'select',
      '#title' => $this->t('Size'),
      '#options' => IiifImageUrlParams::getSizeOptions(),
      '#default_value' => $this->configuration['size'],
      '#attributes' => [
        'data-states' => 'size',
      ],
    ];
    $form['size_w'] = [
      '#title' => $this->t('w'),
      '#type' => 'number',
      '#default_value' => $this->configuration['size_w'],
      // phpcs:ignore
      // '#description' => $this->t(''),
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
      '#default_value' => $this->configuration['size_h'],
      // phpcs:ignore
      // '#description' => $this->t(''),
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
    $form['size_n'] = [
      '#title' => $this->t('n'),
      '#type' => 'number',
      '#default_value' => $this->configuration['size_n'],
      // phpcs:ignore
      // '#description' => $this->t(''),
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

    $this->configuration['size'] = $form_state->getValue('size');
    $this->configuration['size_w'] = $form_state->getValue('size_w');
    $this->configuration['size_h'] = $form_state->getValue('size_h');
    $this->configuration['size_n'] = $form_state->getValue('size_n');
  }

}
