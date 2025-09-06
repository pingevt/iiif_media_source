<?php
// phpcs:ignorefile

/**
 * Test basic IIIF Field Formatters.
 *
 * Example Images:
 * https://media.nga.gov/iiif/at3_1m4_10.tif/full/full/0/default.jpg
 * https://media.nga.gov/iiif/3a81bf90-a961-468d-ae12-1d981e11247a/full/full/0/default.jpg
 * https://media.nga.gov/iiif/7a4bba6e-7c21-42fe-bdd5-7ba7bdeffd16/full/full/0/default.jpg
 * https://media.nga.gov/iiif/fdfa01c4-7334-4a34-a1fa-64429773e96e/full/full/0/default.jpg
 * https://media.nga.gov/iiif/ab18bc0e-2b0e-48db-bf7a-d57f24f9c6a3/full/full/0/default.jpg
 * https://media.nga.gov/iiif/866a1a6c-2e3e-452c-ad91-5b2339e56da2/full/full/0/default.jpg
 */

namespace Drupal\Tests\iiif_media_source\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test basic IIIF Field Formatters.
 *
 * @group iiif_media_source
 */
class FieldFormatterTestWip extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'claro';

  /**
   * A user with administration rights.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * An authenticated user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $authenticatedUser;

  /**
   * A test node.
   *
   * @var \Drupal\node\Entity\Node
   */
  protected $testNode;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'media',
    // 'node',
    'user',
    // 'path',
    // 'field',
    // 'text',
    // 'file',
    // 'image',
    'field_ui',
    // 'iiif_media_source',
    'iiif_media_source_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'access administration pages',
      'access content',
    ], "Bob", TRUE);
    $this->authenticatedUser = $this->drupalCreateUser([
      'access content',
      'bypass node access',
    ]);

    $this->testNode = $this->drupalCreateNode([
      'type' => 'iiif_test',
      'status' => 1,
      'title' => "Test Node",
      'field_iiif_test_1' => 'at3_1m4_10.tif',
      'field_iiif_test_2' => 'at3_1m4_09.tif',
      'field_iiif_test_3' => 'at3_1m4_08.tif',
      'field_iiif_test_4' => 'at3_1m4_07.tif',
      'field_iiif_test_5' => 'at3_1m4_06.tif',
    ]);
  }

  /**
   * Test loading attribute for IIIF Image Formatter.
   */
  public function testLoadingAttribute() {

    // Goto the Test Node detail page.
    $session = $this->assertSession();
    $this->assertTrue(TRUE);

    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/structure/types');
    $session->statusCodeEquals(200);

    // Form Display Settings.
    $this->drupalGet('admin/structure/types/manage/iiif_test/form-display');
    $session->statusCodeEquals(200);

    $this->drupalGet('node/' . $this->testNode->id() . "/edit");
    $session->statusCodeEquals(200);

    $this->drupalGet('node/' . $this->testNode->id());
    $session->statusCodeEquals(200);
    $session->responseContains('<div class="field__item">at3_1m4_10.tif</div>');
    // $session->responseContains('https://media.nga.gov/iiif/3a81bf90-a961-468d-ae12-1d981e11247a/full/full/0/default.png');
    // $session->responseContains('<img loading="eager" src="https://media.nga.gov/iiif/7a4bba6e-7c21-42fe-bdd5-7ba7bdeffd16/full/600,/0/default.png" ');
    // $session->responseContains('https://media.nga.gov/iiif/fdfa01c4-7334-4a34-a1fa-64429773e96e/full/max/0/default.png', "img url");
    // $session->responseContains('<img loading="lazy" src="https://media.nga.gov/iiif/fdfa01c4-7334-4a34-a1fa-64429773e96e/full/max/0/default.png" ', "img tag");
    // $session->responseContains('https://media.nga.gov/iiif/3a81bf90-a961-468d-ae12-1d981e11247a/full/full/0/default.png');

    // View Display Settings.
    $this->drupalGet('admin/structure/types/manage/iiif_test/display');
    $session->statusCodeEquals(200);

    // Click Gear, to change field display settings.
    $this->click('input[name="field_iiif_test_4_settings_edit"]');

    $form_html_id = "entity-view-display-edit-form";
    $form = $form = $session->elementExists('xpath', "//form[@id='{$form_html_id}']");
    $field_name = "fields[field_iiif_test_4][settings_edit_form][settings][format]";
    $field = $session->fieldExists($field_name, $form);
    $field->setValue("jpg");

    // $this->click('input[name="field_iiif_test_4_plugin_settings_update"]');
    $this->click('input[name="op"][value="Save"]');



    $this->drupalGet('node/' . $this->testNode->id());
    $session->statusCodeEquals(200);

    // $session->responseContains('<img loading="lazy" src="https://media.nga.gov/iiif/fdfa01c4-7334-4a34-a1fa-64429773e96e/full/full/0/default.jpg" ');
  }

}
