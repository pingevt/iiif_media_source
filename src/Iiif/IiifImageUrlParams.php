<?php

namespace Drupal\iiif_media_source\Iiif;

/**
 * Class to handle and verify URL params for IIIF Image urls.
 *
 * WIP
 */
final class IiifImageUrlParams implements IiifImageUrlParamsInterface {

  /**
   * Version of IIIF Image API to use.
   *
   * @var float
   */
  private $version = 2.1;

  // todo: look at custom config entity types
  // https://www.drupal.org/docs/drupal-apis/configuration-api/configuration-schemametadata#s-custom-types

  // todo add validation based on IIIF image settings?

  /**
   * Url Parameters and Data to build yrl correctly.
   *
   * @var array
   */
  private $params = [
    'region' => '',
    'region_actual' => NULL,
    'region_x' => '',
    'region_y' => '',
    'region_w' => '',
    'region_h' => '',
    'size' => '',
    'size_actual' => NULL,
    'size_w' => '',
    'size_h' => '',
    'size_n' => '',
    'rotation' => '',
    'quality' => '',
    'format' => '',
  ];

  /**
   * {@inheritdoc}
   */
  public function __construct($version) {
    $this->setVersion($version);
  }

  public function __set($key, $value) {
    // todo any other validation.

    if (isset($this->params[$key])) {
      $this->params[$key] = $value;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function fullImageParams($version = 2.0): static {
    $obj = new static($version);
    $obj->buildFromArray([
      'region' => 'full',
      'region_actual' => 'full',
      'region_x' => '',
      'region_y' => '',
      'region_w' => '',
      'region_h' => '',
      'size' => 'full',
      'size_actual' => 'full',
      'size_w' => '',
      'size_h' => '',
      'size_n' => '',
      'rotation' => 0,
      'quality' => 'default',
      'format' => 'jpg',
    ]);

    return $obj;
  }

  /**
   * {@inheritdoc}
   */
  public static function fromSettingsArray(array $settings, $version = 2.0): static {
    $obj = new static($version);
    $obj->buildFromArray($settings);

    return $obj;
  }

  private function buildFromArray(array $settings): void {
    foreach ($settings as $k => $v) {
      if (isset($this->params[$k])) {
        $this->params[$k] = $v;
      }
    }
  }

  /**
   * Set the IIIF Image API version. Validate and options will run off of this.
   */
  private function setVersion($version): void {

    $acceptable_versions = [
      2.0,
      2.1,
      3.0,
    ];

    $this->version = in_array(floatval($version), $acceptable_versions) ? floatval($version) : $this->version;
  }

  /**
   * {@inheritdoc}
   */
  public static function getDefaultSettings(string $version = "2"): array {

    return [
      'region' => 'full',
      'region_x' => '',
      'region_y' => '',
      'region_w' => '',
      'region_h' => '',
      'size' => 'max',
      'size_w' => '',
      'size_h' => '',
      'rotation' => 0,
      'quality' => 'default',
      'format' => 'png',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function getRegionOptions(string $version = "2"): array {
    $options = [];

    switch ($version) {
      case "2":
      case 2.0;
      case 2.1:
        $options = [
          'full' => 'full',
          'x,y,w,h' => 'x,y,w,h',
          'pct:x,y,w,h' => 'pct:x,y,w,h',
        ];
        break;

      case "3":
      case 3.0:
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

  /**
   * {@inheritdoc}
   */
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

  /**
   * {@inheritdoc}
   */
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

  /**
   * {@inheritdoc}
   */
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

  /**
   * This creates the actuals for 'region' and 'size'.
   */
  private function expandSettings(): void {

    $this->params['region_actual'] = str_replace(['x', 'y', 'w', 'h'], [$this->params['region_x'], $this->params['region_y'], $this->params['region_w'], $this->params['region_h']], $this->params['region']);
    $this->params['size_actual'] = str_replace(['w', 'h', 'n'], [$this->params['size_w'], $this->params['size_h'], $this->params['size_n'] ?? ""], $this->params['size']);
  }

  /**
   * {@inheritdoc}
   */
  public function getSetting(string $setting_name): ?string {
    return $this->params[$setting_name] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionSettings(): array {
    $data = [];

    $keys = [
      'region',
      'region_actual',
      'region_x',
      'region_y',
      'region_w',
      'region_h',
    ];

    foreach ($keys as $k) {
      $data[$k] = $this->params[$k];
    }

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegion(): string {

    $this->expandSettings();

    return $this->params['region_actual'];
  }

  /**
   *
   */
  public function applyRegionSettings(array $settings): void {
    $keys = [
      'region',
      'region_actual',
      'region_x',
      'region_y',
      'region_w',
      'region_h',
    ];

    foreach ($keys as $key) {
      if ($settings[$key]) {
        $this->params[$key] = $settings[$key];
      }
    }

  }


  /**
   * {@inheritdoc}
   */
  public function getSizeSettings(): array {
    $data = [];

    $keys = [
      'size',
      'size_actual',
      'size_x',
      'size_y',
      'size_n',
    ];

    foreach ($keys as $k) {
      $data[$k] = $this->params[$k];
    }

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function getSize(): string {

    $this->expandSettings();

    return $this->params['size_actual'];
  }

  /**
   *
   */
  public function applySizeSettings(array $settings): void {
    $keys = [
      'size',
      'size_actual',
      'size_x',
      'size_y',
      'size_n',
    ];

    foreach ($keys as $key) {
      if ($settings[$key]) {
        $this->params[$key] = $settings[$key];
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getRotation(): string {
    return $this->params['rotation'];
  }

  /**
   * {@inheritdoc}
   */
  public function getQuality(): string {
    return $this->params['quality'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormat(): string {
    return $this->params['format'];
  }

  /**
   * {@inheritdoc}
   */
  public function getVersion(): float {

    return number_format($this->version, 1);
  }

  /**
   * {@inheritdoc}
   */
  public function buildUrlString(): string {
    $this->expandSettings();

    // Combine the settings.
    $url_params = [
      $this->params['region_actual'],
      $this->params['size_actual'],
      $this->params['rotation'],
      $this->params['quality'] . "." . $this->params['format'],
    ];

    return implode(DIRECTORY_SEPARATOR, $url_params);
  }

  /**
   *
   */
  public function transformDimensions(IiifImage $image): array {
    $settings = $this->params;

    $dimensions = [
      'width' => $image->getWidth(),
      'height' => $image->getHeight(),
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
        // todo
        break;

      case 'w,':
        $dimensions['width'] = $settings['size_w'];
        $dimensions['height'] = (int) ceil($settings['size_w'] * $this->getHeight() / $this->getWidth());

        break;

      case '^w,':
        // todo

        break;

      case ',h':

        $dimensions['width'] = (int) ceil($settings['size_h'] * $this->getWidth() / $this->getHeight());
        $dimensions['height'] = $settings['size_h'];

        break;

      case '^,h':
        // todo

        break;

      case 'pct:n':

        $dimensions['width'] = (int) round($dimensions['width'] * $settings['size_n'] / 100);
        $dimensions['height'] = (int) round($dimensions['height'] * $settings['size_n'] / 100);

        break;

      case '^pct:n':
        // todo

        break;

      case 'w,h':

        $dimensions['width'] = (int) round($settings['size_w']);
        $dimensions['height'] = (int) round($settings['size_h']);

        break;

      case '^w,h':
        // todo

        break;

      case '!w,h':
        // todo

        break;

      case '^!w,h':
        // todo

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

  /**
   *
   */
  public function transformPosition(IiifImage $image): array {
    $settings = $this->params;

    $dimensions = [
      'width' => $image->getWidth(),
      'height' => $image->getHeight(),
    ];

    $position = [
      'x' => 0,
      'y' => 0,
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
          $position['x'] = (int) ($dimensions['width'] - $dimensions['height']) / 2;
        }
        else {
          $position['x'] = (int) ($dimensions['height'] - $dimensions['width']) / 2;
        }
        break;

      case 'x,y,w,h':
        $position['x'] = $settings['region_x'];
        $position['y'] = $settings['region_y'];
        break;

      case 'pct:x,y,w,h':

        $position['x'] = (int) $settings['region_x'] / 100 * $dimensions['width'];
        $position['y'] = (int) $settings['region_y'] / 100 * $dimensions['height'];

        break;
    }

    return $position;
  }

  /**
   *
   */
  public function validateParamsAgainstImage(IiifImage $image): bool {
    return TRUE;
  }

}
