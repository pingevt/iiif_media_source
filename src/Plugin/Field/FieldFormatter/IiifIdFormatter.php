<?php

namespace Drupal\iiif_media_source\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\iiif_media_source\Iiif\IiifImage;

/**
 * IIIF ID formatter.
 *
 * @FieldFormatter(
 *   id = "iiif_id_formatter",
 *   label = @Translation("IIIF ID Formatter"),
 *   field_types = {
 *     "iiif_id"
 *   }
 * )
 */
class IiifIdFormatter extends StringFormatter {

  /**
   * {@inheritdoc}
   */
  // public static function defaultSettings() {
  //   $options = parent::defaultSettings();

  //   // $options += [
  //   //   'region' => 'full',
  //   //   'region_x' => '',
  //   //   'region_y' => '',
  //   //   'region_w' => '',
  //   //   'region_h' => '',
  //   //   'size' => 'max',
  //   //   'size_w' => '',
  //   //   'size_h' => '',
  //   //   'rotation' => 0,
  //   //   'quality' => 'default',
  //   //   'format' => 'png',
  //   // ];

  //   return $options;
  // }

  /**
   * {@inheritdoc}
   */
  // public function settingsForm(array $form, FormStateInterface $form_state) {
  //   $form = parent::settingsForm($form, $form_state);

  //   $field_img_api_version = $this->fieldDefinition->getSettings()['img_api_version'];

  //   // Region.
  //   $form['region'] = [
  //     '#type' => 'select',
  //     '#title' => $this->t('Region'),
  //     '#options' => IiifImage::getRegionOptions($field_img_api_version),
  //     '#default_value' => $this->getSetting('region'),
  //     '#attributes' => [
  //       'data-states' => 'region',
  //     ]
  //   ];
  //   $form['region_x'] = [
  //     '#title' => $this->t('x'),
  //     '#type' => 'number',
  //     '#default_value' => $this->getSetting('region_x'),
  //     '#description' => $this->t(''),
  //     '#states' => [
  //       'visible' => [
  //         ':input[data-states="region"]' => [
  //           ['value' => 'x,y,w,h'],
  //           'or',
  //           ['value' => 'pct:x,y,w,h'],
  //         ],
  //       ]
  //     ],
  //   ];
  //   $form['region_y'] = [
  //     '#title' => $this->t('y'),
  //     '#type' => 'number',
  //     '#default_value' => $this->getSetting('region_y'),
  //     '#description' => $this->t(''),
  //     '#states' => [
  //       'visible' => [
  //         ':input[data-states="region"]' => [
  //           ['value' => 'x,y,w,h'],
  //           'or',
  //           ['value' => 'pct:x,y,w,h'],
  //         ],
  //       ]
  //     ],
  //   ];
  //   $form['region_w'] = [
  //     '#title' => $this->t('w'),
  //     '#type' => 'number',
  //     '#default_value' => $this->getSetting('region_w'),
  //     '#description' => $this->t(''),
  //     '#states' => [
  //       'visible' => [
  //         ':input[data-states="region"]' => [
  //           ['value' => 'x,y,w,h'],
  //           'or',
  //           ['value' => 'pct:x,y,w,h'],
  //         ],
  //       ]
  //     ],
  //   ];
  //   $form['region_h'] = [
  //     '#title' => $this->t('h'),
  //     '#type' => 'number',
  //     '#default_value' => $this->getSetting('region_h'),
  //     '#description' => $this->t(''),
  //     '#states' => [
  //       'visible' => [
  //         ':input[data-states="region"]' => [
  //           ['value' => 'x,y,w,h'],
  //           'or',
  //           ['value' => 'pct:x,y,w,h'],
  //         ],
  //       ]
  //     ],
  //   ];

  //   // Size.
  //   $form['size'] = [
  //     '#type' => 'select',
  //     '#title' => $this->t('Size'),
  //     '#options' => IiifImage::getSizeOptions($field_img_api_version),
  //     '#default_value' => $this->getSetting('size'),
  //     '#attributes' => [
  //       'data-states' => 'size',
  //     ]
  //   ];
  //   $form['size_w'] = [
  //     '#title' => $this->t('w'),
  //     '#type' => 'number',
  //     '#default_value' => $this->getSetting('size_w'),
  //     '#description' => $this->t(''),
  //     '#states' => [
  //       'invisible' => [
  //         ':input[data-states="size"]' => [
  //           ['value' => 'max'],
  //           'or',
  //           ['value' => '^max'],
  //           'or',
  //           ['value' => 'full'],
  //           'or',
  //           ['value' => '^full'],
  //           'or',
  //           ['value' => 'pct:n'],
  //           'or',
  //           ['value' => '^pct:n'],
  //         ],
  //       ]
  //     ],
  //   ];
  //   $form['size_h'] = [
  //     '#title' => $this->t('h'),
  //     '#type' => 'number',
  //     '#default_value' => $this->getSetting('size_h'),
  //     '#description' => $this->t(''),
  //     '#states' => [
  //       'invisible' => [
  //         ':input[data-states="size"]' => [
  //           ['value' => 'max'],
  //           'or',
  //           ['value' => '^max'],
  //           'or',
  //           ['value' => 'full'],
  //           'or',
  //           ['value' => '^full'],
  //           'or',
  //           ['value' => 'pct:n'],
  //           'or',
  //           ['value' => '^pct:n'],
  //         ],
  //       ]
  //     ],
  //   ];
  //   $form['size_n'] = [
  //     '#title' => $this->t('n'),
  //     '#type' => 'number',
  //     '#default_value' => $this->getSetting('size_n'),
  //     '#description' => $this->t(''),
  //     '#min' => 0,
  //     '#max' => 100,
  //     '#step' => 0.1,
  //     '#states' => [
  //       'visible' => [
  //         ':input[data-states="size"]' => [
  //           ['value' => 'pct:n'],
  //           'or',
  //           ['value' => '^pct:n'],
  //         ],
  //       ]
  //     ],
  //   ];

  //   // Rotation.
  //   $form['rotation'] = [
  //     '#title' => $this->t('Rotation'),
  //     '#type' => 'number',
  //     '#default_value' => $this->getSetting('rotation'),
  //     '#description' => $this->t(''),
  //     '#min' => 0,
  //     '#max' => 360,
  //     '#step' => 0.1,
  //   ];

  //   // Quality.
  //   $form['quality'] = [
  //     '#type' => 'select',
  //     '#title' => $this->t('Quality'),
  //     '#options' => IiifImage::getQualityOptions($field_img_api_version),
  //     '#default_value' => $this->getSetting('quality'),
  //     '#attributes' => [
  //       'data-states' => 'quality',
  //     ]
  //   ];

  //   // Format.
  //   $form['format'] = [
  //     '#type' => 'select',
  //     '#title' => $this->t('Format'),
  //     '#options' => IiifImage::getFormatOptions($field_img_api_version),
  //     '#default_value' => $this->getSetting('format'),
  //     '#attributes' => [
  //       'data-states' => 'format',
  //     ]
  //   ];

  //   return $form;
  // }

  /**
   * {@inheritdoc}
   */
  // public function settingsSummary() {
  //   $summary = parent::settingsSummary();

  //   $this->processSettings();

  //   $summary[] = $this->t('Region: @region', ['@region' => $this->getSetting('region_actual')]);
  //   $summary[] = $this->t('Size: @size', ['@size' => $this->getSetting('size_actual')]);
  //   $summary[] = $this->t('Rotation: @rotation', ['@rotation' => $this->getSetting('rotation')]);
  //   $summary[] = $this->t('Quality: @quality', ['@quality' => $this->getSetting('quality')]);
  //   $summary[] = $this->t('Format: @format', ['@format' => $this->getSetting('format')]);

  //   return $summary;
  // }

  /**
   * {@inheritdoc}
   */
  // public function viewElements(FieldItemListInterface $items, $langcode): array {

  //   $build = [];

  //   //   $field_def = $items->getFieldDefinition();
  //   //   $field_name = $field_def->getName();

  //   //   $parent_entity = $items->getParent()->getEntity();
  //   //   if (isset($parent_entity->overwritten_property_map)) {
  //   //     $overridden_data = json_decode($parent_entity->overwritten_property_map);
  //   //   }

  //   //   $crop_type = \Drupal::config('focal_point.settings')->get('crop_type');
  //   //   $focal_point_manager = \Drupal::service('iiif_media_source.focal_point_manager');

  //   //   // Get individual fields.
  //   //   foreach ($items as $delta => $item) {
  //   //     $view_value = $this->viewValue($item);

  //   //     $crop = $focal_point_manager->getCropIiifEntity($item, $crop_type, $item->getEntity()->id());

  //   //     // Check for contextual Override.
  //   //     $parent_entity = $item->getParent()->getParent()->getEntity();

  //   //     if (isset($overridden_data) && isset($overridden_data->{$field_name}[$delta])) {
  //   //       if (isset($overridden_data->{$field_name}[$delta]->focal_point)) {
  //   //         [$x, $y] = explode(',', $overridden_data->{$field_name}[$delta]->focal_point);
  //   //         $full_dimens = $item->_image->getDimensions();
  //   //         $new_crop = $focal_point_manager->relativeToAbsolute($x, $y, $full_dimens['w'], $full_dimens['h']);

  //   //         $crop->setPosition($new_crop['x'], $new_crop['y']);
  //   //       }
  //   //     }

  //   //     // Set render array.
  //   //     $view_value = [
  //   //       '#theme' => 'iiif_image',
  //   //       '#image' => $item->_image,
  //   //       '#crop' => $crop,
  //   //       '#size_type' => $this->getSetting('size_type'),
  //   //       '#dest_width' => $this->getSetting('width'),
  //   //       '#dest_height' => $this->getSetting('height'),
  //   //     ];

  //   //     $build[$delta] = $view_value;
  //   //   }

  //   // Process settings.
  //   $this->processSettings();

  //   foreach ($items as $delta => $item) {

  //     $view_value = [
  //       '#theme' => 'iiif_image',
  //       '#image' => $item->getImg($item->getValue()),
  //       '#region' => $this->getSetting('region_actual'),
  //       '#size' => $this->getSetting('size_actual'),
  //       '#rotation' => $this->getSetting('rotation'),
  //       '#quality' => $this->getSetting('quality'),
  //       '#format' => $this->getSetting('format'),
  //     ];
  //     $build[$delta] = $view_value;
  //   }

  //   return $build;
  // }

  // protected function processSettings() {

  //   $region_actual = str_replace(['w', 'h', 'n'], [$this->getSetting('region_w'), $this->getSetting('region_h'), $this->getSetting('region_n')], $this->getSetting('region'));
  //   $size_actual = str_replace(['w', 'h'], [$this->getSetting('size_w'), $this->getSetting('size_h')], $this->getSetting('size'));

  //   $this->setSetting('region_actual', $region_actual);
  //   $this->setSetting('size_actual', $size_actual);
  // }

}
