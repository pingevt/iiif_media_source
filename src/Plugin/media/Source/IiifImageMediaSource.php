<?php

namespace Drupal\iiif_media_source\Plugin\media\Source;

use Drupal\Component\Render\PlainTextOutput;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\File\Exception\FileException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\Core\Utility\Token;
use Drupal\media\IFrameUrlHelper;
use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceBase;
use Drupal\media\OEmbed\ResourceFetcherInterface;
use Drupal\media\OEmbed\UrlResolverInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mime\MimeTypes;

/**
 * External image entity media source.
 *
 * @see \Drupal\file\FileInterface
 *
 * @MediaSource(
 *   id = "iiif_image",
 *   label = @Translation("IIIF External Image"),
 *   description = @Translation("Use remote IIIF Image Data."),
 *   allowed_field_types = {"iiif_id"},
 *   default_thumbnail_filename = "no-thumbnail.png"
 * )
 */
class IiifImageMediaSource extends MediaSourceBase {

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The resource fetcher.
   *
   * @var \Drupal\media\OEmbed\ResourceFetcherInterface
   */
  protected $resourceFetcher;

  /**
   * The url resolver.
   *
   * @var \Drupal\media\OEmbed\UrlResolverInterface
   */
  protected $urlResolver;

  /**
   * The iframe helper.
   *
   * @var \Drupal\media\IFrameUrlHelper
   */
  protected $iFrameUrlHelper;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The token replacement service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * The Stream Wrapper Manager.
   *
   * @var \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface
   */
  protected $streamWrapperManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager, ConfigFactoryInterface $config_factory, FieldTypePluginManagerInterface $field_type_manager, LoggerInterface $logger, MessengerInterface $messenger, ClientInterface $http_client, ResourceFetcherInterface $resource_fetcher, UrlResolverInterface $url_resolver, IFrameUrlHelper $iframe_url_helper, FileSystemInterface $file_system, Token $token, StreamWrapperManagerInterface $stream_wrapper_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $entity_field_manager, $field_type_manager, $config_factory);
    $this->logger = $logger;
    $this->messenger = $messenger;
    $this->httpClient = $http_client;
    $this->resourceFetcher = $resource_fetcher;
    $this->urlResolver = $url_resolver;
    $this->iFrameUrlHelper = $iframe_url_helper;
    $this->fileSystem = $file_system;
    $this->token = $token;
    $this->streamWrapperManager = $stream_wrapper_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('config.factory'),
      $container->get('plugin.manager.field.field_type'),
      $container->get('logger.factory')->get('media'),
      $container->get('messenger'),
      $container->get('http_client'),
      $container->get('media.oembed.resource_fetcher'),
      $container->get('media.oembed.url_resolver'),
      $container->get('media.oembed.iframe_url_helper'),
      $container->get('file_system'),
      $container->get('token'),
      $container->get('stream_wrapper_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getSourceFieldName() {

    $prefix = $this->configFactory->get('field_ui.settings')->get('field_prefix') ?? 'field_';
    $base_id = $prefix . 'media_iiif_id';
    $tries = 0;
    $storage = $this->entityTypeManager->getStorage('field_storage_config');

    // Iterate at least once, until no field with the generated ID is found.
    do {
      $id = $base_id;
      // If we've tried before, increment and append the suffix.
      if ($tries) {
        $id .= '_' . $tries;
      }
      $field = $storage->load('media.' . $id);
      $tries++;
    } while ($field);

    return $id;
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadataAttributes() {
    return [
      // 'title' => $this->t('Title'),
      // 'alt_text' => $this->t('Alternative text'),
      // 'caption' => $this->t('Caption'),
      // 'credit' => $this->t('Credit'),
      // 'id' => $this->t('ID'),
      // 'uri' => $this->t('URL'),
      'width' => $this->t('Width'),
      'height' => $this->t('Height'),
      'formats' => $this->t('Formats'),
      'qualities' => $this->t('Qualities'),
      'supports' => $this->t('Supports'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadata(MediaInterface $media, $attribute_name) {

    // Get the text_long field where the JSON object is stored.
    $remote_field = $media->get($this->configuration['source_field']);
    // $json_arr = json_decode($remote_field->value);
    // ksm($attribute_name, $remote_field, $media);
    // If the source field is not required, it may be empty.
    if ($remote_field === FALSE) {
      return parent::getMetadata($media, $attribute_name);
    }

    switch ($attribute_name) {

      // This is used to set the name of the media entity if the user leaves the field blank.
      // case 'default_name':
      //   return $json_arr->alt_text;.
      // This is used to generate the thumbnail field.
      case 'thumbnail_uri':
        // Return "https://media.nga.gov/iiif/" . $remote_field->value . "/full/!300,300/0/default.jpg";.
        return $this->getLocalThumbnailUri($remote_field->value) ?: parent::getMetadata($media, 'thumbnail_uri');

      // default:
      //   return $json_arr->$attribute_name ?? parent::getMetadata($media, $attribute_name);.
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['thumbnails_directory'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Thumbnails location'),
      '#default_value' => $this->configuration['thumbnails_directory'],
      '#description' => $this->t('Thumbnails will be fetched from the provider for local usage. This is the URI of the directory where they will be placed.'),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $thumbnails_directory = $form_state->getValue('thumbnails_directory');

    /** @var \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface $stream_wrapper_manager */
    $stream_wrapper_manager = $this->streamWrapperManager;

    if (!$stream_wrapper_manager->isValidUri($thumbnails_directory)) {
      $form_state->setErrorByName('thumbnails_directory', $this->t('@path is not a valid path.', [
        '@path' => $thumbnails_directory,
      ]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'thumbnails_directory' => 'public://iiif_thumbnails/[date:custom:Y-m]',
    ];
  }

  /**
   * Returns the local URI for a resource thumbnail.
   *
   * If the thumbnail is not already locally stored, this method will attempt
   * to download it.
   *
   * @return string|null
   *   The local thumbnail URI, or NULL if it could not be downloaded, or if the
   *   resource has no thumbnail at all.
   */
  protected function getLocalThumbnailUri($id) {
    // If there is no remote thumbnail, there's nothing for us to fetch here.
    // $remote_thumbnail_url = $resource->getThumbnailUrl();
    $remote_thumbnail_url = "https://media.nga.gov/iiif/" . $id . "/full/!300,300/0/default.jpg";
    if (!$remote_thumbnail_url) {
      return NULL;
    }

    // Use the configured directory to store thumbnails. The directory can
    // contain basic (i.e., global) tokens. If any of the replaced tokens
    // contain HTML, the tags will be removed and XML entities will be decoded.
    $configuration = $this->getConfiguration();
    $directory = $configuration['thumbnails_directory'];
    $directory = $this->token->replace($directory);
    $directory = PlainTextOutput::renderFromHtml($directory);

    // The local thumbnail doesn't exist yet, so try to download it. First,
    // ensure that the destination directory is writable, and if it's not,
    // log an error and bail out.
    if (!$this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
      $this->logger->warning('Could not prepare thumbnail destination directory @dir for oEmbed media.', [
        '@dir' => $directory,
      ]);
      return NULL;
    }

    // The local filename of the thumbnail is always a hash of its remote URL.
    // If a file with that name already exists in the thumbnails directory,
    // regardless of its extension, return its URI.
    // $remote_thumbnail_url = $remote_thumbnail_url->toString();
    $hash = Crypt::hashBase64($remote_thumbnail_url);
    $files = $this->fileSystem->scanDirectory($directory, "/^$hash\..*/");
    if (count($files) > 0) {
      return reset($files)->uri;
    }

    // The local thumbnail doesn't exist yet, so we need to download it.
    try {
      $response = $this->httpClient->request('GET', $remote_thumbnail_url);
      if ($response->getStatusCode() === 200) {
        $local_thumbnail_uri = $directory . DIRECTORY_SEPARATOR . $hash . '.' . $this->getThumbnailFileExtensionFromUrl($remote_thumbnail_url, $response);
        $this->fileSystem->saveData((string) $response->getBody(), $local_thumbnail_uri, FileSystemInterface::EXISTS_REPLACE);
        return $local_thumbnail_uri;
      }
    }
    catch (TransferException $e) {
      $this->logger->warning('Failed to download remote thumbnail file due to "%error".', [
        '%error' => $e->getMessage(),
      ]);
    }
    catch (FileException $e) {
      $this->logger->warning('Could not download remote thumbnail from {url}.', [
        'url' => $remote_thumbnail_url,
      ]);
    }
    return NULL;
  }

  /**
   * Tries to determine the file extension of a thumbnail.
   *
   * @param string $thumbnail_url
   *   The remote URL of the thumbnail.
   * @param \Psr\Http\Message\ResponseInterface $response
   *   The response for the downloaded thumbnail.
   *
   * @return string|null
   *   The file extension, or NULL if it could not be determined.
   */
  protected function getThumbnailFileExtensionFromUrl(string $thumbnail_url, ResponseInterface $response): ?string {
    // First, try to glean the extension from the URL path.
    $path = parse_url($thumbnail_url, PHP_URL_PATH);
    if ($path) {
      $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
      if ($extension) {
        return $extension;
      }
    }

    // If the URL didn't give us any clues about the file extension, see if the
    // response headers will give us a MIME type.
    $content_type = $response->getHeader('Content-Type');
    // If there was no Content-Type header, there's nothing else we can do.
    if (empty($content_type)) {
      return NULL;
    }
    $extensions = MimeTypes::getDefault()->getExtensions(reset($content_type));
    if ($extensions) {
      return reset($extensions);
    }
    // If no file extension could be determined from the Content-Type header,
    // we're stumped.
    return NULL;
  }

}
