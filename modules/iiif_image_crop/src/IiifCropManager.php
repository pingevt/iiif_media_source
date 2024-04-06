<?php

namespace Drupal\iiif_image_crop;

use Drupal\crop\Entity\Crop;
use Drupal\focal_point\FocalPointManager;
use Drupal\crop\CropInterface;
use Drupal\crop\CropStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides business logic related to focal point.
 */
class IiifCropManager {

  /**
   * Crop entity storage.
   *
   * @var \Drupal\crop\CropStorageInterface
   */
  protected $cropStorage;

  /**
   * Constructs FocalPointManager.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->cropStorage = $entity_type_manager->getStorage('crop');
  }

  /**
   *
   */
  public function getCropIiifEntity($item, $crop_type, $mid) {
// ksm($item, $crop_type, $mid);
    // ksm($item, $item->getEntity());
    // todo: fix this so it doesn't error out when we don't have a crop.
    // return NULL;

    $img = $item->getImg($item->getValue());
    $url = $img->getFullUrl();
// ksm($img, $url);
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

      // ksm($values);

      $crop = $this->cropStorage->create($values);
    }

    // ksm($crop);

    return $crop;
  }

  /**
   * {@inheritdoc}
   */
  public function relativeToAbsolute($x, $y, $h, $w, $width, $height) {
    // Focal point JS provides relative location while crop entity
    // expects exact coordinate on the original image. Let's convert.
    return [
      'x' => (int) round(($x / 100.0) * $width),
      'y' => (int) round(($y / 100.0) * $height),
      'w' => (int) round(($w / 100.0) * $width),
      'h' => (int) round(($h / 100.0) * $height),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function absoluteToRelative($x, $y, $h, $w, $width, $height) {
    return [
      'x' => $width ? (float) round($x / $width * 10000) / 100 : 0,
      'y' => $height ? (float) round($y / $height * 10000) / 100 : 0,
      'w' => $width ? (float) round($w / $width * 10000) / 100 : 0,
      'h' => $height ? (float) round($h / $height * 10000) / 100 : 0,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function saveCropEntity(float $x, float $y, float $w, float $h, int $width, int $height, CropInterface $crop): CropInterface {
    $absolute = $this->relativeToAbsolute($x, $y, $w, $h, $width, $height);

    $crop->setPosition($absolute['x'], $absolute['y']);
    $crop->setSize($absolute['w'], $absolute['h']);
    $crop->save();

    return $crop;
  }

}
