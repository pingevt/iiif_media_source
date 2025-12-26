<?php

namespace Drupal\Tests\iiif_media_source\Kernel;

use Drupal\iiif_media_source\Iiif\IiifBase;
use Drupal\KernelTests\KernelTestBase;

/**
 * @group iiif_media_source
 */
class IiifBaseKernelTest extends KernelTestBase {
  /**
   * Required modules for this kernel test.
   *
   * @var array
   */
  protected static $modules = [
    'system',
    'media',
    'iiif_media_source',
  ];

  /**
   * Helper to create a mock for the abstract IiifBase class.
   *
   * @param mixed $info
   *   The info object to inject.
   *
   * @return \Drupal\iiif_media_source\Iiif\IiifBase
   *   The mocked IiifBase object.
   */
  public function getMockIiifBase($info = new \stdClass()) {
    $mock = $this->getMockForAbstractClass(
      IiifBase::class,
      ['http://example.org/iiif', 'prefix', 'id', $info]
    );
    return $mock;
  }

  /**
   * Tests that the constructor sets all properties correctly.
   */
  public function testConstructorSetsProperties() {
    $info = new \stdClass();
    $info->foo = 'bar';

    $mock = $this->getMockIiifBase($info);
    $reflection = new \ReflectionClass($mock);
    $serverProp = $reflection->getProperty('server');
    $serverProp->setAccessible(TRUE);
    $prefixProp = $reflection->getProperty('prefix');
    $prefixProp->setAccessible(TRUE);
    $iiifIdProp = $reflection->getProperty('iiifId');
    $iiifIdProp->setAccessible(TRUE);
    $infoProp = $reflection->getProperty('info');
    $infoProp->setAccessible(TRUE);

    $this->assertEquals('http://example.org/iiif', $serverProp->getValue($mock));
    $this->assertEquals('prefix', $prefixProp->getValue($mock));
    $this->assertEquals('id', $iiifIdProp->getValue($mock));
    $expected = new \stdClass();
    $expected->foo = 'bar';
    $this->assertEquals($expected, $infoProp->getValue($mock));
  }

  /**
   * Tests getServer() returns the correct server value.
   */
  public function testGetServerReturnsServer() {
    $mock = $this->getMockIiifBase();
    $this->assertEquals('http://example.org/iiif', $mock->getServer());
  }

  /**
   * Tests getPrefix() returns the correct prefix value.
   */
  public function testGetPrefixReturnsPrefix() {
    $mock = $this->getMockIiifBase();
    $this->assertEquals('prefix', $mock->getPrefix());
  }

  /**
   * Tests getIiifId() returns the correct IIIF ID.
   */
  public function testGetIiifIdReturnsIiifId() {
    $mock = $this->getMockIiifBase();
    $this->assertEquals('id', $mock->getIiifId());
  }

  /**
   * Tests getInfo() returns the correct info object.
   */
  public function testGetInfoReturnsInfo() {
    $expected = new \stdClass();
    $expected->foo = 'bar';
    $mock = $this->getMockIiifBase($expected);
    $this->assertEquals($expected, $mock->getInfo());
  }

  /**
   * Tests getInfoEncoded() returns the correct JSON string.
   */
  public function testGetInfoEncodedReturnsJson() {
    $expected = new \stdClass();
    $expected->foo = 'bar';
    $mock = $this->getMockIiifBase($expected);
    $this->assertEquals('{"foo":"bar"}', $mock->getInfoEncoded());
  }

  /**
   * Tests getInfoEncoded() returns '{}' for empty info.
   */
  public function testGetInfoEncodedWithEmptyInfo() {
    $mock = $this->getMockIiifBase();
    // Set info to empty stdClass.
    $reflection = new \ReflectionClass($mock);
    $infoProp = $reflection->getProperty('info');
    $infoProp->setAccessible(TRUE);
    $infoProp->setValue($mock, new \stdClass());
    $this->assertEquals('{}', $mock->getInfoEncoded());
  }

  /**
   * Tests retrieveManifest() handles HTTP errors gracefully.
   */
  public function testRetrieveManifestHandlesHttpError() {
    $mock = $this->getMockIiifBase();
    // Inject a mock HTTP client that throws an exception.
    $reflection = new \ReflectionClass($mock);
    $httpClientProp = $reflection->getProperty('httpClient');
    $httpClientProp->setAccessible(TRUE);
    $httpClientProp->setValue($mock, new class {

      /**
       *
       */
      public function get() {
        throw new \Exception('HTTP error');
      }

      /**
       *
       */
      public function request() {
        throw new \Exception('HTTP error');
      }

    });

    // Use reflection to access the protected method.
    $method = $reflection->getMethod('retrieveManifest');
    $method->setAccessible(TRUE);
    $result = $method->invoke($mock);

    $this->assertNull($result);
  }

}
