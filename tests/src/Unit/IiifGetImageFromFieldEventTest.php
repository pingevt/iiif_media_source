<?php

namespace Drupal\Tests\iiif_media_source\Unit\Event;

use Drupal\iiif_media_source\Event\IiifGetImageFromFieldEvent;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Plugin\Field\FieldType\IiifId;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Drupal\iiif_media_source\Event\IiifGetImageFromFieldEvent
 */
class IiifGetImageFromFieldEventTest extends TestCase {

  /**
   * Tests the constructor and properties.
   *
   * @covers ::__construct
   */
  public function testConstructor() {
    // Mock the IiifId and IiifImage dependencies.
    $mockField = $this->createMock(IiifId::class);
    $mockImage = $this->createMock(IiifImage::class);
    $values = ['key' => 'value'];

    // Create the event object.
    $event = new IiifGetImageFromFieldEvent($mockField, $mockImage, $values);

    // Assert that the properties are set correctly.
    $this->assertSame($mockField, $event->field);
    $this->assertSame($mockImage, $event->image);
    $this->assertSame($values, $event->values);
  }

  /**
   * Tests the EVENT_NAME constant.
   *
   * @covers ::EVENT_NAME
   */
  public function testEventNameConstant() {
    $this->assertEquals('iiif_image_from_field', IiifGetImageFromFieldEvent::EVENT_NAME);
  }

}
