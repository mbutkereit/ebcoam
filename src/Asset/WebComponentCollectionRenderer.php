<?php

namespace Drupal\ebcoam\Asset;

use Drupal\Core\Asset\AssetCollectionRendererInterface;
use Drupal\Core\State\StateInterface;

/**
 * Renders JavaScript assets.
 */
class WebComponentCollectionRenderer implements AssetCollectionRendererInterface {

  /**
   * The state key/value store.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a JsCollectionRenderer.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state key/value store.
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   *
   * This class evaluates the aggregation enabled/disabled condition on a group
   * by group basis by testing whether an aggregate file has been made for the
   * group rather than by testing the site-wide aggregation setting. This allows
   * this class to work correctly even if modules have implemented custom
   * logic for grouping and aggregating files.
   */
  public function render(array $web_component_assets) {
    $elements = [];

    // A dummy query-string is added to filenames, to gain control over
    // browser-caching. The string changes on every update or full cache
    // flush, forcing browsers to load a new copy of the files, as the
    // URL changed. Files that should not be cached get REQUEST_TIME as
    // query-string instead, to enforce reload on every page request.
    $default_query_string = $this->state->get('system.css_js_query_string') ?: '0';

    // Defaults for each SCRIPT element.
    $element_defaults = [
      '#type' => 'html_tag',
      '#tag' => 'link',
    ];

    // Loop through all JS assets.
    foreach ($web_component_assets as $web_component_asset) {
      // Element properties that do not depend on JS asset type.
      $element = $element_defaults;
      $element['#browsers'] = $web_component_asset['browsers'];
      $element['#attributes']['rel'] = 'import';
      // Element properties that depend on item type.
      switch ($web_component_asset['type']) {
        case 'file':
          $query_string = $web_component_asset['version'] == -1 ? $default_query_string : 'v=' . $web_component_asset['version'];
          $query_string_separator = (strpos($web_component_asset['data'], '?') !== FALSE) ? '&' : '?';
          $element['#attributes']['href'] = file_url_transform_relative(file_create_url($web_component_asset['data']));
          // Only add the cache-busting query string if this isn't an aggregate
          // file.
          if (!isset($web_component_asset['preprocessed'])) {
            $element['#attributes']['href'] .= $query_string_separator . ($web_component_asset['cache'] ? $query_string : REQUEST_TIME);
          }
          break;

        case 'external':
          $element['#attributes']['href'] = $web_component_asset['data'];
          break;

        default:
          throw new \Exception('Invalid web_component asset type.');
      }

      // Attributes may only be set if this script is output independently.
      if (!empty($element['#attributes']['src']) && !empty($web_component_asset['attributes'])) {
        $element['#attributes'] += $web_component_asset['attributes'];
      }

      $elements[] = $element;
    }

    return $elements;
  }

}
