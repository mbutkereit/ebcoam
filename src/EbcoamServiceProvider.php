<?php

namespace Drupal\ebcoam;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Modifies the language manager service.
 */
class EbcoamServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    if ($container->has('library.discovery.parser')) {
      try {
        $definition = $container->getDefinition('library.discovery.parser');
        $definition->setClass('Drupal\ebcoam\Asset\LibraryDiscoveryParser');

        $definition = $container->getDefinition('html_response.attachments_processor');
        $definition->setClass('Drupal\ebcoam\Render\HtmlResponseAttachmentsProcessor');
        $definition->addArgument(new Reference('asset.web_components.collection_renderer'));

        $definition = $container->getDefinition('asset.resolver');
        $definition->setClass('Drupal\ebcoam\Asset\AssetResolver');

      }
      catch (\Exception $e) {
      }
    }
  }

}
