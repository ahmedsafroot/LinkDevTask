<?php

namespace Drupal\events_management\Utility;
use Drupal\file\Entity\File;
class EventHelper {


  /**
   * @param $fid
   * @return void
   * @throws \Drupal\Core\Entity\EntityStorageException
   * This function save image permanent
   */
  public static function saveImage($fid) {
    $file = File::load($fid);
    $file->setPermanent();
    $file->save();
  }

  public static function categories($category_id = null, $lang_code = null){
    $all_categories= [
      '1' => t('sport', array(), array('langcode' => $lang_code)),
      '2' => t('politics', array(), array('langcode' => $lang_code)),
      '3' => t('social', array(), array('langcode' => $lang_code)),

    ];
    if(!is_null($category_id))
      return $all_categories[$category_id];
    return $all_categories;
  }

  public static function get_file_url($fid){
    if(is_numeric($fid)){
      $file = File::load($fid);
      if($file){
        return \Drupal::service('file_url_generator')->generateString($file->getFileUri());
      }
    }
    return null;
  }

}
