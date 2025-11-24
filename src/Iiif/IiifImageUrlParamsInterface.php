<?php

namespace Drupal\iiif_media_source\Iiif;

/**
 * Interface for IIIF Image URL parameters.
 */
interface IiifImageUrlParamsInterface {

  /**
   * Constructs a new IIIF Image URL Params object.
   *
   * @param string $version
   *   The IIIF Image API version (e.g., "2", "2.1", "3").
   */
  public function __construct(string $version);

  /**
   * Sets a parameter value.
   *
   * @param string $key
   *   The parameter key.
   * @param mixed $value
   *   The value to set.
   */
  public function __set(string $key, $value): void;

  /**
   * Creates an instance from a settings array.
   *
   * @param array $settings
   *   The settings array.
   *
   * @return static
   *   A new instance of the implementing class.
   */
  public static function fromSettingsArray(array $settings): static;

  /**
   * Gets the default settings for a given IIIF version.
   *
   * @param string $version
   *   The IIIF Image API version.
   *
   * @return array
   *   The default settings.
   */
  public static function getDefaultSettings(string $version = "2"): array;

  /**
   * Gets the available region options for a given IIIF version.
   *
   * @param string $version
   *   The IIIF Image API version.
   *
   * @return array
   *   The available region options.
   */
  public static function getRegionOptions(string $version = "2"): array;

  /**
   * Gets the available size options for a given IIIF version.
   *
   * @param string $version
   *   The IIIF Image API version.
   *
   * @return array
   *   The available size options.
   */
  public static function getSizeOptions(string $version = "2"): array;

  /**
   * Gets the available quality options for a given IIIF version.
   *
   * @param string $version
   *   The IIIF Image API version.
   *
   * @return array
   *   The available quality options.
   */
  public static function getQualityOptions(string $version = "2"): array;

  /**
   * Gets the available format options for a given IIIF version.
   *
   * @param string $version
   *   The IIIF Image API version.
   *
   * @return array
   *   The available format options.
   */
  public static function getFormatOptions(string $version = "2"): array;

  /**
   * Gets a specific setting value.
   *
   * @param string $setting_name
   *   The name of the setting.
   *
   * @return string|null
   *   The setting value, or NULL if not set.
   */
  public function getSetting(string $setting_name): ?string;

  /**
   * Gets the region settings as an array.
   *
   * @return array
   *   The region settings.
   */
  public function getRegionSettings(): array;

  /**
   * Gets the region string for the IIIF URL.
   *
   * @return string
   *   The region string.
   */
  public function getRegion(): string;

  /**
   * Applies region settings from an array.
   *
   * @param array $settings
   *   The region settings to apply.
   */
  public function applyRegionSettings(array $settings): void;

  /**
   * Gets the size settings as an array.
   *
   * @return array
   *   The size settings.
   */
  public function getSizeSettings(): array;

  /**
   * Gets the size string for the IIIF URL.
   *
   * @return string
   *   The size string.
   */
  public function getSize(): string;

  /**
   * Applies size settings from an array.
   *
   * @param array $settings
   *   The size settings to apply.
   */
  public function applySizeSettings(array $settings): void;

  /**
   * Gets the rotation value for the IIIF URL.
   *
   * @return string
   *   The rotation value.
   */
  public function getRotation(): string;

  /**
   * Gets the quality value for the IIIF URL.
   *
   * @return string
   *   The quality value.
   */
  public function getQuality(): string;

  /**
   * Gets the format value for the IIIF URL.
   *
   * @return string
   *   The format value.
   */
  public function getFormat(): string;

  /**
   * Gets the IIIF Image API version.
   *
   * @return string
   *   The IIIF Image API version.
   */
  public function getVersion(): string;

  /**
   * Builds the IIIF image URL string from the parameters.
   *
   * @return string
   *   The IIIF image URL string.
   */
  public function buildUrlString(): string;

  /**
   * Transforms the image dimensions based on the parameters.
   *
   * @param IiifImage $image
   *   The IIIF image object.
   *
   * @return array
   *   The transformed dimensions as [width, height].
   */
  public function transformDimensions(IiifImage $image): array;

  /**
   * Validates the parameters against the given IIIF image.
   *
   * @param IiifImage $image
   *   The IIIF image object.
   *
   * @return bool
   *   TRUE if the parameters are valid, FALSE otherwise.
   */
  public function validateParamsAgainstImage(IiifImage $image): bool;

}
