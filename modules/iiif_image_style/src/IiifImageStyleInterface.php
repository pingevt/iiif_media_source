<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\iiif_media_source\Iiif\IiifImage;

/**
 * Provides an interface defining a Iiif image style entity type.
 */
interface IiifImageStyleInterface extends ConfigEntityInterface {

  /**
   * Returns the URL of this image derivative for an original IIIF image.
   *
   * @param \Drupal\iiif_media_source\Iiif\IiifImage $image
   *   The path or URI to the original image.
   *
   * @return string
   *   The absolute URL where a style image can be downloaded, suitable for use
   *   in an <img> tag.
   */
  public function buildUrl(IiifImage $image): string;

}
