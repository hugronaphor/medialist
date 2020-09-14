<?php

namespace Drupal\ml_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Process plug-in to convert vimeo:// scheme URIs to video URLs.
 *
 * @see https://www.previousnext.com.au/blog/migrating-drupal-7-file-entities-drupal-8-media-entities
 *   Based on code from this blog post.
 *
 * @MigrateProcessPlugin(
 *   id = "vimeo"
 * )
 */
class Vimeo extends ProcessPluginBase {
  /**
   * The file scheme used in the Drupal 7 'file_managed' table.
   */
  const SCHEME = 'vimeo://';

  /**
   * The base URL for Vimeo videos; used to transform SCHEME to full URLs.
   */
  const BASE_URL = 'https://vimeo.com/';

  /**
   * {@inheritdoc}
   */
  public function transform(
    $value,
    MigrateExecutableInterface $migrate_executable,
    Row $row, $destination_property
  ) {
    // Convert vimeo:// scheme URI to video URL.
    if (strpos($value, static::SCHEME) !== false) {
      $value = static::BASE_URL .
        explode('/', str_replace(static::SCHEME, '', $value), 2)[1];

    } else {
      $value = null;
    }

    return $value;
  }
}
