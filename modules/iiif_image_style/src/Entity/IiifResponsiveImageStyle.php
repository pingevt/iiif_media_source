<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\iiif_image_style\BreakpointManagerTrait;
use Drupal\iiif_image_style\IiifResponsiveImageStyleInterface;

/**
 * Defines the iiif responsive image style entity type.
 *
 * @ConfigEntityType(
 *   id = "iiif_responsive_image_style",
 *   label = @Translation("IIIF Responsive Image Style"),
 *   label_collection = @Translation("IIIF Responsive Image Styles"),
 *   label_singular = @Translation("iiif responsive image style"),
 *   label_plural = @Translation("iiif responsive image styles"),
 *   label_count = @PluralTranslation(
 *     singular = "@count iiif responsive image style",
 *     plural = "@count iiif responsive image styles",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\iiif_image_style\IiifResponsiveImageStyleListBuilder",
 *     "form" = {
 *       "add" = "Drupal\iiif_image_style\Form\IiifResponsiveImageStyleForm",
 *       "edit" = "Drupal\iiif_image_style\Form\IiifResponsiveImageStyleForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *   },
 *   config_prefix = "responsive_image",
 *   admin_permission = "administer iiif_responsive_image_style",
 *   links = {
 *     "collection" = "/admin/config/media/iiif-responsive-image-style",
 *     "add-form" = "/admin/config/media/iiif-responsive-image-style/add",
 *     "edit-form" = "/admin/config/media/iiif-responsive-image-style/{iiif_responsive_image_style}",
 *     "delete-form" = "/admin/config/media/iiif-responsive-image-style/{iiif_responsive_image_style}/delete",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "image_style_mappings",
 *     "breakpoint_group",
 *     "fallback_image_style",
 *   },
 * )
 */
final class IiifResponsiveImageStyle extends ConfigEntityBase implements IiifResponsiveImageStyleInterface {

  use BreakpointManagerTrait;
  use LoggerChannelTrait;

  /**
   * The example ID.
   */
  protected string $id;

  /**
   * The example label.
   */
  protected string $label;

  /**
   * The image style mappings.
   *
   * Each image style mapping array contains the following keys:
   *   - image_mapping_type: Either 'image_style' or 'sizes'.
   *   - image_mapping:
   *     - If image_mapping_type is 'image_style', the image style ID (a
   *       string).
   *     - If image_mapping_type is 'sizes', an array with following keys:
   *       - sizes: The value for the 'sizes' attribute.
   *       - sizes_image_styles: The image styles to use for the 'srcset'
   *         attribute.
   *   - breakpoint_id: The breakpoint ID for this image style mapping.
   *   - multiplier: The multiplier for this image style mapping.
   *
   * @var array
   */
  protected $image_style_mappings = [];

  /**
   * The keyed image style mappings.
   *
   * @var ?array
   */
  protected $keyedImageStyleMappings = NULL;

  /**
   * The responsive image breakpoint group.
   *
   * @var string
   */
  protected $breakpoint_group = '';

  /**
   * The fallback image style.
   *
   * @var string
   */
  protected $fallback_image_style = '';

  /**
   * {@inheritdoc}
   */
  public function addImageStyleMapping(string $breakpoint_id, string $multiplier, array $image_style_mapping): static {
    if (empty($image_style_mapping['image_mapping_type']) || empty($image_style_mapping['image_mapping'])) {
      $this->getLogger('iiif_image_style')->error('Invalid image style mapping provided for breakpoint "@breakpoint" and multiplier "@multiplier".', [
        '@breakpoint' => $breakpoint_id,
        '@multiplier' => $multiplier,
      ]);
      return $this;
    }
    // If there is an existing mapping, overwrite it.
    foreach ($this->image_style_mappings as &$mapping) {
      if ($mapping['breakpoint_id'] === $breakpoint_id && $mapping['multiplier'] === $multiplier) {
        $mapping = $image_style_mapping + [
          'breakpoint_id' => $breakpoint_id,
          'multiplier' => $multiplier,
        ];
        $this->sortMappings();
        return $this;
      }
    }
    $this->image_style_mappings[] = $image_style_mapping + [
      'breakpoint_id' => $breakpoint_id,
      'multiplier' => $multiplier,
    ];
    $this->sortMappings();
    return $this;
  }

  /**
   * Sort mappings by breakpoint ID and multiplier.
   */
  protected function sortMappings(): void {
    $this->keyedImageStyleMappings = NULL;
    $breakpoints = $this->getBreakpointManager()->getBreakpointsByGroup($this->getBreakpointGroup());
    if (empty($breakpoints)) {
      $this->getLogger('iiif_image_style')->notice('No breakpoints found for group "@group".', [
        '@group' => $this->getBreakpointGroup(),
      ]);
      return;
    }
    usort($this->image_style_mappings, function (array $a, array $b) use ($breakpoints): int {
      $breakpoint_a = $breakpoints[$a['breakpoint_id']] ?? NULL;
      $breakpoint_b = $breakpoints[$b['breakpoint_id']] ?? NULL;
      if (!$breakpoint_a || !$breakpoint_b) {
        // Log missing breakpoints.
        $this->getLogger('iiif_image_style')->warning('Breakpoint not found: @a or @b', [
          '@a' => $a['breakpoint_id'],
          '@b' => $b['breakpoint_id'],
        ]);
        return 0;
      }
      $first = ((float) mb_substr($a['multiplier'], 0, -1)) * 100;
      $second = ((float) mb_substr($b['multiplier'], 0, -1)) * 100;
      return [$breakpoint_b->getWeight(), $first] <=> [$breakpoint_a->getWeight(), $second];
    });
  }

  /**
   * {@inheritdoc}
   */
  public function hasImageStyleMappings(): bool {
    $mappings = $this->getKeyedImageStyleMappings();
    return !empty($mappings);
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyedImageStyleMappings(): array {
    if (!$this->keyedImageStyleMappings) {
      $this->keyedImageStyleMappings = [];
      foreach ($this->image_style_mappings as $mapping) {
        if (!static::isEmptyImageStyleMapping($mapping)) {
          $this->keyedImageStyleMappings[$mapping['breakpoint_id']][$mapping['multiplier']] = $mapping;
        }
      }
    }
    return $this->keyedImageStyleMappings;
  }

  /**
   * {@inheritdoc}
   */
  public function getImageStyleMappings(): array {
    return $this->image_style_mappings;
  }

  /**
   * {@inheritdoc}
   */
  public function setBreakpointGroup(string $breakpoint_group): static {
    // If the breakpoint group is changed then the image style mappings are
    // invalid.
    if ($breakpoint_group !== $this->breakpoint_group) {
      $this->removeImageStyleMappings();
    }
    $this->breakpoint_group = $breakpoint_group;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBreakpointGroup(): string {
    return $this->breakpoint_group;
  }

  /**
   * {@inheritdoc}
   */
  public function setFallbackImageStyle(string $fallback_image_style): static {
    $this->fallback_image_style = $fallback_image_style;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFallbackImageStyle(): string {
    return $this->fallback_image_style;
  }

  /**
   * {@inheritdoc}
   */
  public function removeImageStyleMappings(): static {
    $this->image_style_mappings = [];
    $this->keyedImageStyleMappings = NULL;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies(): static {
    parent::calculateDependencies();

    $providers = $this->getBreakpointManager()->getGroupProviders($this->breakpoint_group);
    foreach ($providers as $provider => $type) {
      $this->addDependency($type, $provider);
    }
    // Extract all the styles from the image style mappings.
    $styles = IiifImageStyle::loadMultiple($this->getImageStyleIds());
    array_walk($styles, function ($style) {
      if (!$style) {
        $this->getLogger('iiif_image_style')->warning('Referenced IIIF image style could not be loaded during dependency calculation.');
        return;
      }
      $this->addDependency('config', $style->getConfigDependencyName());
    });
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function isEmptyImageStyleMapping(array $image_style_mapping): bool {
    if (!empty($image_style_mapping)) {
      switch ($image_style_mapping['image_mapping_type']) {
        case 'sizes':
          // The image style mapping must have a sizes attribute defined and one
          // or more image styles selected.
          if ($image_style_mapping['image_mapping']['sizes'] && $image_style_mapping['image_mapping']['sizes_image_styles']) {
            return FALSE;
          }
          break;

        case 'image_style':
          // The image style mapping must have an image style selected.
          if ($image_style_mapping['image_mapping']) {
            return FALSE;
          }
          break;
      }
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getImageStyleMapping(string $breakpoint_id, string $multiplier): ?array {
    $map = $this->getKeyedImageStyleMappings();
    if (!isset($map[$breakpoint_id][$multiplier])) {
      $this->getLogger('iiif_image_style')->debug('No image style mapping found for breakpoint "@breakpoint" and multiplier "@multiplier".', [
        '@breakpoint' => $breakpoint_id,
        '@multiplier' => $multiplier,
      ]);
      return NULL;
    }
    return $map[$breakpoint_id][$multiplier];
  }

  /**
   * {@inheritdoc}
   */
  public function getImageStyleIds(): array {
    $image_styles = [$this->getFallbackImageStyle()];
    foreach ($this->getImageStyleMappings() as $image_style_mapping) {
      // Only image styles of non-empty mappings should be loaded.
      if (!$this::isEmptyImageStyleMapping($image_style_mapping)) {
        switch ($image_style_mapping['image_mapping_type']) {
          case 'image_style':
            $image_styles[] = $image_style_mapping['image_mapping'];
            break;

          case 'sizes':
            $image_styles = array_merge($image_styles, $image_style_mapping['image_mapping']['sizes_image_styles']);
            break;
        }
      }
    }
    return array_values(array_filter(array_unique($image_styles)));
  }

}
