<?php

declare(strict_types=1);

namespace Drupal\Tests\iiif_image_style\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the integration of the IiifImageEffect annotation with the plugin system.
 *
 * @group iiif_image_style
 */
class IiifImageEffectAnnotationIntegrationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['iiif_image_style'];

  /**
   * Tests plugin discovery with the annotation.
   */
  public function testPluginDiscovery(): void {
    // Get the plugin manager.
    $plugin_manager = $this->container->get('plugin.manager.iiif_image_effect');

    // Discover plugins.
    $definitions = $plugin_manager->getDefinitions();

    // Assert that the plugin definitions are discovered.
    $this->assertNotEmpty($definitions);
    $this->assertArrayHasKey('iiif_image_resize', $definitions);

    // Assert that the annotation properties are correctly parsed.
    $definition = $definitions['iiif_image_resize'];
    $this->assertEquals('Resize', $definition['label']);
    $this->assertEquals('Simple resizer', $definition['description']);
  }

}
