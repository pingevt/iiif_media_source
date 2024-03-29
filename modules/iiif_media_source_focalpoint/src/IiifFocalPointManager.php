<?php

namespace Drupal\iiif_media_source_focalpoint;

use Drupal\crop\Entity\Crop;
use Drupal\focal_point\FocalPointManager;

/**
 * Provides business logic related to focal point.
 */
class IiifFocalPointManager extends FocalPointManager {

  /**
   *
   */
  public function getCropIiifEntity($item, $crop_type, $mid) {

    // ksm($item, $item->getEntity());
    // todo: fix this so it doesn't error out when we don't have a crop.
    // return NULL;

    $img = $item->getImg($item->getValue());
    $url = $img->getFullUrl();

    if (Crop::cropExists($url, $crop_type)) {
      /** @var \Drupal\crop\CropInterface $crop */
      $crop = Crop::findCrop($url, $crop_type);
    }
    else {

      $values = [
        'type' => $crop_type,
        'entity_id' => $mid,
        'entity_type' => $item->getEntity()->bundle(),
        'uri' => $url,
      ];

      $crop = $this->cropStorage->create($values);
    }

    // ksm($crop);

    return $crop;
  }

}
