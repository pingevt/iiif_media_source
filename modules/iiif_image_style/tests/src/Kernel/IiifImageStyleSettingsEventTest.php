<?php

declare(strict_types=1);

namespace Drupal\Tests\iiif_image_style\Kernel;

use Drupal\iiif_image_style\Entity\IiifImageStyle;
use Drupal\iiif_image_style\Event\IiifImageStyleSettingsEvent;
use Drupal\KernelTests\KernelTestBase;

/**
 * @coversDefaultClass \Drupal\iiif_image_style\Event\IiifImageStyleSettingsEvent
 *
 * Kernel tests for the IiifImageStyleSettingsEvent class.
 *
 * @group iiif_image_style
 */
class IiifImageStyleSettingsEventTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'iiif_image_style',
  ];

  /**
   * Tests the constructor and getters.
   *
   * @covers ::__construct
   * @covers ::getImageStyle
   * @covers ::getSettings
   */
  public function testConstructorAndGetters(): void {
    // Create a real IiifImageStyle entity with a valid ID.
    $imageStyle = IiifImageStyle::create([
    // Ensure the ID is a valid machine name.
      'name' => 'test_style',
      'label' => 'Test Style',
    ]);
    $imageStyle->save();

    $settings = ['key' => 'value'];

    // Create the event.
    $event = new IiifImageStyleSettingsEvent($imageStyle, $settings);

    // Assert that the image style and settings are correctly set.
    $this->assertSame($imageStyle, $event->getImageStyle());
    $this->assertEquals($settings, $event->getSettings());
  }

  /**
   * Tests the setSettings() method.
   *
   * @covers ::setSettings
   */
  public function testSetSettings(): void {
    // Create a real IiifImageStyle entity.
    $imageStyle = IiifImageStyle::create([
      'name' => 'test_style',
      'label' => 'Test Style',
    ]);
    $imageStyle->save();

    // Create the event.
    $event = new IiifImageStyleSettingsEvent($imageStyle);

    // Set new settings.
    $newSettings = ['new_key' => 'new_value'];
    $result = $event->setSettings($newSettings);

    // Assert that the settings were updated and method chaining works.
    $this->assertSame($event, $result);
    $this->assertEquals($newSettings, $event->getSettings());
  }

}
