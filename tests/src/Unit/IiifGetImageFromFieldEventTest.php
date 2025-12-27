<?php

namespace Drupal\Tests\iiif_media_source\Unit\Event;

use Drupal\iiif_media_source\Event\IiifGetImageFromFieldEvent;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Plugin\Field\FieldType\IiifId;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Drupal\iiif_media_source\Event\IiifGetImageFromFieldEvent
 *
 * @group iiif_media_source
 */
class IiifGetImageFromFieldEventTest extends TestCase {

  /**
   * Tests the constructor and getter methods.
   *
   * @covers ::__construct
   * @covers ::getFieldItem
   * @covers ::getIiifImage
   * @covers ::getValues
   */
  public function testConstructorAndGetters() {
    // Mock the IiifId and IiifImage dependencies.
    $mockField = $this->createMock(IiifId::class);
    $mockImage = $this->createMock(IiifImage::class);
    $values = ['key' => 'value'];

    // Create the event object.
    $event = new IiifGetImageFromFieldEvent($mockField, $mockImage, $values);

    // Assert that the getters return the correct values.
    $this->assertSame($mockField, $event->getFieldItem());
    $this->assertSame($mockImage, $event->getIiifImage());
    $this->assertSame($values, $event->getValues());
  }

  /**
   * Tests the EVENT_NAME constant.
   */
  public function testEventNameConstant() {
    $this->assertEquals('iiif_image_from_field', IiifGetImageFromFieldEvent::EVENT_NAME);
  }

}
