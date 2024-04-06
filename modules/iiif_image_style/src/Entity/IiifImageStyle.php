<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\iiif_image_style\EventsTrait;
use Drupal\iiif_image_style\Event\IiifImageStyleSettingsEvent;
use Drupal\iiif_image_style\IiifImageStyleInterface;
use Drupal\iiif_media_source\Iiif\IiifImage;

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
 *       "add" = "Drupal\iiif_image_style\Form\IiifImageStyleForm",
 *       "edit" = "Drupal\iiif_image_style\Form\IiifImageStyleForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
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
 *     "params",
 *   },
 * )
 */
final class IiifImageStyle extends ConfigEntityBase implements IiifImageStyleInterface {

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
   * The array of params data for the IIIF url.
   *
   * @var array
   */
  protected $params = [];

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->name ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getParams(IiifImage $image = NULL): ?array {

    $params = $this->params;
    $e = new IiifImageStyleSettingsEvent($this, $image, $params);
    $this->eventDispatcher()->dispatch($e, IiifImageStyleSettingsEvent::EVENT_NAME);

    return $this->params ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormat(): string {
    return $this->params['format'] ?? "";
  }

}
