<?php

declare(strict_types = 1);

namespace Drupal\Tests\iiif_media_source\Kernel;

use Drupal\Core\Field\FieldItemDataDefinition;
use Drupal\KernelTests\KernelTestBase;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\user\Entity\User;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Form\FormState;
use Drupal\iiif_media_source\Iiif\IiifImage;

/**
 * Kernel tests for the IIIF ID field type.
 *
 * @group iiif_media_source
 */


// Example Table
//
//  Field storage/retrieval	⬜
//  Info property storage/retrieval	⬜
//  Default field settings	⬜
//  Field settings form	⬜
//  Schema definition	⬜
//  Property definitions	⬜
//  setValue() logic	⬜
//  getIiifImageObj() returns correct object	⬜
//  getIiifImageObj() dispatches event	⬜
// getImg() triggers deprecation and returns correct object	⬜
// __get('width') and __get('height')	⬜
// __get() fallback	⬜
// Handles invalid/missing info JSON	⬜
// Handles missing value	⬜
// Event dispatching correctness	⬜



class IiifIdFieldTypeKernelTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'entity_test',
    'iiif_media_source',
  ];

  /**
   * Set up a test entity with the iiif_id field.
   */
  protected function setUp(): void {
    parent::setUp();

    // Install the entity_test schema.
    $this->installEntitySchema('entity_test');

    // Create field storage and field instance for entity_test.
    FieldStorageConfig::create([
      'field_name' => 'field_iiif_id',
      'entity_type' => 'entity_test',
      'type' => 'iiif_id',
    ])->save();

    FieldConfig::create([
      'field_name' => 'field_iiif_id',
      'entity_type' => 'entity_test',
      'bundle' => 'entity_test',
      'settings' => [
        'server' => 'http://example.org/iiif',
        'prefix' => 'prefix',
        'img_api_version' => '3',
      ],
    ])->save();

    // Set up form and view displays.
    EntityFormDisplay::create([
      'targetEntityType' => 'entity_test',
      'bundle' => 'entity_test',
      'mode' => 'default',
      'status' => TRUE,
    ])->setComponent('field_iiif_id', [
      'type' => 'iiif_id_widget',
    ])->save();

    EntityViewDisplay::create([
      'targetEntityType' => 'entity_test',
      'bundle' => 'entity_test',
      'mode' => 'default',
      'status' => TRUE,
    ])->setComponent('field_iiif_id', [
      'type' => 'iiif_id_formatter',
    ])->save();
  }

  /**
   * Tests field storage and retrieval for the iiif_id field.
   */
  public function testFieldStorageAndRetrieval() {
    // Create a test entity and set the iiif_id field value.
    $entity = EntityTest::create([
      'name' => 'Test entity',
      'field_iiif_id' => [
        'value' => 'test-iiif-id',
        'info' => json_encode(['width' => 1000, 'height' => 800]),
      ],
    ]);
    $entity->save();

    // Reload the entity from storage.
    $loaded = EntityTest::load($entity->id());

    // Assert the iiif_id field value is stored and retrieved correctly.
    $this->assertEquals('test-iiif-id', $loaded->field_iiif_id->value);
    $this->assertEquals('{"width":1000,"height":800}', $loaded->field_iiif_id->info);
  }

  /**
   * Tests info property storage and retrieval for the iiif_id field.
   */
  public function testInfoPropertyStorageAndRetrieval() {
    // Create a test entity with a complex info property.
    $info = [
      'width' => 1200,
      'height' => 900,
      'formats' => ['jpg', 'png'],
      'extra' => ['foo' => 'bar'],
    ];
    $entity = EntityTest::create([
      'name' => 'Test entity 2',
      'field_iiif_id' => [
        'value' => 'test-iiif-id-2',
        'info' => json_encode($info),
      ],
    ]);
    $entity->save();

    // Reload the entity from storage.
    $loaded = EntityTest::load($entity->id());

    // Assert the info property is stored and retrieved correctly.
    $this->assertEquals('test-iiif-id-2', $loaded->field_iiif_id->value);
    $this->assertEquals(json_encode($info), $loaded->field_iiif_id->info);

    // Optionally, decode and check the structure.
    $decoded = json_decode($loaded->field_iiif_id->info, TRUE);
    $this->assertEquals($info, $decoded);
  }

  /**
   * Tests that defaultFieldSettings() returns the correct defaults.
   */
  public function testDefaultFieldSettings() {
    $field_type_manager = \Drupal::service('plugin.manager.field.field_type');
    $definition = $field_type_manager->getDefinition('iiif_id');
    $class = $definition['class'];
    $defaults = $class::defaultFieldSettings();
    $this->assertArrayHasKey('server', $defaults);
    $this->assertArrayHasKey('prefix', $defaults);
    $this->assertArrayHasKey('img_api_version', $defaults);
    $this->assertEquals('', $defaults['server']);
    $this->assertEquals('', $defaults['prefix']);
    $this->assertEquals('3', $defaults['img_api_version']);
  }

  /**
   * Tests that fieldSettingsForm() builds the expected form elements.
   */
  public function testFieldSettingsForm() {
    // Create an entity so the field item exists.
    $entity = EntityTest::create([
      'name' => 'Test entity',
      'field_iiif_id' => [
        'value' => 'test-iiif-id',
        'info' => json_encode(['width' => 1000, 'height' => 800]),
      ],
    ]);
    $entity->save();

    // Get the field item instance from the entity.
    $field_item = $entity->get('field_iiif_id')->first();

    // Now you can call methods on $field_item, e.g.:
    $form = [];
    $form_state = new FormState();
    $form = $field_item->fieldSettingsForm($form, $form_state);
    $this->assertArrayHasKey('server', $form);
    $this->assertArrayHasKey('prefix', $form);
    $this->assertArrayHasKey('img_api_version', $form);
  }

  /**
   * Tests that schema() returns the expected schema.
   */
  public function testSchemaDefinition() {
    $field_type_manager = \Drupal::service('plugin.manager.field.field_type');
    $definition = $field_type_manager->getDefinition('iiif_id');
    $class = $definition['class'];
    $field_storage = FieldStorageConfig::loadByName('entity_test', 'field_iiif_id');
    // $instance = new $class($field_storage, 'field_iiif_id', []);
    $schema = $class::schema($field_storage);
    $this->assertArrayHasKey('columns', $schema);
    $this->assertArrayHasKey('value', $schema['columns']);
    $this->assertArrayHasKey('info', $schema['columns']);
  }

  /**
   * Tests that propertyDefinitions() includes the info property.
   */
  public function testPropertyDefinitions() {
    $field_type_manager = \Drupal::service('plugin.manager.field.field_type');
    $definition = $field_type_manager->getDefinition('iiif_id');
    $class = $definition['class'];
    $field_storage = FieldStorageConfig::loadByName('entity_test', 'field_iiif_id');
    $properties = $class::propertyDefinitions($field_storage);
    $this->assertArrayHasKey('value', $properties);
    $this->assertArrayHasKey('info', $properties);
  }

  /**
   * Tests setValue() logic for the iiif_id field.
   */
  public function testSetValueLogic() {
    $entity = EntityTest::create([
      'name' => 'Test entity',
      'field_iiif_id' => [
        'value' => 'init',
        'info' => '',
      ],
    ]);
    $entity->save();

    // Get the field item.
    $field_item = $entity->get('field_iiif_id')->first();

    // Set value with both value and info.
    $info = ['width' => 123, 'height' => 456];
    $field_item->setValue([
      'value' => 'abc123',
      'info' => json_encode($info),
    ]);
    $this->assertEquals('abc123', $field_item->value);
    $this->assertEquals(json_encode($info), $field_item->info);

    // Set value with only value.
    $field_item->setValue(['value' => 'xyz789']);
    $this->assertEquals('xyz789', $field_item->value);

    // Set value with only info.
    $field_item->setValue(['info' => json_encode(['foo' => 'bar'])]);
    $this->assertEquals(json_encode(['foo' => 'bar']), $field_item->info);

    // Set value with an empty array (should not throw).
    $field_item->setValue([]);
    $this->assertNull($field_item->value);
    $this->assertNull($field_item->info);
  }

  /**
   * Tests that getIiifImageObj() returns a valid IiifImage object.
   */
  public function testGetIiifImageObjReturnsCorrectObject() {
    $info = [
      'width' => 800,
      'height' => 600,
      'formats' => ['jpg', 'png'],
    ];
    $entity = EntityTest::create([
      'name' => 'Test entity',
      'field_iiif_id' => [
        'value' => 'my-iiif-id',
        'info' => json_encode($info),
      ],
    ]);
    $entity->save();

    $field_item = $entity->get('field_iiif_id')->first();
    $iiif_image = $field_item->getIiifImageObj();

    $this->assertInstanceOf(IiifImage::class, $iiif_image);
    $this->assertEquals('my-iiif-id', $iiif_image->getIiifId());
    $this->assertEquals($info['width'], $iiif_image->getInfo()->width);
    $this->assertEquals($info['height'], $iiif_image->getInfo()->height);
  }

  /**
   * Tests that getIiifImageObj() dispatches the IiifGetImageFromFieldEvent.
   */
  public function testGetIiifImageObjDispatchesEvent() {
    // Replace the event dispatcher with a spy.
    $container = \Drupal::getContainer();
    $dispatcher = $this->getMockBuilder(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class)
      ->onlyMethods([
        'dispatch',
        'addListener',
        'addSubscriber',
        'removeListener',
        'removeSubscriber',
        'getListeners',
        'getListenerPriority',
        'hasListeners',
      ])
      ->getMock();

    $dispatcher->expects($this->once())
      ->method('dispatch')
      ->with(
        $this->isInstanceOf(\Drupal\iiif_media_source\Event\IiifGetImageFromFieldEvent::class),
        $this->anything()
      );

    $container->set('event_dispatcher', $dispatcher);
    \Drupal::setContainer($container);

    // Now create the entity and field item.
    $info = [
      'width' => 500,
      'height' => 400,
    ];
    $entity = EntityTest::create([
      'name' => 'Test entity',
      'field_iiif_id' => [
        'value' => 'event-iiif-id',
        'info' => json_encode($info),
      ],
    ]);
    $entity->save();

    $field_item = $entity->get('field_iiif_id')->first();

    // Call the method, which should dispatch the event.
    $field_item->getIiifImageObj();
  }

  /**
   * Tests handling of invalid or missing info JSON in the iiif_id field.
   */
  public function testHandlesInvalidOrMissingInfoJson() {
    // Case 1: Missing info property.
    $entity = EntityTest::create([
      'name' => 'Test entity',
      'field_iiif_id' => [
        'value' => 'no-info',
        // 'info' is omitted.
      ],
    ]);
    $entity->save();
    $field_item = $entity->get('field_iiif_id')->first();
    $iiif_image = $field_item->getIiifImageObj();
    $this->assertInstanceOf(\Drupal\iiif_media_source\Iiif\IiifImage::class, $iiif_image);
    $this->assertIsObject($iiif_image->getInfo());

    // Case 2: Invalid JSON in info property.
    $entity2 = EntityTest::create([
      'name' => 'Test entity 2',
      'field_iiif_id' => [
        'value' => 'bad-json',
        'info' => '{invalid json}',
      ],
    ]);
    $entity2->save();
    $field_item2 = $entity2->get('field_iiif_id')->first();
    $iiif_image2 = $field_item2->getIiifImageObj();
    $this->assertInstanceOf(\Drupal\iiif_media_source\Iiif\IiifImage::class, $iiif_image2);
    $this->assertIsObject($iiif_image2->getInfo());
  }

  /**
   * Tests handling of missing value in the iiif_id field.
   */
  public function testHandlesMissingValue() {
    // Case 1: Field item exists but 'value' is missing.
    $entity = EntityTest::create([
      'name' => 'Test entity',
      'field_iiif_id' => [
        // 'value' is omitted.
        'info' => json_encode(['width' => 100, 'height' => 200]),
      ],
    ]);
    $entity->save();
    $field_item = $entity->get('field_iiif_id')->first();
    $this->assertNull($field_item);
    // getIiifImageObj should still return an IiifImage object, but with a null id.
    // $iiif_image = $field_item->getIiifImageObj();
    // $this->assertInstanceOf(\Drupal\iiif_media_source\Iiif\IiifImage::class, $iiif_image);
    // $this->assertNull($iiif_image->getIiifId());

    // Case 2: Field item is completely empty.
    $entity2 = EntityTest::create([
      'name' => 'Test entity 2',
      // 'field_iiif_id' is omitted entirely.
    ]);
    $entity2->save();
    $field_items = $entity2->get('field_iiif_id');
    $this->assertTrue($field_items->isEmpty());
  }

    /**
   * Tests that the event dispatched by getIiifImageObj() contains correct data.
   */
  public function testEventDispatchingCorrectness() {
    $container = \Drupal::getContainer();

    $info = ['width' => 321, 'height' => 654];

    // Create a mock dispatcher that inspects the event.
    $dispatcher = $this->getMockBuilder(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class)
      ->onlyMethods([
        'dispatch',
        'addListener',
        'addSubscriber',
        'removeListener',
        'removeSubscriber',
        'getListeners',
        'getListenerPriority',
        'hasListeners',
      ])
      ->getMock();

    $dispatcher->expects($this->once())
      ->method('dispatch')
      ->with(
        $this->callback(function ($event) use (&$field_item, $info) {
          // Check event type and contents.
          return $event instanceof \Drupal\iiif_media_source\Event\IiifGetImageFromFieldEvent
            && $event->getIiifImage()->getIiifId() === 'event-correctness'
            && $event->getIiifImage()->getInfo()->width === $info['width']
            && $event->getIiifImage()->getInfo()->height === $info['height'];
        }),
        $this->anything()
      );

    $container->set('event_dispatcher', $dispatcher);
    \Drupal::setContainer($container);

    // Now create the entity and field item.
    $entity = EntityTest::create([
      'name' => 'Test entity',
      'field_iiif_id' => [
        'value' => 'event-correctness',
        'info' => json_encode($info),
      ],
    ]);
    $entity->save();
    $field_item = $entity->get('field_iiif_id')->first();

    // Trigger the event.
    $field_item->getIiifImageObj();
  }

}
