<?php

namespace Drupal\iiif_media_source\Iiif;

/**
 *
 */
class IiifImage extends IiifBase {


  // public static $regionOptions = [
  //   'full' => 'full',
  //   'square' => 'square',
  //   'x,y,w,h' => 'x,y,w,h',
  //   'pct:x,y,w,h' => 'pct:x,y,w,h',
  // ];

  public static function getRegionOptions(string $version = "2"): array {
    $options = [];

    switch ($version) {
      case "2":
        $options = [
          'full' => 'full',
          'x,y,w,h' => 'x,y,w,h',
          'pct:x,y,w,h' => 'pct:x,y,w,h',
        ];
        break;

      case "3":
        $options = [
          'full' => 'full',
          'square' => 'square',
          'x,y,w,h' => 'x,y,w,h',
          'pct:x,y,w,h' => 'pct:x,y,w,h',
        ];
        break;
    }

    return $options;
  }

  // public static $sizeOptions = [
  //   'max' => 'max',
  //   '!max' => '!max',
  //   'w,' => 'w,',
  //   '^w,' => '^w,',
  //   ',h' => ',h',
  //   '^,h' => '^,h',
  //   'pct:n' => 'pct:n',
  //   '^pct:n' => '^pct:n',
  //   'w,h' => 'w,h',
  //   '^w,h' => '^w,h',
  //   '!w,h' => '!w,h',
  //   '^!w,h' => '^!w,h',
  // ];

  public static function getSizeOptions(string $version = "2"): array {
    $options = [];

    switch ($version) {
      case "2":
        $options = [
          'full' => 'full',
          'w,' => 'w,',
          ',h' => ',h',
          'pct:n' => 'pct:n',
          'w,h' => 'w,h',
          '!w,h' => '!w,h',
        ];
        break;

      case "3":
        $options = [
          'max' => 'max',
          '!max' => '!max',
          'w,' => 'w,',
          '^w,' => '^w,',
          ',h' => ',h',
          '^,h' => '^,h',
          'pct:n' => 'pct:n',
          '^pct:n' => '^pct:n',
          'w,h' => 'w,h',
          '^w,h' => '^w,h',
          '!w,h' => '!w,h',
          '^!w,h' => '^!w,h',
        ];
        break;
    }

    return $options;
  }

  // public static $qualityOptions = [
  //   'color' => 'color',
  //   'gray' => 'gray',
  //   'bitonal' => 'bitonal',
  //   'default' => 'default',
  // ];

  public static function getQualityOptions(string $version = "2"): array {
    $options = [];

    switch ($version) {
      case "2":
        $options = [
          'color' => 'color',
          'gray' => 'gray',
          'bitonal' => 'bitonal',
          'default' => 'default',
        ];
        break;

      case "3":
        $options = [
          'color' => 'color',
          'gray' => 'gray',
          'bitonal' => 'bitonal',
          'default' => 'default',
        ];
        break;
    }

    return $options;
  }

  // public static $formatOptions = [
  //   'jpg' => 'jpg',
  //   'tif' => 'tif',
  //   'png' => 'png',
  //   'gif' => 'gif',
  //   'jp2' => 'jp2',
  //   'pdf' => 'pdf',
  //   'webp' => 'webp',
  // ];

  public static function getFormatOptions(string $version = "2"): array {
    $options = [];

    switch ($version) {
      case "2":
        $options = [
          'jpg' => 'jpg',
          'tif' => 'tif',
          'png' => 'png',
          'gif' => 'gif',
          'jp2' => 'jp2',
          'pdf' => 'pdf',
          'webp' => 'webp',
        ];
        break;

      case "3":
        $options = [
          'jpg' => 'jpg',
          'tif' => 'tif',
          'png' => 'png',
          'gif' => 'gif',
          'jp2' => 'jp2',
          'pdf' => 'pdf',
          'webp' => 'webp',
        ];
        break;
    }

    return $options;
  }

  protected $thumbWidth = 200;

  protected $thumbHeight = 200;

  // public function __construct(string $base_url, string $id) {
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
