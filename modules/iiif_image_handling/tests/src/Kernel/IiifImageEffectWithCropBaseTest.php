<?php

declare(strict_types=1);

namespace Drupal\Tests\iiif_image_handling\Kernel;

use Drupal\crop\Entity\Crop;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_image_handling\Event\IiifEffectFindCropEvent;
use Drupal\iiif_image_handling\IiifImageEffectWithCropBase;
use Drupal\KernelTests\KernelTestBase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @coversDefaultClass \Drupal\iiif_image_handling\IiifImageEffectWithCropBase
 *
 * Kernel tests for the IiifImageEffectWithCropBase class.
 *
 * @group iiif_image_handling
 */
class IiifImageEffectWithCropBaseTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'iiif_image_handling',
    'crop',
  ];

  /**
   * Sets up the test environment.
   */
  protected function setUp(): void {
    parent::setUp();

    // Install the schema for the crop entity.
    $this->installEntitySchema('crop');
  }

  /**
   * Tests the getCrop() method.
   *
   * @covers ::getCrop
   */
  public function testGetCrop(): void {
    // Mock the IiifImage object.
    $image = $this->createMock(IiifImage::class);
    $image->method('getFullUrl')->willReturn('http://example.com/image.jpg');

    // Create a real crop entity.
    $crop = Crop::create([
      'type' => 'example_crop_type',
      'uri' => 'http://example.com/image.jpg',
      'x' => 0,
      'y' => 0,
      'width' => 100,
      'height' => 100,
    ]);
    $crop->save();

    // Mock the event dispatcher.
    $event_dispatcher = $this->createMock(EventDispatcherInterface::class);
    $event_dispatcher->expects($this->once())
      ->method('dispatch')
      ->with(
        $this->isInstanceOf(IiifEffectFindCropEvent::class),
        IiifEffectFindCropEvent::EVENT_NAME
      );

    // Create an instance of the class.
    $effect = $this->getMockForAbstractClass(
      IiifImageEffectWithCropBase::class,
      [
        [],
        'plugin_id',
        [],
        $this->container->get('logger.factory')->get('image'),
        $event_dispatcher,
      ]
    );

    // Use reflection to access the protected getCrop() method.
    $reflection = new \ReflectionClass($effect);
    $method = $reflection->getMethod('getCrop');
    $method->setAccessible(true);

    // Call the getCrop() method.
    $result = $method->invoke($effect, $image, 'example_crop_type', ['key' => 'value']);

    // Assert the result.
    $this->assertNotNull($result);
    $this->assertEquals($crop->id(), $result->id());
  }

}
