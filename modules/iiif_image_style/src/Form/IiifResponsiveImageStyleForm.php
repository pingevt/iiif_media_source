<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Form;

use Drupal\breakpoint\BreakpointManagerInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\iiif_image_style\Entity\IiifResponsiveImageStyle;
use Drupal\iiif_image_style\IiifResponsiveImageStyleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * IIIF Responsive Image Style form.
 */
final class IiifResponsiveImageStyleForm extends EntityForm {

  /**
   * The breakpoint manager.
   *
   * @var \Drupal\breakpoint\BreakpointManagerInterface
   */
  protected $breakpointManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('breakpoint.manager')
    );
  }

  /**
   * Constructs the responsive image style form.
   *
   * @param \Drupal\breakpoint\BreakpointManagerInterface $breakpoint_manager
   *   The breakpoint manager.
   */
  public function __construct(BreakpointManagerInterface $breakpoint_manager) {
    $this->breakpointManager = $breakpoint_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    if ($this->operation == 'duplicate') {
      $form['#title'] = $this->t('<em>Duplicate responsive image style</em> @label', ['@label' => $this->entity->label()]);
      $this->entity = $this->entity->createDuplicate();
    }
    if ($this->operation == 'edit') {
      $form['#title'] = $this->t('<em>Edit responsive image style</em> @label', ['@label' => $this->entity->label()]);
    }

    $form = parent::form($form, $form_state);

    /** @var \Drupal\iiif_image_style\Entity\IiifResponsiveImageStyle $responsive_image_style */
    $responsive_image_style = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $responsive_image_style->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $responsive_image_style->id(),
      '#machine_name' => [
        'exists' => [IiifResponsiveImageStyle::class, 'load'],
      ],
      '#disabled' => !$responsive_image_style->isNew(),
    ];

    // @todo should this be editable? I don't think so.
    // $form['status'] = [
    //   '#type' => 'checkbox',
    //   '#title' => $this->t('Enabled'),
    //   '#default_value' => TRUE,
    // ];.
    $image_styles = iiif_image_style_options(TRUE);

    $image_styles[IiifResponsiveImageStyleInterface::ORIGINAL_IMAGE] = $this->t('- None (original image) -');
    $image_styles[IiifResponsiveImageStyleInterface::EMPTY_IMAGE] = $this->t('- empty image -');

    if ((bool) $responsive_image_style->id() && $this->operation != 'duplicate') {
      $description = $this->t('Select a breakpoint group from the installed themes and modules. Below you can select which breakpoints to use from this group. You can also select which image style or styles to use for each breakpoint you use.') . ' ' . $this->t("Warning: if you change the breakpoint group you lose all your image style selections for each breakpoint.");
    }
    else {
      $description = $this->t('Select a breakpoint group from the installed themes and modules.');
    }

    $form['breakpoint_group'] = [
      '#type' => 'select',
      '#title' => $this->t('Breakpoint group'),
      '#default_value' => $responsive_image_style->getBreakpointGroup() ?: 'responsive_image',
      '#options' => $this->breakpointManager->getGroups(),
      '#required' => TRUE,
      '#description' => $description,
      '#ajax' => [
        'callback' => '::breakpointMappingFormAjax',
        'wrapper' => 'responsive-image-style-breakpoints-wrapper',
      ],
    ];

    $form['keyed_styles'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'responsive-image-style-breakpoints-wrapper',
      ],
    ];

    // By default, breakpoints are ordered from smallest weight to largest:
    // the smallest weight is expected to have the smallest breakpoint width,
    // while the largest weight is expected to have the largest breakpoint
    // width. For responsive images, we need largest breakpoint widths first, so
    // we need to reverse the order of these breakpoints.
    $breakpoints = array_reverse($this->breakpointManager->getBreakpointsByGroup($responsive_image_style->getBreakpointGroup()));

    foreach ($breakpoints as $breakpoint_id => $breakpoint) {
      foreach ($breakpoint->getMultipliers() as $multiplier) {
        $label = $multiplier . ' ' . $breakpoint->getLabel() . ' [' . $breakpoint->getMediaQuery() . ']';
        $form['keyed_styles'][$breakpoint_id][$multiplier] = [
          '#type' => 'details',
          '#title' => $label,
        ];
        $image_style_mapping = $responsive_image_style->getImageStyleMapping($breakpoint_id, $multiplier);
        // If (\Drupal::moduleHandler()->moduleExists('help')) {
        //   $description = $this->t('See the <a href=":responsive_image_help">Responsive Image help page</a> for information on the sizes attribute.', [':responsive_image_help' => Url::fromRoute('help.page', ['name' => 'responsive_image'])->toString()]);
        // }
        // else {.
        $description = $this->t('Enable the Help module for more information on the sizes attribute.');
        // }
        $form['keyed_styles'][$breakpoint_id][$multiplier]['image_mapping_type'] = [
          '#title' => $this->t('Type'),
          '#type' => 'radios',
          '#options' => [
            'sizes' => $this->t('Select multiple image styles and use the sizes attribute.'),
            'image_style' => $this->t('Select a single image style.'),
            '_none' => $this->t('Do not use this breakpoint.'),
          ],
          '#default_value' => $image_style_mapping['image_mapping_type'] ?? '_none',
          '#description' => $description,
        ];
        $form['keyed_styles'][$breakpoint_id][$multiplier]['image_style'] = [
          '#type' => 'select',
          '#title' => $this->t('Image style'),
          '#options' => $image_styles,
          '#default_value' => isset($image_style_mapping['image_mapping']) && is_string($image_style_mapping['image_mapping']) ? $image_style_mapping['image_mapping'] : '',
          '#description' => $this->t('Select an image style for this breakpoint.'),
          '#states' => [
            'visible' => [
              ':input[name="keyed_styles[' . $breakpoint_id . '][' . $multiplier . '][image_mapping_type]"]' => ['value' => 'image_style'],
            ],
          ],
        ];
        $form['keyed_styles'][$breakpoint_id][$multiplier]['sizes'] = [
          '#type' => 'textarea',
          '#title' => $this->t('Sizes'),
          '#default_value' => $image_style_mapping['image_mapping']['sizes'] ?? '100vw',
          '#description' => $this->t('Enter the value for the sizes attribute, for example: %example_sizes.', ['%example_sizes' => '(min-width:700px) 700px, 100vw']),
          '#states' => [
            'visible' => [
              ':input[name="keyed_styles[' . $breakpoint_id . '][' . $multiplier . '][image_mapping_type]"]' => ['value' => 'sizes'],
            ],
            'required' => [
              ':input[name="keyed_styles[' . $breakpoint_id . '][' . $multiplier . '][image_mapping_type]"]' => ['value' => 'sizes'],
            ],
          ],
        ];
        $form['keyed_styles'][$breakpoint_id][$multiplier]['sizes_image_styles'] = [
          '#title' => $this->t('Image styles'),
          '#type' => 'checkboxes',
          '#options' => array_diff_key($image_styles, ['' => '']),
          '#description' => $this->t('Select image styles with widths that range from the smallest amount of space this image will take up in the layout to the largest, bearing in mind that high resolution screens will need images 1.5x to 2x larger.'),
          '#default_value' => $image_style_mapping['image_mapping']['sizes_image_styles'] ?? [],
          '#states' => [
            'visible' => [
              ':input[name="keyed_styles[' . $breakpoint_id . '][' . $multiplier . '][image_mapping_type]"]' => ['value' => 'sizes'],
            ],
            'required' => [
              ':input[name="keyed_styles[' . $breakpoint_id . '][' . $multiplier . '][image_mapping_type]"]' => ['value' => 'sizes'],
            ],
          ],
        ];

        // Expand the details if "do not use this breakpoint" was not selected.
        if ($form['keyed_styles'][$breakpoint_id][$multiplier]['image_mapping_type']['#default_value'] != '_none') {
          $form['keyed_styles'][$breakpoint_id][$multiplier]['#open'] = TRUE;
        }
      }
    }

    $form['fallback_image_style'] = [
      '#title' => $this->t('Fallback image style'),
      '#type' => 'select',
      '#default_value' => $responsive_image_style->getFallbackImageStyle(),
      '#options' => $image_styles,
      '#required' => TRUE,
      '#description' => $this->t('Select the image style you wish to use as the style when a browser does not support responsive images.'),
    ];

    $form['#tree'] = TRUE;

    return parent::form($form, $form_state);
  }

  /**
   * Get the form for mapping breakpoints to image styles.
   */
  public function breakpointMappingFormAjax($form, FormStateInterface $form_state) {
    return $form['keyed_styles'];
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\iiif_image_style\IiifResponsiveImageStyleInterface $responsive_image_style */
    $responsive_image_style = $this->entity;

    // Remove all the existing mappings and replace with submitted values.
    $responsive_image_style->removeImageStyleMappings();
    if ($form_state->hasValue('keyed_styles')) {
      foreach ($form_state->getValue('keyed_styles') as $breakpoint_id => $multipliers) {
        foreach ($multipliers as $multiplier => $image_style_mapping) {
          if ($image_style_mapping['image_mapping_type'] === 'sizes') {
            $mapping = [
              'image_mapping_type' => 'sizes',
              'image_mapping' => [
                'sizes' => $image_style_mapping['sizes'],
                'sizes_image_styles' => array_keys(array_filter($image_style_mapping['sizes_image_styles'])),
              ],
            ];
            $responsive_image_style->addImageStyleMapping($breakpoint_id, $multiplier, $mapping);
          }
          elseif ($image_style_mapping['image_mapping_type'] === 'image_style') {
            $mapping = [
              'image_mapping_type' => 'image_style',
              'image_mapping' => $image_style_mapping['image_style'],
            ];
            $responsive_image_style->addImageStyleMapping($breakpoint_id, $multiplier, $mapping);
          }
        }
      }
    }
    $responsive_image_style->save();

    $this->logger('responsive_image')->notice('Responsive image style @label saved.', ['@label' => $responsive_image_style->label()]);
    $this->messenger()->addStatus($this->t('Responsive image style %label saved.', ['%label' => $responsive_image_style->label()]));

    // Redirect to edit form after creating a new responsive image style or
    // after selecting another breakpoint group.
    if (!$responsive_image_style->hasImageStyleMappings()) {
      $form_state->setRedirect(
        'entity.responsive_image_style.edit_form',
        ['responsive_image_style' => $responsive_image_style->id()]
      );
    }
    else {
      $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    }

    return TRUE;
  }

}
