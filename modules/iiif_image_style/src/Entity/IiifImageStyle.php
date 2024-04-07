<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\iiif_image_style\EventsTrait;
use Drupal\iiif_image_style\Event\IiifImageStyleSettingsEvent;
use Drupal\iiif_image_style\IiifImageStyleInterface;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_image_style\IiifImageEffectPluginCollection;
use Drupal\iiif_image_style\IiifImageEffectInterface;

/**
 * Defines the iiif image style entity type.
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

  /**
   * The example name.
   */
  protected string $name;

  /**
   * The example label.
   */
  protected string $label;

  /**
   * The array of effects for the image.
   *
   * @var array
   */
  protected $effects = [];

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->name ?? NULL;
  }

  /**
   * Holds the collection of image effects that are used by this image style.
   *
   * @var \Drupal\iiif_image_style\IiifImageEffectPluginCollection
   */
  protected $effectsCollection;

  /**
   * {@inheritdoc}
   */
  public function addImageEffect(array $configuration) {
    $configuration['uuid'] = $this->uuidGenerator()->generate();
    $this->getEffects()->addInstanceId($configuration['uuid'], $configuration);
    return $configuration['uuid'];
  }

  /**
   * {@inheritdoc}
   */
  public function deleteImageEffect( IiifImageEffectInterface $effect) {
    $this->getEffects()->removeInstanceId($effect->getUuid());
    $this->save();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEffect($effect) {
    return $this->getEffects()->get($effect);
  }

  /**
   * {@inheritdoc}
   */
  public function getEffects() {
    if (!$this->effectsCollection) {
      $this->effectsCollection = new IiifImageEffectPluginCollection($this->getImageEffectPluginManager(), $this->effects);
      $this->effectsCollection->sort();
    }
    return $this->effectsCollection;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginCollections() {
    return ['effects' => $this->getEffects()];
  }

  /**
   * Returns the image effect plugin manager.
   *
   * @return \Drupal\Component\Plugin\PluginManagerInterface
   *   The image effect plugin manager.
   */
  protected function getImageEffectPluginManager() {
    return \Drupal::service('plugin.manager.iiif_image_effect');
  }

  /**
   * {@inheritdoc}
   */
  // public function getParams(IiifImage $image = NULL): ?array {

  //   $params = $this->params;
  //   $e = new IiifImageStyleSettingsEvent($this, $image, $params);
  //   $this->eventDispatcher()->dispatch($e, IiifImageStyleSettingsEvent::EVENT_NAME);

  //   return $this->params ?? [];
  // }

  /**
   * {@inheritdoc}
   */
  // public function getFormat(): string {
  //   return $this->params['format'] ?? "";
  // }

}
