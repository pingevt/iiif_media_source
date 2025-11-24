<?php

namespace Drupal\iiif_media_source\Iiif;

use GuzzleHttp\Psr7\Response;

/**
 * IIIF Base class.
 */
abstract class IiifBase {

  /**
   * An http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The IIIF server.
   *
   * @var string
   */
  protected $server = "";

  /**
   * The IIIF prefix.
   *
   * @var string
   */
  protected $prefix = "";

  /**
   * The IIIF id.
   *
   * @var string
   */
  protected $iiifId = "";

  /**
   * The IIIF info.
   *
   * @var \stdClass
   */
  protected $info;

  /**
   * Cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * Logger channel.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructor.
   */
  public function __construct(string $server, string $prefix, string $id, \stdClass $info = new \stdClass()) {

    // Set up cache bin.
    $this->cache = \Drupal::service('cache.iiif_media_source');
    $this->logger = \Drupal::logger('iiif_media_source');
    $this->httpClient = \Drupal::httpClient();

    $this->server = $server;
    $this->prefix = $prefix;
    $this->iiifId = $id;

    // If object is not empty.
    if ($info == new \stdClass()) {
      $this->retrieveManifest();
    }
    else {
      $this->info = $info;
    }
  }

  /**
   * Get the server.
   */
  public function getServer() {
    return $this->server;
  }

  /**
   * Get the prefix.
   */
  public function getPrefix() {
    return $this->prefix;
  }

  /**
   * Get the iiif id.
   */
  public function getIiifId() {
    return $this->iiifId;
  }

  /**
   * Retrieve the iiif image manifest.
   */
  protected function retrieveManifest(): void {

    $cacheKey = 'iiif_manifest_' . md5($this->server . $this->prefix . $this->iiifId);
    $cache = $this->cache->get($cacheKey);

    if ($cache) {
      $this->info = $cache->data;
      return;
    }

    $url = implode("/", [$this->server, $this->prefix, $this->iiifId, "info.json"]);
    $data = $this->call($url);

    if ($data && $data->getBody()) {
      $decodedData = json_decode($data->getBody()->__toString());
      if (json_last_error() === JSON_ERROR_NONE) {
        $this->info = $decodedData;
        // Cache the data for future use.
        $this->cache->set($cacheKey, $decodedData, strtotime('+1 day'));
      }
      else {
        // Log JSON decoding error.
        $this->logger->error('JSON decoding error: @error. URL: @url', [
          '@error' => json_last_error_msg(),
          '@url' => $url,
        ]);
      }
    }
    else {
      // Log or handle the case where $data is null or invalid.
      $this->logger->warning('Failed to retrieve data from URL: @url', [
        '@url' => $url,
      ]);    }

    return;
  }

  /**
   * Get the info.
   */
  public function getInfo() {
    return $this->info;
  }

  /**
   * Get the info encoded.
   */
  public function getInfoEncoded() {
    return json_encode($this->info);
  }

  /**
   * Get the image width.
   */
  protected function call(string $url, array $headers = []): ?Response {
    $response = NULL;
    try {
      $response = $this->httpClient->get($url, $headers);
    }
    catch (\Exception $e) {
      // Log the error.
      $this->logger->error('HTTP request failed: @message. URL: @url', [
        '@message' => $e->getMessage(),
        '@url' => $url,
      ]);
    }

    return $response;
  }

}
