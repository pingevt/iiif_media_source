<?php

namespace Drupal\iiif_image_focalpoint;

use Drupal\crop\CropInterface;
use Drupal\focal_point\FocalPointManager;

/**
 * Provides business logic related to focal point.
 */
class IiifFocalPointManager extends FocalPointManager {

  /**
   * Get the crop for the image.
   */
  public function getCropIiifEntity($item, $crop_type, $mid) {

    $img = $item->getImg($item->getValue());
    $url = $img->getFullUrl();

    $crop = $this->findCrop($item, $crop_type, $mid, $url);

    if ($crop === NULL) {
      $values = [
        'type' => $crop_type,
        'entity_id' => $mid,
        'entity_type' => $item->getEntity()->bundle(),
        'uri' => $url,
      ];

      $crop = $this->cropStorage->create($values);
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
  protected function findCrop($item, $crop_type, $mid, $url): CropInterface|null {
    $query = $this->cropStorage->getQuery();
    $query->accessCheck(FALSE);
    $query->condition('uri', $url);
    $query->condition('type', $crop_type);
    $query->condition('entity_id', $mid);
    $query->condition('entity_type', $item->getEntity()->bundle());
    $ids = $query->execute();

    if (count($ids) > 0) {
      $crop = $this->cropStorage->load(reset($ids));
      return $crop;
    }

    return NULL;
  }

}
