<?php

namespace Drupal\iiif_image_contextual_media_adapter\EventSubscriber;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactory;
use Drupal\focal_point\FocalPointManager;
use Drupal\iiif_image_handling\Event\IiifEffectFindCropEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class IiifEffectFindCropSubscriber.
 *
 * Create Crop entities for Contextual Crops.
 */
class IiifEffectFindCropSubscriber implements EventSubscriberInterface {

  protected $entityTypeManager;
  protected $focalPointManager;
  protected $configFactory;

  public function __construct($entity_type_manager, FocalPointManager $focal_point_manager, ConfigFactory $config_factory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->focalPointManager = $focal_point_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      // Static class constant => method on this class.
      IiifEffectFindCropEvent::EVENT_NAME => 'alterCrop',
    ];
  }

  /**
   * Subscribe to the user login event dispatched.
   */
  public function alterCrop(IiifEffectFindCropEvent $event) {
    if (!empty($event->context['field'])) {
      $entity_parent = $event->context['field']->getEntity() ?? NULL;

      if ($entity_parent) {
        $crop_context_ref = $entity_parent->entity_reference_entity_modify ?? NULL;

        if ($crop_context_ref) {
          $crop_data = [];
          if ($crop_context_ref != NULL && $referringItem = $entity_parent->_referringItem) {
            $media_override = $referringItem->getValue() ?? [];
            // If there is overwritten data.
            if (isset($media_override['overwritten_property_map'])) {
              $crop_data = Json::decode($media_override['overwritten_property_map']);
            }

            // ksm($crop_data);
          }

          // Recover field image in the media.
          $media_image_field = NULL;
          $field_definition = $entity_parent->getSource()->getSourceFieldDefinition($entity_parent->bundle->entity);
          $item_class = $field_definition->getItemDefinition()->getClass();
          $media_image_field = $field_definition->getName();

          // ksm($item_class, $media_image_field);.
          if ($crop_data != [] && $media_image_field != NULL) {

            foreach ($crop_data as $field_name => $data_values) {
              if ($media_image_field == $field_name) {
                // ksm($media_image_field, $crop_data[$media_image_field]);.
                // Prepare Crop Data.
                $override_data = current($crop_data[$media_image_field]);
                $iiif_focal_point_override_value = $override_data['iiif_focal_point'] ?? "";
                $iiif_crop_override_value = $override_data['iiif_crop'] ?? "";
              }
            }
          }

          // IIIF Focal Point.
          // Create or Update Crop.
          // $crop_type = $this->configFactory->get('iiif_image_focalpoint.settings')->get('crop_type');.
          $crop_type = $event->cropType;
          $original_uri = $event->image->getFullUrl();
          $current_crop = $this->retrieveContextualCrop($crop_context_ref, $crop_type, $original_uri, $entity_parent);

          if (!empty($current_crop)) {
            $event->crop = $current_crop;
          }

        }
      }
    }
  }

  /**
   * @todo move to base class to re-use.
   */
  public function retrieveContextualCrop($context, $crop_type, $original_uri, $entity_parent) {
    $cropStorage = $this->entityTypeManager->getStorage('crop');

    // Try to load crop for the current context.
    $base_crop = ['uri' => $original_uri, 'type' => $crop_type, 'context' => $context];
    $crop = $cropStorage->loadByProperties($base_crop);
    $crop = reset($crop) ?: NULL;

    return $crop;

  }

}
