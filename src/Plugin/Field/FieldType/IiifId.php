<?php

namespace Drupal\iiif_media_source\Plugin\Field\FieldType;

use Drupal\Core\Field\Plugin\Field\FieldType\StringItem;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\ComplexDataDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\iiif_media_source\Event\IiiifGetImageFromFieldEvent;
use Drupal\iiif_media_source\Iiif\IiifImage;

/**
 * Defines the 'string' entity field type.
 *
 * @FieldType(
 *   id = "iiif_id",
 *   label = @Translation("IIIF ID field"),
 *   description = @Translation("A field containing a IIIF id"),
 *   category = @Translation("IIIF"),
 *   default_widget = "iiif_id_widget",
 *   default_formatter = "iiif_id_formatter"
 * )
 */
class IiifId extends StringItem {

  protected $dispatcher;

  /**
   * {@inheritdoc}
   */
  public function __construct(ComplexDataDefinitionInterface $definition, $name = NULL, TypedDataInterface $parent = NULL) {
    parent::__construct($definition, $name, $parent);

    $this->dispatcher = \Drupal::service('event_dispatcher');
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    $settings = parent::defaultFieldSettings();

    $settings['server'] = "";
    $settings['prefix'] = "";
    $settings['img_api_version'] = "2";

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::fieldSettingsForm($form, $form_state);

    $form['server'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Server'),
      '#default_value' => $this->getSetting('server'),
      '#description' => $this->t('IIIF Server, including scheme'),
      '#required' => TRUE,
    ];

    $form['prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prefix'),
      '#default_value' => $this->getSetting('prefix'),
      '#description' => $this->t('IIIF Prefix'),
      '#required' => TRUE,
    ];

    $form['img_api_version'] = [
      '#type' => 'select',
      '#title' => $this->t('IIIF Image API version'),
      '#default_value' => $this->getSetting('img_api_version'),
      '#description' => $this->t(''),
      '#required' => TRUE,
      '#options' => [
        2 => "v2.0",
        3 => "v3.0",
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);

    $schema['columns']['info'] = [
      'type' => 'text',
      'size' => 'big',
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['info'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('info'))
      ->setRequired(FALSE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    // ksm($values);

    // todo: double check this logic. Is it correct?
    if (isset($values['value']) && !empty($values['value']) && !isset($this->_image)) {
      // ksm('bob', $this);
      $img = $this->getImg($values); // new IiifImage($this->getSetting('server'), $this->getSetting('prefix'), $values['value']);
      // $this->_image = $img;
      // $this->width = $img->getWidth();
      // $this->height = $img->getHeight();

      $values['info'] = $img->getInfoEncoded();
    }

    parent::setValue($values, $notify);

  }

  public function getImg($values) {
    $info = new \stdClass();
    if (!empty($values['info'])) {
      $info = json_decode($values['info']);
    }

    $image = new IiifImage($this->getSetting('server'), $this->getSetting('prefix'), $values['value'], $info);

    // Dispatch Event.
    $event = new IiiifGetImageFromFieldEvent($this, $image, $values);
    $this->dispatcher->dispatch($event, IiiifGetImageFromFieldEvent::EVENT_NAME);

    return $image;
  }

  public function __get($name) {
    // // echo "Getting '$name'\n";

    // if (array_key_exists($name, $this->data)) {
    //     return $this->data[$name];
    // }

    // $trace = debug_backtrace();
    // trigger_error(
    //   'Undefined property via __get(): ' . $name .
    //   ' in ' . $trace[0]['file'] .
    //   ' on line ' . $trace[0]['line'],
    //   E_USER_NOTICE);
    // return null;

    if ($name == "width") {

      $img = $this->getImg($this->getValue());
      // ksm("Width", $img->getWidth());
      return $img->getWidth();
    }
    if ($name == "height") {
      $img = $this->getImg($this->getValue());
      // ksm("height", $img->getHeight());

      return $img->getHeight();
    }

    return parent::__get($name);
  }

}
