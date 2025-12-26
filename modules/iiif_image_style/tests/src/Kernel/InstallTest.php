<?php

namespace Drupal\Tests\iiif_image_style\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests that the IIIF Image Style module installs correctly.
 *
 * @group iiif_image_style
 */
class InstallTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'iiif_media_source',
    'iiif_image_style',
    // Add any other required dependencies here.
  ];

  /**
   * Tests module installation.
   */
  public function testModuleIsInstalled() {
    $this->assertTrue(\Drupal::moduleHandler()->moduleExists('iiif_image_style'), 'IIIF Image Style module is installed.');
  }

}
