<?php

namespace Drupal\events_management\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\etax_pos\Controller\PosController;
use Drupal\etax_settings\Utility\EtaxHelper;
use Drupal\events_management\Repository\EventDB;
use Drupal\events_management\Utility\EventHelper;
use Drupal\file\FileInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class EditEventForm.
 *
 * @package Drupal\events_management\Form
 */
class EditEventForm extends FormBase
{
  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'edit-event-form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$id=NULL)
  {
   $event=EventDB::get_event_details($id);
    if($event==false){
      throw new NotFoundHttpException();
    }

    $form['id'] = [
      '#type' => 'hidden',
      '#title' => $this->t(' ID'),
      '#required' => TRUE,
      '#default_value' => $id,
    ];
    $form['title'] = [
      '#type'         => 'textfield',
      '#title'         => $this->t('Title'),
      '#maxlength'    => 255,
      '#required'     => true,
      '#attributes' => [
        'placeholder'=> $this->t('Title'),
      ],
      '#default_value'=>$event['title']
    ];

    $form['image'] = [
      '#type' => 'managed_file',
      '#description'   => $this->t('Maximum size: @size - Extensions: @ext',
        ['@size' => '2 MB', '@ext' => 'png,jpg,jpeg and gif']),
      '#upload_location' => 'public://events',
      '#title' => $this->t('Image'),
      '#upload_validators' => [
        'file_validate_size' => [2 * 1024 * 1024],
        'file_validate_extensions' => ['png jpg jpeg gif'],
        '_events_management_validate_file_upload' => [],
      ],
      '#default_value' => !empty($model['image']) ? array($model['image']) : array(),

    ];

    $form['description'] = [
      '#type'         => 'textarea',
      '#title'         => $this->t('Description'),
      '#required'     => true,
      '#attributes' => [
        'placeholder'=> $this->t('Description'),
        'class'=>['form-control']
      ],
      '#default_value'=>$event['description']
    ];

    $form['start_date'] = [
      '#type' => 'date',
      '#title' => t('Start Date'),
      '#date_format' => "Y-m-d",
      '#required'     => true,
      '#default_value'=>date('Y-d-m', $event['start_date'])

    ];

    $form['end_date'] = [
      '#type' => 'date',
      '#title' => t('End Date'),
      '#date_format' => "Y-m-d",
      '#required'     => true,
      '#default_value'=>date('Y-d-m', $event['end_date'])

    ];

    $curr_lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $categories = EventHelper::categories(null,$curr_lang);
    $form['category'] = [
      '#type' => 'select',
      '#title'         => $this->t('Category'),
      '#options' => $categories,
      '#required' => true,
      '#attributes' => [
        'class' => ['form-control']
      ],
      '#default_value'=>$event['category']

    ];


    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#attributes' => [
        'class' => ['bizwheel-btn theme-2'],
      ],
    );

    $form_state->setCached(false);
    $form['#cache'] = ['max-age' => 0];
    $form['#theme'] = 'new_event_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $start_date = $form_state->getValue('start_date');
    $end_date = $form_state->getValue('end_date');
    if($end_date<$start_date){
      $form_state->setErrorByName('end_date', t('end date should be greater than start date'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $form_values = $form_state->getValues();
    unset($form_values['submit'], $form_values['form_build_id'], $form_values['form_token'],
      $form_values['form_id'], $form_values['op']);

    //Save Image
    if(count($form_values['image']) > 0) {
      EventHelper::saveImage($form_values['image'][0]);
      $form_values['image'] = $form_values['image'][0];
    }else{
      unset($form_values['image']);
    }

    $form_values['start_date'] = strtotime($form_values['start_date']);
    $form_values['end_date'] = strtotime($form_values['end_date']);

    $event = EventDB::update_event($form_values);
    if($event){
      \Drupal::messenger()->addStatus($this->t('Event updated successfully'));
    }else{
      \Drupal::messenger()->addError($this->t('Error, please try again later'));
    }

    $form_state->setRedirect('events_management.admin_listing_events');
  }

  function _events_management_validate_file_upload(FileInterface $file) {
    $fullname = $file->getFilename();
    $filename = explode('.',$fullname);
    $errors = [];
    if(count($filename)>2){
      $errors[] = t("File name should have one extension only");
    }elseif(count($filename)==2 && preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬]/', $filename[0]) ){
      $errors[] = t("File name shouldn't have special characters");
    }
    elseif(!in_array(mime_content_type($file->getFileUri()), ['image/png', 'image/jpeg', 'image/jpg','image/gif'])){
      $errors[] = t("File type not allowed");
    }
    return $errors;
  }

}
