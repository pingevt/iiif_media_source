<?php

namespace Drupal\Tests\iiif_media_source\Functional;

use Drupal\media\Entity\MediaType;
use Drupal\Tests\BrowserTestBase;

/**
 * Functional tests for the IIIF Image Media Source.
 *
 * This test covers:
 * - Creating a IIIF Image media type via the UI.
 * - Configuring the source field and its display.
 * - Creating a media entity using the IIIF source.
 * - Asserting that the entity is created and displayed as expected.
 *
 * @group iiif_media_source
 */
class IiifImageMediaSourceFunctionalTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   *
   * Use the Claro admin theme for UI consistency.
   */
  protected $defaultTheme = 'claro';

  /**
   * {@inheritdoc}
   *
   * Disable strict config schema checking for custom config.
   */
  protected $strictConfigSchema = FALSE;

  /**
   * {@inheritdoc}
   *
   * Required modules for the test.
   */
  protected static $modules = [
    'node',
    'user',
    'field',
    'field_ui',
    'file',
    'image',
    'media',
    'iiif_media_source',
    // Add any other dependencies your test needs.
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
  }

  /**
   * Tests that the IIIF media source configuration form appears and validates.
   */
  // public function __testMediaSourceConfigForm() {
  //   // Log in as admin.
  //   $this->drupalLogin($this->rootUser);.
  // // Go to the media type creation form.
  //   $this->drupalGet('admin/structure/media/add');
  //   $this->assertSession()->statusCodeEquals(200);
  // // Fill in the form to create a new IIIF Image media type.
  //   $label = 'IIIF Image';
  //   $machine_name = 'iiif_image';
  //   $this->assertSession()->fieldExists('label')->setValue($label);
  //   $this->assertSession()->fieldExists('id')->setValue($machine_name);
  //   $this->assertSession()->selectExists('source')->selectOption('IIIF External Image'); // Use the label of your plugin.
  // // Save and manage fields.
  //   $this->assertSession()->buttonExists('Save and manage fields')->press();
  //   $this->assertSession()->statusCodeEquals(200);
  // $this->assertSession()->buttonExists('Save and manage fields')->press();
  //   $this->assertSession()->statusCodeEquals(200);
  //   $this->assertSession()->addressEquals("admin/structure/media/manage/$machine_name/fields");
  // $this->assertInstanceOf(MediaType::class, MediaType::load($machine_name));
  // $this->drupalGet('admin/structure/media');
  // // Now go to the edit form for the media type.
  //   $this->drupalGet("admin/structure/media/manage/$machine_name");
  //   $this->assertSession()->statusCodeEquals(200);
  // // Assert the thumbnails directory field is present.
  //   $this->assertSession()->fieldExists('source_configuration[thumbnails_directory]');
  // // Submit an invalid value and check for validation error.
  //   $edit = ['source_configuration[thumbnails_directory]' => 'invalid://bad*path'];
  // $this->assertSession()->fieldExists('source_configuration[thumbnails_directory]')->setValue('invalid://bad*path');
  //   $this->assertSession()->buttonExists('Save')->press();
  //   $this->assertSession()->statusCodeEquals(200);
  //   $this->assertSession()->pageTextContains('is not a valid path.');
  // }.

  /**
   * Tests creating a IIIF Image media entity via the UI.
   *
   * Steps:
   * - Enables the "Standalone media URL" setting.
   * - Creates the IIIF Image media type.
   * - Configures the IIIF source field settings.
   * - Creates a media entity using the IIIF source.
   * - Asserts that the entity is created and visible.
   */
  public function testCreateIiifMediaEntity() {
    $this->drupalLogin($this->rootUser);

    // Enable the "Standalone media URL" setting.
    $this->drupalGet('admin/config/media/media-settings');
    $this->assertSession()->fieldExists('Standalone media URL')->check();
    $this->assertSession()->buttonExists('Save configuration')->press();

    // Go to the media admin page.
    $this->drupalGet('admin/structure/media');

    // Create the IIIF Image media type.
    $this->createIiifImageMediaType();

    // Configure the IIIF source field settings and display.
    $this->drupalGet('admin/structure/media');
    $this->updateIiifIdFieldSettings('iiif_image', 'https://iiif-10-5.ddev.site:8182', 'iiif/3');

    // Go to the add media form for IIIF Image.
    $this->drupalGet('media/add/iiif_image');
    $this->assertSession()->statusCodeEquals(200);

    // Fill in the IIIF ID field and create the media entity.
    $this->assertSession()->fieldExists('name[0][value]')->setValue('Test Image 1');
    $this->assertSession()->fieldExists('field_media_iiif_id[0][value]')->setValue('sample.png');

    // Save the media entity.
    $this->assertSession()->buttonExists('Save')->press();
    $this->assertSession()->statusCodeEquals(200);

    // Assert that the media entity was created and is visible.
    $this->assertSession()->pageTextContains('IIIF Image');
    $this->assertSession()->linkExists('Test Image 1');
    $this->clickLink('Test Image 1');
  }

  /**
   * Creates a IIIF Image media type via the UI.
   *
   * @param string $label
   *   The label for the media type.
   * @param string $machine_name
   *   The machine name for the media type.
   */
  private function createIiifImageMediaType($label = 'IIIF Image', $machine_name = 'iiif_image') {
    // Go to the media type creation form.
    $this->drupalGet('admin/structure/media/add');
    $this->assertSession()->statusCodeEquals(200);

    // Fill in the form to create a new IIIF Image media type.
    $this->assertSession()->fieldExists('label')->setValue($label);
    $this->assertSession()->fieldExists('id')->setValue($machine_name);
    // Use your plugin label.
    $this->assertSession()->selectExists('source')->selectOption('IIIF External Image');

    // Save and manage fields.
    $this->assertSession()->buttonExists('Save and manage fields')->press();
    $this->assertSession()->statusCodeEquals(200);

    $this->assertSession()->buttonExists('Save and manage fields')->press();
    $this->assertSession()->statusCodeEquals(200);

    // Optionally, verify the media type was created.
    $this->assertInstanceOf(MediaType::class, MediaType::load($machine_name));
  }

  /**
   * Updates the IIIF ID source field settings and display for the media type.
   *
   * @param string $machine_name
   *   The media type machine name.
   * @param string $server
   *   The IIIF server URL.
   * @param string $prefix
   *   The IIIF prefix.
   */
  private function updateIiifIdFieldSettings($machine_name = 'iiif_image', $server = 'https://iiif-10-5.ddev.site:8182', $prefix = 'iiif/3') {
    // Go to the field settings form for the source field.
    $this->drupalGet("admin/structure/media/manage/$machine_name/fields");
    $this->assertSession()->statusCodeEquals(200);
    $this->drupalGet("admin/structure/media/manage/$machine_name/fields/media.$machine_name.field_media_iiif_id");
    $this->assertSession()->statusCodeEquals(200);

    // Set the server and prefix.
    $this->assertSession()->fieldExists('settings[server]')->setValue($server);
    $this->assertSession()->fieldExists('settings[prefix]')->setValue($prefix);

    // Save the field settings.
    $this->assertSession()->buttonExists('Save settings')->press();
    $this->assertSession()->statusCodeEquals(200);

    // Go to the "Manage display" page for the media type.
    $this->drupalGet("admin/structure/media/manage/$machine_name/display");
    $this->assertSession()->statusCodeEquals(200);

    // Change the formatter for the source field (e.g., to "IIIF image").
    $this->assertSession()->selectExists('fields[field_media_iiif_id][type]')
      ->selectOption('iiif_image_formatter');

    // Optionally, set the label display (e.g., "Above", "Inline", "Hidden").
    $this->assertSession()->selectExists('fields[field_media_iiif_id][label]')
      ->selectOption('hidden');

    // Save the display settings.
    $this->assertSession()->buttonExists('Save')->press();
    $this->assertSession()->statusCodeEquals(200);
  }

}
