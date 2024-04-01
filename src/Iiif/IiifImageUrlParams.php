<?php

namespace Drupal\iiif_media_source\Iiif;

/**
 * Class to handle and verify URL params for IIIF Image urls.
 */
final class IiifImageUrlParams {

  protected $version = 2;

  // todo: look at ccustom config entity types
  // https://www.drupal.org/docs/drupal-apis/configuration-api/configuration-schemametadata#s-custom-types

  // protected $region;
  // protected $region_actual;
  // protected $region_x;
  // protected $region_y;
  // protected $region_w;
  // protected $region_h;

  // protected $size;
  // protected $size_actual;
  // protected $size_w;
  // protected $size_h;
  // protected $size_n;

  // protected $rotation;
  // protected $quality;
  // protected $format;

  protected $params = [];

  public function __construct($version) {
    $this->setVersion($version);
  }

  private function setVersion($version) {
    $v = floatval($version);
    // todo validate.
    $this->version = floatval($version);
  }

  public function __get($key) {
    return $this->params[$key] ?? NULL;
  }

  public function __set($key, $value) {
    // todo validate.
    $this->params[$key] = $value;
  }

}
