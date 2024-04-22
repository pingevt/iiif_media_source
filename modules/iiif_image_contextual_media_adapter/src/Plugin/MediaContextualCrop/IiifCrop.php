<?php

declare(strict_types=1);

namespace Drupal\iiif_image_contextual_media_adapter\Plugin\MediaContextualCrop;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\image_widget_crop\ImageWidgetCropManager;
use Drupal\media_contextual_crop\MediaContextualCropPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the media_contextual_crop.
 *
 * @MediaContextualCrop(
 *   id = "iiif_crop",
 *   target_field_name = "iiif_crop",
 *   label = @Translation("Iiif Crop"),
 *   description = @Translation("Manage Multicrop for iiif_crop."),
 *   image_style_effect = {""}
 * )
 */
class IiifCrop extends MediaContextualCropPluginBase {

  /**
   * IMC Manager.
   *
   * @var \Drupal\image_widget_crop\ImageWidgetCropManager
   */
  protected $imageWidgetCropManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration,
                                    $plugin_id,
                                    $plugin_definition,
                              EntityTypeManagerInterface $entityTypeManager,
                              ImageWidgetCropManager $imageWidgetCropManager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entityTypeManager);
    $this->imageWidgetCropManager = $imageWidgetCropManager;

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
      $container->get('iiif_image_crop.crop_manage')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function saveCrop($crop_settings, string $image_style_name, string $old_uri, string $context, int $width, int $height) {

    $style = ImageStyle::load($image_style_name);

    $style_dependencies = $style->getDependencies();
    $croptype = '';
    foreach ($style_dependencies as $dep) {
      if (strstr($dep[0], 'crop.type')) {
        $croptype = str_replace('crop.type.', '', $dep[0]);
      }
    }

    // Filter to the concerned crop setting.
    foreach ($crop_settings['crop_wrapper'] as $crop_type => $container) {
      // Get Crop value.
      if ($croptype != $crop_type) {
        continue;
      }

      // Recover Crop.
      $crop = $this->retrieveContextualCrop($context, $crop_type, $old_uri);

      $crop_values = $container['crop_container']['values'];

      // If crop are set.
      if ($crop_values['crop_applied'] == '1') {

        // Get Center coordinate of crop zone on original image.
        $axis_coordinate = $this->imageWidgetCropManager->getAxisCoordinates(
          ['x' => $crop_values['x'], 'y' => $crop_values['y']],
          ['width' => $crop_values['width'], 'height' => $crop_values['height']]
        );

        // Set position & size.
        $crop->setPosition($axis_coordinate['x'], $axis_coordinate['y']);
        $crop->setSize($crop_values['width'], $crop_values['height']);

        // Save Crop.
        $crop->save();
        return $crop->id();
      }
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function processFieldData($field_data) {
    // Since its a single crop, we're good.
    return $field_data;
  }

  /**
   * {@inheritdoc}
   */
  public function getComponentConfig($default_values, $image_style_crops, $preview_image_style = NULL) {
    $component_data = [
      'type' => 'image_widget_crop',
      'settings' => [
        'progress_indicator' => 'throbber',
        'preview_image_style' => $preview_image_style ?? 'thumbnail',
        'crop_preview_image_style' => 'crop_thumbnail',
        'crop_list' => $image_style_crops,
        'warn_multiple_usages' => FALSE,
        'show_crop_area' => TRUE,
        'show_default_crop' => TRUE,

      ],
    ];
    return $component_data;
  }

  /**
   * {@inheritdoc}
   */
  public function finishElement(&$form, $source_field_name, $default_values) {
    parent::finishElement($form, $source_field_name, $default_values);

    $form['#attached']['library'][] = 'media_contextual_crop_iwc_adapter/editor_media_dialog_fix';
    $widget = &$form[$source_field_name]['widget'][0];

    if (array_key_exists('data-crop-settings', $default_values) && $default_values['data-crop-settings'] != '') {
      $widget['#default_value']['image_crop']['crop_wrapper'] = @unserialize($default_values['data-crop-settings'], ['allowed_classes' => FALSE]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function widgetSave(array $form, FormStateInterface $form_state) {
    $target = ['field_media_image', 0, 'image_crop', 'crop_wrapper'];
    $settings = $form_state->getValue($target);
    foreach ($settings as $key => $data) {
      unset($settings[$key]['crop_container']['reset']);
    }
    if ($settings) {
      $form_state->setValue(['attributes', 'data-crop-settings'], serialize($settings));
    }

    $form_state->setValue(['attributes', 'data-crop-type'], 'image_widget_crop');
  }

  /**
   * {@inheritdoc}
   */
  public function processEmbedData($embed_settings) {

    $settings = unserialize($embed_settings['crop'], ['allowed_classes' => FALSE]);

    $crop_settings = [
      'plugin_id' => 'image_widget_crop',
      'crop_setting' => ['crop_wrapper' => $settings],
      'context' => $embed_settings['context'],
      'base_crop_folder' => 'multi_crop_embed',
    ];

    return $crop_settings;
  }

}
