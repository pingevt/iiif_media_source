<?php

namespace Drupal\iiif_media_source\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;

/**
 * IIIF ID formatter.
 *
 * Copy of StringFormatter.
 *
 * @FieldFormatter(
 *   id = "iiif_id_formatter",
 *   label = @Translation("IIIF ID Formatter"),
 *   field_types = {
 *     "iiif_id"
 *   }
 * )
 */
class IiifIdFormatter extends StringFormatter {

}
