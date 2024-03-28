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
  //   '^max' => '^max',
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
          '^max' => '^max',
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

  public static function processSettings($settings) {
    $region_actual = str_replace(['w', 'h'], [$settings['region_w'], $settings['region_h']], $settings['region']);
    $size_actual = str_replace(['w', 'h', 'n'], [$settings['size_w'], $settings['size_h'], $settings['size_n'] ?? ""], $settings['size']);

    return [
      $region_actual,
      $size_actual,
    ];
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
  public function getWidth(): ?int {
    return $this->info->width ?? NULL;
  }

  /**
   *
   */
  public function getHeight(): ?int {
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
  public function getBuiltImageUrl($region, $size, $rotation, $quality, $format): string {
    // @todo validate these values.
    $url = implode("/", [
      $this->server,
      $this->prefix,
      $this->iiifId,
      $region,
      $size,
      $rotation,
      $quality . "." . $format,
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
  public function getDefaultExtension(): string {
    // Asummption here that the first element is the default Extension.
    return current($this->info->profile[1]->formats) ?? "jpg";
  }

  /**
   *
   */
  public function transformDimensions(array $settings): array {
    $dimensions = [
      'width' => $this->getWidth(),
      'height' => $this->getHeight(),
    ];

    // Process the region dimension.
    switch ($settings['region']) {
      // case 'full':
      //   break;

      case 'square':
        /**
         * Defined in IIIF as:
         * The region is defined as an area where the width
         * and height are both equal to the length of the shorter dimension of
         * the complete image. The region may be positioned anywhere in the
         * longer dimension of the image content at the server’s discretion,
         * and centered is often a reasonable default.
         */
        if ($dimensions['width'] > $dimensions['height']) {
          $dimensions['width'] = $dimensions['height'];
        }
        else {
          $dimensions['height'] = $dimensions['width'];
        }
        break;

      case 'x,y,w,h':
        $dimensions['width'] = $settings['region_w'];
        $dimensions['height'] = $settings['region_h'];
        break;

      case 'pct:x,y,w,h':
        // Percentages are 0-100, and can be floats.
        $dimensions['width'] *= $settings['region_w'] / 100;
        $dimensions['height'] *= $settings['region_h'] / 100;

        break;
    }

    switch ($settings['size']) {
      case 'max':
        // nothing.
        break;

      case '^max':
        // Upscale as permitted by maxWidth, maxHeight, maxArea.


        break;

      case 'w,':

        break;

      case '^w,':

        break;

      case ',h':

        break;

      case '^,h':

        break;

      case 'pct:n':

        break;

      case '^pct:n':

        break;

      case 'w,h':

        break;

      case '^w,h':

        break;

      case '!w,h':

        break;

      case '^!w,h':

        break;

    }

    // Resize for rotation. sines an cosines!
    if ($settings['rotation'] === 90 || $settings['rotation'] === 270) {
      $w = $dimensions['width'];
      $h = $dimensions['height'];
      $dimensions['width'] = $h;
      $dimensions['height'] = $w;
    }
    elseif ($settings['rotation'] !== 0) {

      $settings['rotation'] = ($settings['rotation'] % 360);

      if (($settings['rotation'] > 0 && $settings['rotation'] < 90) || ($settings['rotation'] > 180 && $settings['rotation'] < 270)) {
        $w1 = sin(deg2rad($settings['rotation'] % 90)) * $dimensions['height'];
        $w2 = cos(deg2rad($settings['rotation'] % 90)) * $dimensions['width'];

        $h1 = sin(deg2rad($settings['rotation'] % 90)) * $dimensions['width'];
        $h2 = cos(deg2rad($settings['rotation'] % 90)) * $dimensions['height'];

        $dimensions['width'] = (int) ceil($w1 + $w2);
        $dimensions['height'] = (int) ceil($h1 + $h2);
      }
      else {
        $h1 = sin(deg2rad($settings['rotation'] % 90)) * $dimensions['height'];
        $h2 = cos(deg2rad($settings['rotation'] % 90)) * $dimensions['width'];

        $w1 = sin(deg2rad($settings['rotation'] % 90)) * $dimensions['width'];
        $w2 = cos(deg2rad($settings['rotation'] % 90)) * $dimensions['height'];

        $dimensions['width'] = (int) ceil($w1 + $w2);
        $dimensions['height'] = (int) ceil($h1 + $h2);
      }
    }

    // Validate maxWidth/maxHeight/MaxArea.

    return $dimensions;
  }

}
