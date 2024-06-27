<?php

namespace Drupal\iiif_media_source\Iiif;

/**
 *
 */
class IiifImage extends IiifBase {

  /**
   * @todo should be set in config somewhere?
   */
  protected $thumbWidth = 200;

  /**
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
      rtrim($this->server, DIRECTORY_SEPARATOR),
      ltrim($this->prefix, DIRECTORY_SEPARATOR),
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
    // Assumption here that the first element is the default Extension.
    return isset($this->info->profile[1]->formats) ? current($this->info->profile[1]->formats) : "jpg";
  }

  /**
   *
   */
  public function getApiVersion() {
    if (isset($this->info->{'@context'}) && $this->info->{'@context'} == "http://iiif.io/api/image/2/context.json") {
      return 2.1;
    }
    if (isset($this->info->{'@context'}) && $this->info->{'@context'} == "http://iiif.io/api/image/3/context.json") {
      return 3;
    }

    return 2.0;
  }

  /**
   *
   */
  public function downloadImage(IiifImageUrlParams $params, string $directory) {

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
  }

}
