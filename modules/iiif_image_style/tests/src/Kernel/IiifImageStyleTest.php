<?php

declare(strict_types=1);

namespace Drupal\Tests\iiif_image_style\Kernel;

use Drupal\iiif_image_style\Entity\IiifImageStyle;
use Drupal\KernelTests\KernelTestBase;

/**
 * @coversDefaultClass \Drupal\iiif_image_style\Entity\IiifImageStyle
 *
 * Kernel tests for the IiifImageStyle class.
 *
 * @group iiif_image_style
 */
class IiifImageStyleTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'iiif_image_style',
  ];

  /**
   * Tests the id() method.
   *
   * @covers ::id
   */
  public function testId(): void {
    $iiifImageStyle = IiifImageStyle::create([
      'name' => 'test_style',
      'label' => 'Test Style',
    ]);

    // Assert that the id() method returns the correct value.
    $this->assertEquals('test_style', $iiifImageStyle->id());
  }

  /**
   * Tests the addImageEffect() method.
   *
   * @covers ::addImageEffect
   */
  public function testAddImageEffect(): void {
    $iiifImageStyle = IiifImageStyle::create([
      'name' => 'test_style',
      'label' => 'Test Style',
    ]);

    // Add an image effect using a valid plugin ID.
    $uuid = $iiifImageStyle->addImageEffect(['id' => 'iiif_image_resize']);
    $this->assertNotEmpty($uuid);
    $this->assertIsString($uuid);

    // Verify the effect was added.
    $effects = $iiifImageStyle->getEffects();
    $effect = $effects->get($uuid);

    $this->assertNotEmpty($effect);
    $configuration = $effect->getConfiguration();
    $this->assertEquals($uuid, $configuration['uuid']);
    $this->assertEquals('iiif_image_resize', $configuration['id']);
  }

  /**
   * Tests the deleteImageEffect() method.
   *
   * @covers ::deleteImageEffect
   */
  public function testDeleteImageEffect(): void {
    $iiifImageStyle = IiifImageStyle::create([
      'name' => 'test_style',
      'label' => 'Test Style',
    ]);

    // Mock an IiifImageEffectInterface instance.
    $effect = $this->createMock('Drupal\iiif_image_style\IiifImageEffectInterface');
    $effect->method('getUuid')->willReturn('test_uuid');

    // Use reflection to set the `effects` property.
    $reflection = new \ReflectionClass($iiifImageStyle);
    $property = $reflection->getProperty('effects');
    $property->setAccessible(true);
    $property->setValue($iiifImageStyle, [
      'test_uuid' => ['id' => 'test_effect'],
    ]);

    // Delete the image effect.
    $iiifImageStyle->deleteImageEffect($effect);

    // Verify the effect was removed.
    $effects = $property->getValue($iiifImageStyle);
    $this->assertArrayNotHasKey('test_uuid', $effects);
  }

  /**
   * Tests the buildUrl() method.
   *
   * @covers ::buildUrl
   */
  public function testBuildUrl(): void {
    $iiifImageStyle = IiifImageStyle::create([
      'name' => 'test_style',
      'label' => 'Test Style',
    ]);

    // Mock the IiifImage object.
    $image = $this->createMock('Drupal\iiif_media_source\Iiif\IiifImage');
    $image->method('getApiVersion')->willReturn('2.0');
    $image->method('getBuiltImageUrl')->willReturn('http://example.com/iiif/image');

    // Build the URL.
    $url = $iiifImageStyle->buildUrl($image);
    $this->assertEquals('http://example.com/iiif/image', $url);
  }

  /**
   * Tests the calculateDependencies() method.
   *
   * todo: Re-write when dependencies are fixed/updated.
   *
   * @covers ::calculateDependencies
   */
  // public function testCalculateDependencies(): void {
  //   $iiifImageStyle = IiifImageStyle::create([
  //     'name' => 'test_style',
  //     'label' => 'Test Style',
  //     'effects' => [
  //       'test_uuid' => ['id' => 'iiif_image_resize'], // Use a valid plugin ID.
  //     ],
  //   ]);

  //   // Calculate dependencies.
  //   $dependencies = $iiifImageStyle->calculateDependencies();
  //   $this->assertArrayHasKey('module', $dependencies);
  // }

}
