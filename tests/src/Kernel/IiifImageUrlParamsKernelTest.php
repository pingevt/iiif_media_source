<?php

namespace Drupal\Tests\iiif_media_source\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

/**
 * @coversDefaultClass \Drupal\iiif_media_source\Iiif\IiifImage
 *
 * @group iiif_media_source
 */
class IiifImageUrlParamsKernelTest extends KernelTestBase {

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
   * Tests the transformDimensions method for IIIF Image API v2.
   *
   * @covers ::transformDimensions
   */
  public function testTransformDimensionsV2() {
    $landscape_image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/2/context.json",
      'width' => 2000,
      'height' => 1000,
    ]);

    $region_options = [
      'full' => ['region' => 'full'],
      'x,y,w,h' => ['region' => 'x,y,w,h', 'region_x' => 10, 'region_y' => 10, 'region_w' => 500, 'region_h' => 350],
      'pct:x,y,w,h' => ['region' => 'pct:x,y,w,h', 'region_x' => 10, 'region_y' => 10, 'region_w' => 80, 'region_h' => 80],
    ];
    $size_options = [
      'full' => ['size' => 'full'],
      'max' => ['size' => 'max'],
      'w,' => ['size' => 'w,', 'size_w' => 100],
      ',h' => ['size' => ',h', 'size_h' => 100],
      'pct:n' => ['size' => 'pct:n', 'size_n' => 25],
      'w,h' => ['size' => 'w,h', 'size_w' => 100, 'size_h' => 300],
      '!w,h' => ['size' => '!w,h', 'size_w' => 100, 'size_h' => 300],
    ];

    $rotation_options = [
      '0' => ['rotation' => 0],
      '90' => ['rotation' => 90],
      '180' => ['rotation' => 180],
      '270' => ['rotation' => 270],
      // '45' => ['rotation' => 45],
      // '135' => ['rotation' => 135],
      // '225' => ['rotation' => 225],
      // '315' => ['rotation' => 315],
    ];

    // $quality_options = [
    //   'color',
    //   'gray',
    //   'bitonal',
    //   'default',
    // ];
    $results['full']['full']['0'] = [2000, 1000];
    $results['full']['max']['0'] = [2000, 1000];
    $results['full']['w,']['0'] = [100, 50];
    $results['full'][',h']['0'] = [200, 100];
    $results['full']['pct:n']['0'] = [500, 250];
    $results['full']['w,h']['0'] = [100, 300];
    $results['full']['!w,h']['0'] = [100, 50];

    $results['x,y,w,h']['full']['0'] = [500, 350];
    $results['x,y,w,h']['max']['0'] = [500, 350];
    $results['x,y,w,h']['w,']['0'] = [100, 50];
    $results['x,y,w,h'][',h']['0'] = [200, 100];
    $results['x,y,w,h']['pct:n']['0'] = [125, 88];
    $results['x,y,w,h']['w,h']['0'] = [100, 300];
    $results['x,y,w,h']['!w,h']['0'] = [100, 70];

    $results['pct:x,y,w,h']['full']['0'] = [1600, 800];
    $results['pct:x,y,w,h']['max']['0'] = [1600, 800];
    $results['pct:x,y,w,h']['w,']['0'] = [100, 50];
    $results['pct:x,y,w,h'][',h']['0'] = [200, 100];
    $results['pct:x,y,w,h']['pct:n']['0'] = [400, 200];
    $results['pct:x,y,w,h']['w,h']['0'] = [100, 300];
    $results['pct:x,y,w,h']['!w,h']['0'] = [100, 50];

    $results['full']['full']['180'] = [2000, 1000];
    $results['full']['max']['180'] = [2000, 1000];
    $results['full']['w,']['180'] = [100, 50];
    $results['full'][',h']['180'] = [200, 100];
    $results['full']['pct:n']['180'] = [500, 250];
    $results['full']['w,h']['180'] = [100, 300];
    $results['full']['!w,h']['180'] = [100, 50];

    $results['x,y,w,h']['full']['180'] = [500, 350];
    $results['x,y,w,h']['max']['180'] = [500, 350];
    $results['x,y,w,h']['w,']['180'] = [100, 50];
    $results['x,y,w,h'][',h']['180'] = [200, 100];
    $results['x,y,w,h']['pct:n']['180'] = [125, 88];
    $results['x,y,w,h']['w,h']['180'] = [100, 300];
    $results['x,y,w,h']['!w,h']['180'] = [100, 70];

    $results['pct:x,y,w,h']['full']['180'] = [1600, 800];
    $results['pct:x,y,w,h']['max']['180'] = [1600, 800];
    $results['pct:x,y,w,h']['w,']['180'] = [100, 50];
    $results['pct:x,y,w,h'][',h']['180'] = [200, 100];
    $results['pct:x,y,w,h']['pct:n']['180'] = [400, 200];
    $results['pct:x,y,w,h']['w,h']['180'] = [100, 300];
    $results['pct:x,y,w,h']['!w,h']['180'] = [100, 50];

    $results['full']['full']['90'] = [1000, 2000];
    $results['full']['max']['90'] = [1000, 2000];
    $results['full']['w,']['90'] = [50, 100];
    $results['full'][',h']['90'] = [100, 200];
    $results['full']['pct:n']['90'] = [250, 500];
    $results['full']['w,h']['90'] = [300, 100];
    $results['full']['!w,h']['90'] = [50, 100];

    $results['x,y,w,h']['full']['90'] = [350, 500];
    $results['x,y,w,h']['max']['90'] = [350, 500];
    $results['x,y,w,h']['w,']['90'] = [50, 100];
    $results['x,y,w,h'][',h']['90'] = [100, 200];
    $results['x,y,w,h']['pct:n']['90'] = [88, 125];
    $results['x,y,w,h']['w,h']['90'] = [300, 100];
    $results['x,y,w,h']['!w,h']['90'] = [70, 100];

    $results['pct:x,y,w,h']['full']['90'] = [800, 1600];
    $results['pct:x,y,w,h']['max']['90'] = [800, 1600];
    $results['pct:x,y,w,h']['w,']['90'] = [50, 100];
    $results['pct:x,y,w,h'][',h']['90'] = [100, 200];
    $results['pct:x,y,w,h']['pct:n']['90'] = [200, 400];
    $results['pct:x,y,w,h']['w,h']['90'] = [300, 100];
    $results['pct:x,y,w,h']['!w,h']['90'] = [50, 100];

    $results['full']['full']['270'] = [1000, 2000];
    $results['full']['max']['270'] = [1000, 2000];
    $results['full']['w,']['270'] = [50, 100];
    $results['full'][',h']['270'] = [100, 200];
    $results['full']['pct:n']['270'] = [250, 500];
    $results['full']['w,h']['270'] = [300, 100];
    $results['full']['!w,h']['270'] = [50, 100];

    $results['x,y,w,h']['full']['270'] = [350, 500];
    $results['x,y,w,h']['max']['270'] = [350, 500];
    $results['x,y,w,h']['w,']['270'] = [50, 100];
    $results['x,y,w,h'][',h']['270'] = [100, 200];
    $results['x,y,w,h']['pct:n']['270'] = [88, 125];
    $results['x,y,w,h']['w,h']['270'] = [300, 100];
    $results['x,y,w,h']['!w,h']['270'] = [70, 100];

    $results['pct:x,y,w,h']['full']['270'] = [800, 1600];
    $results['pct:x,y,w,h']['max']['270'] = [800, 1600];
    $results['pct:x,y,w,h']['w,']['270'] = [50, 100];
    $results['pct:x,y,w,h'][',h']['270'] = [100, 200];
    $results['pct:x,y,w,h']['pct:n']['270'] = [200, 400];
    $results['pct:x,y,w,h']['w,h']['270'] = [300, 100];
    $results['pct:x,y,w,h']['!w,h']['270'] = [50, 100];

    foreach ($region_options as $region_key => $region) {
      foreach ($size_options as $size_key => $size) {
        foreach ($rotation_options as $rotation_key => $rotation) {
          $array_merge = array_merge($region, $size, $rotation, [
            'quality' => "default",
            'format' => "jpg",
          ]);
          $params = IiifImageUrlParams::fromSettingsArray($array_merge, "2.0");

          $dimensions = $params->transformDimensions($landscape_image);
          // $results[$region][$size][$rotation] = [$dimensions['width'], $dimensions['height']];
          // echo "Region: '$region_key', Size: '$size_key', Rotation: $rotation_key => Width: {$dimensions['width']}, Height: {$dimensions['height']}\n";
          // echo "=> Width: {$dimensions['width']}, Height: {$dimensions['height']}\n";
          $this->assertEquals($results[$region_key][$size_key][$rotation_key][0], $dimensions['width'], 'Error on Width. Region: ' . $region_key . ', Size: ' . $size_key . ', Rotation: ' . $rotation_key);
          $this->assertEquals($results[$region_key][$size_key][$rotation_key][1], $dimensions['height'], 'Error on Height. Region: ' . $region_key . ', Size: ' . $size_key . ', Rotation: ' . $rotation_key);
        }
      }
    }
  }

  /**
   * Tests the transformDimensions method for IIIF Image API v3.
   */
  // public function testTransformDimensionsV3() {
  //   $landscape_image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
  //     '@context' => "http://iiif.io/api/image/3/context.json",
  //     'width' => 2000,
  //     'height' => 1000,
  //   ]);.
  // $region_options = [
  //     'full' => ['region' => 'full'],
  //     'x,y,w,h' => ['region' => 'x,y,w,h', 'region_x' => 10, 'region_y' => 10, 'region_w' => 500, 'region_h' => 350],
  //     'pct:x,y,w,h' => ['region' => 'pct:x,y,w,h', 'region_x' => 10, 'region_y' => 10, 'region_w' => 80, 'region_h' => 80],
  //   ];
  //   $size_options = [
  //     'max' => ['size' => 'max'],
  //     '^max' => ['size' => '^max'],
  //     'w,' => ['size' => 'w,', 'size_w' => 100],
  //     '^w,' => ['size' => 'w,', 'size_w' => 100],
  //     ',h' => ['size' => ',h', 'size_h' => 100],
  //     '^,h' => ['size' => ',h', 'size_h' => 100],
  //     'pct:n' => ['size' => 'pct:n', 'size_n' => 25],
  //     '^pct:n' => ['size' => 'pct:n', 'size_n' => 25],
  //     'w,h' => ['size' => 'w,h', 'size_w' => 100, 'size_h' => 300],
  //     '^w,h' => ['size' => 'w,h', 'size_w' => 100, 'size_h' => 300],
  //     '!w,h' => ['size' => '!w,h', 'size_w' => 100, 'size_h' => 300],
  //     '^!w,h' => ['size' => '!w,h', 'size_w' => 100, 'size_h' => 300],
  //   ];
  // $rotation_options = [
  //     '0' => ['rotation' => 0],
  //     '90' => ['rotation' => 90],
  //     '180' => ['rotation' => 180],
  //     '270' => ['rotation' => 270],
  //     // '45' => ['rotation' => 45],
  //     // '135' => ['rotation' => 135],
  //     // '225' => ['rotation' => 225],
  //     // '315' => ['rotation' => 315],
  //   ];
  // // $quality_options = [
  //   //   'color',
  //   //   'gray',
  //   //   'bitonal',
  //   //   'default',
  //   // ];
  // $results['full']['max']['0'] = [2000, 1000];
  //   $results['full']['^max']['0'] = [2000, 1000];
  //   $results['full']['w,']['0'] = [100, 50];
  //   $results['full']['^w,']['0'] = [100, 50];
  //   $results['full'][',h']['0'] = [200, 100];
  //   $results['full']['^,h']['0'] = [200, 100];
  //   $results['full']['pct:n']['0'] = [500, 250];
  //   $results['full']['^pct:n']['0'] = [500, 250];
  //   $results['full']['w,h']['0'] = [100, 300];
  //   $results['full']['^w,h']['0'] = [100, 300];
  //   $results['full']['!w,h']['0'] = [100, 50];
  //   $results['full']['^!w,h']['0'] = [100, 50];
  // $results['x,y,w,h']['max']['0'] = [500, 350];
  //   $results['x,y,w,h']['w,']['0'] = [100, 50];
  //   $results['x,y,w,h'][',h']['0'] = [200, 100];
  //   $results['x,y,w,h']['pct:n']['0'] = [125, 88];
  //   $results['x,y,w,h']['w,h']['0'] = [100, 300];
  //   $results['x,y,w,h']['!w,h']['0'] = [100, 70];
  // $results['pct:x,y,w,h']['max']['0'] = [1600, 800];
  //   $results['pct:x,y,w,h']['w,']['0'] = [100, 50];
  //   $results['pct:x,y,w,h'][',h']['0'] = [200, 100];
  //   $results['pct:x,y,w,h']['pct:n']['0'] = [400, 200];
  //   $results['pct:x,y,w,h']['w,h']['0'] = [100, 300];
  //   $results['pct:x,y,w,h']['!w,h']['0'] = [100, 50];
  // $results['full']['full']['180'] = [2000, 1000];
  //   $results['full']['max']['180'] = [2000, 1000];
  //   $results['full']['w,']['180'] = [100, 50];
  //   $results['full'][',h']['180'] = [200, 100];
  //   $results['full']['pct:n']['180'] = [500, 250];
  //   $results['full']['w,h']['180'] = [100, 300];
  //   $results['full']['!w,h']['180'] = [100, 50];
  // $results['x,y,w,h']['full']['180'] = [500, 350];
  //   $results['x,y,w,h']['max']['180'] = [500, 350];
  //   $results['x,y,w,h']['w,']['180'] = [100, 50];
  //   $results['x,y,w,h'][',h']['180'] = [200, 100];
  //   $results['x,y,w,h']['pct:n']['180'] = [125, 88];
  //   $results['x,y,w,h']['w,h']['180'] = [100, 300];
  //   $results['x,y,w,h']['!w,h']['180'] = [100, 70];
  // $results['pct:x,y,w,h']['full']['180'] = [1600, 800];
  //   $results['pct:x,y,w,h']['max']['180'] = [1600, 800];
  //   $results['pct:x,y,w,h']['w,']['180'] = [100, 50];
  //   $results['pct:x,y,w,h'][',h']['180'] = [200, 100];
  //   $results['pct:x,y,w,h']['pct:n']['180'] = [400, 200];
  //   $results['pct:x,y,w,h']['w,h']['180'] = [100, 300];
  //   $results['pct:x,y,w,h']['!w,h']['180'] = [100, 50];
  // $results['full']['full']['90'] = [1000, 2000];
  //   $results['full']['max']['90'] = [1000, 2000];
  //   $results['full']['w,']['90'] = [50, 100];
  //   $results['full'][',h']['90'] = [100, 200];
  //   $results['full']['pct:n']['90'] = [250, 500];
  //   $results['full']['w,h']['90'] = [300, 100];
  //   $results['full']['!w,h']['90'] = [50, 100];
  // $results['x,y,w,h']['full']['90'] = [350, 500];
  //   $results['x,y,w,h']['max']['90'] = [350, 500];
  //   $results['x,y,w,h']['w,']['90'] = [50, 100];
  //   $results['x,y,w,h'][',h']['90'] = [100, 200];
  //   $results['x,y,w,h']['pct:n']['90'] = [88, 125];
  //   $results['x,y,w,h']['w,h']['90'] = [300, 100];
  //   $results['x,y,w,h']['!w,h']['90'] = [70, 100];
  // $results['pct:x,y,w,h']['full']['90'] = [800, 1600];
  //   $results['pct:x,y,w,h']['max']['90'] = [800, 1600];
  //   $results['pct:x,y,w,h']['w,']['90'] = [50, 100];
  //   $results['pct:x,y,w,h'][',h']['90'] = [100, 200];
  //   $results['pct:x,y,w,h']['pct:n']['90'] = [200, 400];
  //   $results['pct:x,y,w,h']['w,h']['90'] = [300, 100];
  //   $results['pct:x,y,w,h']['!w,h']['90'] = [50, 100];
  // $results['full']['full']['270'] = [1000, 2000];
  //   $results['full']['max']['270'] = [1000, 2000];
  //   $results['full']['w,']['270'] = [50, 100];
  //   $results['full'][',h']['270'] = [100, 200];
  //   $results['full']['pct:n']['270'] = [250, 500];
  //   $results['full']['w,h']['270'] = [300, 100];
  //   $results['full']['!w,h']['270'] = [50, 100];
  // $results['x,y,w,h']['full']['270'] = [350, 500];
  //   $results['x,y,w,h']['max']['270'] = [350, 500];
  //   $results['x,y,w,h']['w,']['270'] = [50, 100];
  //   $results['x,y,w,h'][',h']['270'] = [100, 200];
  //   $results['x,y,w,h']['pct:n']['270'] = [88, 125];
  //   $results['x,y,w,h']['w,h']['270'] = [300, 100];
  //   $results['x,y,w,h']['!w,h']['270'] = [70, 100];
  // $results['pct:x,y,w,h']['full']['270'] = [800, 1600];
  //   $results['pct:x,y,w,h']['max']['270'] = [800, 1600];
  //   $results['pct:x,y,w,h']['w,']['270'] = [50, 100];
  //   $results['pct:x,y,w,h'][',h']['270'] = [100, 200];
  //   $results['pct:x,y,w,h']['pct:n']['270'] = [200, 400];
  //   $results['pct:x,y,w,h']['w,h']['270'] = [300, 100];
  //   $results['pct:x,y,w,h']['!w,h']['270'] = [50, 100];
  // foreach ($region_options as $region_key => $region) {
  //     foreach ($size_options as $size_key => $size) {
  //       foreach ($rotation_options as $rotation_key => $rotation) {
  //         $array_merge = array_merge($region, $size, $rotation, [
  //           'quality' => "default",
  //           'format' => "jpg",
  //         ]);
  //         $params = IiifImageUrlParams::fromSettingsArray($array_merge, "3.0");.
  // $dimensions = $params->transformDimensions($landscape_image);
  //         // $results[$region][$size][$rotation] = [$dimensions['width'], $dimensions['height']];
  //         echo "Region: '$region_key', Size: '$size_key', Rotation: $rotation_key => Width: {$dimensions['width']}, Height: {$dimensions['height']}\n";
  //         echo "=> Width: {$dimensions['width']}, Height: {$dimensions['height']}\n";
  // $this->assertEquals($results[$region_key][$size_key][$rotation_key][0], $dimensions['width'], 'Error on Width. Region: ' . $region_key . ', Size: ' . $size_key . ', Rotation: ' . $rotation_key);
  //         $this->assertEquals($results[$region_key][$size_key][$rotation_key][1], $dimensions['height'], 'Error on Height. Region: ' . $region_key . ', Size: ' . $size_key . ', Rotation: ' . $rotation_key);
  //       }
  //     }
  //   }
  // }.

  /**
   * @dataProvider transformDimensionsV3Provider
   */
  public function testTransformDimensionsV3DataProvider($imageInfo, $params, $expectedWidth, $expectedHeight, $message) {
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) $imageInfo);
    $paramsObj = IiifImageUrlParams::fromSettingsArray($params, "3.0");
    $dimensions = $paramsObj->transformDimensions($image);
    $this->assertEquals($expectedWidth, $dimensions['width'], "Width: $message");
    $this->assertEquals($expectedHeight, $dimensions['height'], "Height: $message");
  }

  /**
   *
   */
  public function transformDimensionsV3Provider() {
    // [imageInfo, params, expectedWidth, expectedHeight, message]
    return [
      // Full region, max size, no rotation.
      'full-max-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 2000, 'height' => 1000],
        ['region' => 'full', 'size' => 'max', 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        2000, 1000, 'full region, max size, 0° rotation',
      ],
      // Full region, max size, 90 rotation.
      'full-max-90' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 2000, 'height' => 1000],
        ['region' => 'full', 'size' => 'max', 'rotation' => 90, 'quality' => "default", 'format' => "jpg"],
        1000, 2000, 'full region, max size, 90° rotation',
      ],
      // Full region, w, size, no rotation.
      'full-w,-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 2000, 'height' => 1000],
        ['region' => 'full', 'size' => 'w,', 'size_w' => 100, 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        100, 50, 'full region, w, size, 0° rotation',
      ],
      // Full region, ,h size, no rotation.
      'full-,h-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 2000, 'height' => 1000],
        ['region' => 'full', 'size' => ',h', 'size_h' => 100, 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        200, 100, 'full region, ,h size, 0° rotation',
      ],
      // Full region, pct:n size, no rotation.
      'full-pct:n-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 2000, 'height' => 1000],
        ['region' => 'full', 'size' => 'pct:n', 'size_n' => 25, 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        500, 250, 'full region, pct:n size, 0° rotation',
      ],
      // Full region, w,h size, no rotation.
      'full-w,h-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 2000, 'height' => 1000],
        ['region' => 'full', 'size' => 'w,h', 'size_w' => 100, 'size_h' => 300, 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        100, 300, 'full region, w,h size, 0° rotation',
      ],
      // Full region, !w,h size, no rotation.
      'full-!w,h-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 2000, 'height' => 1000],
        ['region' => 'full', 'size' => '!w,h', 'size_w' => 100, 'size_h' => 300, 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        100, 50, 'full region, !w,h size, 0° rotation',
      ],
      // x,y,w,h region, max size, 0 rotation.
      'xywh-max-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 2000, 'height' => 1000],
        ['region' => 'x,y,w,h', 'region_x' => 10, 'region_y' => 10, 'region_w' => 500, 'region_h' => 350, 'size' => 'max', 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        500, 350, 'xywh region, max size, 0° rotation',
      ],
      // x,y,w,h region, max size, 90 rotation.
      'xywh-max-90' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 2000, 'height' => 1000],
        ['region' => 'x,y,w,h', 'region_x' => 10, 'region_y' => 10, 'region_w' => 500, 'region_h' => 350, 'size' => 'max', 'rotation' => 90, 'quality' => "default", 'format' => "jpg"],
        350, 500, 'xywh region, max size, 90° rotation',
      ],
      // pct:x,y,w,h region, max size, 0 rotation.
      'pctxywh-max-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 2000, 'height' => 1000],
        ['region' => 'pct:x,y,w,h', 'region_x' => 10, 'region_y' => 10, 'region_w' => 80, 'region_h' => 80, 'size' => 'max', 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        1600, 800, 'pct:x,y,w,h region, max size, 0° rotation',
      ],
      // pct:x,y,w,h region, max size, 90 rotation.
      'pctxywh-max-90' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 2000, 'height' => 1000],
        ['region' => 'pct:x,y,w,h', 'region_x' => 10, 'region_y' => 10, 'region_w' => 80, 'region_h' => 80, 'size' => 'max', 'rotation' => 90, 'quality' => "default", 'format' => "jpg"],
        800, 1600, 'pct:x,y,w,h region, max size, 90° rotation',
      ],
      // Full region, ^max size, 0 rotation (upscale allowed)
      'full-^max-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 1000, 'height' => 800, 'maxWidth' => 2000, 'maxHeight' => 1600],
        ['region' => 'full', 'size' => '^max', 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        2000, 1600, 'full region, ^max size, 0° rotation, upscaling',
      ],
      // Full region, ^w, size, 0 rotation (upscale allowed)
      'full-^w,-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 1000, 'height' => 800, 'maxWidth' => 2000],
        ['region' => 'full', 'size' => '^w,', 'size_w' => 2000, 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        2000, 1600, 'full region, ^w, size, 0° rotation, upscaling',
      ],
      // Full region, ^,h size, 0 rotation (upscale allowed)
      'full-^,h-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 1000, 'height' => 800, 'maxHeight' => 1600],
        ['region' => 'full', 'size' => '^,h', 'size_h' => 1600, 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        2000, 1600, 'full region, ^,h size, 0° rotation, upscaling',
      ],
      // Full region, ^pct:n size, 0 rotation (upscale allowed)
      'full-^pct:n-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 1000, 'height' => 800, 'maxWidth' => 2000, 'maxHeight' => 1600],
        ['region' => 'full', 'size' => '^pct:n', 'size_n' => 200, 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        2000, 1600, 'full region, ^pct:n size, 0° rotation, upscaling',
      ],
      // Full region, ^w,h size, 0 rotation (upscale allowed)
      'full-^w,h-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 1000, 'height' => 800, 'maxWidth' => 2000, 'maxHeight' => 1600],
        ['region' => 'full', 'size' => '^w,h', 'size_w' => 2000, 'size_h' => 1600, 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        2000, 1600, 'full region, ^w,h size, 0° rotation, upscaling',
      ],
      // Full region, ^!w,h size, 0 rotation (upscale allowed, best fit)
      'full-^!w,h-0' => [
        ['@context' => "http://iiif.io/api/image/3/context.json", 'width' => 1000, 'height' => 800, 'maxWidth' => 1500, 'maxHeight' => 1200, 'maxArea' => 1600000],
        ['region' => 'full', 'size' => '^!w,h', 'size_w' => 4000, 'size_h' => 3200, 'rotation' => 0, 'quality' => "default", 'format' => "jpg"],
        1414, 1131, 'full region, ^!w,h size, 0° rotation, all max props',
      ],
      // Add more combinations as needed...
    ];
  }

  /**
   * Tests IIIF v3 "max" and "full" sizing with and without maxWidth/maxHeight/maxArea.
   *
   * This test checks how the IIIF Image API v3 "max" and "full" size parameters are
   * affected by the presence of maxWidth, maxHeight, and maxArea properties in the
   * image info. It ensures that:
   *   - When no max properties are set, "max" and "full" both return the full image size.
   *   - When maxWidth/maxHeight/maxArea are set, "max" returns the restricted size,
   *     but "full" still returns the full image size.
   *
   * | Scenario                | Image Used         | Params      | Expected Dimensions | Tested? |
   * |-------------------------|-------------------|-------------|--------------------|---------|
   * | max, no max props       | $no_max_img       | size=max    | 2048x2048          | Yes     |
   * | max, with max props     | $landscape_image  | size=max    | 1024x1024          | Yes     |
   * | max, only maxWidth      | $max_width_img    | size=max    | 2048x2048          | No      |
   *
   * @coversNothing
   */
  public function testMaxProps() {

    $no_max_img = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 2048,
      'height' => 2048,
    ]);

    $max_width_img = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 2048,
      'height' => 2048,
      'maxWidth' => 2048,
    ]);

    $landscape_image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 2048,
      'height' => 2048,
      'maxWidth' => 1024,
      'maxHeight' => 1024,
    // 1024*1024.
      'maxArea' => 1048576,
    ]);

    // Check v3, max without any max restrictions. Should be 2048x2048.
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => 'max',
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($no_max_img);
    $this->assertEquals(2048, $dimensions['width'], "Error on Width ({$dimensions['width']}) for v3 max without maxWidth/Height.");
    $this->assertEquals(2048, $dimensions['height'], "Error on Height ({$dimensions['height']}) for v3 max without maxWidth/Height.");

    // Check v3, max. Should be 1024x1024.
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => 'max',
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($landscape_image);
    $this->assertEquals(1024, $dimensions['width'], "Error on Width ({$dimensions['width']}) for v3 max with maxWidth/Height.");
    $this->assertEquals(1024, $dimensions['height'], "Error on Height ({$dimensions['height']}) for v3 max with maxWidth/Height.");

  }

  /**
   * Tests the '^max' size option for IIIF v3 with various max constraints.
   *
   * | Scenario                | Image Properties                  | Region Size | maxWidth | maxHeight | maxArea | Expected Output        |
   * |-------------------------|-----------------------------------|-------------|----------|-----------|---------|------------------------|
   * | No max props            | width=1000, height=800            | 1000x800    | null     | null      | null    | 1000x800               |
   * | maxWidth only           | width=1000, height=800            | 1000x800    | 2000     | null      | null    | 2000x1600              |
   * | maxHeight only          | width=1000, height=800            | 1000x800    | null     | 1600      | null    | 2000x1600              |
   * | maxArea only            | width=1000, height=800            | 1000x800    | null     | null      | 6400000 | 2828x2263              |
   * | All max props           | width=1000, height=800            | 1000x800    | 1500     | 1200      | 1600000 | 1414x1131              |
   * | Region already at max   | width=2000, height=1600           | 2000x1600   | 2000     | 1600      | 3200000 | 2000x1600              |
   * | Region larger than max  | width=3000, height=2400           | 3000x2400   | 2000     | 1600      | 3200000 | 2000x1600              |
   */
  public function testTransformDimensionsV3MaxUpscale() {
    // 1. No max props
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^max',
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1000, $dimensions['width'], "No max props: width");
    $this->assertEquals(800, $dimensions['height'], "No max props: height");

    // 2. maxWidth only
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxWidth' => 2000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^max',
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width'], "maxWidth only: width");
    $this->assertEquals(1600, $dimensions['height'], "maxWidth only: height");

    // 3. maxHeight only
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxHeight' => 1600,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^max',
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width'], "maxHeight only: width");
    $this->assertEquals(1600, $dimensions['height'], "maxHeight only: height");

    // 4. maxArea only
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxArea' => 6400000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^max',
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    // sqrt(6400000 / (1000*800)) = 2.828..., so width = 2828, height = 2263.
    $this->assertEquals(2828, $dimensions['width'], "maxArea only: width");
    $this->assertEquals(2263, $dimensions['height'], "maxArea only: height");

    // 5. All max props (restrict)
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxWidth' => 1500,
      'maxHeight' => 1200,
      'maxArea' => 1600000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^max',
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    // sqrt(1600000 / (1000*800)) = 1131..., so width = 1414, height = 1131.
    $this->assertEquals(1414, $dimensions['width'], "all max props: width");
    $this->assertEquals(1131, $dimensions['height'], "all max props: height");

    // 6. Region already at max
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 2000,
      'height' => 1600,
      'maxWidth' => 2000,
      'maxHeight' => 1600,
      'maxArea' => 3200000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^max',
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width'], "already at max: width");
    $this->assertEquals(1600, $dimensions['height'], "already at max: height");

    // 7. Region larger than max
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 3000,
      'height' => 2400,
      'maxWidth' => 2000,
      'maxHeight' => 1600,
      'maxArea' => 3200000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^max',
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width'], "larger than max: width");
    $this->assertEquals(1600, $dimensions['height'], "larger than max: height");
  }

  /**
   * Tests the '^w,' size option for IIIF v3 with various max constraints.
   *
   * This test checks that upscaling or downscaling to a specific width
   * (preserving aspect ratio) respects maxWidth, maxHeight, and maxArea.
   *
   * | Scenario                | Image Properties                  | Requested Width | maxWidth | maxHeight | maxArea | Expected Output         |
   * |-------------------------|-----------------------------------|-----------------|----------|-----------|---------|------------------------|
   * | No max props            | width=1000, height=800            | 2000            | null     | null      | null    | 2000x1600              |
   * | maxWidth only           | width=1000, height=800            | 3000            | 2000     | null      | null    | 2000x1600              |
   * | maxHeight only          | width=1000, height=800            | 3000            | null     | 1200      | null    | 1500x1200              |
   * | maxArea only            | width=1000, height=800            | 4000            | null     | null      | 3200000 | 2000x1600              |
   * | All max props           | width=1000, height=800            | 4000            | 1500     | 1200      | 1600000 | 1414x1131              |
   * | Requested width < image | width=1000, height=800            | 500             | null     | null      | null    | 500x400                |
   * | Requested width = image | width=1000, height=800            | 1000            | null     | null      | null    | 1000x800               |
   */
  public function testTransformDimensionsV3WUp() {
    // 1. No max props, upscale to 2000
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,',
      'size_w' => 2000,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 2. maxWidth only, request 3000
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxWidth' => 2000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,',
      'size_w' => 3000,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 3. maxHeight only, request 3000
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxHeight' => 1200,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,',
      'size_w' => 3000,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1500, $dimensions['width']);
    $this->assertEquals(1200, $dimensions['height']);

    // 4. maxArea only, request 4000
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxArea' => 3200000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,',
      'size_w' => 4000,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 5. All max props, request 4000
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxWidth' => 1500,
      'maxHeight' => 1200,
      'maxArea' => 1600000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,',
      'size_w' => 4000,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1414, $dimensions['width']);
    $this->assertEquals(1131, $dimensions['height']);

    // 6. Requested width < image
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,',
      'size_w' => 500,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(500, $dimensions['width']);
    $this->assertEquals(400, $dimensions['height']);

    // 7. Requested width = image
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,',
      'size_w' => 1000,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1000, $dimensions['width']);
    $this->assertEquals(800, $dimensions['height']);
  }

  /**
   * Tests the '^,h' size option for IIIF v3 with various max constraints.
   *
   * This test checks that upscaling or downscaling to a specific height
   * (preserving aspect ratio) respects maxWidth, maxHeight, and maxArea.
   *
   * | Scenario                | Image Properties                  | Requested Height | maxWidth | maxHeight | maxArea | Expected Output         |
   * |-------------------------|-----------------------------------|------------------|----------|-----------|---------|------------------------|
   * | No max props            | width=1000, height=800            | 1600             | null     | null      | null    | 2000x1600              |
   * | maxHeight only          | width=1000, height=800            | 3000             | null     | 1200      | null    | 1500x1200              |
   * | maxWidth only           | width=1000, height=800            | 3000             | 2000     | null      | null    | 2000x1600              |
   * | maxArea only            | width=1000, height=800            | 4000             | null     | null      | 3200000 | 2000x1600              |
   * | All max props           | width=1000, height=800            | 4000             | 1500     | 1200      | 1600000 | 1414x1131              |
   * | Requested height < img  | width=1000, height=800            | 400              | null     | null      | null    | 500x400                |
   * | Requested height = img  | width=1000, height=800            | 800              | null     | null      | null    | 1000x800               |
   */
  public function testTransformDimensionsV3HUp() {
    // 1. No max props, upscale to 1600
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^,h',
      'size_h' => 1600,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 2. maxHeight only, request 3000
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxHeight' => 1200,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^,h',
      'size_h' => 3000,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1500, $dimensions['width']);
    $this->assertEquals(1200, $dimensions['height']);

    // 3. maxWidth only, request 3000
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxWidth' => 2000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^,h',
      'size_h' => 3000,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 4. maxArea only, request 4000
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxArea' => 3200000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^,h',
      'size_h' => 4000,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 5. All max props, request 4000
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxWidth' => 1500,
      'maxHeight' => 1200,
      'maxArea' => 1600000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^,h',
      'size_h' => 4000,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1414, $dimensions['width']);
    $this->assertEquals(1131, $dimensions['height']);

    // 6. Requested height < image
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^,h',
      'size_h' => 400,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(500, $dimensions['width']);
    $this->assertEquals(400, $dimensions['height']);

    // 7. Requested height = image
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^,h',
      'size_h' => 800,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1000, $dimensions['width']);
    $this->assertEquals(800, $dimensions['height']);
  }

  /**
   * Tests the '^pct:n' size option for IIIF v3 with various max constraints.
   *
   * This test checks that upscaling or downscaling by a percentage
   * respects maxWidth, maxHeight, and maxArea.
   *
   * | Scenario                | Image Properties                  | pct:n | maxWidth | maxHeight | maxArea | Expected Output         |
   * |-------------------------|-----------------------------------|-------|----------|-----------|---------|------------------------|
   * | No max props            | width=1000, height=800            | 200   | null     | null      | null    | 2000x1600              |
   * | maxWidth only           | width=1000, height=800            | 300   | 2000     | null      | null    | 2000x1600              |
   * | maxHeight only          | width=1000, height=800            | 300   | null     | 1200      | null    | 1500x1200              |
   * | maxArea only            | width=1000, height=800            | 400   | null     | null      | 3200000 | 2000x1600              |
   * | All max props           | width=1000, height=800            | 400   | 1500     | 1200      | 1600000 | 1414x1131              |
   * | pct:n < 100             | width=1000, height=800            | 50    | null     | null      | null    | 500x400                |
   * | pct:n = 100             | width=1000, height=800            | 100   | null     | null      | null    | 1000x800               |
   */
  public function testTransformDimensionsV3PctUp() {
    // 1. No max props, pct:n = 200
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^pct:n',
      'size_n' => 200,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 2. maxWidth only, pct:n = 300
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxWidth' => 2000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^pct:n',
      'size_n' => 300,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 3. maxHeight only, pct:n = 300
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxHeight' => 1200,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^pct:n',
      'size_n' => 300,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1500, $dimensions['width']);
    $this->assertEquals(1200, $dimensions['height']);

    // 4. maxArea only, pct:n = 400
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxArea' => 3200000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^pct:n',
      'size_n' => 400,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 5. All max props, pct:n = 400
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxWidth' => 1500,
      'maxHeight' => 1200,
      'maxArea' => 1600000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^pct:n',
      'size_n' => 400,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1414, $dimensions['width']);
    $this->assertEquals(1131, $dimensions['height']);

    // 6. pct:n < 100 (downscale)
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^pct:n',
      'size_n' => 50,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(500, $dimensions['width']);
    $this->assertEquals(400, $dimensions['height']);

    // 7. pct:n = 100 (no scale)
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^pct:n',
      'size_n' => 100,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1000, $dimensions['width']);
    $this->assertEquals(800, $dimensions['height']);
  }

  /**
   * Tests the '^w,h' size option for IIIF v3 with various max constraints.
   *
   * This test checks that upscaling or downscaling to a specific width and height
   * respects maxWidth, maxHeight, and maxArea.
   *
   * | Scenario                | Image Properties                  | Requested w,h | maxWidth | maxHeight | maxArea | Expected Output         |
   * |-------------------------|-----------------------------------|--------------|----------|-----------|---------|------------------------|
   * | No max props            | width=1000, height=800            | 2000,1600    | null     | null      | null    | 2000x1600              |
   * | maxWidth only           | width=1000, height=800            | 3000,2400    | 2000     | null      | null    | 2000x1600              |
   * | maxHeight only          | width=1000, height=800            | 3000,2400    | null     | 1200      | null    | 1500x1200              |
   * | maxArea only            | width=1000, height=800            | 4000,3200    | null     | null      | 3200000 | 2000x1600              |
   * | All max props           | width=1000, height=800            | 4000,3200    | 1500     | 1200      | 1600000 | 1414x1131              |
   * | Requested < image       | width=1000, height=800            | 500,400      | null     | null      | null    | 500x400                |
   * | Requested = image       | width=1000, height=800            | 1000,800     | null     | null      | null    | 1000x800               |
   */
  public function testTransformDimensionsV3WHUp() {
    // 1. No max props, upscale to 2000x1600
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,h',
      'size_w' => 2000,
      'size_h' => 1600,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 2. maxWidth only, request 3000x2400
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxWidth' => 2000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,h',
      'size_w' => 3000,
      'size_h' => 2400,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 3. maxHeight only, request 3000x2400
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxHeight' => 1200,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,h',
      'size_w' => 3000,
      'size_h' => 2400,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1500, $dimensions['width']);
    $this->assertEquals(1200, $dimensions['height']);

    // 4. maxArea only, request 4000x3200
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxArea' => 3200000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,h',
      'size_w' => 4000,
      'size_h' => 3200,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 5. All max props, request 4000x3200
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxWidth' => 1500,
      'maxHeight' => 1200,
      'maxArea' => 1600000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,h',
      'size_w' => 4000,
      'size_h' => 3200,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1414, $dimensions['width']);
    $this->assertEquals(1131, $dimensions['height']);

    // 6. Requested < image
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,h',
      'size_w' => 500,
      'size_h' => 400,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(500, $dimensions['width']);
    $this->assertEquals(400, $dimensions['height']);

    // 7. Requested = image
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^w,h',
      'size_w' => 1000,
      'size_h' => 800,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1000, $dimensions['width']);
    $this->assertEquals(800, $dimensions['height']);
  }

  /**
   * Tests the '^!w,h' size option for IIIF v3 with various max constraints.
   *
   * This test checks that upscaling or downscaling to best fit within a box
   * (preserving aspect ratio) respects maxWidth, maxHeight, and maxArea.
   *
   * | Scenario                | Image Properties                  | Requested w,h | maxWidth | maxHeight | maxArea | Expected Output         |
   * |-------------------------|-----------------------------------|--------------|----------|-----------|---------|------------------------|
   * | No max props            | width=1000, height=800            | 2000,1200    | null     | null      | null    | 1500x1200              |
   * | maxWidth only           | width=1000, height=800            | 3000,2400    | 2000     | null      | null    | 2000x1600              |
   * | maxHeight only          | width=1000, height=800            | 3000,2400    | null     | 1200      | null    | 1500x1200              |
   * | maxArea only            | width=1000, height=800            | 4000,3200    | null     | null      | 3200000 | 2000x1600              |
   * | All max props           | width=1000, height=800            | 4000,3200    | 1500     | 1200      | 1600000 | 1414x1131              |
   * | Requested < image       | width=1000, height=800            | 500,400      | null     | null      | null    | 500x400                |
   * | Requested = image       | width=1000, height=800            | 1000,800     | null     | null      | null    | 1000x800               |
   */
  public function testTransformDimensionsV3WHBangUp() {
    // 1. No max props, best fit in 2000x1200
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^!w,h',
      'size_w' => 2000,
      'size_h' => 1200,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1500, $dimensions['width']);
    $this->assertEquals(1200, $dimensions['height']);

    // 2. maxWidth only, best fit in 3000x2400
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxWidth' => 2000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^!w,h',
      'size_w' => 3000,
      'size_h' => 2400,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 3. maxHeight only, best fit in 3000x2400
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxHeight' => 1200,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^!w,h',
      'size_w' => 3000,
      'size_h' => 2400,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1500, $dimensions['width']);
    $this->assertEquals(1200, $dimensions['height']);

    // 4. maxArea only, best fit in 4000x3200
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxArea' => 3200000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^!w,h',
      'size_w' => 4000,
      'size_h' => 3200,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(2000, $dimensions['width']);
    $this->assertEquals(1600, $dimensions['height']);

    // 5. All max props, best fit in 4000x3200
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
      'maxWidth' => 1500,
      'maxHeight' => 1200,
      'maxArea' => 1600000,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^!w,h',
      'size_w' => 4000,
      'size_h' => 3200,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1414, $dimensions['width']);
    $this->assertEquals(1131, $dimensions['height']);

    // 6. Requested < image
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^!w,h',
      'size_w' => 500,
      'size_h' => 400,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(500, $dimensions['width']);
    $this->assertEquals(400, $dimensions['height']);

    // 7. Requested = image
    $image = new IiifImage('http://example.com', 'prefix', 'id', (object) [
      '@context' => "http://iiif.io/api/image/3/context.json",
      'width' => 1000,
      'height' => 800,
    ]);
    $params = IiifImageUrlParams::fromSettingsArray([
      'region' => 'full',
      'size' => '^!w,h',
      'size_w' => 1000,
      'size_h' => 800,
      'rotation' => 0,
      'quality' => "default",
      'format' => "jpg",
    ], "3.0");
    $dimensions = $params->transformDimensions($image);
    $this->assertEquals(1000, $dimensions['width']);
    $this->assertEquals(800, $dimensions['height']);
  }

}
