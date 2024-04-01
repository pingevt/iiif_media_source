<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\iiif_image_style\Entity\IiifImageStyle;
use Drupal\iiif_media_source\Iiif\IiifImage;

/**
 * IIIF Image Style form.
 */
final class IiifImageStyleForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    // ksm($this->entity);

    $form = parent::form($form, $form_state);

    $form['#tree'] = TRUE;

    $field_img_api_version = '2';

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->label(),
      '#required' => TRUE,
    ];

    $form['name'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#machine_name' => [
        'exists' => [IiifImageStyle::class, 'load'],
      ],
      '#disabled' => !$this->entity->isNew(),
    ];

    // Grab Style definition.
    $params = $this->entity->getParams();

    // Region.
    $form['params']['region'] = [
      '#type' => 'select',
      '#title' => $this->t('Region'),
      '#options' => IiifImage::getRegionOptions($field_img_api_version),
      '#default_value' => $params['region'] ?? "full",
      '#attributes' => [
        'data-states' => 'region',
      ]
    ];
    $form['params']['region_x'] = [
      '#title' => $this->t('x'),
      '#type' => 'number',
      '#default_value' => $params['region_x'] ?? NULL,
      '#description' => $this->t(''),
      '#states' => [
        'visible' => [
          ':input[data-states="region"]' => [
            ['value' => 'x,y,w,h'],
            'or',
            ['value' => 'pct:x,y,w,h'],
          ],
        ]
      ],
    ];
    $form['params']['region_y'] = [
      '#title' => $this->t('y'),
      '#type' => 'number',
      '#default_value' => $params['region_y'] ?? NULL,
      '#description' => $this->t(''),
      '#states' => [
        'visible' => [
          ':input[data-states="region"]' => [
            ['value' => 'x,y,w,h'],
            'or',
            ['value' => 'pct:x,y,w,h'],
          ],
        ]
      ],
    ];
    $form['params']['region_w'] = [
      '#title' => $this->t('w'),
      '#type' => 'number',
      '#default_value' => $params['region_w'] ?? NULL,
      '#description' => $this->t(''),
      '#states' => [
        'visible' => [
          ':input[data-states="region"]' => [
            ['value' => 'x,y,w,h'],
            'or',
            ['value' => 'pct:x,y,w,h'],
          ],
        ]
      ],
    ];
    $form['params']['region_h'] = [
      '#title' => $this->t('h'),
      '#type' => 'number',
      '#default_value' => $params['region_h'] ?? NULL,
      '#description' => $this->t(''),
      '#states' => [
        'visible' => [
          ':input[data-states="region"]' => [
            ['value' => 'x,y,w,h'],
            'or',
            ['value' => 'pct:x,y,w,h'],
          ],
        ]
      ],
    ];

    // Size.
    $form['params']['size'] = [
      '#type' => 'select',
      '#title' => $this->t('Size'),
      '#options' => IiifImage::getSizeOptions($field_img_api_version),
      '#default_value' => $params['size'] ?? "full",
      '#attributes' => [
        'data-states' => 'size',
      ]
    ];
    $form['params']['size_w'] = [
      '#title' => $this->t('w'),
      '#type' => 'number',
      '#default_value' => $params['size_w'] ?? NULL,
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
        ]
      ],
    ];
    $form['params']['size_h'] = [
      '#title' => $this->t('h'),
      '#type' => 'number',
      '#default_value' => $params['size_h'] ?? NULL,
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
        ]
      ],
    ];
    $form['params']['size_n'] = [
      '#title' => $this->t('n'),
      '#type' => 'number',
      '#default_value' => $params['size_n'] ?? NULL,
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
        ]
      ],
    ];

    // Rotation.
    $form['params']['rotation'] = [
      '#title' => $this->t('Rotation'),
      '#type' => 'number',
      '#default_value' => $params['rotation'] ?? 0,
      '#description' => $this->t(''),
      '#min' => 0,
      '#max' => 360,
      '#step' => 0.1,
    ];

    // Quality.
    $form['params']['quality'] = [
      '#type' => 'select',
      '#title' => $this->t('Quality'),
      '#options' => IiifImage::getQualityOptions($field_img_api_version),
      '#default_value' => $params['quality'] ?? "default",
      '#attributes' => [
        'data-states' => 'quality',
      ]
    ];

    // Format.
    $form['params']['format'] = [
      '#type' => 'select',
      '#title' => $this->t('Format'),
      '#options' => IiifImage::getFormatOptions($field_img_api_version),
      '#default_value' => $params['format'] ?? "jpg",
      '#attributes' => [
        'data-states' => 'format',
      ]
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);

    $values = $form_state->getValues();

    // ksm($values, $this->entity);
    // todo: this should be moved to the entity set() method.
    foreach ($values['params'] as $key => $value) {
      if (strpos($key, "region_") !== FALSE || strpos($key, "size_") !== FALSE || $key == "rotation") {
        $values['params'][$key] = empty($value) ? NULL : floatval($value);
      }
    }
    ksm($values['params']);

    $this->entity->set('params', $values['params']);
    ksm($this->entity);

    $message_args = ['%label' => $this->entity->label()];
    $this->messenger()->addStatus(
      match($result) {
        \SAVED_NEW => $this->t('Created new example %label.', $message_args),
        \SAVED_UPDATED => $this->t('Updated example %label.', $message_args),
      }
    );
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    return $result;
  }

}
