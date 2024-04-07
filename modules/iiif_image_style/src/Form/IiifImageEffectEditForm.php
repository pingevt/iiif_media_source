<?php

namespace Drupal\iiif_image_style\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\iiif_image_style\IiifImageStyleInterface;

/**
 * Provides an edit form for image effects.
 *
 * @internal
 */
class IiifImageEffectEditForm extends IiifImageEffectFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, IiifImageStyleInterface $iiif_image_style = NULL, $image_effect = NULL) {
    $form = parent::buildForm($form, $form_state, $iiif_image_style, $image_effect);

    $form['#title'] = $this->t('Edit %label effect on style %style', [
      '%label' => $this->imageEffect->label(),
      '%style' => $iiif_image_style->label(),
    ]);
    $form['actions']['submit']['#value'] = $this->t('Update effect');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareImageEffect($image_effect) {
    return $this->imageStyle->getEffect($image_effect);
  }

}
