<?php

namespace Drupal\ebcoam\Asset;

use Drupal\Core\Asset\AttachedAssetsInterface;

interface AssetResolverWebInterface extends \Drupal\Core\Asset\AssetResolverInterface {


  /**
   * Returns the JavaScript assets for the current response's libraries.
   *
   * References to JavaScript files are placed in a certain order: first, all
   * 'core' files, then all 'module' and finally all 'theme' JavaScript files
   * are added to the page. Then, all settings are output, followed by 'inline'
   * JavaScript code. If running update.php, all preprocessing is disabled.
   *
   * Note that hook_js_alter(&$javascript) is called during this function call
   * to allow alterations of the JavaScript during its presentation. The correct
   * way to add JavaScript during hook_js_alter() is to add another element to
   * the $javascript array, deriving from drupal_js_defaults(). See
   * locale_js_alter() for an example of this.
   *
   * @param \Drupal\Core\Asset\AttachedAssetsInterface $assets
   *   The assets attached to the current response.
   *   Note that this object is modified to reflect the final JavaScript
   *   settings assets.
   * @param bool $optimize
   *   Whether to apply the JavaScript asset collection optimizer, to return
   *   optimized JavaScript asset collections rather than an unoptimized ones.
   *
   * @return array
   *   A nested array containing 2 values:
   *   - at index zero: the (possibly optimized) collection of JavaScript assets
   *     for the top of the page
   *   - at index one: the (possibly optimized) collection of JavaScript assets
   *     for the bottom of the page
   */
  public function getWebcomponentsAssets(AttachedAssetsInterface $assets, $optimize);
}