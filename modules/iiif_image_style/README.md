# IIIF Image Style

A Drupal module for managing and applying [IIIF](https://iiif.io/) (International Image Interoperability Framework) image styles and effects to images, with support for responsive images and advanced image transformations.

---

## Features

- **IIIF Image Styles:**
  Create and manage image styles that generate IIIF-compliant image URLs.
- **Image Effects:**
  Add configurable effects (format, quality, region, size, rotation, etc.) to image styles.
- **Responsive Image Styles:**
  Define responsive image styles with breakpoint mappings and fallbacks.
- **Field Formatters:**
  Display IIIF images and responsive images using custom field formatters.
- **Event System:**
  Alter image style settings and effects via events and subscribers.
- **Plugin Architecture:**
  Easily extend with custom IIIF image effects using the plugin system.

---

## Requirements

- Drupal 10 or later
- [iiif_media_source](https://www.drupal.org/project/iiif_media_source) module

---

## Installation

1. Download and enable the module:
   ```sh
   composer require drupal/iiif_image_style
   drush en iiif_image_style
   ```
2. Configure IIIF image styles and responsive image styles at
   **Admin > Configuration > Media > IIIF Image Styles**

---

## Usage

1. **Create IIIF Image Styles:**
   - Go to **Configuration > Media > IIIF Image Styles**.
   - Add a new image style and configure effects as needed.

2. **Create Responsive Image Styles:**
   - Go to **Configuration > Media > IIIF Responsive Image Styles**.
   - Map breakpoints and multipliers to IIIF image styles.

3. **Configure Field Formatters:**
   - Edit a field using the "IIIF Image Style Formatter" or "IIIF Responsive Image Style Formatter".
   - Select the desired image style and loading attributes.

4. **Extend with Custom Effects:**
   - Implement new plugins in `src/Plugin/IiifImageEffect/`.
   - See existing effects for examples.

---

## Hooks & API

- **Events:**
  - `iiif_image_style_settings` — Alter image style settings.
- **Plugin System:**
  - Add new effects by creating plugins with the `IiifImageEffect` attribute.

---

## Troubleshooting

- Ensure all dependencies are enabled.
- Check logs for errors if image styles or effects are not applied as expected.
- Use the admin UI to verify style and effect configuration.

---

## Maintainers

- [Pete Inge](https://www.drupal.org/u/pingevt)

---

## See Also

- [IIIF Image API Specification](https://iiif.io/api/image/)
- [Drupal Media Module](https://www.drupal.org/project/media)
