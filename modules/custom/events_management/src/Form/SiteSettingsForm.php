<?php

namespace Drupal\events_management\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\events_management\Repository\EtaxDB;
use Drupal\events_management\Utility\EtaxHelper;

class SiteSettingsForm extends ConfigFormBase
{
    public function getFormId() {
        return 'site_settings_form';
    }

    public function getEditableConfigNames() {
        return ['events_management.site_settings'];
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

        $config_values = \Drupal::config('events_management.linkdev_task_settings')->get('site_settings');

        $form['past_events'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Show Past Events'),
          '#attributes' => [
            'class' => ['form-control']
          ],
          '#default_value'=> $config_values['past_events'] ?: '',
        ];
        $form['events_numbers'] = [
            '#type'         => 'number',
            '#title'        => $this->t('A number of events to list on the listing page'),
            '#required' => true,
            '#min'=>5,
            '#max'=>100,
            '#default_value'=> $config_values['events_numbers'] ?: '',
            '#attributes' => [
            'class' => ['form-control']
            ],
        ];

        $form['submit'] = [
            '#type'     => 'submit',
            '#value'    => $this->t('Save'),
            '#weight'   => 999,
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {}

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $values = $form_state->cleanValues()->getValues();
        \Drupal::configFactory()->getEditable('events_management.linkdev_task_settings')
            ->set('site_settings', $values)
            ->save();
        \Drupal::messenger()->addStatus($this->t('Successfully saved settings.'));
    }
}
