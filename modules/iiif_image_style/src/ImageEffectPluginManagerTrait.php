<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\iiif_image_style\IiifImageEffectPluginManagerInterface;

trait ImageEffectPluginManagerTrait {

  /**
   * Returns the IIIF image effect plugin manager.
   *
   * @return \Drupal\iiif_image_style\IiifImageEffectPluginManagerInterface
   *   The IIIF image effect plugin manager.
   */
  protected function getIiifImageEffectPluginManager(): IiifImageEffectPluginManagerInterface {
    return \Drupal::service('plugin.manager.iiif_image_effect');
  }

}
