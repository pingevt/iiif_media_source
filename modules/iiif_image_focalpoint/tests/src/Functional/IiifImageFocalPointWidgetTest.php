<?php

namespace Drupal\Tests\iiif_image_focalpoint\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\WebDriverTestBase;

/**
 * Functional tests for the IIIF Image Focal Point Widget.
 *
 * @group iiif_image_focalpoint
 */
class IiifImageFocalPointWidgetTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'claro';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field',
    'field_ui',
    'user',
    'iiif_image_focalpoint',
    'iiif_media_source',
  ];

  /**
   * A user with permission to create and edit content.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $webUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create the content type first.
    $this->drupalCreateContentType(['type' => 'article', 'name' => 'Article']);

    // Create a user with permissions to create and edit content.
    // $this->webUser = $this->drupalCreateUser([
    //   'administer content types',
    //   'administer nodes',
    //   'create article content',
    //   'edit any article content',
    // ]);

    $this->webUser = $this->drupalCreateUser([], NULL, TRUE); // TRUE assigns the admin role.
    $this->drupalLogin($this->webUser);

    // Add the IIIF Image Focal Point field to the content type.
    $this->addFieldToContentType();
  }

  /**
   * Adds the IIIF Image Focal Point field to the content type.
   */
  protected function addFieldToContentType(): void {
    $this->drupalGet('admin/structure/types/manage/article/');
    $this->drupalGet('admin/structure/types/manage/article/fields');
    $this->drupalGet('admin/structure/types/manage/article/fields/add-field');
    $this->submitForm([
      'new_storage_type' => 'iiif',
      // 'label' => 'IIIF Media',
    ], 'Continue');
    $this->submitForm([
      'group_field_options_wrapper' => 'iiif_id',
      'field_name' => 'iiif_id',
      'label' => 'IIIF Media',
    ], 'Continue');
    $this->submitForm([
      'settings[server]' => 'http://example.com/iiif',
      'settings[prefix]' => '/image/',
      'settings[img_api_version]' => '3',
    ], 'Save settings');

    $this->drupalGet('admin/structure/types/manage/article/form-display');

    $html = $this->getSession()->getPage()->getHtml();
$p = file_put_contents(getcwd() . '/debug.html', $html);

echo getcwd();

if ($p !== false) {
    echo "Wrote debug.html\n";
} else {
    echo "Failed to write debug.html\n";
}

    // $this->submitForm([
    //   'fields[field_iiif_id][type]' => 'iiif_image_widget',
    //   // 'field_name' => 'iiif_id',
    //   // 'label' => 'IIIF Media',
    // ], 'Save');

    // $this->assertSession()->waitForElementVisible('css', '#edit-fields-title-type');
    // sleep(2);
    // $this->assertSession()->waitForField('fields[title][type]');
    // $this->drupalGet('admin/structure/types/manage/article/form-display');
    $this->assertSession()->fieldExists('fields[title][type]');
    $this->assertSession()->fieldExists('edit-fields-title-settings-edit');
    $this->assertSession()->fieldExists('title_settings_edit');

    // $this->getSession()->getPage()->findField('edit-fields-field-iiif-id-settings-edit')->click();


  }

  /**
   * Tests that the widget renders correctly.
   */
  public function testWidgetRendering(): void {
    $this->drupalGet('node/add/article');
    $this->assertSession()->fieldExists('Focal Point Field[0][value]');
    $this->assertSession()->pageTextContains('Default focal point value');
  }

  /**
   * Tests validation of the widget.
   */
  public function testWidgetValidation(): void {
    $this->drupalGet('node/add/article');
    $this->submitForm([
      'title[0][value]' => 'Test Node',
      'field_focal_point_field[0][value]' => 'abc,xyz',
    ], 'Save');
    $this->assertSession()->pageTextContains('The focal point field should be in the form "leftoffset,topoffset"');
  }

  /**
   * Tests saving a valid focal point value.
   */
  public function testWidgetSave(): void {
    $this->drupalGet('node/add/article');
    $this->submitForm([
      'title[0][value]' => 'Test Node',
      'field_focal_point_field[0][value]' => '25,75',
    ], 'Save');
    $this->assertSession()->pageTextContains('Article Test Node has been created.');
    $this->drupalGet('node/1/edit');
    $this->assertSession()->fieldValueEquals('field_focal_point_field[0][value]', '25,75');
  }

  /**
   * Tests the IIIF Image Focal Point Widget instantiation.
   */
  public function testWidgetInstantiation(): void {
    $field_definition = $this->createMock(\Drupal\Core\Field\FieldDefinitionInterface::class);
    $widget = new IiifImageFocalPointWidget([], 'iiif_image_focal_point_widget', $plugin_definition, $field_definition, $focal_point_manager);
  }

}
