<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;

/**
 * IIIF Responsive Image formatter.
 *
 * @FieldFormatter(
 *   id = "iiif_responsive_image_style_formatter",
 *   label = @Translation("IIIF Responsive Image Style Formatter"),
 *   field_types = {
 *     "iiif_id"
 *   }
 * )
 */
class IiifResponsiveImageStyleFormatter extends StringFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'responsive_image_style' => '',
      'image_loading' => [
        'attribute' => 'lazy',
      ],
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   *
   * Builds the settings form for the formatter, including responsive image style
   * and image loading options.
   *
   * @param array $form
   *   The form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The settings form elements.
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element = parent::settingsForm($form, $form_state);

    $responsive_image_styles = iiif_responsive_image_style_options(FALSE);

    $element['responsive_image_style'] = [
      '#title' => $this->t('Image style'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('responsive_image_style'),
      '#empty_option' => $this->t('None (original image)'),
      '#options' => $responsive_image_styles,
    ];

    // Image loading setting.
    $image_loading = $this->getSetting('image_loading');
    $element['image_loading'] = [
      '#type' => 'details',
      '#title' => $this->t('Image loading'),
      '#weight' => 10,
      '#description' => $this->t('Lazy render images with native image loading attribute (<em>loading="lazy"</em>). This improves performance by allowing browsers to lazily load images.'),
    ];
    $loading_attribute_options = [
      'lazy' => $this->t('Lazy (<em>loading="lazy"</em>)'),
      'eager' => $this->t('Eager (<em>loading="eager"</em>)'),
    ];
    $element['image_loading']['attribute'] = [
      '#title' => $this->t('Image loading attribute'),
      '#type' => 'radios',
      '#default_value' => $image_loading['attribute'],
      '#options' => $loading_attribute_options,
      '#description' => $this->t('Select the loading attribute for images. <a href=":link">Learn more about the loading attribute for images.</a>', [
        ':link' => 'https://html.spec.whatwg.org/multipage/urls-and-fetching.html#lazy-loading-attributes',
      ]),
    ];
    $element['image_loading']['attribute']['lazy']['#description'] = $this->t('Delays loading the image until that section of the page is visible in the browser. When in doubt, lazy loading is recommended.');
    $element['image_loading']['attribute']['eager']['#description'] = $this->t('Force browsers to download an image as soon as possible. This is the browser default for legacy reasons. Only use this option when the image is always expected to render.');

    return $element;
  }

  /**
   * {@inheritdoc}
   *
   * Provides a summary of the formatter settings.
   *
   * @return array
   *   An array of summary strings.
   */
  public function settingsSummary(): array {
    $summary = [];
    $responsive_image_styles = iiif_responsive_image_style_options(FALSE);
    unset($responsive_image_styles['']);

    $responsive_image_style_setting = $this->getSetting('responsive_image_style');
    if (isset($responsive_image_styles[$responsive_image_style_setting])) {
      $summary[] = $this->t('Image style: @style', ['@style' => $responsive_image_styles[$responsive_image_style_setting]]);
    }
    else {
      $summary[] = $this->t('Original image');
    }

    $link_types = [
      'content' => $this->t('Linked to content'),
      'file' => $this->t('Linked to file'),
    ];
    $image_link_setting = $this->getSetting('image_link');
    if (isset($link_types[$image_link_setting])) {
      $summary[] = $link_types[$image_link_setting];
    }

    $image_loading = $this->getSetting('image_loading');
    $summary[] = $this->t('Image loading: @attribute', [
      '@attribute' => $image_loading['attribute'],
    ]);

    return array_merge($summary, parent::settingsSummary());
  }

  /**
   * {@inheritdoc}
   *
   * Builds the render array for the IIIF responsive image field items.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The field items to render.
   * @param string|null $langcode
   *   The language code to use for rendering, or NULL for default.
   *
   * @return array
   *   A render array for the field items.
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $image_loading = $this->getSetting('image_loading');
    $attribute = $image_loading['attribute'] ?? 'lazy';

    foreach ($items as $delta => $item) {
      $view_value = [
        '#theme' => 'iiif_responsive_image_style',
        '#item' => $item,
        '#image' => $item->getImg($item->getValue()),
        '#iiif_responsive_image_style' => $this->getSetting('responsive_image_style'),
        '#attributes' => [
          'loading' => $attribute,
        ],
      ];
      $elements[$delta] = $view_value;
    }

    return $elements;
  }

}
