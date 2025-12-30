<?php

namespace Drupal\iiif_media_source;

/**
 * Interface for IIIF Image objects.
 */
interface IiifImageInterface {

  /**
   * Gets the full URL of the image.
   *
   * @return string
   *   The full URL of the image.
   */
  public function getFullUrl(): string;

}
