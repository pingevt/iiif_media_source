<?php

namespace Drupal\iiif_media_source\Iiif;

/**
 *
 */
class IiifImage extends IiifBase {

  /**
   * @todo shoud be set in config somewhere?
   */
  protected $thumbWidth = 200;

  /**
   * @todo shoud be set in config somewhere?
   */
  protected $thumbHeight = 200;

  /**
   *
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
  public function getThumbnailUrl(): string {

    $url = implode(DIRECTORY_SEPARATOR, [
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
  public function getFullUrl(): string {

    $url = implode(DIRECTORY_SEPARATOR, [
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
  public function getBuiltImageUrl(IiifImageUrlParams $params): string {

    $url = implode(DIRECTORY_SEPARATOR, [
      $this->server,
      $this->prefix,
      $this->iiifId,
      $params->buildUrlString(),
    ]);

    return $url;
  }

  /**
   *
   */
  public function getScaledUrl($width, $height): string {

    // @todo create settings obj, so proper validation happens.
    $url = implode(DIRECTORY_SEPARATOR, [
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
    return isset($this->info->profile[1]->formats) ? current($this->info->profile[1]->formats) : "jpg";
  }

  /**
   *
   */
  public function getApiVersion() {
    if ($this->info->{'@context'} == "http://iiif.io/api/image/2/context.json") {
      return 2.1;
    }
    if ($this->info->{'@context'} == "http://iiif.io/api/image/3/context.json") {
      return 3;
    }

    return 2.0;
  }

}
