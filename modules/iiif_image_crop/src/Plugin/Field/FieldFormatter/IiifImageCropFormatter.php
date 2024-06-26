<?php

namespace Drupal\iiif_image_crop\Plugin\Field\FieldFormatter;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\crop\Entity\Crop;
use Drupal\iiif_image_crop\IiifCropManager;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;
use Drupal\iiif_media_source\Plugin\Field\FieldFormatter\IiifImageFormatter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * IIIF Image Crop formatter.
 *
 * This uses the cropped image as the source.
 *
 * @FieldFormatter(
 *   id = "iiif_image_crop_formatter",
 *   label = @Translation("IIIF Image Crop Formatter"),
 *   field_types = {
 *     "iiif_id"
 *   }
 * )
 */
class IiifImageCropFormatter extends IiifImageFormatter {

  /**
   * IIIF Crop Manager.
   *
   * @var \Drupal\iiif_image_crop\IiifCropManager
   */
  protected $cropManager;

  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, EntityTypeManagerInterface $entity_type_manager, ContainerAwareEventDispatcher $event_dispatcher, IiifCropManager $crop_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $entity_type_manager, $event_dispatcher);

    $this->cropManager = $crop_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('event_dispatcher'),
      $container->get('iiif_image_crop.crop_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $summary[] = "Will use the Cropped Image when applicable";

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {

    $build = [];

    foreach ($items as $delta => $item) {

      $img = $item->getImg($item->getValue());

      $crop_type = \Drupal::config('iiif_image_crop.settings')->get('crop_type');
      $crop = Crop::findCrop($img->getFullUrl(), $crop_type);

      // Process settings.
      $style_settings = $this->getSettings();

      $params = IiifImageUrlParams::fromSettingsArray($style_settings);
      $this->cropManager->applyCrop($img, $params, $crop);

      $view_value = [
        '#theme' => 'iiif_image',
        'item' => NULL,
        '#image' => $img,
        '#url_params' => $params,
      ];

      $build[$delta] = $view_value;
    }

    return $build;
  }

}
