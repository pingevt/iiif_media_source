<?php

namespace Drupal\Tests\iiif_image_focalpoint\Kernel;

use Drupal\crop\Entity\Crop;
use Drupal\iiif_image_focalpoint\IiifFocalPointManager;
use Drupal\iiif_media_source\Plugin\Field\FieldType\IiifId;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\Entity\EntityInterface;

/**
 * @coversDefaultClass \Drupal\iiif_image_focalpoint\IiifFocalPointManager
 *
 * Kernel tests for the IiifFocalPointManager service.
 *
 * @group iiif_image_focalpoint
 */
class IiifFocalPointManagerTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'text',
    'node',
    'crop',
    'iiif_image_focalpoint',
    'iiif_media_source',
  ];

  /**
   * The focal point manager service.
   *
   * @var \Drupal\iiif_image_focalpoint\IiifFocalPointManager
   */
  protected IiifFocalPointManager $focalPointManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Install the crop entity schema.
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('crop');

    // Install the node module configuration.
    $this->installConfig(['node']);
    $this->installConfig(['user']);

    // Get the focal point manager service.
    $this->focalPointManager = $this->container->get('iiif_image_focalpoint.focal_point_manager');

    // Create a custom content type.
    $this->container->get('entity_type.manager')
      ->getStorage('node_type')
      ->create([
        'type' => 'custom_type',
        'name' => 'Custom Type',
      ])
      ->save();
  }

  /**
   * Tests getCropIiifEntity() when a crop entity already exists.
   *
   * @covers ::getCropIiifEntity
   */
  public function testGetCropIiifEntityExistingCrop(): void {
    // Create a real node entity.
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'custom_type',
        'title' => 'Test Node',
      ]);
    $node->save();

    // Create a mock IiifId item.
    $item = $this->createMock(IiifId::class);
    $item->method('getIiifImageObj')->willReturn($this->createMockImage('http://example.com/image.jpg'));
    $item->method('getEntity')->willReturn($node);

    // Create an existing crop entity.
    $crop = Crop::create([
      'type' => 'example_crop_type',
      'entity_id' => $node->id(),
      'entity_type' => 'node',
      'uri' => 'http://example.com/image.jpg',
    ]);
    $crop->save();

    // Call the method.
    $result = $this->focalPointManager->getCropIiifEntity($item, 'example_crop_type', $node->id());

    // Assert the result.
    $this->assertNotNull($result);
    $this->assertEquals($crop->id(), $result->id());
  }

  /**
   * Tests getCropIiifEntity() when a new crop entity is created.
   *
   * @covers ::getCropIiifEntity
   */
  public function testGetCropIiifEntityNewCrop(): void {
    // Create a mock IiifId item.
    $item = $this->createMock(IiifId::class);
    $item->method('getIiifImageObj')->willReturn($this->createMockImage('http://example.com/image.jpg'));
    $item->method('getEntity')->willReturn($this->createMockEntity('node'));

    // Call the method.
    $result = $this->focalPointManager->getCropIiifEntity($item, 'example_crop_type', '123');

    // Assert the result.
    $this->assertNotNull($result);
    $this->assertEquals('example_crop_type', $result->bundle());
    $this->assertEquals('http://example.com/image.jpg', $result->get('uri')->value);
  }

  /**
   * Tests findCrop() when a crop entity exists.
   *
   * @covers ::findCrop
   */
  public function testFindCropExisting(): void {

    // Create a real node entity.
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'custom_type',
        'title' => 'Test Node',
      ]);
    $node->save();

    // Create an existing crop entity.
    $crop = Crop::create([
      'type' => 'example_crop_type',
      'entity_id' => $node->id(),
      'entity_type' => $node->getEntityTypeId(),
      'uri' => 'http://example.com/image.jpg',
    ]);
    $crop->save();

    $this->assertEquals('http://example.com/image.jpg', $crop->get('uri')->value);
    $this->assertEquals('example_crop_type', $crop->bundle());
    $this->assertEquals($node->id(), $crop->get('entity_id')->value);
    $this->assertEquals('node', $crop->get('entity_type')->value);

    $reflection = new \ReflectionMethod($this->focalPointManager, 'findCrop');
    $reflection->setAccessible(true);

    // Create a mock IiifId item.
    $item = $this->createMock(IiifId::class);
    $item->method('getIiifImageObj')->willReturn($this->createMockImage('http://example.com/image.jpg'));
    $item->method('getEntity')->willReturn($node);

    // Call the method.
    $result = $reflection->invoke(
      $this->focalPointManager,
      $item,
      'example_crop_type',
      $node->id(),
      'http://example.com/image.jpg'
    );

    // Assert the result.
    $this->assertNotNull($result);
    $this->assertEquals($crop->id(), $result->id());
  }

  /**
   * Tests findCrop() when no crop entity exists.
   *
   * @covers ::findCrop
   */
  public function testFindCropNone(): void {

    // Create a real node entity.
    $node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'custom_type',
        'title' => 'Test Node',
      ]);
    $node->save();

    // Create a mock IiifId item.
    $item = $this->createMock(IiifId::class);
    $item->method('getIiifImageObj')->willReturn($this->createMockImage('http://example.com/image.jpg'));
    $item->method('getEntity')->willReturn($node);

    $reflection = new \ReflectionMethod($this->focalPointManager, 'findCrop');
    $reflection->setAccessible(true);

    // Call the method.
    $result = $reflection->invoke(
      $this->focalPointManager,
      $item,
      'example_crop_type',
      $node->id(),
      'http://example.com/image.jpg'
    );

    // Assert the result.
    $this->assertNull($result);
  }

  private function createMockImage(string $url) {
    $image = $this->getMockBuilder(IiifImage::class)
      // ->addMethods(['getFullUrl'])
      ->disableOriginalConstructor()
      ->getMock();
    $image->method('getFullUrl')->willReturn($url);
    return $image;
  }

  private function createMockEntity(string $bundle) {
    $entity = $this->getMockBuilder(EntityInterface::class)
      // ->addMethods(['bundle'])
      ->getMock();
    $entity->method('bundle')->willReturn($bundle);
    return $entity;
  }

  private function createMockIiifId(string $bundle) {
    $item = $this->getMockBuilder(IiifId::class)
      // ->addMethods(['getEntity'])
      ->disableOriginalConstructor()
      ->getMock();
    $item->method('getEntity')->willReturn($this->createMockEntity($bundle));
    return $item;
  }

}
