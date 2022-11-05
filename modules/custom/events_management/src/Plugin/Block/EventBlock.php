<?php

namespace Drupal\events_management\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\events_management\Repository\EventDB;
use Drupal\events_management\Utility\EventHelper;

/**
 * Provides a 'Event' Block.
 *
 * @Block(
 *   id = "event_block",
 *   admin_label = @Translation("Eevnt block"),
 *   category = @Translation("Event"),
 * )
 */
class EventBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $events=EventDB::get_latest_events();
    $categories=EventHelper::categories();
    return [
      '#theme' => 'event_block',
      '#events' => $events,
      '#categories'=>$categories
      ];
  }

}
