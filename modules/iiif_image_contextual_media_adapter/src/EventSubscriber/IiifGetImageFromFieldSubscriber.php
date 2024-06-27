<?php

namespace Drupal\iiif_image_contextual_media_adapter\EventSubscriber;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactory;
use Drupal\focal_point\FocalPointManager;
use Drupal\iiif_media_source\Event\IiifGetImageFromFieldEvent;
use Drupal\Component\Utility\NestedArray;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\iiif_image_crop\IiifCropManager;

/**
 * Class IiifGetImageFromFieldSubscriber.
 *
 * Create Crop entities for Contextual Crops.
 */
class IiifGetImageFromFieldSubscriber implements EventSubscriberInterface {

  protected $entityTypeManager;
  protected $focalPointManager;
  protected $cropManager;
  protected $configFactory;

  public function __construct($entity_type_manager, FocalPointManager $focal_point_manager, IiifCropManager $crop_manager, ConfigFactory $config_factory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->focalPointManager = $focal_point_manager;
    $this->cropManager = $crop_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      // Static class constant => method on this class.
      IiifGetImageFromFieldEvent::EVENT_NAME => 'imgFromField',
    ];
  }

  /**
   * Subscribe to the user login event dispatched.
   */
  public function imgFromField(IiifGetImageFromFieldEvent $event) {
    // ksm($event);

    $entity_parent = $event->field->getEntity() ?? NULL;

    if ($entity_parent) {
      $crop_context_ref = $entity_parent->entity_reference_entity_modify ?? NULL;

      if ($crop_context_ref) {

        $crop_data = [];
        if ($crop_context_ref != NULL && $referringItem = $entity_parent->_referringItem) {
          $media_override = $referringItem->getValue() ?? [];
          // ksm($referringItem, $media_override);
          // If there is overwritten data.
          if (isset($media_override['overwritten_property_map'])) {
            $crop_data = Json::decode($media_override['overwritten_property_map']);
          }

          // ksm($crop_data);
        }

        // Recover field image in the media.
        $media_image_field = NULL;
        $field_definition = $event->field->getFieldDefinition();
        $item_class = $field_definition->getItemDefinition()->getClass();
        $media_image_field = $field_definition->getName();

        // ksm($item_class, $media_image_field);

        $iiif_focal_point_override_value = "";
        $iiif_crop_override_value = "";

        if ($crop_data != [] && $media_image_field != NULL) {

          foreach ($crop_data as $field_name => $data_values) {
            if ($media_image_field == $field_name) {
              // Prepare Crop Data.
              $override_data = current($crop_data[$media_image_field]);
              $iiif_focal_point_override_value = $override_data['iiif_focal_point'] ?? "";
              $iiif_crop_override_value = $override_data['iiif_crop'] ?? "";
            }
          }
        }

        // IIIF Focal Point.
        // Create or Update Crop.
        $crop_type = $this->configFactory->get('iiif_image_focalpoint.settings')->get('crop_type');
        $original_uri = $event->image->getFullUrl();
        $current_crop = $this->retrieveContextualCrop($crop_context_ref, $crop_type, $original_uri, $entity_parent);
        if (empty($iiif_focal_point_override_value)) {
          $iiif_focal_point_override_value = $this->configFactory->get('iiif_image_focalpoint.settings')->get('default_value');
        }

        // Recalculate coordinate.
        [$x, $y] = explode(',', $iiif_focal_point_override_value);
        $absolute = $this->focalPointManager->relativeToAbsolute((float) $x, (float) $y, $event->image->getWidth(), $event->image->getHeight());

        // Set anchor.
        $anchor = $current_crop->anchor();
        if ($anchor['x'] != $absolute['x'] || $anchor['y'] != $absolute['y']) {
          $current_crop->setPosition($absolute['x'], $absolute['y']);
          $current_crop->save();
        }

        // IIIF Crop.
        // Create or Update Crop.
        $crop_type = $this->configFactory->get('iiif_image_crop.settings')->get('crop_type');
        $current_crop = $this->retrieveContextualCrop($crop_context_ref, $crop_type, $original_uri, $entity_parent);
        if (empty($iiif_crop_override_value)) {
          $iiif_crop_override_value = $this->configFactory->get('iiif_image_crop.settings')->get('default_value');
        }

        // Recalculate coordinate.
        [$x, $y, $w, $h] = explode(',', $iiif_crop_override_value);
        $absolute = $this->cropManager->relativeToAbsolute((float) $x, (float) $y, (float) $w, (float) $h, $event->image->getWidth(), $event->image->getHeight());

        // Set anchor & w/h.
        $anchor = $current_crop->anchor();
        $size = $current_crop->size();

        if ($anchor['x'] != $absolute['x'] || $anchor['y'] != $absolute['y'] || $size['width'] != $absolute['w'] || $size['height'] != $absolute['h']) {
          $current_crop->setPosition($absolute['x'], $absolute['y']);
          $current_crop->setSize($absolute['w'], $absolute['h']);
          $current_crop->save();
        }
      }
    }
  }

  /**
   *
   *
   * todo: move to base class to re-use.
   */
  public function retrieveContextualCrop($context, $crop_type, $original_uri, $entity_parent) {

    $cropStorage = $this->entityTypeManager->getStorage('crop');

    // Try to load crop for the current context.
    $base_crop = ['uri' => $original_uri, 'type' => $crop_type, 'context' => $context];
    $crop = $cropStorage->loadByProperties($base_crop);
    $crop = reset($crop) ?: NULL;

    // Create a new crop.
    if ($crop == NULL) {

      $values = [
        'type' => $crop_type,
        'entity_id' => $entity_parent->id(),
        'entity_type' => $entity_parent->bundle(),
        'uri' => $original_uri,
        'context' => $context,
      ];

      // Create new crop.
      $crop = $cropStorage->create($values);
    }

    return $crop;

  }

}
