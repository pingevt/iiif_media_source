<?php

namespace Drupal\Tests\iiif_media_source\Kernel;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;
use Drupal\media\Entity\Media;
use Drupal\media\MediaInterface;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\iiif_media_source\Plugin\media\Source\IiifImageMediaSource;
use Drupal\media\Entity\MediaType;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Component\Utility\Crypt;

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
    'field_ui',
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

  /**
   * {@inheritdoc}
   */
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
      'settings' => [
        'server' => 'http://example.com',
        'prefix' => 'prefix',
        'img_api_version' => '2',
      ],
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
   * Tests the getSourceFieldName() method.
   */
  public function testGetSourceFieldName() {
    // Create a mock plugin definition.
    $plugin_definition = [
      'id' => 'iiif_image',
      'label' => 'IIIF Image',
      'description' => 'Use remote IIIF Image Data.',
      'allowed_field_types' => ['iiif_id'],
    ];

    // Create an instance of the IiifImageMediaSource plugin.
    $plugin = new IiifImageMediaSource([], 'iiif_image', $plugin_definition, $this->container->get('entity_type.manager'), $this->container->get('entity_field.manager'), $this->container->get('config.factory'), $this->container->get('plugin.manager.field.field_type'), $this->container->get('logger.factory')->get('media'), $this->container->get('messenger'), $this->container->get('http_client'), $this->container->get('media.oembed.resource_fetcher'), $this->container->get('media.oembed.url_resolver'), $this->container->get('media.oembed.iframe_url_helper'), $this->container->get('file_system'), $this->container->get('token'), $this->container->get('stream_wrapper_manager'));

    // Use reflection to access the protected method.
    $reflection = new \ReflectionMethod($plugin, 'getSourceFieldName');
    $reflection->setAccessible(TRUE);

    // Call the getSourceFieldName() method.
    $field_name = $reflection->invoke($plugin);

    // Assert that the field name is generated correctly.
    $this->assertStringStartsWith('field_media_iiif_id', $field_name, 'The generated field name starts with the expected prefix.');
  }

  /**
   * Tests the getSourceFieldName() method with an existing field.
   */
  public function testGetSourceFieldNameWithExistingField() {
    // Create a mock plugin definition.
    $plugin_definition = [
      'id' => 'iiif_image',
      'label' => 'IIIF Image',
      'description' => 'Use remote IIIF Image Data.',
      'allowed_field_types' => ['iiif_id'],
    ];

    // Create an instance of the IiifImageMediaSource plugin.
    $plugin = new IiifImageMediaSource([], 'iiif_image', $plugin_definition, $this->container->get('entity_type.manager'), $this->container->get('entity_field.manager'), $this->container->get('config.factory'), $this->container->get('plugin.manager.field.field_type'), $this->container->get('logger.factory')->get('media'), $this->container->get('messenger'), $this->container->get('http_client'), $this->container->get('media.oembed.resource_fetcher'), $this->container->get('media.oembed.url_resolver'), $this->container->get('media.oembed.iframe_url_helper'), $this->container->get('file_system'), $this->container->get('token'), $this->container->get('stream_wrapper_manager'));

    // Simulate an existing field with the base ID.
    $storage = $this->container->get('entity_type.manager')->getStorage('field_storage_config');
    $existing_field = $storage->create([
      'id' => 'media.field_media_iiif_id',
      'field_name' => 'field_media_iiif_id',
      'entity_type' => 'media',
      'type' => 'string',
    ]);
    $existing_field->save();

    // Use reflection to access the protected method.
    $reflection = new \ReflectionMethod($plugin, 'getSourceFieldName');
    $reflection->setAccessible(TRUE);

    // Call the getSourceFieldName() method.
    // This should now increment `$tries` and append a suffix to the field ID.
    $field_name = $reflection->invoke($plugin);

    // Assert that the field name includes the incremented suffix.
    $this->assertEquals('field_media_iiif_id_1', $field_name, 'The generated field name includes the incremented suffix.');
  }

  /**
   * Tests configuration form validation for the thumbnails directory.
   */
  public function testConfigurationFormValidation() {

    $form = [];
    $form_state = new FormState();
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
        'value' => 'image-id',
      ],
    ]);
    $media->save();

    // Mock the HTTP client to return a fake image.
    $mock_response = new Response(200, [], 'FAKE_IMAGE_DATA');
    $mock_client = $this->getMockBuilder(ClientInterface::class)
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
    $property->setAccessible(TRUE);
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
        'value' => 'image-id',
      ],
    ]);
    $media->save();

    // Mock the HTTP client to return a 404 response.
    $mock_response = new Response(404, [], '');
    $mock_client = $this->getMockBuilder(ClientInterface::class)
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
    $property->setAccessible(TRUE);
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
        'value' => 'image-id',
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
    $property->setAccessible(TRUE);
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
        'value' => 'image-id',
      ],
    ]);
    $media->save();

    $mock_response = new Response(200, [], '');
    $mock_client = $this->getMockBuilder(ClientInterface::class)
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
    $property->setAccessible(TRUE);
    $property->setValue($this->mediaSource, $mock_client);

    $uri = $this->mediaSource->getLocalThumbnailUri($media);
    $this->assertNull($uri, 'No URI returned when response body is empty.');
  }

  /**
   * Tests getLocalThumbnailUri() with an existing file.
   */
  public function testGetLocalThumbnailUriWithExistingFile() {
    // Create a mock media entity.
    $media = $this->createMock(MediaInterface::class);

    // Mock the field definition.
    $field_definition = $this->createMock(FieldDefinitionInterface::class);
    $field_definition->method('getSettings')->willReturn([
      'server' => 'http://example.com',
      'prefix' => 'prefix',
    ]);

    // Mock the source field configuration.
    $source_field = $this->createMock(FieldItemListInterface::class);
    $source_field->method('getFieldDefinition')->willReturn($field_definition);
    $source_field->method('__get')->with('value')->willReturn('remote_id');
    $media->method('get')->with('field_iiif_id')->willReturn($source_field);

    // Simulate the remote thumbnail URL.
    $remote_thumbnail_url = 'http://example.com/prefix/remote_id/full/!300,300/0/default.jpg';

    // Calculate the hash for the remote thumbnail URL.
    $hash = Crypt::hashBase64($remote_thumbnail_url);

    // Mock the file system service to return a file with the hashed name.
    $mock_file = (object) ['uri' => "public://iiif_thumbnails/{$hash}.jpg"];
    $mock_file_system = $this->getMockBuilder(FileSystemInterface::class)
      ->disableOriginalConstructor()
      ->getMock();
    $mock_file_system->method('scanDirectory')->with(
      'public://iiif_thumbnails',
      "/^$hash\..*/"
    )->willReturn([$mock_file]);

    $mock_file_system->method('prepareDirectory')->willReturn(TRUE);

    // $files = $mock_file_system->scanDirectory(
    //   'public://iiif_thumbnails',
    //   "/^$hash\..*/"
    // );
    // var_dump($files);

    // Create an instance of the IiifImageMediaSource plugin.
    $plugin = new IiifImageMediaSource(
      [
        'source_field' => 'field_iiif_id',
        'thumbnails_directory' => 'public://iiif_thumbnails',
      ],
      'iiif_image',
      [
        'source_field' => 'field_iiif_id',
        'thumbnails_directory' => 'public://iiif_thumbnails',
      ],
      $this->container->get('entity_type.manager'),
      $this->container->get('entity_field.manager'),
      $this->container->get('config.factory'),
      $this->container->get('plugin.manager.field.field_type'),
      $this->container->get('logger.factory')->get('media'),
      $this->container->get('messenger'),
      $this->container->get('http_client'),
      $this->container->get('media.oembed.resource_fetcher'),
      $this->container->get('media.oembed.url_resolver'),
      $this->container->get('media.oembed.iframe_url_helper'),
      $mock_file_system,
      $this->container->get('token'),
      $this->container->get('stream_wrapper_manager')
    );

    // Call the getLocalThumbnailUri() method.
    $thumbnail_uri = $plugin->getLocalThumbnailUri($media);

    // Assert that the method returns the URI of the first file.
    $this->assertEquals('public://iiif_thumbnails/' . $hash . '.jpg', $thumbnail_uri, 'The method returned the URI of the first file.');
  }

  /**
   * Tests metadata extraction for IIIF v2 and v3 info JSON.
   */
  public function testMetadataExtractionForIiifV2AndV3() {
    // IIIF v2 example info.json.
    $info_v2 = [
      '@context' => 'http://iiif.io/api/image/2/context.json',
      '@id' => 'https://example.org/iiif/2/image-id',
      'width' => 1000,
      'height' => 800,
      'profile' => ['http://iiif.io/api/image/2/level2.json'],
      'protocol' => 'http://iiif.io/api/image',
      'tiles' => [['width' => 256, 'scaleFactors' => [1, 2, 4, 8]]],
    ];

    // IIIF v3 example info.json.
    $info_v3 = [
      '@context' => 'http://iiif.io/api/image/3/context.json',
      'id' => 'https://example.org/iiif/3/image-id',
      'width' => 2000,
      'height' => 1600,
      'profile' => ['level2'],
      'protocol' => 'http://iiif.io/api/image',
      'tiles' => [['width' => 512, 'scaleFactors' => [1, 2, 4, 8, 16]]],
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
    // $metadata_v3 = $this->mediaSource->getMetadata($media_v3);.
    $this->assertEquals(2000, $this->mediaSource->getMetadata($media_v3, 'width'), 'IIIF v3 width extracted.');
    $this->assertEquals(1600, $this->mediaSource->getMetadata($media_v3, 'height'), 'IIIF v3 height extracted.');
    $this->assertEquals('https://example.org/iiif/3/image-id', $this->mediaSource->getMetadata($media_v3, 'id'), 'IIIF v3 id extracted.');
  }

}
