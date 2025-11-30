<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for all IiifImageEffect plugins.
 */
abstract class IiifImageEffectBase extends PluginBase implements IiifImageEffectInterface, ContainerFactoryPluginInterface {

  /**
   * The image effect ID.
   *
   * @var string
   */
  protected $uuid;

  /**
   * The weight of the image effect.
   *
   * @var ?int
   */
  protected $weight = NULL;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->setConfiguration($configuration);
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory')->get('iiif_image_style')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transformDimensions(array &$dimensions, $uri) {
    // Most image effects will not change the dimensions. This base
    // implementation represents this behavior. Override this method if your
    // image effect does change the dimensions.
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeExtension($extension) {
    // Most image effects will not change the extension. This base
    // implementation represents this behavior. Override this method if your
    // image effect does change the extension.
    return $extension;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary(): array {
    return [
      '#markup' => '',
      '#effect' => [
        'id' => $this->pluginDefinition['id'],
        'label' => $this->label(),
        'description' => $this->pluginDefinition['description'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getUuid(): string {
    return $this->uuid;
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight(int $weight): static {
    $this->weight = $weight;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight(): ?int {
    return $this->weight;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration(): array {
    return [
      'uuid' => $this->getUuid(),
      'id' => $this->getPluginId(),
      'weight' => $this->getWeight(),
      'data' => $this->configuration,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration): static {
    $configuration += [
      'data' => [],
      'uuid' => '',
      'weight' => NULL,
    ];
    $this->configuration = $configuration['data'] + $this->defaultConfiguration();
    $this->uuid = $configuration['uuid'];
    $this->weight = $configuration['weight'];
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   *
   * Calculates dependencies and stores them in the dependency property.
   *
   * @return $this
   *
   * @see \Drupal\Core\Config\Entity\ConfigEntityInterface
   * @see \Drupal\Core\Config\Entity\ConfigDependencyManager
   */
  public function calculateDependencies(): static {
    return $this;
  }

}
