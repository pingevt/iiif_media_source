<?php

namespace Drupal\iiif_media_source\Iiif;

use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
abstract class IiifBase {

  /**
   * An http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  protected $server = "";

  protected $prefix = "";

  protected $iiifId = "";

  protected $info;

  /**
   *
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
   *
   */
  protected function retrieveManifest(): void {
    // @todo cache this call on usage and for long term.
    $url = implode("/", [$this->server, $this->prefix, $this->iiifId, "info.json"]);
    $data = $this->call($url);
    // ksm($url, $data);
    if ($data) {
      $this->info = json_decode($data->getBody()->__toString());
    }
  }

  public function getInfo() {
    return $this->info;
  }

  public function getInfoEncoded() {
    return json_encode($this->info);
  }

  /**
   *
   */
  protected function call(string $url, array $headers = []): ?Response {
    $response = NULL;
    // $headers = [
    //   'User-Agent' => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36",
    // ];
    // ksm($headers);
    try {
      $response = $this->httpClient->get($url, $headers);
    }
    catch (\Exception $e) {
      // todo: log or something.
      ksm($e);
    }
    return $response;
  }

}
