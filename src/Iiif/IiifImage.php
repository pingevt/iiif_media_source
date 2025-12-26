<?php

namespace Drupal\iiif_media_source\Iiif;

/**
 * IIIF Image class.
 */
class IiifImage extends IiifBase {

  /**
   * Thumbnail width.
   *
   * @var int
   *
   * @todo should be set in config somewhere?
   */
  protected $thumbWidth = 200;

  /**
   * Thumbnail Height.
   *
   * @var int
   *
   * @todo should be set in config somewhere?
   */
  protected $thumbHeight = 200;

  /**
   * Build the full manifest URL.
   */
  public function getManifestUrl() {
    return implode("/", [$this->server, $this->prefix, $this->iiifId, "info.json"]);
  }

  /**
   * Get the image width.
   */
  public function getWidth(): ?int {
    return $this->info->width ?? NULL;
  }

  /**
   * Get the image height.
   */
  public function getHeight(): ?int {
    return $this->info->height ?? NULL;
  }

  /**
   * Get the image dimensions.
   */
  public function getDimensions(): ?array {
    return [
      "w" => $this->getWidth(),
      "h" => $this->getHeight(),
    ];
  }

  /**
   * Get the max height.
   */
  public function getMaxHeight(): ?int {

    if ($this->getApiVersion() == "3") {
      return $this->info->maxHeight ?? NULL;
    }

    return $this->info->profile->maxHeight ?? NULL;
  }

  /**
   * Get the max width.
   */
  public function getMaxWidth(): ?int {

    if ($this->getApiVersion() == "3") {
      return $this->info->maxWidth ?? NULL;
    }
    return $this->info->profile->maxWidth ?? NULL;
  }

  /**
   * Get the max area.
   */
  public function getMaxArea(): ?int {

    if ($this->getApiVersion() == "3") {
      return $this->info->maxArea ?? NULL;
    }
    return $this->info->profile->maxArea ?? NULL;
  }

  /**
   * Get the thumbnail URL.
   */
  public function getThumbnailUrl(): string {

    $url = implode(DIRECTORY_SEPARATOR, [
      $this->server,
      $this->prefix,
      $this->iiifId,
      "full",
      "!" . $this->thumbWidth . "," . $this->thumbHeight,
      0,
      // "default." . $this->getDefaultExtension(),
      "default.jpg",
    ]);

    return $url;
  }

  /**
   * Get the full URL.
   */
  public function getFullUrl(): string {

    $url = implode(DIRECTORY_SEPARATOR, [
      $this->server,
      $this->prefix,
      $this->iiifId,
      "full",
      "full",
      0,
      "default." . $this->getDefaultExtension(),
    ]);

    return $url;
  }

  /**
   * Get the built image URL.
   */
  public function getBuiltImageUrl(IiifImageUrlParams $params): string {

    $url = implode(DIRECTORY_SEPARATOR, [
      rtrim($this->server, DIRECTORY_SEPARATOR),
      ltrim($this->prefix, DIRECTORY_SEPARATOR),
      $this->iiifId,
      $params->buildUrlString(),
    ]);

    return $url;
  }

  /**
   * Get the scaled URL.
   *
   * @param int $width
   *   The width, in pixels.
   * @param int $height
   *   The height, in pixels.
   */
  public function getScaledUrl(int $width, int $height): string {

    // @todo create settings obj, so proper validation happens.
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => "full",
    // "!" . $width . "," . $height,
      'size' => "!w,h",
      'size_w' => $width,
      'size_h' => $height,
      'rotation' => 0,
      'quality' => "default",
      'format' => $this->getDefaultExtension(),
      'version' => $this->getApiVersion(),
    ], $this->getApiVersion());

    $url = implode(DIRECTORY_SEPARATOR, [
      $this->server,
      $this->prefix,
      $this->iiifId,
      $params->buildUrlString(),
    ]);

    return $url;
  }

  /**
   * Get the default extension.
   *
   * @return string
   *   The default image format extension (e.g., "jpg", "png").
   */
  public function getDefaultExtension(): string {
    // IIIF v3: preferredFormats is a top-level array.
    if (isset($this->info->preferredFormats) && is_array($this->info->preferredFormats) && count($this->info->preferredFormats) > 0) {
      return current($this->info->preferredFormats);
    }
    // IIIF v3: extraFormats is a top-level array.
    if (isset($this->info->extraFormats) && is_array($this->info->extraFormats) && count($this->info->extraFormats) > 0) {
      return current($this->info->extraFormats);
    }

    // IIIF v2: formats may be in profile[1]->formats.
    if (isset($this->info->profile[1]->formats) && is_array($this->info->profile[1]->formats) && count($this->info->profile[1]->formats) > 0) {
      return current($this->info->profile[1]->formats);
    }

    // Fallback.
    return "jpg";
  }

  /**
   * Get the API version.
   */
  public function getApiVersion(): string {
    if (isset($this->info->{'@context'}) && $this->info->{'@context'} == "http://iiif.io/api/image/2/context.json") {
      return "2.1";
    }
    if (isset($this->info->{'@context'}) && $this->info->{'@context'} == "http://iiif.io/api/image/3/context.json") {
      return "3";
    }

    return "2";
  }

  /**
   * Get the image info.
   */
  public function downloadImage(IiifImageUrlParams $params, string $directory) {
    // phpcs:disable
    // $remote_thumbnail_url = $this->getBuiltImageUrl($params);
    // // ksm($remote_thumbnail_url);
    // if (!$remote_thumbnail_url) {
    //   return NULL;
    // }
    // // Ensure that the destination directory is writable, and if it's not,
    // // log an error and bail out.
    // if (!$this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
    //   // $this->logger->warning('Could not prepare thumbnail destination directory @dir for oEmbed media.', [
    //   //   '@dir' => $directory,
    //   // ]);
    //   return NULL;
    // }
    // // The local filename of the thumbnail is always a hash of its remote URL.
    // // If a file with that name already exists in the thumbnails directory,
    // // regardless of its extension, return its URI.
    // // $remote_thumbnail_url = $remote_thumbnail_url->toString();
    // $hash = Crypt::hashBase64($remote_thumbnail_url);
    // $files = $this->fileSystem->scanDirectory($directory, "/^$hash\..*/");
    // if (count($files) > 0) {
    //   return reset($files)->uri;
    // }
    // // The local thumbnail doesn't exist yet, so we need to download it.
    // try {
    //   $response = $this->httpClient->request('GET', $remote_thumbnail_url);
    //   // ksm($response);
    //   if ($response->getStatusCode() === 200) {
    //     $local_thumbnail_uri = $directory . DIRECTORY_SEPARATOR . $hash . '.' . $ext;
    //     // ksm($local_thumbnail_uri);
    //     $this->fileSystem->saveData((string) $response->getBody(), $local_thumbnail_uri, FileSystemInterface::EXISTS_REPLACE);
    //     return $local_thumbnail_uri;
    //   }
    // }
    // catch (TransferException $e) {
    //   $this->logger->warning('Failed to download remote thumbnail file due to "%error".', [
    //     '%error' => $e->getMessage(),
    //   ]);
    // }
    // catch (FileException $e) {
    //   $this->logger->warning('Could not download remote thumbnail from {url}.', [
    //     'url' => $remote_thumbnail_url,
    //   ]);
    // }
    // phpcs:enable
  }

}
