<?php

namespace Drupal\iiif_image_style;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\iiif_image_style\Attribute\IiifImageEffect;

/**
 * Manages IIIF image effect plugins.
 *
 * @see plugin_api
 */
final class IiifImageEffectManager extends DefaultPluginManager {

  /**
   * Constructs a new ImageEffectManager.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/IiifImageEffect', $namespaces, $module_handler, IiifImageEffectInterface::class, IiifImageEffect::class, "Drupal\iiif_image_style\Annotation\IiifImageEffect");

    $this->alterInfo('iiif_image_effect_info');
    $this->setCacheBackend($cache_backend, 'iiif_image_effect_plugins');
  }

}
