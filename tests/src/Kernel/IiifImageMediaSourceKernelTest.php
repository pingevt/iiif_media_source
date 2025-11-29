<?php

namespace Drupal\Tests\iiif_media_source\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\media\Entity\Media;
use Drupal\iiif_media_source\Plugin\media\Source\IiifImageMediaSource;
use Drupal\media\MediaTypeInterface;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\media\Entity\MediaType;
use Drupal\user\Entity\User;
use Drupal\Core\File\FileSystemInterface;
use GuzzleHttp\Psr7\Response;

/**
 * Kernel tests for the IIIF Image Media Source plugin.
 *
 * @group iiif_media_source
 */
class IiifImageMediaSourceKernelTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'file',
    'image',
    'media',
    'iiif_media_source',
    // Add other dependencies as needed.
  ];

  /**
   * The media source plugin under test.
   *
   * @var \Drupal\iiif_media_source\Plugin\media\Source\IiifImageMediaSource
   */
  protected $mediaSource;

  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('file');
    $this->installEntitySchema('media');
    $this->installEntitySchema('user');
    $this->installSchema('file', ['file_usage']);

    $this->installConfig(['file', 'image', 'media']);

    MediaType::create([
      'id' => 'iiif_image',
      'label' => 'IIIF Image',
      'source' => 'iiif_image',
      'source_configuration' => [
        'source_field' => 'field_iiif_id',
        'thumbnails_directory' => 'public://iiif_thumbnails/test',
      ],
    ])->save();

    // Set up a minimal media type and field for IIIF.
    FieldStorageConfig::create([
      'field_name' => 'field_iiif_id',
      'entity_type' => 'media',
      'type' => 'iiif_id',
    ])->save();

    FieldConfig::create([
      'field_name' => 'field_iiif_id',
      'entity_type' => 'media',
      'bundle' => 'iiif_image',
      'label' => 'IIIF ID',
    ])->save();

    // Add this line:
    \Drupal::entityTypeManager()->getStorage('media_type')->resetCache();
    \Drupal::service('plugin.manager.media.source')->clearCachedDefinitions();

    $this->mediaSource = \Drupal::service('plugin.manager.media.source')->createInstance('iiif_image', [
      'source_field' => 'field_iiif_id',
      'thumbnails_directory' => 'public://iiif_thumbnails/test',
    ]);
  }

  /**
   * Tests configuration form validation for the thumbnails directory.
   */
  public function testConfigurationFormValidation() {

    $form = [];
    $form_state = new \Drupal\Core\Form\FormState();
    $form_state->setValues([
      'thumbnails_directory' => 'invalid://path/with?bad*chars',
    ]);

    // Build the config form.
    $form = $this->mediaSource->buildConfigurationForm($form, $form_state);

    // Validate the config form.
    $this->mediaSource->validateConfigurationForm($form, $form_state);

    // Check for errors.
    $errors = $form_state->getErrors();
    $this->assertNotEmpty($errors, 'Validation errors found for invalid thumbnails directory.');
    $this->assertArrayHasKey('thumbnails_directory', $errors, 'Error set for thumbnails_directory.');
  }

  /**
   * Tests that getLocalThumbnailUri() saves a thumbnail locally.
   */
  public function testGetLocalThumbnailUriDownloadsAndSavesFile() {
    // Create a mock media entity with a valid IIIF ID.
    $media = Media::create([
      'bundle' => 'iiif_image',
      'field_iiif_id' => [
        'value' => 'https://example.org/iiif/image-id',
      ],
    ]);
    $media->save();

    // Mock the HTTP client to return a fake image.
    $mock_response = new \GuzzleHttp\Psr7\Response(200, [], 'FAKE_IMAGE_DATA');
    $mock_client = $this->getMockBuilder(\GuzzleHttp\ClientInterface::class)
      ->onlyMethods(['request'])
      ->getMockForAbstractClass();
    $mock_client->expects($this->once())
      ->method('request')
      ->willReturn($mock_response);

    $this->mediaSource = \Drupal::service('plugin.manager.media.source')->createInstance('iiif_image', [
      'source_field' => 'field_iiif_id',
      'thumbnails_directory' => 'public://iiif_thumbnails/test',
    ]);

    // Inject the mock HTTP client into the media source.
    $reflection = new \ReflectionClass($this->mediaSource);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($this->mediaSource, $mock_client);

    // Call the method under test.
    $uri = $this->mediaSource->getLocalThumbnailUri($media);

    // Assert that a local file URI is returned.
    $this->assertNotNull($uri, 'A local thumbnail URI was returned.');
    $this->assertStringContainsString('public://iiif_thumbnails', $uri, 'Thumbnail is saved in the correct directory.');
  }

  /**
   * Tests getLocalThumbnailUri() when the remote thumbnail cannot be downloaded.
   */
  public function testGetLocalThumbnailUriHandlesDownloadFailure() {
    // Create a media entity with a valid IIIF ID.
    $media = Media::create([
      'bundle' => 'iiif_image',
      'field_iiif_id' => [
        'value' => 'https://example.org/iiif/image-id',
      ],
    ]);
    $media->save();

    // Mock the HTTP client to return a 404 response.
    $mock_response = new \GuzzleHttp\Psr7\Response(404, [], '');
    $mock_client = $this->getMockBuilder(\GuzzleHttp\ClientInterface::class)
      ->onlyMethods(['request'])
      ->getMockForAbstractClass();
    $mock_client->expects($this->once())
      ->method('request')
      ->willReturn($mock_response);

    $this->mediaSource = \Drupal::service('plugin.manager.media.source')->createInstance('iiif_image', [
      'source_field' => 'field_iiif_id',
      'thumbnails_directory' => 'public://iiif_thumbnails/test',
    ]);

    // Inject the mock HTTP client.
    $reflection = new \ReflectionClass($this->mediaSource);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($this->mediaSource, $mock_client);

    // Call the method under test.
    $uri = $this->mediaSource->getLocalThumbnailUri($media);

    // Assert that no URI is returned on failure.
    $this->assertNull($uri, 'No local thumbnail URI is returned when download fails.');
  }

  /**
   * Tests getLocalThumbnailUri() when directory creation fails.
   */
  public function testGetLocalThumbnailUriDirectoryCreationFailure() {
    $media = Media::create([
      'bundle' => 'iiif_image',
      'field_iiif_id' => [
        'value' => 'https://example.org/iiif/image-id',
      ],
    ]);
    $media->save();

    // Mock the file system to fail directory creation.
    $mock_fs = $this->getMockBuilder(FileSystemInterface::class)
      ->disableOriginalConstructor()
      ->getMock();
    $mock_fs->method('prepareDirectory')->willReturn(FALSE);

    $reflection = new \ReflectionClass($this->mediaSource);
    $property = $reflection->getProperty('fileSystem');
    $property->setAccessible(true);
    $property->setValue($this->mediaSource, $mock_fs);

    $uri = $this->mediaSource->getLocalThumbnailUri($media);
    $this->assertNull($uri, 'No URI returned when directory creation fails.');
  }

  /**
   * Tests getLocalThumbnailUri() when IIIF ID is empty.
   */
  public function testGetLocalThumbnailUriWithEmptyIiifId() {
    $media = Media::create([
      'bundle' => 'iiif_image',
      'field_iiif_id' => [
        'value' => '',
      ],
    ]);
    $media->save();

    $uri = $this->mediaSource->getLocalThumbnailUri($media);
    $this->assertNull($uri, 'No URI returned when IIIF ID is empty.');
  }

  /**
   * Tests getLocalThumbnailUri() when response body is empty.
   */
  public function testGetLocalThumbnailUriWithEmptyResponseBody() {
    $media = Media::create([
      'bundle' => 'iiif_image',
      'field_iiif_id' => [
        'value' => 'https://example.org/iiif/image-id',
      ],
    ]);
    $media->save();

    $mock_response = new \GuzzleHttp\Psr7\Response(200, [], '');
    $mock_client = $this->getMockBuilder(\GuzzleHttp\ClientInterface::class)
      ->onlyMethods(['request'])
      ->getMockForAbstractClass();
    $mock_client->expects($this->once())
      ->method('request')
      ->willReturn($mock_response);

    $this->mediaSource = \Drupal::service('plugin.manager.media.source')->createInstance('iiif_image', [
      'source_field' => 'field_iiif_id',
      'thumbnails_directory' => 'public://iiif_thumbnails/test',
    ]);
    $reflection = new \ReflectionClass($this->mediaSource);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($this->mediaSource, $mock_client);

    $uri = $this->mediaSource->getLocalThumbnailUri($media);
    $this->assertNull($uri, 'No URI returned when response body is empty.');
  }

  /**
   * Tests metadata extraction for IIIF v2 and v3 info JSON.
   */
  public function testMetadataExtractionForIiifV2AndV3() {
    // IIIF v2 example info.json
    $info_v2 = [
      '@context' => 'http://iiif.io/api/image/2/context.json',
      '@id' => 'https://example.org/iiif/2/image-id',
      'width' => 1000,
      'height' => 800,
      'profile' => ['http://iiif.io/api/image/2/level2.json'],
      'protocol' => 'http://iiif.io/api/image',
      'tiles' => [['width' => 256, 'scaleFactors' => [1,2,4,8]]],
    ];

    // IIIF v3 example info.json
    $info_v3 = [
      '@context' => 'http://iiif.io/api/image/3/context.json',
      'id' => 'https://example.org/iiif/3/image-id',
      'width' => 2000,
      'height' => 1600,
      'profile' => ['level2'],
      'protocol' => 'http://iiif.io/api/image',
      'tiles' => [['width' => 512, 'scaleFactors' => [1,2,4,8,16]]],
    ];

    // Create media entities for v2 and v3.
    $media_v2 = Media::create([
      'bundle' => 'iiif_image',
      'field_iiif_id' => [
        'value' => 'https://example.org/iiif/2/image-id',
        'info' => json_encode($info_v2),
      ],
    ]);
    $media_v2->save();

    $media_v3 = Media::create([
      'bundle' => 'iiif_image',
      'field_iiif_id' => [
        'value' => 'https://example.org/iiif/3/image-id',
        'info' => json_encode($info_v3),
      ],
    ]);
    $media_v3->save();

    // Get metadata attributes and values for v2.
    $attributes = $this->mediaSource->getMetadataAttributes();
    // $metadata_v2 = $this->mediaSource->getMetadata($media_v2);

    $this->assertEquals(1000, $this->mediaSource->getMetadata($media_v2, 'width'), 'IIIF v2 width extracted.');
    $this->assertEquals(800, $this->mediaSource->getMetadata($media_v2, 'height'), 'IIIF v2 height extracted.');
    $this->assertEquals('https://example.org/iiif/2/image-id', $this->mediaSource->getMetadata($media_v2, '@id'), 'IIIF v2 @id extracted.');

    // Get metadata attributes and values for v3.
    // $metadata_v3 = $this->mediaSource->getMetadata($media_v3);

    $this->assertEquals(2000, $this->mediaSource->getMetadata($media_v3, 'width'), 'IIIF v3 width extracted.');
    $this->assertEquals(1600, $this->mediaSource->getMetadata($media_v3, 'height'), 'IIIF v3 height extracted.');
    $this->assertEquals('https://example.org/iiif/3/image-id', $this->mediaSource->getMetadata($media_v3, 'id'), 'IIIF v3 id extracted.');
  }

}
