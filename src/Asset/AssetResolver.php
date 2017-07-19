<?php

namespace Drupal\ebcoam\Asset;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Asset\AttachedAssetsInterface;
use Drupal\Core\Asset\AssetResolver as AssetResolverDrupal;

/**
 * The default asset resolver.
 */
class AssetResolver extends AssetResolverDrupal implements AssetResolverWebInterface {

  /**
   *
   */
  public function getWebcomponentsAssets(AttachedAssetsInterface $assets, $optimize) {
    $theme_info = $this->themeManager->getActiveTheme();
    // Add the theme name to the cache key since themes may implement
    // hook_library_info_alter(). Additionally add the current language to
    // support translation of JavaScript files via hook_js_alter().
    $libraries_to_load = $this->getLibrariesToLoad($assets);
    $cid = 'webcomponent:' . $theme_info->getName() . ':' . $this->languageManager->getCurrentLanguage()
      ->getId() . ':' . Crypt::hashBase64(serialize($libraries_to_load)) . (int) (count($assets->getSettings()) > 0) . (int) $optimize;

    if ($cached = $this->cache->get($cid)) {
      list($js_assets_header, $js_assets_footer, $settings, $settings_in_header) = $cached->data;
    }
    else {
      $default_options = [
        'type' => 'file',
        'group' => JS_DEFAULT,
        'weight' => 0,
        'cache' => TRUE,
        'preprocess' => TRUE,
        'attributes' => [],
        'version' => NULL,
        'browsers' => [],
      ];

      $webcomponent = [];
      foreach ($libraries_to_load as $library) {
        list($extension, $name) = explode('/', $library, 2);
        $definition = $this->libraryDiscovery->getLibraryByName($extension, $name);
        if (isset($definition['webcomponent'])) {
          foreach ($definition['webcomponent'] as $options) {
            $options += $default_options;

            // Preprocess can only be set if caching is enabled and no
            // attributes are set.
            $options['preprocess'] = $options['cache'] && empty($options['attributes']) ? $options['preprocess'] : FALSE;

            // Always add a tiny value to the weight, to conserve the insertion
            // order.
            $options['weight'] += count($webcomponent) / 1000;

            // Local and external files must keep their name as the associative
            // key so the same JavaScript file is not added twice.
            $webcomponent[$options['data']] = $options;
          }
        }
      }

      // Allow modules and themes to alter the JavaScript assets.
      $this->moduleHandler->alter('webcomponent', $javascript, $assets);
      $this->themeManager->alter('webcomponent', $javascript, $assets);

      // Sort JavaScript assets, so that they appear in the correct order.
      uasort($webcomponent, 'static::sort');

      // @todo maybe
      /* if ($optimize) {
      $collection_optimizer = \Drupal::service('asset.js.collection_optimizer');
      $js_assets_header = $collection_optimizer->optimize($js_assets_header);
      $js_assets_footer = $collection_optimizer->optimize($js_assets_footer);
      }*/

      return $webcomponent;
    }
  }

}
