<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\iiif_image_style\EventsTrait;
use Drupal\iiif_image_style\IiifImageEffectInterface;
use Drupal\iiif_image_style\IiifImageEffectPluginCollection;
use Drupal\iiif_image_style\IiifImageStyleInterface;
use Drupal\iiif_image_style\ImageEffectPluginManagerTrait;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

/**
 * Defines the IIIF Image Style config entity.
 *
 * IIIF Image Styles are collections of IIIF image effects that can be applied
 * to IIIF images to generate styled image URLs.
 *
 * @ConfigEntityType(
 *   id = "iiif_image_style",
 *   label = @Translation("IIIF Image Style"),
 *   label_collection = @Translation("IIIF Image Styles"),
 *   label_singular = @Translation("iiif image style"),
 *   label_plural = @Translation("iiif image styles"),
 *   label_count = @PluralTranslation(
 *     singular = "@count iiif image style",
 *     plural = "@count iiif image styles",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\iiif_image_style\IiifImageStyleListBuilder",
 *     "form" = {
 *       "add" = "Drupal\iiif_image_style\Form\IiifImageStyleAddForm",
 *       "edit" = "Drupal\iiif_image_style\Form\IiifImageStyleEditForm",
 *       "delete" = "Drupal\iiif_image_style\Form\IiifImageStyleDeleteForm",
 *     },
 *   },
 *   config_prefix = "style",
 *   admin_permission = "administer iiif_image_style",
 *   links = {
 *     "collection" = "/admin/config/media/iiif-image-style",
 *     "add-form" = "/admin/config/media/iiif-image-style/add",
 *     "edit-form" = "/admin/config/media/iiif-image-style/{iiif_image_style}",
 *     "delete-form" = "/admin/config/media/iiif-image-style/{iiif_image_style}/delete",
 *   },
 *   entity_keys = {
 *     "id" = "name",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_export = {
 *     "name",
 *     "label",
 *     "effects",
 *   },
 * )
 */
final class IiifImageStyle extends ConfigEntityBase implements IiifImageStyleInterface, EntityWithPluginCollectionInterface {

  use EventsTrait;
  use LoggerChannelTrait;
  use ImageEffectPluginManagerTrait;

  /**
   * The unique machine name of the IIIF image style.
   *
   * @var string
   */
  protected string $name;

  /**
   * The human-readable label for the IIIF image style.
   *
   * @var string
   */
  protected string $label;

  /**
   * The array of effect plugin configurations for this image style.
   *
   * @var array
   */
  protected $effects = [];

  /**
   * Returns the machine name of the image style.
   */
  public function id(): ?string {
    return $this->name ?? NULL;
  }

  /**
   * Holds the collection of image effects that are used by this image style.
   *
   * @var \Drupal\iiif_image_style\IiifImageEffectPluginCollection
   */
  protected ?IiifImageEffectPluginCollection $effectsCollection = NULL;

  /**
   * Adds an image effect to the style and returns its UUID.
   *
   * @param array $configuration
   *   The effect plugin configuration array.
   *
   * @return string
   *   The UUID of the added effect.
   */
  public function addImageEffect(array $configuration) {
    $configuration['uuid'] = $this->uuidGenerator()->generate();
    $this->getEffects()->addInstanceId($configuration['uuid'], $configuration);
    return $configuration['uuid'];
  }

  /**
   * Removes an image effect from the style.
   *
   * @param \Drupal\iiif_image_style\IiifImageEffectInterface $effect
   *   The effect plugin instance to remove.
   *
   * @return $this
   */
  public function deleteImageEffect(IiifImageEffectInterface $effect) {
    $this->getEffects()->removeInstanceId($effect->getUuid());
    $this->save();
    return $this;
  }

  /**
   * Gets a specific image effect by UUID or plugin ID.
   *
   * @param string $effect
   *   The UUID or plugin ID of the effect.
   *
   * @return \Drupal\iiif_image_style\IiifImageEffectInterface|null
   *   The effect plugin instance, or NULL if not found.
   */
  public function getEffect($effect): ?IiifImageEffectInterface {
    return $this->getEffects()->get($effect);
  }

  /**
   * Gets the plugin collection of effects for this style.
   *
   * @return \Drupal\iiif_image_style\IiifImageEffectPluginCollection
   *   The plugin collection of effects.
   */
  public function getEffects(): IiifImageEffectPluginCollection {
    if (!$this->effectsCollection) {
      $this->effectsCollection = new IiifImageEffectPluginCollection($this->getIiifImageEffectPluginManager(), $this->effects);
      $this->effectsCollection->sort();
    }
    return $this->effectsCollection;
  }

  /**
   * Returns all plugin collections for this entity.
   *
   * @return array
   *   An array of plugin collections keyed by collection name.
   */
  public function getPluginCollections(): array {
    return ['effects' => $this->getEffects()];
  }

  /**
   * {@inheritdoc}
   */
  public function buildUrl(IiifImage $image): string {
    $img_url = "";

    // Get the base IIIF URL parameters for the image.
    $params = IiifImageUrlParams::fullImageParams($image->getApiVersion());

    // Apply all effects in order.
    foreach ($this->getEffects() as $effect) {
      try {
        $effect->applyEffect($image, $params, []);
      }
      catch (\Exception $e) {
        $this->getLogger('iiif_image_style')->error('Error applying effect "@effect": @message', [
          '@effect' => $effect->getPluginId(),
          '@message' => $e->getMessage(),
        ]);
        continue;
      }
    }

    // Build the final IIIF image URL.
    $img_url = $image->getBuiltImageUrl($params);

    return $img_url;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies(): static {
    parent::calculateDependencies();

    // phpcs:disable
    // Add this module as a dependency.
    // $dependencies['module'][] = 'iiif_image_style';.
    // Add dependencies for each effect plugin.
    // foreach ($this->effects as $effect_config) {
    //   // Get the plugin definition.
    //   $plugin_id = $effect_config['id'] ?? NULL;
    //   $plugin_manager = $this->getIiifImageEffectPluginManager();
    //   $definition = $plugin_manager->getDefinition($plugin_id, FALSE);
    //   if (!$definition) {
    //     $this->getLogger('iiif_image_style')->error('Image effect plugin with ID "@id" not found.', ['@id' => $plugin_id]);
    //     continue;
    //   }
    //   if (!empty($definition['provider'])) {
    //     $dependencies['module'][] = $definition['provider'];
    //   }
    //   try {
    //     $plugin = $plugin_manager->createInstance($plugin_id, $effect_config);
    //     if (method_exists($plugin, 'calculateDependencies')) {
    //       $plugin_deps = $plugin->calculateDependencies();
    //       if (is_array($plugin_deps)) {
    //         foreach ($plugin_deps as $type => $deps) {
    //           foreach ($deps as $dep) {
    //             $dependencies[$type][] = $dep;
    //           }
    //         }
    //       }
    //     }
    //   }
    //   catch (\Exception $e) {
    //     $this->getLogger('iiif_image_style')->error('Failed to instantiate image effect plugin "@id": @message', [
    //       '@id' => $plugin_id,
    //       '@message' => $e->getMessage(),
    //     ]);
    //     continue;
    //   }
    // }
    // // Remove duplicate dependencies for all types.
    // foreach ($dependencies as $type => &$deps) {
    //   $deps = array_unique($deps);
    // }
    // phpcs:enable
    return $this;
  }

}
