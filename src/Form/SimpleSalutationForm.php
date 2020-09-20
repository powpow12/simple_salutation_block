<?php

namespace Drupal\simple_salutation_block\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure example settings for this site.
 */
class SimpleSalutationForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'simple_salutation_form.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'simple_salutation_block_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['greetings'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Greetings'),
      '#default_value' => $config->get('greetings'),
      '#description' => t('This is the text to be displayed to all users when they log in.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      // Set the submitted configuration setting.
      ->set('greetings', $form_state->getValue('greetings'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
