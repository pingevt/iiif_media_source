<?php

namespace Drupal\Tests\iiif_media_source\Unit;

use Drupal\iiif_media_source\Iiif\IiifImage;
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
    // Default version should remain unchanged.
    $this->assertEquals("2.1", $params->getVersion());
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
   * Tests buildUrlString with data provider.
   *
   * @dataProvider buildUrlStringProvider
   */
  public function testBuildUrlStringDataProvider($version, $expected) {
    $params = new IiifImageUrlParams($version);
    $params->__set('region', 'full');
    $params->__set('size', 'max');
    $params->__set('rotation', '0');
    $params->__set('quality', 'default');
    $params->__set('format', 'jpg');
    $url = $params->buildUrlString();
    $this->assertEquals($expected, $url);
  }

  /**
   * Data provider for testBuildUrlStringDataProvider.
   */
  public function buildUrlStringProvider() {
    return [
      ['2', 'full/max/0/default.jpg'],
      ['2.1', 'full/max/0/default.jpg'],
      ['3', 'full/max/0/default.jpg'],
    ];
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
    $this->assertIsString($region);
  }

  /**
   * Tests the getRegion method with missing parameters.
   *
   * @covers ::getRegion
   */
  public function testGetRegionWithMissingParams() {
    $params = new IiifImageUrlParams("2.1");
    $params->__set('region', 'x,y,w,h');
    // Only set some params.
    $params->__set('region_x', '10');
    $params->__set('region_y', '20');
    // region_w and region_h missing.
    $region = $params->getRegion();
    $this->assertStringContainsString('10,20', $region);
  }

  /**
   * Tests getRegion with missing all region parameters.
   */
  public function testGetRegionAllMissing() {
    $params = new IiifImageUrlParams("2.1");
    $params->__set('region', 'x,y,w,h');
    // No region_x, region_y, region_w, region_h set.
    $region = $params->getRegion();
    $this->assertEquals(',,,', $region);
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
    $this->assertIsString($size);
  }

  /**
   * Tests getSize with missing size parameters.
   */
  public function testGetSizeMissingParams() {
    $params = new IiifImageUrlParams("2.1");
    $params->__set('size', 'w,h');
    // No size_w or size_h set.
    $size = $params->getSize();
    // Or your default/fallback.
    $this->assertEquals(',', $size);
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

  /**
   * Tests getSetting with a completely missing key.
   */
  public function testGetSettingMissingKey() {
    $params = new IiifImageUrlParams("2.1");
    $this->assertNull($params->getSetting('not_a_real_key'));
  }

  /**
   * @covers ::applyMaxConstraints
   */
  public function testApplyMaxConstraints() {
    $stub = $this->getMockBuilder(IiifImage::class)
      ->disableOriginalConstructor()
      ->getMock();

    // No constraints.
    $stub->method('getMaxWidth')->willReturn(NULL);
    $stub->method('getMaxHeight')->willReturn(NULL);
    $stub->method('getMaxArea')->willReturn(NULL);

    $params = new IiifImageUrlParams("3");
    $result = $this->invokeMethod($params, 'applyMaxConstraints', [1000, 800, $stub]);
    $this->assertEquals([1000, 800], $result);

    // maxWidth only.
    $stub = $this->getMockBuilder(IiifImage::class)
      ->disableOriginalConstructor()
      ->getMock();
    $stub->method('getMaxWidth')->willReturn(500);
    $stub->method('getMaxHeight')->willReturn(NULL);
    $stub->method('getMaxArea')->willReturn(NULL);
    $result = $this->invokeMethod($params, 'applyMaxConstraints', [1000, 800, $stub]);
    $this->assertEquals([500, 400], $result);

    // maxHeight only.
    $stub = $this->getMockBuilder(IiifImage::class)
      ->disableOriginalConstructor()
      ->getMock();
    $stub->method('getMaxWidth')->willReturn(NULL);
    $stub->method('getMaxHeight')->willReturn(400);
    $stub->method('getMaxArea')->willReturn(NULL);
    $result = $this->invokeMethod($params, 'applyMaxConstraints', [1000, 800, $stub]);
    $this->assertEquals([500, 400], $result);

    // maxArea only.
    $stub = $this->getMockBuilder(IiifImage::class)
      ->disableOriginalConstructor()
      ->getMock();
    $stub->method('getMaxHeight')->willReturn(NULL);
    $stub->method('getMaxArea')->willReturn(200000);
    $result = $this->invokeMethod($params, 'applyMaxConstraints', [1000, 800, $stub]);
    // sqrt(200000 / 800000) = 0.5, so 500x400.
    $this->assertEquals([500, 400], $result);

    // All constraints (should clamp to most restrictive)
    $stub = $this->getMockBuilder(IiifImage::class)
      ->disableOriginalConstructor()
      ->getMock();
    $stub->method('getMaxWidth')->willReturn(400);
    $stub->method('getMaxHeight')->willReturn(300);
    $stub->method('getMaxArea')->willReturn(100000);
    $result = $this->invokeMethod($params, 'applyMaxConstraints', [1000, 800, $stub]);
    // 400x300 = 120000, so area will clamp further
    $this->assertEquals([354, 283], $result);
  }

  /**
   * @covers ::validateSettings
   */
  public function testValidateSettings() {
    $params = new IiifImageUrlParams("3");
    $settings = [
      'size' => '^w,',
      'size_w' => 100,
      'size_h' => 200,
      'size_n' => 50,
      'region_x' => 0,
      'region_y' => 0,
      'region_w' => 100,
      'region_h' => 100,
      'rotation' => 0,
    ];
    // Should not throw.
    $this->invokeMethod($params, 'validateSettings', [&$settings]);

    // Negative value.
    $settings['size_w'] = -1;
    $this->expectException(\InvalidArgumentException::class);
    $this->invokeMethod($params, 'validateSettings', [&$settings]);
  }

  /**
   * Tests validateSettings with missing required parameter.
   */
  public function testValidateSettingsMissingRequired() {
    $params = new IiifImageUrlParams("3");
    $settings = [
      'size' => '^w,',
      // 'size_w' is missing
    ];
    $this->expectException(\InvalidArgumentException::class);
    $this->invokeMethod($params, 'validateSettings', [&$settings]);
  }

  /**
   * Tests validateSettings with invalid (non-numeric) parameter.
   */
  public function testValidateSettingsNonNumeric() {
    $params = new IiifImageUrlParams("3");
    $settings = [
      'size' => '^w,',
      'size_w' => 'notanumber',
    ];
    $this->expectException(\InvalidArgumentException::class);
    $this->invokeMethod($params, 'validateSettings', [&$settings]);
  }

  /**
   * @covers ::applyRotationToDimensions
   */
  public function testApplyRotationToDimensions() {
    $params = new IiifImageUrlParams("3");

    // 0 degrees
    $result = $this->invokeMethod($params, 'applyRotationToDimensions', [100, 50, 0]);
    $this->assertEquals([100, 50], $result);

    // 90 degrees
    $result = $this->invokeMethod($params, 'applyRotationToDimensions', [100, 50, 90]);
    $this->assertEquals([50, 100], $result);

    // 180 degrees
    $result = $this->invokeMethod($params, 'applyRotationToDimensions', [100, 50, 180]);
    $this->assertEquals([100, 50], $result);

    // 270 degrees
    $result = $this->invokeMethod($params, 'applyRotationToDimensions', [100, 50, 270]);
    $this->assertEquals([50, 100], $result);

    // 45 degrees (bounding box)
    $result = $this->invokeMethod($params, 'applyRotationToDimensions', [100, 100, 45]);
    $expected = (int) ceil(abs(100 * cos(deg2rad(45))) + abs(100 * sin(deg2rad(45))));
    $this->assertEquals([$expected, $expected], $result);
  }

  /**
   * Helper to invoke protected/private methods for testing.
   */
  protected function invokeMethod(&$object, $methodName, array $parameters = []) {
    $reflection = new \ReflectionClass(get_class($object));
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(TRUE);
    return $method->invokeArgs($object, $parameters);
  }

}
