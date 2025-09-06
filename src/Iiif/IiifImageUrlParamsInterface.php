<?php

namespace Drupal\iiif_media_source\Iiif;

/**
 * Interface for IIIF Image URL parameters.
 */
interface IiifImageUrlParamsInterface {

  /**
   * Constructor.
   */
  public function __construct($version);

  /**
   * Setter.
   */
  public function __set($key, $value);

  /**
   * Array of settings to be used in the form.
   */
  public static function fromSettingsArray(array $settings): static;

  /**
   * Get the default settings.
   */
  public static function getDefaultSettings(string $version = "2"): array;

  /**
   * Get the region options.
   */
  public static function getRegionOptions(string $version = "2"): array;

  /**
   * Get the size options.
   */
  public static function getSizeOptions(string $version = "2"): array;

  /**
   * Get the quality options.
   */
  public static function getQualityOptions(string $version = "2"): array;

  /**
   * Get the format options.
   */
  public static function getFormatOptions(string $version = "2"): array;

  /**
   * Get the settings.
   */
  public function getSetting(string $setting_name): ?string;

  /**
   * Get the region settings.
   */
  public function getRegionSettings(): array;

  /**
   * Get the region of the image.
   */
  public function getRegion(): string;

  /**
   * Apply the region settings to the image.
   */
  public function applyRegionSettings(array $settings): void;

  /**
   * Get the size settings.
   */
  public function getSizeSettings(): array;

  /**
   * Get the size of the image.
   */
  public function getSize(): string;

  /**
   * Apply the size settings to the image.
   */
  public function applySizeSettings(array $settings): void;

  /**
   * Get the rotation of the image.
   */
  public function getRotation(): string;

  /**
   * Get the quality of the image.
   */
  public function getQuality(): string;

  /**
   * Get the format of the image.
   */
  public function getFormat(): string;

  /**
   * Get the version of the IIIF Image API.
   *
   * @return float
   *   The version of the IIIF Image API.
   */
  public function getVersion(): float;

  /**
   * Build the URL string for the IIIF image.
   */
  public function buildUrlString(): string;

  /**
   * Transform the dimensions of the image based on the parameters.
   *
   * @param IiifImage $image
   *   The image to transform.
   *
   * @return array
   *   The transformed dimensions.
   */
  public function transformDimensions(IiifImage $image): array;

  /**
   * Validate the parameters against the image.
   *
   * @param IiifImage $image
   *   The image to validate against.
   *
   * @return bool
   *   Whether the parameters are valid.
   */
  public function validateParamsAgainstImage(IiifImage $image): bool;

}
