<?php

namespace Drupal\events_management\Repository;

use Drupal\Core\Database\Database;
use Drupal\user\Entity\User;

class EventDB {

  /** @var \Drupal\Core\Database\Connection $db_conn */
  private static $db_conn;
  private static $initialized = false;

  private static function initialize() {
    if (self::$initialized) return;
    self::$db_conn = Database::getConnection();
    self::$initialized = true;
  }

  /**
   * This function : Add config log
   */
  public static function insert_config_log($fields)
  {
    self::initialize();

    $data = array_merge([
      'admin_id' => \Drupal::currentUser()->id(),
      'created_at' => time()
    ], $fields);

    $query = self::$db_conn->insert('config_logs')->fields($data);
    try{
      $exec = $query->execute();
    } catch (\Exception $e) {
      $exec = false;
    }
    return $exec;
  }


}
