<?php

namespace Drupal\Tests\iiif_media_source\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

/**
 * @coversDefaultClass \Drupal\iiif_media_source\Iiif\IiifImageUrlParams
 *
 * @group iiif_media_source
 */
class IiifImageUrlParamsTest extends UnitTestCase {

  /**
   * Tests the constructor and setVersion method.
   *
   * @covers ::__construct
   * @covers ::setVersion
   */
  public function testConstructorAndSetVersion() {
    // Test valid version.
    $params = new IiifImageUrlParams("2");
    $this->assertEquals("2", $params->getVersion());

    $params = new IiifImageUrlParams("2.0");
    $this->assertEquals("2", $params->getVersion());

    $params = new IiifImageUrlParams("2.1");
    $this->assertEquals("2.1", $params->getVersion());

    $params = new IiifImageUrlParams("2.1.1");
    $this->assertEquals("2.1", $params->getVersion());

    $params = new IiifImageUrlParams("3");
    $this->assertEquals("3", $params->getVersion());

    $params = new IiifImageUrlParams("3.0");
    $this->assertEquals("3", $params->getVersion());

    $params = new IiifImageUrlParams("3.0.0");
    $this->assertEquals("3", $params->getVersion());

    // Test invalid version.
    $this->expectWarning();
    $this->expectWarningMessage("Invalid version provided: 4");
    $params = new IiifImageUrlParams("4");
    $this->assertEquals("2.1", $params->getVersion()); // Default version should remain unchanged.
  }

  /**
   * Tests the buildUrlString method.
   *
   * @covers ::buildUrlString
   */
  public function testBuildUrlString() {
    // Version 2.
    $params = new IiifImageUrlParams("2");
    $params->__set('region', 'full');
    $params->__set('size', 'max');
    $params->__set('rotation', '0');
    $params->__set('quality', 'default');
    $params->__set('format', 'jpg');

    $url = $params->buildUrlString();
    $this->assertEquals('full/max/0/default.jpg', $url);

    // Version 2.1.
    $params = new IiifImageUrlParams("2.1");
    $params->__set('region', 'full');
    $params->__set('size', 'max');
    $params->__set('rotation', '0');
    $params->__set('quality', 'default');
    $params->__set('format', 'jpg');

    $url = $params->buildUrlString();
    $this->assertEquals('full/max/0/default.jpg', $url);

    // Version 3.
    $params = new IiifImageUrlParams("3");
    $params->__set('region', 'full');
    $params->__set('size', 'max');
    $params->__set('rotation', '0');
    $params->__set('quality', 'default');
    $params->__set('format', 'png');

    $url = $params->buildUrlString();
    $this->assertEquals('full/max/0/default.png', $url);
  }

  /**
   * Tests the getRegion method.
   *
   * @covers ::getRegion
   */
  public function testGetRegion() {
    $params = new IiifImageUrlParams("2.1");
    $params->__set('region', 'x,y,w,h');
    $params->__set('region_x', '10');
    $params->__set('region_y', '20');
    $params->__set('region_w', '100');
    $params->__set('region_h', '200');

    $region = $params->getRegion();
    $this->assertEquals('10,20,100,200', $region);
  }

  /**
   * Tests the getSize method.
   *
   * @covers ::getSize
   */
  public function testGetSize() {
    $params = new IiifImageUrlParams("2.1");
    $params->__set('size', 'w,h');
    $params->__set('size_w', '100');
    $params->__set('size_h', '200');

    $size = $params->getSize();
    $this->assertEquals('100,200', $size);
  }

  /**
   * Tests the getSetting method.
   *
   * @covers ::getSetting
   */
  public function testGetSetting() {
    $params = new IiifImageUrlParams("2.1");
    $params->__set('quality', 'default');

    $quality = $params->getSetting('quality');
    $this->assertEquals('default', $quality);

    $nonExistent = $params->getSetting('non_existent');
    $this->assertNull($nonExistent);
  }

}
