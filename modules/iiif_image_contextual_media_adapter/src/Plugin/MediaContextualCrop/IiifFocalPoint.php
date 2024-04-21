<?php

declare(strict_types=1);

namespace Drupal\iiif_image_contextual_media_adapter\Plugin\MediaContextualCrop;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\focal_point\FocalPointManager;
use Drupal\media_contextual_crop\MediaContextualCropPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the media_contextual_crop.
 *
 * @MediaContextualCrop(
 *   id = "iiif_focal_point",
 *   target_field_name = "iiif_focal_point",
 *   label = @Translation("IIIF Focal Point"),
 *   description = @Translation("Manage Media Contextual Crop for IIIF Focal Point Crop."),
 *   image_style_effect = {}
 * )
 */
class IiifFocalPoint extends MediaContextualCropPluginBase {

  /**
   * FocalPoint Manager.
   *
   * @var Drupal\focal_point\FocalPointManager
   */
  protected $focalPointManager;

  /**
   * Focal Point Crop Data.
   *
   * @var array
   */
  protected $cropType;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration,
                                    $plugin_id,
                                    $plugin_definition,
                              EntityTypeManagerInterface $entityTypeManager,
                              FocalPointManager $focalPointManager,
                                ConfigFactory $config
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entityTypeManager);
    $this->focalPointManager = $focalPointManager;
    $this->cropType = $config->get('iiif_image_focalpoint.settings')->get('crop_type');

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('iiif_image_focalpoint.focal_point_manager'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function saveCrop($crop_settings, string $image_style_name, string $old_uri, string $context, int $width, int $height) {

    // Recover Crop.
    $crop = $this->retrieveContextualCrop($context, $this->cropType, $old_uri);

    // Recalculate coordinate.
    [$x, $y] = explode(',', $crop_settings);
    $absolute = $this->focalPointManager->relativeToAbsolute((float) $x, (float) $y, $width, $height);

    // Set anchor.
    $anchor = $crop->anchor();
    if ($anchor['x'] != $absolute['x'] || $anchor['y'] != $absolute['y']) {
      $crop->setPosition($absolute['x'], $absolute['y']);
      $crop->save();
    }

    return $crop->id();
  }

  /**
   * {@inheritdoc}
   */
  public function getComponentConfig($default_values, $image_style_crops, $preview_image_style = NULL) {
    $component_data = [
      'type' => 'image_focal_point',
      'settings' => [
        'progess_indicator' => 'throbber',
        'preview_image_style' => $preview_image_style ?? 'crop_thumbnail',
        'preview_link' => TRUE,
        'offsets' => $default_values['data-crop-settings'] ?? '30,30',
      ],
    ];

    return $component_data;
  }

  /**
   * {@inheritdoc}
   */
  public function finishElement(&$form, $source_field_name, $default_values) {
    parent::finishElement($form, $source_field_name, $default_values);

    $form[$source_field_name]['#parents'] = [];
    $form[$source_field_name]['#weight'] = -10;
    $widget = &$form[$source_field_name]['widget'][0];
    if (array_key_exists('data-crop-settings', $default_values)) {
      $widget['#default_value']['focal_point'] = $default_values['data-crop-settings'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function widgetSave(array $form, FormStateInterface $form_state) {
    parent::widgetSave($form, $form_state);

    $iiif_focal_point = $form_state->getValue(['field_media_image', 0, 'iiif_focal_point']);
    if ($iiif_focal_point) {
      $form_state->setValue(['attributes', 'data-crop-settings'], $iiif_focal_point);
    }

    $form_state->setValue(['attributes', 'data-crop-type'], 'iiif_focal_point');
  }

  /**
   * {@inheritdoc}
   */
  public static function widgetModify(array $element): array {
    $element = parent::widgetModify($element);

    // Since core does not support nested modal dialogs, we need to ensure that
    // the preview page opens in a new tab, rather than a modal dialog via AJAX.
    // $preview_link_attributes = &$element['preview']['preview_link']['#attributes'];
    // unset($preview_link_attributes['data-dialog-type']);
    // $preview_link_attributes['class'] = array_diff($preview_link_attributes['class'], ['use-ajax']);.
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function processEmbedData($embed_settings) {
    // Model the attributes after the multi_crop focal_point plugin.
    $crop_settings = [
      'plugin_id' => 'iiif_focal_point',
      'crop_setting' => $embed_settings['crop'],
      'context' => $embed_settings['context'],
      'base_crop_folder' => 'multi_crop_embed',
    ];

    return $crop_settings;
  }

  /**
   * Create or load crop instance.
   *
   * @param string $context
   *   Context of the crop.
   * @param string $crop_type
   *   Crop Type id.
   * @param string $original_uri
   *   Uri of original image.
   *
   * @return \Drupal\crop\Entity\Crop
   *   Return the crop entity.
   */
  public function retrieveContextualCrop($context, $crop_type, $original_uri) {
    $cropStorage = $this->entityTypeManager->getStorage('crop');

    // Try to load crop for the current context.
    $base_crop = ['uri' => $original_uri, 'type' => $crop_type, 'context' => $context];
    $crop = $cropStorage->loadByProperties($base_crop);
    $crop = reset($crop) ?: NULL;

    // Create a new crop.
    // if ($crop == NULL) {.
    // /** @var \Drupal\file\FileInterface[] $files */
    //   $files = $this->entityTypeManager
    //     ->getStorage('file')
    //     ->loadByProperties(['uri' => $original_uri]);
    // /** @var \Drupal\file\FileInterface|null $file */
    //   $file = reset($files) ?: NULL;
    // $values = [
    //     'type' => $crop_type,
    //     'entity_id' => $file->id(),
    //     'entity_type' => 'file',
    //     'uri' => $original_uri,
    //     'context' => $context,
    //   ];
    // // Create new cron.
    //   $crop = $cropStorage->create($values);
    // }
    return $crop;

  }

}
