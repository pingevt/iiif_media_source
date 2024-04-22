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
  id: "iiif_image_region",
  label: new TranslatableMarkup("Region Param"),
  description: new TranslatableMarkup("Just a settings form for the basic IIIF Region Param Settings")
)]
class IiifRegionEffect extends IiifConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params, array $context = NULL) {
    $params->region = $this->configuration['region'];
    $params->region_x = $this->configuration['region_x'];
    $params->region_y = $this->configuration['region_y'];
    $params->region_w = $this->configuration['region_w'];
    $params->region_h = $this->configuration['region_h'];

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = [
      '#theme' => 'iiif_image_region_summary',
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
      'region' => 'full',
      'region_x' => '',
      'region_y' => '',
      'region_w' => '',
      'region_h' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    // Region.
    $form['region'] = [
      '#type' => 'select',
      '#title' => $this->t('Region'),
      '#options' => IiifImageUrlParams::getRegionOptions(),
      '#default_value' => $this->configuration['region'],
      '#attributes' => [
        'data-states' => 'region',
      ],
    ];
    $form['region_x'] = [
      '#title' => $this->t('x'),
      '#type' => 'number',
      '#default_value' => $this->configuration['region_x'],
      '#description' => $this->t(''),
      '#states' => [
        'visible' => [
          ':input[data-states="region"]' => [
            ['value' => 'x,y,w,h'],
            'or',
            ['value' => 'pct:x,y,w,h'],
          ],
        ],
      ],
    ];
    $form['region_y'] = [
      '#title' => $this->t('y'),
      '#type' => 'number',
      '#default_value' => $this->configuration['region_y'],
      '#description' => $this->t(''),
      '#states' => [
        'visible' => [
          ':input[data-states="region"]' => [
            ['value' => 'x,y,w,h'],
            'or',
            ['value' => 'pct:x,y,w,h'],
          ],
        ],
      ],
    ];
    $form['region_w'] = [
      '#title' => $this->t('w'),
      '#type' => 'number',
      '#default_value' => $this->configuration['region_w'],
      '#description' => $this->t(''),
      '#states' => [
        'visible' => [
          ':input[data-states="region"]' => [
            ['value' => 'x,y,w,h'],
            'or',
            ['value' => 'pct:x,y,w,h'],
          ],
        ],
      ],
    ];
    $form['region_h'] = [
      '#title' => $this->t('h'),
      '#type' => 'number',
      '#default_value' => $this->configuration['region_h'],
      '#description' => $this->t(''),
      '#states' => [
        'visible' => [
          ':input[data-states="region"]' => [
            ['value' => 'x,y,w,h'],
            'or',
            ['value' => 'pct:x,y,w,h'],
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

    $this->configuration['region'] = $form_state->getValue('region');
    $this->configuration['region_x'] = $form_state->getValue('region_x');
    $this->configuration['region_y'] = $form_state->getValue('region_y');
    $this->configuration['region_w'] = $form_state->getValue('region_w');
    $this->configuration['region_h'] = $form_state->getValue('region_h');

    // $this->configuration['size_w'] = $form_state->getValue('size_w');
    // $this->configuration['size_h'] = $form_state->getValue('size_h');
    // $this->configuration['size_n'] = $form_state->getValue('size_n');
    // $this->configuration['size_n'] = $form_state->getValue('size_n');
  }

}
