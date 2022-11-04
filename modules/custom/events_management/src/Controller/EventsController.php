<?php
namespace Drupal\events_management\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\events_management\Repository\EventDB;
use Drupal\events_management\Utility\EventHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class EventsController extends ControllerBase
{

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

  public function add_new_event(){
    $form = \Drupal::formBuilder()->getForm('Drupal\events_management\Form\NewEventForm');
    return [
      '#new_event_form' => $form,
      '#theme' => 'new_event_form_wrapper',
    ];
  }
  public function view_event($id){
    $event=EventDB::get_event_details($id);
    $curr_lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $categories=EventHelper::categories(null,$curr_lang);
    $event['image'] =EventHelper::get_file_url($event['image']);
    return [
      '#theme' => 'event_details',
      '#event' => $event,
      '#categories'=>$categories,
    ];
  }
  public function edit_event($id){
    $form = \Drupal::formBuilder()->getForm('Drupal\events_management\Form\EditEventForm',$id);
    return [
      '#edit_event_form' => $form,
      '#theme' => 'edit_event_form_wrapper',
    ];
  }
  public function delete_event($id){
    $deleted=EventDB::delete_event($id);
    if($deleted){
      \Drupal::messenger()->addStatus($this->t('Event deleted Successfully'));
    }else{
      \Drupal::messenger()->addError($this->t('Error, please try again later'));
    }
    return new RedirectResponse(Url::fromRoute('events_management.admin_listing_events')->setAbsolute()->toString());

  }


}
