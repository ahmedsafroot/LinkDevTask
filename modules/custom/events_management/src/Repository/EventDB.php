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

  /**
   * This function get events
   */
  public static function get_events(){
    self::initialize();

    $query = self::$db_conn->select('events', 'e');
    $query->fields('e');
    $config_values = \Drupal::config('events_management.linkdev_task_settings')->get('site_settings');

    if(empty($config_values['events_numbers'])){
      $num_events=10;
    }else{
      $num_events=$config_values['events_numbers'];
    }

    $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->limit($num_events);
    $result = $pager->execute()->fetchAll(\PDO::FETCH_ASSOC);
    return $result;
  }

  /**
   * This function : insert event
   */
  public static function insert_event($fields)
  {
    self::initialize();

    $data = array_merge([
      'admin_id' => \Drupal::currentUser()->id(),
      'created_at' => time()
    ], $fields);

    $query = self::$db_conn->insert('events')->fields($data);
    try{
      $exec = $query->execute();
    } catch (\Exception $e) {
      $exec = false;
    }
    return $exec;
  }

  /**
   * This function get event by id
   * @param $event_id
   */
  public static function get_event_details($event_id){
    self::initialize();

    $query = self::$db_conn->select('events', 'e');
    $query->fields('e',[]);
    $query->condition('e.id', $event_id);
    $result = $query->execute()->fetchAssoc();
    return $result;
  }

  /**
   * This function : update event
   * @param $event
   * @return mixed
   */
  public static function update_event($event){
    self::initialize();

    $query = self::$db_conn->update('events');
    $query->fields($event);
    $query->condition('id', $event['id']);
    $result = $query->execute();
    return $result;
  }

  /**
   * This function : delete event
   * @param $id
   * @return mixed
   */
  public static function delete_event($id){
    self::initialize();

    $query = self::$db_conn->delete('events');
    $query->condition('id', $id);
    try{
      $exec = $query->execute();
    } catch (\Exception $e) {
      $exec = false;
    }
    return $exec;
  }

  /**
   * This function get published events
   */
  public static function get_published_events(){
    self::initialize();

    $query = self::$db_conn->select('events', 'e');
    $query->fields('e');
    $config_values = \Drupal::config('events_management.linkdev_task_settings')->get('site_settings');
    $query->condition('e.start_date', time(),'<=');
    if(empty($config_values['past_events']) || $config_values['past_events']==0){
      $query->condition('e.end_date', time(),'>=');
    }

    if(empty($config_values['events_numbers'])){
      $num_events=10;
    }else{
      $num_events=$config_values['events_numbers'];
    }

    $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->limit($num_events);
    $result = $pager->execute()->fetchAll(\PDO::FETCH_ASSOC);
    return $result;
  }

}
