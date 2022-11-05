<?php
namespace Drupal\events_management\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\events_management\Repository\EventDB;
use Drupal\events_management\Utility\EventHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class EventsController extends ControllerBase
{
  /**
   * @return array
   * listing all events for admin only
   */
  public function admin_listing_events(){
    $events = EventDB::get_events();
    $curr_lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $categories=EventHelper::categories(null,$curr_lang);
    return [
      '#theme' => 'admin_listing_events',
      '#events' => $events,
      '#categories'=>$categories,
      '#pager' => [
        '#type' => 'pager',
      ]
    ];
  }

  /**
   * @return array
   * create new event for admin only
   */
  public function add_new_event(){
    $form = \Drupal::formBuilder()->getForm('Drupal\events_management\Form\NewEventForm');
    return [
      '#new_event_form' => $form,
      '#theme' => 'new_event_form_wrapper',
    ];
  }

  /**
   * @param $id
   * @return array
   * details event page for both admin and front end user
   */
  public function view_event($id){
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();

    $event=EventDB::get_event_details($id);
    $config_values = \Drupal::config('events_management.linkdev_task_settings')->get('site_settings');
    //if user not admin and (event not started or in past with not allowed to see past event) throw exception
    if(!in_array('administrator', $roles) && ($event['start_date']>time() || (empty($config_values['past_events'])&&$event['end_date']<time()))){
      throw new AccessDeniedHttpException();
    }
    $curr_lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $categories=EventHelper::categories(null,$curr_lang);
    $event['image'] =EventHelper::get_file_url($event['image']);
    return [
      '#theme' => 'event_details',
      '#event' => $event,
      '#categories'=>$categories,
    ];
  }

  /**
   * @param $id
   * @return array
   * edit event for admin only
   */
  public function edit_event($id){
    $form = \Drupal::formBuilder()->getForm('Drupal\events_management\Form\EditEventForm',$id);
    return [
      '#edit_event_form' => $form,
      '#theme' => 'edit_event_form_wrapper',
    ];
  }

  /**
   * @param $id
   * @return RedirectResponse
   * delete an event
   */
  public function delete_event($id){
    $deleted=EventDB::delete_event($id);
    if($deleted){
      \Drupal::messenger()->addStatus($this->t('Event deleted Successfully'));
    }else{
      \Drupal::messenger()->addError($this->t('Error, please try again later'));
    }
    return new RedirectResponse(Url::fromRoute('events_management.admin_listing_events')->setAbsolute()->toString());

  }

  /**
   * @return array
   * listing published events page for front end user
   */
  public function listing_events(){
    $events = EventDB::get_published_events();
    $curr_lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $categories=EventHelper::categories(null,$curr_lang);
    return [
      '#theme' => 'listing_events',
      '#events' => $events,
      '#categories'=>$categories,
      '#pager' => [
        '#type' => 'pager',
      ]
    ];
  }


}
