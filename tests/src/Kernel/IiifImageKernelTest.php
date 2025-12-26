<?php

namespace Drupal\Tests\iiif_media_source\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\iiif_media_source\Iiif\IiifImage;

/**
 * @coversDefaultClass \Drupal\iiif_media_source\Iiif\IiifImage
 *
 * @group iiif_media_source
 */
class IiifImageKernelTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    // Required for basic Drupal functionality.
    'system',
    'media',
    'iiif_media_source',
  ];

  /**
   * Tests the constructor and property initialization.
   */
  public function testConstructor() {
    $server = 'http://example.com';
    $prefix = 'prefix';
    $id = 'image_id';
    $info = (object) ['width' => 1000, 'height' => 800];

    // Create an instance of IiifImage.
    $iiifImage = new IiifImage($server, $prefix, $id, $info);

    // Assert that the properties are set correctly.
    $this->assertEquals($server, $iiifImage->getServer());
    $this->assertEquals($prefix, $iiifImage->getPrefix());
    $this->assertEquals($id, $iiifImage->getIiifId());
    $this->assertEquals($info, $iiifImage->getInfo());
  }

  /**
   * Tests the URL generation method.
   */
  public function testGenerateUrl() {
    $server = 'http://example.com';
    $prefix = 'prefix';
    $id = 'image_id';
    $info = (object) ['width' => 1000, 'height' => 800];

    // Create an instance of IiifImage.
    $iiifImage = new IiifImage($server, $prefix, $id, $info);

    // Test the full URL generation.
    $url = $iiifImage->getFullUrl();
    $expectedUrl = 'http://example.com/prefix/image_id/full/full/0/default.jpg';
    $this->assertEquals($expectedUrl, $url);
  }

  /**
   * Tests the image dimensions.
   */
  public function testGetDimensions() {
    $server = 'http://example.com';
    $prefix = 'prefix';
    $id = 'image_id';
    $info = (object) ['width' => 1000, 'height' => 800];

    // Create an instance of IiifImage.
    $iiifImage = new IiifImage($server, $prefix, $id, $info);

    // Test the dimensions.
    $this->assertEquals(1000, $iiifImage->getWidth());
    $this->assertEquals(800, $iiifImage->getHeight());
  }

  /**
   * Tests the thumbnail URL generation.
   */
  public function testGetThumbnailUrl() {
    $server = 'http://example.com';
    $prefix = 'prefix';
    $id = 'image_id';
    $info = (object) ['width' => 1000, 'height' => 800];

    // Create an instance of IiifImage.
    $iiifImage = new IiifImage($server, $prefix, $id, $info);

    // Test the thumbnail URL generation.
    $url = $iiifImage->getThumbnailUrl();
    $expectedUrl = 'http://example.com/prefix/image_id/full/!200,200/0/default.jpg';
    $this->assertEquals($expectedUrl, $url);
  }

  /**
   * Tests the thumbnail URL generation.
   */
  public function testGetScaledUrl() {
    $server = 'http://example.com';
    $prefix = 'prefix';
    $id = 'image_id';
    $info = (object) ['width' => 1000, 'height' => 800];

    // Create an instance of IiifImage.
    $iiifImage = new IiifImage($server, $prefix, $id, $info);

    // Test the scaled URL generation.
    $url = $iiifImage->getScaledUrl(200, 100);
    $expectedUrl = 'http://example.com/prefix/image_id/full/!200,100/0/default.jpg';
    $this->assertEquals($expectedUrl, $url);
  }

  /**
   * Tests the getDefaultExtension method.
   */
  public function testGetDefaultExtension() {
    $profile_data = new \stdClass();
    $profile_data->formats = ['png', 'tif', 'jpg', 'gif'];
    $withFormatsV2 = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      "@context" => "http://iiif.io/api/image/2/context.json",
      'width' => 1000,
      'height' => 800,
      "profile" => [
        "http://iiif.io/api/image/2/level2.json",
        $profile_data,
      ],
    ]);
    $this->assertEquals('png', $withFormatsV2->getDefaultExtension());

    $noFormatsV2 = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      "@context" => "http://iiif.io/api/image/2/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $this->assertEquals('jpg', $noFormatsV2->getDefaultExtension());

    $withFormatsV3 = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      "@context" => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'extraFormats' => ['webp', 'jpg'],
    ]);
    $this->assertEquals('webp', $withFormatsV3->getDefaultExtension());

    $noFormatsV3 = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      "@context" => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $this->assertEquals('jpg', $noFormatsV3->getDefaultExtension());

    $withFormatsV3wPreffered = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      "@context" => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'extraFormats' => ['jpg', 'png', 'webp', 'gif'],
      'preferredFormats' => ['webp', 'png'],
    ]);
    $this->assertEquals('webp', $withFormatsV3wPreffered->getDefaultExtension());

  }

  /**
   * Tests the getApiVersion method.
   */
  public function testGetApiVersion() {
    $v2 = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/2/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $this->assertEquals("2.1", $v2->getApiVersion());

    $v3 = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $this->assertEquals("3", $v3->getApiVersion());

    $unknown = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      'width' => 1000,
      'height' => 800,
    ]);
    $this->assertEquals("2", $unknown->getApiVersion());
  }

}
