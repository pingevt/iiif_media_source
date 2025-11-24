<?php

declare(strict_types=1);

namespace Drupal\iiif_media_source\Iiif;

/**
 * Class to handle and verify URL params for IIIF Image urls.
 *
 * WIP.
 */
final class IiifImageUrlParams implements IiifImageUrlParamsInterface {

  /**
   * Version of IIIF Image API to use.
   *
   * @var string
   */
  private $version = "2.1";

  // @todo look at custom config entity types
  // https://www.drupal.org/docs/drupal-apis/configuration-api/configuration-schemametadata#s-custom-types
  // @todo add validation based on IIIF image settings?

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
   * Constructs a new IIIF Image URL Params object.
   *
   * @param string $version
   *   The IIIF Image API version (e.g., "2", "2.1", "3").
   */
  public function __construct(string $version) {
    $this->setVersion($version);
  }

  /**
   * Sets a parameter value.
   *
   * @param string $key
   *   The parameter key.
   * @param mixed $value
   *   The value to set.
   */
  public function __set(string $key, $value): void {
    // @todo any other validation.
    if (isset($this->params[$key])) {
      $this->params[$key] = $value;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function fullImageParams($version = "2.0"): static {
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
   * Creates an instance from a settings array.
   *
   * @param array $settings
   *   The settings array.
   * @param string $version
   *   The IIIF Image API version.
   *
   * @return static
   *   A new instance of the implementing class.
   */
  public static function fromSettingsArray(array $settings, string $version = "2.0"): static {
    $obj = new static($version);
    $obj->buildFromArray($settings);

    return $obj;
  }

  /**
   * Build the object from an array of settings.
   */
  private function buildFromArray(array $settings): void {
    foreach ($settings as $k => $v) {
      if (isset($this->params[$k])) {
        $this->params[$k] = $v;
      }
    }
  }

  /**
   * Set the IIIF Image API version.
   *
   * Validation and options will run off of this value.
   *
   * @param string $version
   */
  private function setVersion(string $version): void {

    $acceptable_versions = [
      "2",
      "2.1",
      "3",
    ];

    // Normalize some common inputs.
    if (in_array($version, ["2", "2.0"])) {
      $version = "2";
    }
    if (in_array($version, ["2.1", "2.1.1"])) {
      $version = "2.1";
    }
    if (in_array($version, ["3.0", "3.0.0"])) {
      $version = "3";
    }
    if (in_array($version, $acceptable_versions)) {
      $this->version = $version;
    }
    else {
      // Throw Warning.
      trigger_error("Invalid version provided: $version", E_USER_WARNING);
      $this->version = $this->version;
    }
  }

  /**
   * Gets the default settings for a given IIIF version.
   *
   * @param string $version
   *   The IIIF Image API version.
   *
   * @return array
   *   The default settings.
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
   * Gets the available region options for a given IIIF version.
   *
   * @param string $version
   *   The IIIF Image API version.
   *
   * @return array
   *   The available region options.
   */
  public static function getRegionOptions(string $version = "2"): array {
    $options = [];

    switch ($version) {
      case "2":
      case "2.1":
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

  /**
   * Gets the available size options for a given IIIF version.
   *
   * @param string $version
   *   The IIIF Image API version.
   *
   * @return array
   *   The available size options.
   */
  public static function getSizeOptions(string $version = "2"): array {
    $options = [];

    switch ($version) {
      case "2":
      case "2.1":
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
   * Gets the available quality options for a given IIIF version.
   *
   * @param string $version
   *   The IIIF Image API version.
   *
   * @return array
   *   The available quality options.
   */
  public static function getQualityOptions(string $version = "2"): array {
    $options = [];

    switch ($version) {
      case "2":
      case "2.1":
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
   * Gets the available format options for a given IIIF version.
   *
   * @param string $version
   *   The IIIF Image API version.
   *
   * @return array
   *   The available format options.
   */
  public static function getFormatOptions(string $version = "2"): array {
    $options = [];

    switch ($version) {
      case "2":
      case "2.1":
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

    $this->params['region_actual'] = str_replace(['x', 'y', 'w', 'h'], [
      $this->params['region_x'],
      $this->params['region_y'],
      $this->params['region_w'],
      $this->params['region_h'],
    ], $this->params['region']);

    $this->params['size_actual'] = str_replace(['w', 'h', 'n'], [
      $this->params['size_w'],
      $this->params['size_h'],
      $this->params['size_n'] ?? "",
    ], $this->params['size']);
  }

  /**
   * Gets a specific setting value.
   *
   * @param string $setting_name
   *   The name of the setting.
   *
   * @return string|null
   *   The setting value, or NULL if not set.
   */
  public function getSetting(string $setting_name): ?string {
    return $this->params[$setting_name] ?? NULL;
  }

  /**
   * Gets the region settings as an array.
   *
   * @return array
   *   The region settings.
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
   * Gets the region string for the IIIF URL.
   *
   * @return string
   *   The region string.
   */
  public function getRegion(): string {

    $this->expandSettings();

    return $this->params['region_actual'];
  }

  /**
   * Applies region settings from an array.
   *
   * @param array $settings
   *   The region settings to apply.
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
      if (!empty($settings[$key])) {
        $this->params[$key] = $settings[$key];
      }
    }

  }

  /**
   * Gets the size settings as an array.
   *
   * @return array
   *   The size settings.
   */
  public function getSizeSettings(): array {
    $data = [];

    $keys = [
      'size',
      'size_actual',
      'size_w',
      'size_h',
      'size_n',
    ];

    foreach ($keys as $k) {
      $data[$k] = $this->params[$k];
    }

    return $data;
  }

  /**
   * Gets the size string for the IIIF URL.
   *
   * @return string
   *   The size string.
   */
  public function getSize(): string {

    $this->expandSettings();

    return $this->params['size_actual'];
  }

  /**
   * Applies size settings from an array.
   *
   * @param array $settings
   *   The size settings to apply.
   */
  public function applySizeSettings(array $settings): void {
    $keys = [
      'size',
      'size_actual',
      'size_w',
      'size_h',
      'size_n',
    ];

    foreach ($keys as $key) {
      if (!empty($settings[$key])) {
        $this->params[$key] = $settings[$key];
      }
    }

  }

  /**
   * Gets the rotation value for the IIIF URL.
   *
   * @return string
   *   The rotation value.
   */
  public function getRotation(): string {
    return $this->params['rotation'];
  }

  /**
   * Gets the quality value for the IIIF URL.
   *
   * @return string
   *   The quality value.
   */
  public function getQuality(): string {
    return $this->params['quality'];
  }

  /**
   * Gets the format value for the IIIF URL.
   *
   * @return string
   *   The format value.
   */
  public function getFormat(): string {
    return $this->params['format'];
  }

  /**
   * Gets the IIIF Image API version.
   *
   * @return string
   *   The IIIF Image API version.
   */
  public function getVersion(): string {
    return $this->version;
  }

  /**
   * Builds the IIIF image URL string from the parameters.
   *
   * @return string
   *   The IIIF image URL string.
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
   * Transforms the image dimensions based on the parameters.
   *
   * @param IiifImage $image
   *   The IIIF image object.
   *
   * @return array
   *   The transformed dimensions as [width, height].
   */
  public function transformDimensions(IiifImage $image): array {
    $settings = $this->params;

    // If for some reason we don't have image data yet, just return a 1x1.
    if ($image->getWidth() === NULL && $image->getHeight() === NULL) {
      return [
        'width' => 1,
        'height' => 1,
      ];
    }

    // New Dimensions.
    $dimensions = [
      'width' => $image->getWidth(),
      'height' => $image->getHeight(),
    ];

    // ksm($settings, $image->getWidth());
    // Process the region dimension.
    switch ($settings['region']) {
      // phpcs:disable
      // case 'full':
      //   break;
      // phpcs:enable
      case 'square':
        /*
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

      // The extracted region is scaled to the maximum size permitted by
      // maxWidth, maxHeight, or maxArea as defined in the Technical Properties
      // section. If the resulting dimensions are greater than the pixel width
      // and height of the extracted region, the extracted region is upscaled.
      case '^max':
        if ($image->getApiVersion() == "3") {
          $maxWidth = $image->getMaxWidth();
          $maxHeight = $image->getMaxHeight();
          $maxArea = $image->getMaxArea();

          // Start with no scaling (1.0).
          $scale = 1.0;

          // If maxWidth is set, calculate scale needed to reach it.
          if ($maxWidth !== NULL && $dimensions['width'] > 0) {
            $scale = max($scale, $maxWidth / $dimensions['width']);
          }
          // If maxHeight is set, calculate scale needed to reach it.
          if ($maxHeight !== NULL && $dimensions['height'] > 0) {
            $scale = max($scale, $maxHeight / $dimensions['height']);
          }
          // If maxArea is set, calculate scale needed to reach it.
          if ($maxArea !== NULL && $dimensions['width'] > 0 && $dimensions['height'] > 0) {
            $areaScale = sqrt($maxArea / ($dimensions['width'] * $dimensions['height']));
            $scale = max($scale, $areaScale);
          }

          // Apply the scaling factor.
          $dimensions['width'] = (int) round($dimensions['width'] * $scale);
          $dimensions['height'] = (int) round($dimensions['height'] * $scale);

          // After upscaling, if we exceed any max, clamp down.
          list($dimensions['width'], $dimensions['height']) = $this->applyMaxConstraints(
            $dimensions['width'],
            $dimensions['height'],
            $image
          );
        }
        break;

      case 'w,':
        $dimensions['width'] = $settings['size_w'];
        $dimensions['height'] = (int) ceil($settings['size_w'] * $image->getHeight() / $image->getWidth());

        break;

      // The extracted region should be scaled so that the width of the returned
      // image is exactly equal to w. If w is greater than the pixel width of
      // the extracted region, the extracted region is upscaled.
      case '^w,':
        if ($image->getApiVersion() == "3") {
          $target_width = (int) $settings['size_w'];
          $scale = $target_width / $dimensions['width'];
          $new_width = $target_width;
          $new_height = (int) round($dimensions['height'] * $scale);

          list($new_width, $new_height) = $this->applyMaxConstraints($new_width, $new_height, $image);

          $dimensions['width'] = $new_width;
          $dimensions['height'] = $new_height;
        }
        break;

      case ',h':

        $dimensions['width'] = (int) ceil($settings['size_h'] * $image->getWidth() / $image->getHeight());
        $dimensions['height'] = $settings['size_h'];

        break;

      case '^,h':
        if ($image->getApiVersion() == "3") {
          $target_height = (int) $settings['size_h'];
          $scale = $target_height / $dimensions['height'];
          $new_height = $target_height;
          $new_width = (int) round($dimensions['width'] * $scale);

          list($new_width, $new_height) = $this->applyMaxConstraints($new_width, $new_height, $image);

          $dimensions['width'] = $new_width;
          $dimensions['height'] = $new_height;
        }
        break;

      case 'pct:n':

        $dimensions['width'] = (int) round($dimensions['width'] * $settings['size_n'] / 100);
        $dimensions['height'] = (int) round($dimensions['height'] * $settings['size_n'] / 100);

        break;

      case '^pct:n':
        if ($image->getApiVersion() == "3") {
          $scale = $settings['size_n'] / 100;
          $new_width = (int) round($dimensions['width'] * $scale);
          $new_height = (int) round($dimensions['height'] * $scale);

          list($new_width, $new_height) = $this->applyMaxConstraints($new_width, $new_height, $image);

          $dimensions['width'] = $new_width;
          $dimensions['height'] = $new_height;
        }
        break;

      case 'w,h':

        $dimensions['width'] = (int) round($settings['size_w']);
        $dimensions['height'] = (int) round($settings['size_h']);

        break;

      case '^w,h':
        if ($image->getApiVersion() == "3") {
          $new_width = (int) round($settings['size_w']);
          $new_height = (int) round($settings['size_h']);

          list($new_width, $new_height) = $this->applyMaxConstraints($new_width, $new_height, $image);

          $dimensions['width'] = $new_width;
          $dimensions['height'] = $new_height;
        }
        break;

      case '!w,h':
        // Figure out percentages.
        $width_ratio = (float) $settings['size_w'] / (float) $dimensions['width'];
        $height_ratio = (float) $settings['size_h'] / (float) $dimensions['height'];

        if ($dimensions['width'] < $dimensions['height']) {
          $dimensions['width'] = (int) round($height_ratio * $dimensions['width']);
          $dimensions['height'] = (int) round($height_ratio * $dimensions['height']);
        }
        else {
          $dimensions['width'] = (int) round($width_ratio * $dimensions['width']);
          $dimensions['height'] = (int) round($width_ratio * $dimensions['height']);
        }

        break;

      case '^!w,h':
        if ($image->getApiVersion() == "3") {
          $target_width = (int) $settings['size_w'];
          $target_height = (int) $settings['size_h'];

          // Calculate scale to best fit inside target box (preserve aspect ratio, up or down).
          $width_ratio = $target_width / $dimensions['width'];
          $height_ratio = $target_height / $dimensions['height'];
          $scale = min($width_ratio, $height_ratio);

          $new_width = (int) round($dimensions['width'] * $scale);
          $new_height = (int) round($dimensions['height'] * $scale);

          list($new_width, $new_height) = $this->applyMaxConstraints($new_width, $new_height, $image);

          $dimensions['width'] = $new_width;
          $dimensions['height'] = $new_height;
        }
        break;

    }

    // Apply rotation to dimensions.
    list($dimensions['width'], $dimensions['height']) =
      $this->applyRotationToDimensions($dimensions['width'], $dimensions['height'], $settings['rotation']);

    $dimensions = $this->transformWithinMax($dimensions, $image);

    // Force int values.
    $dimensions = array_map('intval', $dimensions);

    // Validate maxWidth/maxHeight/MaxArea.
    return $dimensions;
  }

  /**
   * Validates the parameters against the given IIIF image.
   *
   * @param IiifImage $image
   *   The IIIF image object.
   *
   * @return bool
   *   TRUE if the parameters are valid, FALSE otherwise.
   */
  public function validateParamsAgainstImage(IiifImage $image): bool {
    return TRUE;
  }

  public function transformWithinMax(array $dimensions, IiifImage $image): array {

    if ($image->getApiVersion() == "3") {
      $maxWidth = $image->getMaxWidth();
      $maxHeight = $image->getMaxHeight();
      $maxArea = $image->getMaxArea();

      // print_r([$maxWidth, $maxHeight, $maxArea, $dimensions]);

      if ($maxWidth !== NULL && $dimensions['width'] > $maxWidth) {
        $ratio = $maxWidth / $dimensions['width'];
        $dimensions['width'] = (int) round($dimensions['width'] * $ratio);
        $dimensions['height'] = (int) round($dimensions['height'] * $ratio);
        // print_r($dimensions);
      }

      if ($maxHeight !== NULL && $dimensions['height'] > $maxHeight) {
        $ratio = $maxHeight / $dimensions['height'];
        $dimensions['width'] = (int) round($dimensions['width'] * $ratio);
        $dimensions['height'] = (int) round($dimensions['height'] * $ratio);
      }

      if ($maxArea !== NULL && ($dimensions['width'] * $dimensions['height']) > $maxArea) {
        $ratio = sqrt($maxArea / ($dimensions['width'] * $dimensions['height']));
        $dimensions['width'] = (int) round($dimensions['width'] * $ratio);
        $dimensions['height'] = (int) round($dimensions['height'] * $ratio);
      }
    }

    return $dimensions;
  }

  /**
   * {@inheritdoc}
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
      // phpcs:disable
      // case 'full':
      //   break;
      // phpcs:enable
      case 'square':
        /*
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

    // Force int values.
    $position = array_map('intval', $position);

    return $position;
  }

  /**
   * Applies maxWidth, maxHeight, and maxArea constraints to the given dimensions.
   *
   * If any of the constraints are set on the image, this method will scale down
   * the width and height proportionally so that neither dimension nor the area
   * exceeds the allowed maximums.
   *
   * @param int $width
   *   The proposed width.
   * @param int $height
   *   The proposed height.
   * @param \Drupal\iiif_media_source\Iiif\IiifImage $image
   *   The IIIF image object, which may define maxWidth, maxHeight, and maxArea.
   *
   * @return array
   *   An array with two elements: [width, height], after applying constraints.
   */
  private function applyMaxConstraints($width, $height, $image) {
    $maxWidth = $image->getMaxWidth();
    $maxHeight = $image->getMaxHeight();
    $maxArea = $image->getMaxArea();

    if ($maxWidth !== NULL && $width > $maxWidth) {
      $ratio = $maxWidth / $width;
      $width = (int) round($width * $ratio);
      $height = (int) round($height * $ratio);
    }
    if ($maxHeight !== NULL && $height > $maxHeight) {
      $ratio = $maxHeight / $height;
      $width = (int) round($width * $ratio);
      $height = (int) round($height * $ratio);
    }
    if ($maxArea !== NULL && ($width * $height) > $maxArea) {
      $ratio = sqrt($maxArea / ($width * $height));
      $width = (int) round($width * $ratio);
      $height = (int) round($height * $ratio);
    }
    return [$width, $height];
  }

  /**
   * Validates the settings array for required numeric and option values.
   *
   * Ensures that numeric parameters are non-negative, that percentage values
   * are greater than zero, and that the size option is valid for the IIIF version.
   * Throws InvalidArgumentException if any validation fails.
   *
   * @param array $settings
   *   The settings array to validate (by reference).
   *
   * @throws \InvalidArgumentException
   *   If a parameter is invalid.
   */
  private function validateSettings(array &$settings): void {
    $numeric_keys = ['size_w', 'size_h', 'size_n', 'region_x', 'region_y', 'region_w', 'region_h'];
    foreach ($numeric_keys as $key) {
      if (isset($settings[$key]) && $settings[$key] !== '') {
        if (!is_numeric($settings[$key]) || $settings[$key] < 0) {
          throw new \InvalidArgumentException("Parameter $key must be a non-negative number.");
        }
      }
    }

    // Validate rotation: must be numeric and between 0 and 360 (inclusive)
    if (isset($settings['rotation']) && $settings['rotation'] !== '') {
      if (!is_numeric($settings['rotation']) || $settings['rotation'] < 0 || $settings['rotation'] > 360) {
        throw new \InvalidArgumentException("Parameter rotation must be a number between 0 and 360.");
      }
    }

    if (isset($settings['size_n']) && $settings['size_n'] <= 0) {
      throw new \InvalidArgumentException("Parameter size_n (percentage) must be greater than 0.");
    }
    if (!in_array($settings['size'], array_keys(self::getSizeOptions($this->version)))) {
      throw new \InvalidArgumentException("Invalid size option: " . $settings['size']);
    }

    // Check required parameters for each size mode
    if (isset($settings['size'])) {
      switch ($settings['size']) {
        case '^w,':
          if (!isset($settings['size_w']) || $settings['size_w'] === '') {
            throw new \InvalidArgumentException("Parameter size_w is required for ^w, size mode.");
          }
          break;
        case '^,h':
          if (!isset($settings['size_h']) || $settings['size_h'] === '') {
            throw new \InvalidArgumentException("Parameter size_h is required for ^,h size mode.");
          }
          break;
        case '^w,h':
        case '^!w,h':
          if (!isset($settings['size_w']) || $settings['size_w'] === '') {
            throw new \InvalidArgumentException("Parameter size_w is required for {$settings['size']} size mode.");
          }
          if (!isset($settings['size_h']) || $settings['size_h'] === '') {
            throw new \InvalidArgumentException("Parameter size_h is required for {$settings['size']} size mode.");
          }
          break;
        case '^pct:n':
          if (!isset($settings['size_n']) || $settings['size_n'] === '') {
            throw new \InvalidArgumentException("Parameter size_n is required for ^pct:n size mode.");
          }
          break;
      }
    }
  }

  /**
   * Applies rotation to the given dimensions.
   *
   * Handles 90/180/270 and arbitrary angles, returning the new width and height.
   *
   * @param float|int $width
   *   The original width.
   * @param float|int $height
   *   The original height.
   * @param float|int $rotation
   *   The rotation angle in degrees.
   *
   * @return array
   *   An array with two elements: [width, height] after rotation.
   */
  private function applyRotationToDimensions(float|int $width, float|int $height, $rotation): array {
    $rotation = $rotation % 360;
    if ($rotation === 90 || $rotation === 270) {
      return [$height, $width];
    }
    if ($rotation === 0 || $rotation === 180 || $rotation === 360) {
      return [$width, $height];
    }

    // For arbitrary angles, calculate bounding box.
    $radians = deg2rad($rotation);
    $new_width = (int) ceil(abs($width * cos($radians)) + abs($height * sin($radians)));
    $new_height = (int) ceil(abs($width * sin($radians)) + abs($height * cos($radians)));
    return [$new_width, $new_height];
  }

}
