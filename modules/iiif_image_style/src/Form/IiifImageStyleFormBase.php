<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * IIIF Image Style form.
 */
class IiifImageStyleFormBase extends EntityForm {
  /**
   * The entity being used by this form.
   *
   * @var \Drupal\iiif_image_style\Entity\IiifImageStyle
   */
  protected $entity;

  /**
   * The image style entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $imageStyleStorage;

  /**
   * Constructs a base class for image style add and edit forms.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $image_style_storage
   *   The image style entity storage.
   */
  public function __construct(EntityStorageInterface $image_style_storage) {
    $this->imageStyleStorage = $image_style_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('iiif_image_style')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Image style name'),
      '#default_value' => $this->entity->label(),
      '#required' => TRUE,
    ];
    $form['name'] = [
      '#type' => 'machine_name',
      '#machine_name' => [
        'exists' => [$this->imageStyleStorage, 'load'],
      ],
      '#default_value' => $this->entity->id(),
      '#required' => TRUE,
    ];

    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    $form_state->setRedirectUrl($this->entity->toUrl('edit-form'));

    return TRUE;
  }

}
