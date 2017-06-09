<?php

namespace Drupal\test_block\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Render\Markup;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Syndicate' block that links to the site's RSS feed.
 *
 * @Block(
 *   id = "comm_press_block",
 *   admin_label = @Translation("CommPressBlock"),
 *   category = @Translation("System")
 * )
 */
class CommPressBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'block_count' => 10,
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#attached' => ['library' => ['test_block/carousel']],
      '#markup' => Markup::create('<my-carousel><my-carousel>'),
    ];
  }

}
