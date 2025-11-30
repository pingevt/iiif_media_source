<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style;

use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Defines the interface for configurable image effects.
 */
interface IiifConfigurableImageEffectInterface extends IiifImageEffectInterface, PluginFormInterface {
}
