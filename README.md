# IIIF Media Source

A Drupal module for integrating [IIIF Image API](https://iiif.io/) image sources into Drupal media entities.
Supports IIIF v2 and v3, region/size/rotation/quality/format parameterization, and robust validation.

---

## Features

- Fetch and parse IIIF image manifests (`info.json`)
- Generate IIIF-compliant image URLs with region, size, rotation, quality, and format options
- Support for IIIF Image API v2 and v3
- Thumbnail and full image URL generation
- Handles maxWidth, maxHeight, and maxArea constraints
- Extensible and testable architecture

---

## Requirements

- Drupal 9 or 10
- PHP 8.1+
- [media](https://www.drupal.org/project/media) module enabled

---

## Installation

1. Place this module in your `modules/contrib` or `modules/custom` directory.
2. Enable the module via the Drupal admin UI or with Drush:
   ```sh
   drush en iiif_media_source
   ```

---

## Configuration

- No configuration is required by default.
- Thumbnail dimensions and other settings can be customized in the code or via configuration (future feature).
- To use with media entities, add a media type and select "IIIF Image" as the source plugin.

---

## Usage

- When creating or editing a media entity, provide a IIIF manifest URL or image ID.
- The module will fetch the manifest and make image derivatives available.
- Use the provided image formatters to display IIIF images in your site.

---

## API

### Main Classes

- `IiifImage`: Represents a IIIF image and provides methods for manifest parsing and URL generation.
- `IiifImageUrlParams`: Handles IIIF URL parameter construction and validation.
- `IiifBase`: Abstract base for IIIF-related classes.

### Example: Generate a IIIF Image URL

```php
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

$image = new IiifImage($server, $prefix, $id, $info);
$params = IiifImageUrlParams::fromSettingsArray([
  'region' => 'full',
  'size' => 'max',
  'rotation' => 0,
  'quality' => 'default',
  'format' => 'jpg',
], '3.0');
$url = $image->getBuiltImageUrl($params);
```

---

## Testing

- PHPUnit and Kernel tests are provided for all core logic.
- To run tests:
  ```sh
  phpunit --testsuite iiif_media_source
  ```
- See `/tests/src/Unit` and `/tests/src/Kernel` for test coverage.

---

## Submodules

This project includes the following submodules:

- [IIIF Image Style](web/modules/synced/Modules/iiif_image_style/README.md):
  Provides IIIF image styles, effects, and responsive image integration for IIIF images in Drupal.

<!-- Add more submodules here as needed -->

---

## Roadmap

- UI for configuring default thumbnail sizes and other options
- Support for additional IIIF features (auth, presentation, etc.)
- Improved error handling and logging
- More flexible integration with Drupal media and image styles

---

## Maintainers

- [Pete Inge](https://www.drupal.org/u/pingevt)

---

## See Also

- [IIIF Image API Specification](https://iiif.io/api/image/)
- [Drupal Media Module](https://www.drupal.org/project/media)
