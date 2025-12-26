<?php

declare(strict_types=1);

namespace Drupal\Tests\iiif_image_style\Unit;

use Drupal\iiif_image_style\Annotation\IiifImageEffect;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\iiif_image_style\Annotation\IiifImageEffect
 *
 * Unit test for the IiifImageEffect annotation.
 */
class IiifImageEffectTest extends UnitTestCase {

  /**
   * Tests the annotation properties.
   *
   * @covers ::__construct
   */
  public function testAnnotationProperties(): void {
    $values = [
      'id' => 'crop',
      'label' => 'Crop',
      'description' => 'Crops the IIIF image.',
    ];

    // Create an instance of the annotation.
    $annotation = new IiifImageEffect($values);

    $definition = $annotation->get();

    // Assert that the properties are correctly set.
    $this->assertEquals('crop', $definition['id']);
    $this->assertEquals('Crop', $definition['label']);
    $this->assertEquals('Crops the IIIF image.', $definition['description']);
  }

}
