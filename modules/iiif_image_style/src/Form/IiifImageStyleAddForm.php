<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * IIIF Image Style form.
 */
class IiifImageStyleAddForm extends IiifImageStyleFormBase {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->messenger()->addStatus($this->t('Style %name was created.', ['%name' => $this->entity->label()]));
  }

  /**
   * {@inheritdoc}
   */
  public function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Create new style');

    return $actions;
  }

}
