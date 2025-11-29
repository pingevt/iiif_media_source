<?php

namespace Drupal\iiif_image_handling;

use Drupal\crop\Entity\Crop;
use Drupal\iiif_image_handling\Event\IiifEffectFindCropEvent;
use Drupal\iiif_image_style\IiifImageEffectBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Provides a base class for configurable image effects.
 */
abstract class IiifImageEffectWithCropBase extends IiifImageEffectBase implements IiifImageEffectWithCropInterface {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected EventDispatcherInterface $dispatcher;

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
   * Gets the crop for the image, allowing event subscribers to alter it.
   *
   * @param \Drupal\iiif_image_style\IiifImage $image
   *   The IIIF image object.
   * @param string $crop_type
   *   The crop type.
   * @param mixed $context
   *   Additional context.
   *
   * @return \Drupal\crop\Entity\Crop|null
   *   The crop entity, or NULL if none found.
   */
  protected function getCrop($image, $crop_type, $context): ?Crop {
    $crop = Crop::findCrop($image->getFullUrl(), $crop_type);

    // Dispatch Event.
    $event = new IiifEffectFindCropEvent($crop, $image, $crop_type, $context);
    $this->dispatcher->dispatch($event, IiifEffectFindCropEvent::EVENT_NAME);
    $crop = $event->getCrop();

    return $crop;
  }

}
