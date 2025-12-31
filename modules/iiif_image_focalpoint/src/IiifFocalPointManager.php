<?php

namespace Drupal\iiif_image_focalpoint;

use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\crop\CropInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\StringItemBase;
use Drupal\focal_point\FocalPointManager;
use Drupal\iiif_media_source\Plugin\Field\FieldType\IiifId;

/**
 * Provides business logic related to focal point.
 */
class IiifFocalPointManager extends FocalPointManager {

  use LoggerChannelTrait;

  /**
   * Get the crop for the image.
   *
   * Drupal\Core\Field\Plugin\Field\FieldType\StringItemBase
   */
  public function getCropIiifEntity(StringItemBase $item, string $crop_type, string $mid): ?CropInterface {
    $img = $item->getIiifImageObj($item->getValue());
    if (!$img) {
      $this->getLogger('iiif_image_focalpoint')->error('Failed to retrieve image for item.');
      return NULL;
    }

    $url = $img->getFullUrl();
    $crop = $this->findCrop($item, $crop_type, $mid, $url);

    if ($crop === NULL) {

      $values = [
        'type' => $crop_type,
        'entity_id' => $mid,
        'entity_type' => $item->getEntity()->getEntityTypeId(),
        'uri' => $url,
      ];

      try {
        $crop = $this->cropStorage->create($values);
        $crop->save();
      }
      catch (\Exception $e) {
        $this->getLogger('iiif_image_focalpoint')->error('Failed to create crop: @message', ['@message' => $e->getMessage()]);
        return NULL;
      }
    }

    return $crop;
  }

  /**
   * Find the crop entity.
   *
   * @param string $item
   *   The item.
   * @param string $crop_type
   *   The crop type.
   * @param string $mid
   *   The mid.
   * @param string $url
   *   The url.
   *
   * @return \Drupal\crop\CropInterface|null
   *   Crop entity or NULL if crop doesn't exist.
   */
  protected function findCrop($item, $crop_type, $mid, $url): ?CropInterface {

    $query = $this->cropStorage->getQuery();
    $query->accessCheck(FALSE);
    $query->condition('uri', $url);
    $query->condition('type', $crop_type);
    $query->condition('entity_id', $mid);
    $query->condition('entity_type', $item->getEntity()->getEntityTypeId());
    $ids = $query->execute();

    if (count($ids) > 0) {
      $crop = $this->cropStorage->load(reset($ids));
      return $crop;
    }

    return NULL;
  }

}
