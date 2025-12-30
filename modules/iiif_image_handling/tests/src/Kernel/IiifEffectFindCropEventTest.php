<?php

declare(strict_types=1);

namespace Drupal\Tests\iiif_image_handling\Kernel;

use Drupal\iiif_image_handling\Event\IiifEffectFindCropEvent;
use Drupal\KernelTests\KernelTestBase;

/**
 * @coversDefaultClass \Drupal\iiif_image_handling\Event\IiifEffectFindCropEvent
 *
 * Kernel tests for the IiifEffectFindCropEvent class.
 *
 * @group iiif_image_handling
 */
class IiifEffectFindCropEventTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'iiif_image_handling',
  ];

  /**
   * Tests the constructor and getters.
   *
   * @covers ::__construct
   * @covers ::getCrop
   * @covers ::getImage
   * @covers ::getCropType
   * @covers ::getContext
   */
  public function testConstructorAndGetters(): void {
    $crop = 'example_crop';
    $image = 'example_image';
    $cropType = 'example_crop_type';
    $context = ['key' => 'value'];

    // Create the event.
    $event = new IiifEffectFindCropEvent($crop, $image, $cropType, $context);

    // Assert that the properties are correctly set.
    $this->assertSame($crop, $event->getCrop());
    $this->assertSame($image, $event->getImage());
    $this->assertSame($cropType, $event->getCropType());
    $this->assertSame($context, $event->getContext());
  }

  /**
   * Tests the event mutability.
   *
   * @covers ::getCrop
   * @covers ::getImage
   * @covers ::getCropType
   * @covers ::getContext
   */
  public function testEventMutability(): void {
    $crop = 'initial_crop';
    $image = 'initial_image';
    $cropType = 'initial_crop_type';
    $context = ['initial_key' => 'initial_value'];

    // Create the event.
    $event = new IiifEffectFindCropEvent($crop, $image, $cropType, $context);

    // Modify the properties directly (if needed in the future).
    $newCrop = 'new_crop';
    $newImage = 'new_image';
    $newCropType = 'new_crop_type';
    $newContext = ['new_key' => 'new_value'];

    // Directly set new values (if setters are added in the future).
    $eventReflection = new \ReflectionClass($event);
    $cropProperty = $eventReflection->getProperty('crop');
    $cropProperty->setAccessible(true);
    $cropProperty->setValue($event, $newCrop);

    $imageProperty = $eventReflection->getProperty('image');
    $imageProperty->setAccessible(true);
    $imageProperty->setValue($event, $newImage);

    $cropTypeProperty = $eventReflection->getProperty('cropType');
    $cropTypeProperty->setAccessible(true);
    $cropTypeProperty->setValue($event, $newCropType);

    $contextProperty = $eventReflection->getProperty('context');
    $contextProperty->setAccessible(true);
    $contextProperty->setValue($event, $newContext);

    // Assert the new values.
    $this->assertSame($newCrop, $event->getCrop());
    $this->assertSame($newImage, $event->getImage());
    $this->assertSame($newCropType, $event->getCropType());
    $this->assertSame($newContext, $event->getContext());
  }

}
