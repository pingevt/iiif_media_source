<?php

namespace Drupal\iiif_media_source\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;

/**
 * IIIF Image formatter.
 *
 * @FieldFormatter(
 *   id = "iiif_image_formatter",
 *   label = @Translation("IIIF Image Formatter"),
 *   field_types = {
 *     "iiif_id"
 *   }
 * )
 */
class IiifImageFormatter extends StringFormatter {

  protected $sizingOptions = [
    'full' => 'Full',
    'scale_and_crop' => "Scale and Crop",
    'resize' => "Resize",
    'scale' => "Scale",
    'crop' => "Crop",
  ];

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $options = parent::defaultSettings();

    $options['size_type'] = "full";
    $options['width'] = NULL;
    $options['height'] = NULL;

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['size_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Sizing Type'),
      '#options' => $this->sizingOptions,
      '#default_value' => $this->getSetting('size_type'),
    ];

    $form['width'] = [
      '#title' => $this->t('Width'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('width'),
      '#description' => $this->t(''),
      '#weight' => 16,
    ];

    $form['height'] = [
      '#title' => $this->t('Height'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('height'),
      '#description' => $this->t(''),
      '#weight' => 16,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    if ($this->getSetting('size_type')) {
      $summary[] = $this->t('Sizing type: @size_type', ['@size_type' => $this->sizingOptions[$this->getSetting('size_type')]]);
    }
    if ($this->getSetting('width')) {
      $summary[] = $this->t('Width: @widthpx', ['@width' => $this->getSetting('width')]);
    }
    if ($this->getSetting('height')) {
      $summary[] = $this->t('Height: @heightpx', ['@height' => $this->getSetting('height')]);
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  // public function viewElements(FieldItemListInterface $items, $langcode): array {

  //   $build = [];

  //   $field_def = $items->getFieldDefinition();
  //   $field_name = $field_def->getName();

  //   $parent_entity = $items->getParent()->getEntity();
  //   if (isset($parent_entity->overwritten_property_map)) {
  //     $overridden_data = json_decode($parent_entity->overwritten_property_map);
  //   }

  //   $crop_type = \Drupal::config('focal_point.settings')->get('crop_type');
  //   $focal_point_manager = \Drupal::service('iiif_media_source.focal_point_manager');

  //   // Get individual fields.
  //   foreach ($items as $delta => $item) {
  //     $view_value = $this->viewValue($item);

  //     $crop = $focal_point_manager->getCropIiifEntity($item, $crop_type, $item->getEntity()->id());

  //     // Check for contextual Override.
  //     $parent_entity = $item->getParent()->getParent()->getEntity();

  //     if (isset($overridden_data) && isset($overridden_data->{$field_name}[$delta])) {
  //       if (isset($overridden_data->{$field_name}[$delta]->focal_point)) {
  //         [$x, $y] = explode(',', $overridden_data->{$field_name}[$delta]->focal_point);
  //         $full_dimens = $item->_image->getDimensions();
  //         $new_crop = $focal_point_manager->relativeToAbsolute($x, $y, $full_dimens['w'], $full_dimens['h']);

  //         $crop->setPosition($new_crop['x'], $new_crop['y']);
  //       }
  //     }

  //     // Set render array.
  //     $view_value = [
  //       '#theme' => 'iiif_image',
  //       '#image' => $item->_image,
  //       '#crop' => $crop,
  //       '#size_type' => $this->getSetting('size_type'),
  //       '#dest_width' => $this->getSetting('width'),
  //       '#dest_height' => $this->getSetting('height'),
  //     ];

  //     $build[$delta] = $view_value;
  //   }

  //   return $build;
  // }

}
