<?php

namespace Drupal\iiif_media_source\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * IIIF Image Widget.
 *
 * @FieldWidget(
 *   id = "iiif_image_widget",
 *   label = @Translation("IIIF Image Widget"),
 *   field_types = {
 *     "iiif_id"
 *   }
 * )
 */
class IiifImageWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'size' => 60,
      'placeholder' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['size'] = [
      '#type' => 'number',
      '#title' => $this->t('Size of textfield'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 1,
    ];
    $element['placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => $this->t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $field_name = $this->fieldDefinition->getName();
    $element['#field_name'] = $field_name;

    $element['value'] = $element + [
      '#type' => 'textfield',
      '#default_value' => $items[$delta]->value ?? NULL,
      '#size' => $this->getSetting('size'),
      '#placeholder' => $this->getSetting('placeholder'),
      '#maxlength' => $this->getFieldSetting('max_length'),
      '#attributes' => ['class' => ['js-text-full', 'text-full']],
    ];

    $element['info'] = [
      '#type' => 'hidden',
      "#default_value" => $items[$delta]->info,
    ];

    if ($items[$delta]->value) {
      $img = $items[$delta]->getImg($items[$delta]->getValue());
      $url = $img->getThumbnailUrl();

      $element['thumbnail'] = [
        '#theme' => 'image',
        '#uri' => $url,
        '#weight' => -1,
        '#attributes' => [
          'style' => "margin: 0 1rem 3rem 0",
        ],
        '#weight' => $element['value']['#weight'] - 0.1,
      ];

    }

    return $element;
  }

}
