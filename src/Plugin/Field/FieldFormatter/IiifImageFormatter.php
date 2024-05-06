<?php

namespace Drupal\iiif_media_source\Plugin\Field\FieldFormatter;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

  // Protected $sizingOptions = [
  //   'full' => 'Full',
  //   'scale_and_crop' => "Scale and Crop",
  //   'resize' => "Resize",
  //   'scale' => "Scale",
  //   'crop' => "Crop",
  // ];.

  /**
   * The event Dispatcher.
   *
   * @var \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher
   */
  protected $eventDispatcher;

  /**
   * Constructs a StringFormatter instance.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher $event_dispatcher
   *   The event Dispatcher.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, EntityTypeManagerInterface $entity_type_manager, ContainerAwareEventDispatcher $event_dispatcher) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $entity_type_manager);

    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('event_dispatcher')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $options = parent::defaultSettings();

    $options += IiifImageUrlParams::getDefaultSettings();

    $options += [
      'image_loading' => [
        'attribute' => 'lazy',
      ],
    ];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $field_img_api_version = $this->fieldDefinition->getSettings()['img_api_version'];

    // Region.
    $form['region'] = [
      '#type' => 'select',
      '#title' => $this->t('Region'),
      '#options' => IiifImageUrlParams::getRegionOptions($field_img_api_version),
      '#default_value' => $this->getSetting('region'),
      '#attributes' => [
        'data-states' => 'region',
      ],
    ];
    $form['region_x'] = [
      '#title' => $this->t('x'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('region_x'),
      '#description' => $this->t(''),
      '#states' => [
        'visible' => [
          ':input[data-states="region"]' => [
            ['value' => 'x,y,w,h'],
            'or',
            ['value' => 'pct:x,y,w,h'],
          ],
        ],
      ],
    ];
    $form['region_y'] = [
      '#title' => $this->t('y'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('region_y'),
      '#description' => $this->t(''),
      '#states' => [
        'visible' => [
          ':input[data-states="region"]' => [
            ['value' => 'x,y,w,h'],
            'or',
            ['value' => 'pct:x,y,w,h'],
          ],
        ],
      ],
    ];
    $form['region_w'] = [
      '#title' => $this->t('w'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('region_w'),
      '#description' => $this->t(''),
      '#states' => [
        'visible' => [
          ':input[data-states="region"]' => [
            ['value' => 'x,y,w,h'],
            'or',
            ['value' => 'pct:x,y,w,h'],
          ],
        ],
      ],
    ];
    $form['region_h'] = [
      '#title' => $this->t('h'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('region_h'),
      '#description' => $this->t(''),
      '#states' => [
        'visible' => [
          ':input[data-states="region"]' => [
            ['value' => 'x,y,w,h'],
            'or',
            ['value' => 'pct:x,y,w,h'],
          ],
        ],
      ],
    ];

    // Size.
    $form['size'] = [
      '#type' => 'select',
      '#title' => $this->t('Size'),
      '#options' => IiifImageUrlParams::getSizeOptions($field_img_api_version),
      '#default_value' => $this->getSetting('size'),
      '#attributes' => [
        'data-states' => 'size',
      ],
    ];
    $form['size_w'] = [
      '#title' => $this->t('w'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('size_w'),
      '#description' => $this->t(''),
      '#states' => [
        'invisible' => [
          ':input[data-states="size"]' => [
            ['value' => 'max'],
            'or',
            ['value' => '^max'],
            'or',
            ['value' => 'full'],
            'or',
            ['value' => '^full'],
            'or',
            ['value' => 'pct:n'],
            'or',
            ['value' => '^pct:n'],
          ],
        ],
      ],
    ];
    $form['size_h'] = [
      '#title' => $this->t('h'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('size_h'),
      '#description' => $this->t(''),
      '#states' => [
        'invisible' => [
          ':input[data-states="size"]' => [
            ['value' => 'max'],
            'or',
            ['value' => '^max'],
            'or',
            ['value' => 'full'],
            'or',
            ['value' => '^full'],
            'or',
            ['value' => 'pct:n'],
            'or',
            ['value' => '^pct:n'],
          ],
        ],
      ],
    ];
    $form['size_n'] = [
      '#title' => $this->t('n'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('size_n'),
      '#description' => $this->t(''),
      '#min' => 0,
      '#max' => 100,
      '#step' => 0.1,
      '#states' => [
        'visible' => [
          ':input[data-states="size"]' => [
            ['value' => 'pct:n'],
            'or',
            ['value' => '^pct:n'],
          ],
        ],
      ],
    ];

    // Rotation.
    $form['rotation'] = [
      '#title' => $this->t('Rotation'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('rotation'),
      '#description' => $this->t(''),
      '#min' => 0,
      '#max' => 360,
      '#step' => 0.1,
    ];

    // Quality.
    $form['quality'] = [
      '#type' => 'select',
      '#title' => $this->t('Quality'),
      '#options' => IiifImageUrlParams::getQualityOptions($field_img_api_version),
      '#default_value' => $this->getSetting('quality'),
      '#attributes' => [
        'data-states' => 'quality',
      ],
    ];

    // Format.
    $form['format'] = [
      '#type' => 'select',
      '#title' => $this->t('Format'),
      '#options' => IiifImageUrlParams::getFormatOptions($field_img_api_version),
      '#default_value' => $this->getSetting('format'),
      '#attributes' => [
        'data-states' => 'format',
      ],
    ];

    // Image Loading.
    $image_loading = $this->getSetting('image_loading');
    $form['image_loading'] = [
      '#type' => 'details',
      '#title' => $this->t('Image loading'),
      '#weight' => 10,
      '#description' => $this->t('Lazy render images with native image loading attribute (<em>loading="lazy"</em>). This improves performance by allowing browsers to lazily load images.'),
    ];
    $loading_attribute_options = [
      'lazy' => $this->t('Lazy (<em>loading="lazy"</em>)'),
      'eager' => $this->t('Eager (<em>loading="eager"</em>)'),
    ];
    $form['image_loading']['attribute'] = [
      '#title' => $this->t('Image loading attribute'),
      '#type' => 'radios',
      '#default_value' => $image_loading['attribute'],
      '#options' => $loading_attribute_options,
      '#description' => $this->t('Select the loading attribute for images. <a href=":link">Learn more about the loading attribute for images.</a>', [
        ':link' => 'https://html.spec.whatwg.org/multipage/urls-and-fetching.html#lazy-loading-attributes',
      ]),
    ];
    $form['image_loading']['attribute']['lazy']['#description'] = $this->t('Delays loading the image until that section of the page is visible in the browser. When in doubt, lazy loading is recommended.');
    $form['image_loading']['attribute']['eager']['#description'] = $this->t('Force browsers to download an image as soon as possible. This is the browser default for legacy reasons. Only use this option when the image is always expected to render.');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $params = IiifImageUrlParams::fromSettingsArray($this->getSettings());

    $summary[] = $this->t('Region: @region', ['@region' => $params->getRegion()]);
    $summary[] = $this->t('Size: @size', ['@size' => $params->getSize()]);
    $summary[] = $this->t('Rotation: @rotation', ['@rotation' => $params->getRotation()]);
    $summary[] = $this->t('Quality: @quality', ['@quality' => $params->getQuality()]);
    $summary[] = $this->t('Format: @format', ['@format' => $params->getFormat()]);

    $image_loading = $this->getSetting('image_loading');
    $summary[] = $this->t('Image loading: @attribute', [
      '@attribute' => $image_loading['attribute'],
    ]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {

    $build = [];
    $image_loading = $this->getSetting('image_loading');

    foreach ($items as $delta => $item) {

      // Process settings.
      $params = IiifImageUrlParams::fromSettingsArray($this->getSettings());

      $view_value = [
        '#theme' => 'iiif_image',
        '#image' => $item->getImg($item->getValue()),
        '#url_params' => $params,
        '#attributes' => [
          'loading' => $image_loading['attribute'],
        ],
      ];
      $build[$delta] = $view_value;
    }

    return $build;
  }

}
