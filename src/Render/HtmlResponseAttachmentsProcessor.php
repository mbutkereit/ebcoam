<?php

namespace Drupal\ebcoam\Render;

use Drupal\Core\Render\HtmlResponseAttachmentsProcessor;
use Drupal\Core\Asset\AssetCollectionRendererInterface;
use Drupal\Core\Asset\AssetResolverInterface;
use Drupal\Core\Asset\AttachedAssetsInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\ebcoam\Asset\AssetResolverWebInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 *
 */
class HtmlResponseAttachmentsProcessor extends HtmlResponseAttachmentsProcessor {


  protected $webComponentCollectionRenderer;

  /**
   * Constructs a HtmlResponseAttachmentsProcessor object.
   *
   * @param \Drupal\Core\Asset\AssetResolverInterface $asset_resolver
   *   An asset resolver.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   A config factory for retrieving required config objects.
   * @param \Drupal\Core\Asset\AssetCollectionRendererInterface $css_collection_renderer
   *   The CSS asset collection renderer.
   * @param \Drupal\Core\Asset\AssetCollectionRendererInterface $js_collection_renderer
   *   The JS asset collection renderer.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   */
  public function __construct(
    AssetResolverInterface $asset_resolver,
    ConfigFactoryInterface $config_factory,
    AssetCollectionRendererInterface $css_collection_renderer,
    AssetCollectionRendererInterface $js_collection_renderer,
    RequestStack $request_stack,
    RendererInterface $renderer,
    ModuleHandlerInterface $module_handler,
    AssetCollectionRendererInterface $web_component_collection_renderer) {
    parent::__construct($asset_resolver, $config_factory, $css_collection_renderer, $js_collection_renderer, $request_stack, $renderer, $module_handler);
    $this->webComponentCollectionRenderer = $web_component_collection_renderer;
  }

  /**
   * Processes asset libraries into render arrays.
   *
   * @param \Drupal\Core\Asset\AttachedAssetsInterface $assets
   *   The attached assets collection for the current response.
   * @param array $placeholders
   *   The placeholders that exist in the response.
   *
   * @return array
   *   An array keyed by asset type, with keys:
   *     - styles
   *     - scripts
   *     - scripts_bottom
   */
  protected function processAssetLibraries(AttachedAssetsInterface $assets, array $placeholders) {
    $libraries = $assets->getLibraries();
    $libraries[] = 'ebcoam/polymer';
    $assets->setLibraries($libraries);
    $variables = parent::processAssetLibraries($assets, $placeholders);
    // Print scripts - if any are present.
    $assetResolver = $this->assetResolver;
    if (isset($placeholders['webcomponent']) && $assetResolver instanceof AssetResolverWebInterface) {

      // $optimize_js = !defined('MAINTENANCE_MODE') && !\Drupal::state()->get('system.maintenance_mode') && $this->config->get('js.preprocess');.
      $result = $assetResolver->getWebcomponentsAssets($assets, FALSE);
      $variables['webcomponent'] = $this->webComponentCollectionRenderer->render($result);
    }

    return $variables;
  }

}
