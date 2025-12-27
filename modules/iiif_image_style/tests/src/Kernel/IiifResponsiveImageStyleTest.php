<?php

declare(strict_types=1);

namespace Drupal\Tests\iiif_image_style\Kernel;

use Drupal\iiif_image_style\Entity\IiifResponsiveImageStyle;
use Drupal\KernelTests\KernelTestBase;

/**
 * @coversDefaultClass \Drupal\iiif_image_style\Entity\IiifResponsiveImageStyle
 *
 * Kernel tests for the IiifResponsiveImageStyle class.
 *
 * @group iiif_image_style
 */
class IiifResponsiveImageStyleTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'iiif_image_style',
    'breakpoint',
  ];

  /**
   * Tests the creation of a responsive image style entity.
   *
   * @covers ::create
   */
  public function testCreateResponsiveImageStyle(): void {
    $responsiveImageStyle = IiifResponsiveImageStyle::create([
      'id' => 'test_responsive_style',
      'label' => 'Test Responsive Style',
      'breakpoint_group' => 'test_breakpoint_group',
      'fallback_image_style' => 'fallback_style',
    ]);

    $this->assertEquals('test_responsive_style', $responsiveImageStyle->id());
    $this->assertEquals('Test Responsive Style', $responsiveImageStyle->label());
    $this->assertEquals('test_breakpoint_group', $responsiveImageStyle->getBreakpointGroup());
    $this->assertEquals('fallback_style', $responsiveImageStyle->getFallbackImageStyle());
  }

  /**
   * Tests adding and retrieving image style mappings.
   *
   * @covers ::addImageStyleMapping
   * @covers ::getImageStyleMappings
   * @covers ::getImageStyleMapping
   */
  public function testImageStyleMappings(): void {
    $responsiveImageStyle = IiifResponsiveImageStyle::create([
      'id' => 'test_responsive_style',
      'label' => 'Test Responsive Style',
    ]);

    // Add an image style mapping.
    $responsiveImageStyle->addImageStyleMapping('test_breakpoint', '1x', [
      'image_mapping_type' => 'image_style',
      'image_mapping' => 'test_image_style',
    ]);

    // Verify the mapping was added.
    $mappings = $responsiveImageStyle->getImageStyleMappings();
    $this->assertCount(1, $mappings);
    $this->assertEquals('test_image_style', $mappings[0]['image_mapping']);

    // Retrieve the mapping by breakpoint and multiplier.
    $mapping = $responsiveImageStyle->getImageStyleMapping('test_breakpoint', '1x');
    $this->assertNotNull($mapping);
    $this->assertEquals('test_image_style', $mapping['image_mapping']);
  }

  /**
   * Tests removing image style mappings.
   *
   * @covers ::removeImageStyleMappings
   */
  public function testRemoveImageStyleMappings(): void {
    $responsiveImageStyle = IiifResponsiveImageStyle::create([
      'id' => 'test_responsive_style',
      'label' => 'Test Responsive Style',
    ]);

    // Add an image style mapping.
    $responsiveImageStyle->addImageStyleMapping('test_breakpoint', '1x', [
      'image_mapping_type' => 'image_style',
      'image_mapping' => 'test_image_style',
    ]);

    // Remove all mappings.
    $responsiveImageStyle->removeImageStyleMappings();

    // Verify that mappings are empty.
    $mappings = $responsiveImageStyle->getImageStyleMappings();
    $this->assertEmpty($mappings);
  }

  /**
   * Tests the calculation of dependencies.
   *
   * @covers ::calculateDependencies
   */
  // public function testCalculateDependencies(): void {
  //   $responsiveImageStyle = IiifResponsiveImageStyle::create([
  //     'id' => 'test_responsive_style',
  //     'label' => 'Test Responsive Style',
  //     'breakpoint_group' => 'test_breakpoint_group',
  //     'fallback_image_style' => 'fallback_style',
  //   ]);

  //   // Calculate dependencies.
  //   $dependencies = $responsiveImageStyle->calculateDependencies();

  //   // Verify that the breakpoint group and fallback image style are included.
  //   $this->assertArrayHasKey('module', $dependencies);
  // }

  /**
   * Tests the keyed image style mappings.
   *
   * @covers ::getKeyedImageStyleMappings
   */
  public function testKeyedImageStyleMappings(): void {
    $responsiveImageStyle = IiifResponsiveImageStyle::create([
      'id' => 'test_responsive_style',
      'label' => 'Test Responsive Style',
    ]);

    // Add multiple image style mappings.
    $responsiveImageStyle->addImageStyleMapping('breakpoint_1', '1x', [
      'image_mapping_type' => 'image_style',
      'image_mapping' => 'style_1',
    ]);
    $responsiveImageStyle->addImageStyleMapping('breakpoint_1', '2x', [
      'image_mapping_type' => 'image_style',
      'image_mapping' => 'style_2',
    ]);

    // Retrieve keyed mappings.
    $keyedMappings = $responsiveImageStyle->getKeyedImageStyleMappings();
    $this->assertArrayHasKey('breakpoint_1', $keyedMappings);
    $this->assertArrayHasKey('1x', $keyedMappings['breakpoint_1']);
    $this->assertArrayHasKey('2x', $keyedMappings['breakpoint_1']);
    $this->assertEquals('style_1', $keyedMappings['breakpoint_1']['1x']['image_mapping']);
    $this->assertEquals('style_2', $keyedMappings['breakpoint_1']['2x']['image_mapping']);
  }

}
