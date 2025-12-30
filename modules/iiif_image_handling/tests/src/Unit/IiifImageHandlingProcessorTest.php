<?php

declare(strict_types=1);

namespace Drupal\Tests\iiif_image_handling\Unit;

use Drupal\Core\Form\FormStateInterface;
use Drupal\iiif_image_handling\IiifImageHandlingProcessor;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\iiif_image_handling\IiifImageHandlingProcessor
 *
 * Unit tests for the IiifImageHandlingProcessor class.
 *
 * @group iiif_image_handling
 */
class IiifImageHandlingProcessorTest extends UnitTestCase {

  /**
   * Tests the buildElementCrop() method.
   *
   * @covers ::buildElementCrop
   */
  public function testBuildElementCrop(): void {
    $form_state = $this->createMock(FormStateInterface::class);

    $context = [
      'items' => [
        0 => $this->createMockItem(['value' => 'example_value']),
      ],
      'delta' => 0,
      'widget' => $this->createMockWidget([
        'iiif_image_crop' => [
          'crop_preview_image_style_size' => 500,
          'iiif_crop_offsets' => [0, 0, 100, 100], // Add the missing key here.
        ],
      ]),
    ];

    $element = [
      '#type' => 'textfield',
      'iiif_crop_offsets' => [0, 0, 100, 100], // Add the missing key here.
    ];

    $result = IiifImageHandlingProcessor::buildElementCrop($element, $form_state, $context);

    // Assert that the result contains the expected keys.
    $this->assertArrayHasKey('iiif_crop_preview', $result);
    $this->assertArrayHasKey('#iiif_crop', $result);
    $this->assertEquals("margin: 0 1rem 3rem 0", $result['iiif_crop_preview']['#attributes']['style']);
  }

  /**
   * Creates a mock item.
   */
  private function createMockItem(array $values) {
    $item = $this->getMockBuilder(\stdClass::class)
      ->addMethods(['getValue', 'isEmpty', 'getImg'])
      ->getMock();

    $item->method('getValue')->willReturn($values);
    $item->method('isEmpty')->willReturn(empty($values));
    $item->method('getImg')->willReturn($this->createMockImage());

    return $item;
  }

  /**
   * Creates a mock widget.
   */
  private function createMockWidget(array $settings) {
    $widget = $this->getMockBuilder(\stdClass::class)
      ->addMethods(['getThirdPartySettings', 'getSettings'])
      ->getMock();

    $widget->method('getThirdPartySettings')->willReturn($settings);
    $widget->method('getSettings')->willReturn($settings);

    return $widget;
  }

  /**
   * Creates a mock image.
   */
  private function createMockImage() {
    $image = $this->getMockBuilder(\stdClass::class)
      ->addMethods(['getFullUrl', 'getWidth', 'getHeight', 'getScaledUrl'])
      ->getMock();

    $image->method('getFullUrl')->willReturn('http://example.com/image.jpg');
    $image->method('getWidth')->willReturn(1000);
    $image->method('getHeight')->willReturn(800);
    $image->method('getScaledUrl')->willReturn('http://example.com/scaled-image.jpg');

    return $image;
  }

}
