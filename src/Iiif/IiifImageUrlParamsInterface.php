<?php

namespace Drupal\iiif_media_source\Iiif;

/**
 *
 */
interface IiifImageUrlParamsInterface {

  /**
   *
   */
  public function __construct($version);

  /**
   *
   */
  public function __set($key, $value);

  /**
   *
   */
  public static function fromSettingsArray(array $settings): static;

  /**
   *
   */
  public static function getDefaultSettings(string $version = "2"): array;

  /**
   *
   */
  public static function getRegionOptions(string $version = "2"): array;

  /**
   *
   */
  public static function getSizeOptions(string $version = "2"): array;

  /**
   *
   */
  public static function getQualityOptions(string $version = "2"): array;

  /**
   *
   */
  public static function getFormatOptions(string $version = "2"): array;

  /**
   *
   */
  public function getSetting(string $setting_name): ?string;

  /**
   *
   */
  public function getRegion(): string;

  /**
   *
   */
  public function getSize(): string;

  /**
   *
   */
  public function getRotation(): string;

  /**
   *
   */
  public function getQuality(): string;

  /**
   *
   */
  public function getFormat(): string;

  /**
   *
   */
  public function getVersion(): float;

  /**
   *
   */
  public function buildUrlString(): string;

  /**
   *
   */
  public function transformDimensions(IiifImage $image): array;

  /**
   *
   */
  public function validateParamsAgainstImage(IiifImage $image): bool;

}
