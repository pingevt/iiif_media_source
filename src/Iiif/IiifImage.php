<?php

namespace Drupal\iiif_media_source\Iiif;

/**
 *
 */
class IiifImage extends IiifBase {

  protected $thumbWidth = 200;
  protected $thumbHeight = 200;

  // Public function __construct(string $base_url, string $id) {
  //   parent::__construct($base_url, $id);.
  // if (isset($this->info->width)) {
  //     $this->width = $this->info->width;
  //   }.

  /**
   * }.
   */
  public function getWidth():?int {
    return $this->info->width ?? NULL;
  }

  /**
   *
   */
  public function getHeight():?int {
    return $this->info->height ?? NULL;
  }

  /**
   *
   */
  public function getDimensions():?array {
    return [
      "w" => $this->getWidth(),
      "h" => $this->getHeight(),
    ];
  }

  /**
   *
   */
  public function getThumbnailUrl():string {

    $url = implode("/", [
      $this->server,
      $this->prefix,
      $this->iiifId,
      "full",
      "!" . $this->thumbWidth . "," . $this->thumbHeight,
      0,
      "default." . $this->getDefaultExtension(),
    ]);

    return $url;
  }

  /**
   *
   */
  public function getFullUrl():string {

    $url = implode("/", [
      $this->server,
      $this->prefix,
      $this->iiifId,
      "full",
      "max",
      0,
      "default." . $this->getDefaultExtension(),
    ]);

    return $url;
  }

  /**
   *
   */
  public function getBuiltImageUrl($region, $size, $rotation, $quality):string {
    // @todo validate these values.
    $url = implode("/", [
      $this->server,
      $this->prefix,
      $this->iiifId,
      $region,
      $size,
      $rotation,
      $quality . "." . $this->getDefaultExtension(),
    ]);

    return $url;
  }

  /**
   *
   */
  public function getScaledUrl($width, $height):string {

    $url = implode("/", [
      $this->server,
      $this->prefix,
      $this->iiifId,
      "full",
      "!" . $width . "," . $height,
      0,
      "default." . $this->getDefaultExtension(),
    ]);

    return $url;
  }

  /**
   *
   */
  protected function getDefaultExtension():string {
    // Asummption here that the first element is the default Extension.
    return current($this->info->profile[1]->formats) ?? "jpg";
  }

}
