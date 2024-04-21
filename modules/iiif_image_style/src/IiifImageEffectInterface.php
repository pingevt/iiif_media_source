<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Plugin\DependentPluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

interface IiifImageEffectInterface extends PluginInspectionInterface, ConfigurableInterface, DependentPluginInterface {

  /**
   * Applies an image effect to the image object.
   *
   * @param \Drupal\iiif_media_source\Iiif\IiifImage $image
   *   An image file object.
   * @param \Drupal\iiif_media_source\Iiif\IiifImageUrlParams $params
   *   The current Url Params.
   *
   * @return bool
   *   TRUE on success. FALSE if unable to perform the image effect on the image.
   */
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params, array $context = NULL);

  /**
   * Returns a render array summarizing the configuration of the image effect.
   *
   * @return array
   *   A render array.
   */
  public function getSummary();

  /**
   * Returns the image effect label.
   *
   * @return string
   *   The image effect label.
   */
  public function label();

  /**
   * Returns the unique ID representing the image effect.
   *
   * @return string
   *   The image effect ID.
   */
  public function getUuid();

  /**
   * Returns the weight of the image effect.
   *
   * @return int|string
   *   Either the integer weight of the image effect, or an empty string.
   */
  public function getWeight();

  /**
   * Sets the weight for this image effect.
   *
   * @param int $weight
   *   The weight for this image effect.
   *
   * @return $this
   */
  public function setWeight($weight);

}
