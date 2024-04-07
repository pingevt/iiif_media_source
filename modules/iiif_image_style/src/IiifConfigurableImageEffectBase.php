<?php

namespace Drupal\iiif_image_style;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a base class for configurable image effects.
 */
abstract class IiifConfigurableImageEffectBase extends IiifImageEffectBase implements IiifConfigurableImageEffectInterface {

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

}
