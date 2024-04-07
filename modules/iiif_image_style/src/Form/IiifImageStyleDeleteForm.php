<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\iiif_image_style\Entity\IiifImageStyle;
use Drupal\iiif_media_source\Iiif\IiifImage;

/**
 * IIIF Image Style delete form.
 */
final class IiifImageStyleDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {

    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    parent::submitForm($form, $form_state);
  }

}
