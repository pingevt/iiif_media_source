<?php

namespace Drupal\Tests\iiif_media_source\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;
use Drupal\Tests\UnitTestCase;

/**
 *
 */
class IiifImageTests extends UnitTestCase {

  /**
   * Json string of an image source.
   *
   * @var string
   */
  protected $sourceInfo1 = '{"@context" : "http://iiif.io/api/image/2/context.json","@id" : "https://api.nga.gov/iiif/e8bb571a-a90d-45fa-8589-cd98b4f952a4","protocol" : "http://iiif.io/api/image","width" : 3209,"height" : 5109,"sizes" : [{ "width" : 50, "height" : 79 },{ "width" : 100, "height" : 159 },{ "width" : 200, "height" : 319 },{ "width" : 401, "height" : 638 },{ "width" : 802, "height" : 1277 },{ "width" : 1604, "height" : 2554 }],"tiles" : [{ "width" : 256, "height" : 256, "scaleFactors" : [ 1, 2, 4, 8, 16, 32, 64 ] }],"profile" : ["http://iiif.io/api/image/2/level1.json",{ "formats" : [ "jpg" ],"qualities" : [ "native","color","gray" ],"supports" : ["regionByPct","regionSquare","sizeByForcedWh","sizeByWh","sizeAboveFull","rotationBy90s","mirroring"] }]}';

  protected $sourceInfo2 = '{"profile": ["http://iiif.io/api/image/2/level2.json", {"supports": ["canonicalLinkHeader", "profileLinkHeader", "mirroring", "rotationArbitrary", "sizeAboveFull", "regionSquare"], "qualities": ["default", "bitonal", "gray", "color"], "formats": ["jpg", "png", "gif", "webp"]}], "tiles": [{"width": 1024, "scaleFactors": [1, 2, 4, 8, 16, 32, 64, 128]}], "protocol": "http://iiif.io/api/image", "sizes": [{"width": 16, "height": 16}, {"width": 32, "height": 31}, {"width": 63, "height": 61}, {"width": 125, "height": 122}, {"width": 250, "height": 244}, {"width": 500, "height": 487}, {"width": 1000, "height": 974}, {"width": 2000, "height": 1948}], "height": 1948, "width": 2000, "@context": "http://iiif.io/api/image/2/context.json", "@id": "http://puam-loris.aws.princeton.edu/loris/y1972-15.jp2"}';

  /**
   * Before a test method is run, setUp() is invoked.
   *
   * Create new unit object.
   */
  public function setUp(): void {
    parent::setUp();

    \Drupal::unsetContainer();
    $container = new ContainerBuilder();

    $client = $this->getMockBuilder('GuzzleHttp\Client')
      ->disableOriginalConstructor()
      ->getMock();
    $container->set('http_client', $client);

    \Drupal::setContainer($container);
  }

  /**
   * Test Image dimension transformation.
   */
  public function testIiifImageDimensions() {
    $urlParams = IiifImageUrlParams::fullImageParams();
    $testImg1 = new IiifImage("https://media.nga.gov", "iiif", "e8bb571a-a90d-45fa-8589-cd98b4f952a4", json_decode($this->sourceInfo1));
    $testImg2 = new IiifImage("https://puam-loris.aws.princeton.edu", "loris", "y1972-15.jp2", json_decode($this->sourceInfo2));

    // Full default image.
    $d1 = IiifImageUrlParams::fromSettingsArray([
      'region' => "full",
      'size' => "full",
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ])->transformDimensions($testImg1);

    $this->assertEquals($d1['width'], 3209);
    $this->assertEquals($d1['height'], 5109);

    // // Full default image, rotated 90.
    // $d2 = $testImg1->transformDimensions([
    //   'region' => "full",
    //   'size' => "full",
    //   'rotation' => 90,
    //   'quality' => "default",
    //   'format' => "jpg",
    // ]);

    // $this->assertEquals($d2['width'], 5109);
    // $this->assertEquals($d2['height'], 3209);

    // // Full default image, rotated 15.
    // $d3 = $testImg2->transformDimensions([
    //   'region' => "full",
    //   'size' => "full",
    //   'rotation' => 15,
    //   'quality' => "default",
    //   'format' => "jpg",
    // ]);

    // $this->assertEquals($d3['width'], 2437);
    // $this->assertEquals($d3['height'], 2400);

    // // Full default image, rotated 105.
    // $d3 = $testImg2->transformDimensions([
    //   'region' => "full",
    //   'size' => "full",
    //   'rotation' => 105,
    //   'quality' => "default",
    //   'format' => "jpg",
    // ]);

    // $this->assertEquals($d3['width'], 2400);
    // $this->assertEquals($d3['height'], 2437);

    // // Full default image, rotated 195.
    // $d3 = $testImg2->transformDimensions([
    //   'region' => "full",
    //   'size' => "full",
    //   'rotation' => 195,
    //   'quality' => "default",
    //   'format' => "jpg",
    // ]);

    // $this->assertEquals($d3['width'], 2437);
    // $this->assertEquals($d3['height'], 2400);

    // // Full 100,.
    // $d3 = $testImg1->transformDimensions([
    //   'region' => "full",
    //   'size' => "w,",
    //   'size_w' => 100,
    //   'rotation' => 0,
    //   'quality' => "default",
    //   'format' => "jpg",
    // ]);

    // $this->assertEquals($d3['width'], 100);
    // $this->assertEquals($d3['height'], 160);

    // // Full ,200.
    // $d3 = $testImg1->transformDimensions([
    //   'region' => "full",
    //   'size' => ",h",
    //   'size_h' => 200,
    //   'rotation' => 0,
    //   'quality' => "default",
    //   'format' => "jpg",
    // ]);

    // $this->assertEquals($d3['width'], 126);
    // $this->assertEquals($d3['height'], 200);

    // // Full pct:2.5.
    // $d3 = $testImg1->transformDimensions([
    //   'region' => "full",
    //   'size' => "pct:n",
    //   'size_n' => 2.5,
    //   'rotation' => 0,
    //   'quality' => "default",
    //   'format' => "jpg",
    // ]);

    // $this->assertEquals($d3['width'], 80);
    // $this->assertEquals($d3['height'], 128);

    // // Full 100,400.
    // $d3 = $testImg1->transformDimensions([
    //   'region' => "full",
    //   'size' => "w,h",
    //   'size_w' => 100,
    //   'size_h' => 400,
    //   'rotation' => 0,
    //   'quality' => "default",
    //   'format' => "jpg",
    // ]);

    // $this->assertEquals($d3['width'], 100);
    // $this->assertEquals($d3['height'], 400);
  }

  /**
   * Testing IIIF Image URL Params.
   */
  public function testIiifImageUrlParams() {

    // Test Version Inputs.
    $params = new IiifImageUrlParams(2.0);
    $version = $params->getVersion();
    $this->assertEquals(2.0, $version);

    $params = new IiifImageUrlParams(2);
    $version = $params->getVersion();
    $this->assertEquals(2.0, $version);

    $params = new IiifImageUrlParams("2");
    $version = $params->getVersion();
    $this->assertEquals(2.0, $version);

    $params = new IiifImageUrlParams("2.0");
    $version = $params->getVersion();
    $this->assertEquals(2.0, $version);

    $params = new IiifImageUrlParams(52);
    $version = $params->getVersion();
    $this->assertEquals(2.1, $version);
  }

  /**
   * Once test method has finished running, whether it succeeded or failed, tearDown() will be invoked.
   */
  public function tearDown(): void {

  }

}
