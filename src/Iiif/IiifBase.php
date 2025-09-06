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
   * Constructor.
   */
  public function __construct(string $server, string $prefix, string $id, \stdClass $info = new \stdClass()) {
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
   * Retrieve the manifest.
   */
  protected function retrieveManifest(): void {
    // @todo cache this call on usage and for long term.
    $url = implode("/", [$this->server, $this->prefix, $this->iiifId, "info.json"]);
    $data = $this->call($url);
    // ksm($url, $data);.
    if ($data) {
      $this->info = json_decode($data->getBody()->__toString());
    }
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
      // @todo log or something.
    }

    return $response;
  }

}
