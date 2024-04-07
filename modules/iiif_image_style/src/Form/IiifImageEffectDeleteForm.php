<?php

namespace Drupal\iiif_image_style\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\iiif_image_style\IiifImageStyleInterface;

/**
 * Form for deleting an image effect.
 *
 * @internal
 */
class IiifImageEffectDeleteForm extends ConfirmFormBase {

  /**
   * The image style containing the image effect to be deleted.
   *
   * @var \Drupal\iiif_image_style\IiifImageStyleInterface
   */
  protected $imageStyle;

  /**
   * The image effect to be deleted.
   *
   * @var \Drupal\iiif_image_style\IiifImageEffectInterface
   */
  protected $imageEffect;

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the @effect effect from the %style style?', ['%style' => $this->imageStyle->label(), '@effect' => $this->imageEffect->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->imageStyle->toUrl('edit-form');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'image_effect_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, IiifImageStyleInterface $iiif_image_style = NULL, $image_effect = NULL) {
    $this->imageStyle = $iiif_image_style;
    $this->imageEffect = $this->imageStyle->getEffect($iiif_image_style);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->imageStyle->deleteImageEffect($this->imageEffect);
    $this->messenger()->addStatus($this->t('The image effect %name has been deleted.', ['%name' => $this->imageEffect->label()]));
    $form_state->setRedirectUrl($this->imageStyle->toUrl('edit-form'));
  }

}
