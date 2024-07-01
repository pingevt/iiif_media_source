<?php

namespace Drupal\iiif_image_style;

use Drupal\Core\Plugin\DefaultLazyPluginCollection;

/**
 * A collection of image effects.
 */
class IiifImageEffectPluginCollection extends DefaultLazyPluginCollection {

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\iiif_image_style\IiifImageEffectInterface
   *   The Iiif Image Effect plugin.
   */
  // phpcs:ignore
  public function &get($instance_id) {
    return parent::get($instance_id);
  }

  /**
   * {@inheritdoc}
   */
  public function sortHelper($aID, $bID) {
    return $this->get($aID)->getWeight() <=> $this->get($bID)->getWeight();
  }

}
