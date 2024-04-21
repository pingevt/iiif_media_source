<?php

namespace Drupal\iiif_image_handling;

use Drupal\crop\Entity\Crop;
use Drupal\iiif_image_handling\Event\IiifEffectFindCropEvent;
use Drupal\iiif_image_style\IiifConfigurableImageEffectBase;
use Drupal\iiif_image_style\IiifConfigurableImageEffectInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class for configurable image effects.
 */
abstract class IiifConfigurableImageEffectWithCropBase extends IiifConfigurableImageEffectBase implements IiifConfigurableImageEffectInterface {

  protected $dispatcher;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoggerInterface $logger, $event_dispatcher) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger);
    $this->dispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory')->get('image'),
      $container->get('event_dispatcher')
    );
  }

  /**
   *
   */
  protected function getCrop($image, $crop_type, $context): ?Crop {
    $crop = Crop::findCrop($image->getFullUrl(), $crop_type);

    // Dispatch Event.
    $event = new IiifEffectFindCropEvent($crop, $image, $crop_type, $context);
    $this->dispatcher->dispatch($event, IiifEffectFindCropEvent::EVENT_NAME);
    $crop = $event->crop;

    return $crop;
  }

}
