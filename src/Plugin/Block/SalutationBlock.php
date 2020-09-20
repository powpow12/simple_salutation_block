<?php

namespace Drupal\simple_salutation_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Salutation block' Block.
 *
 * @Block(
 *   id = "salutation_block",
 *   admin_label = @Translation("Salutation block"),
 *   category = @Translation("Salutation block"),
 * )
 */
class SalutationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Drupal account to use for checking for profile info.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  protected $config;

  /**
   * SalutationBlock constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account for which view access should be checked.
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The salutation block config.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountInterface $account, ConfigFactory $config) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->account = $account;
    $this->config = $config->get('simple_salutation_form.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $display_name = $this->account->getDisplayName();
    /* @var \Drupal\Core\Session\UserSession $user_session */
    $user_session = $this->account->getAccount();
    $last_logged_in = $user_session->login;
    // Date format as, 'December 21st, 2012 9:01 am'.
    $date_format = 'F dS, Y g:h a';
    $dd_time = DrupalDateTime::createFromTimestamp($last_logged_in)->format($date_format);
    $user_id = $this->account->id();

    $profile_link = Link::createFromRoute('Visit your profile', 'entity.user.canonical', ['user' => $user_id]);

    $hello_user = $this->t('Hello %username!', ['%username' => $display_name]);
    $login_date = $this->t('Your last log in was @last_login_date.', ['@last_login_date' => $dd_time]);
    $user_profile = $this->t('@profile_link', ['@profile_link' => $profile_link->toString()]);

    $user_greeting = [$hello_user, $login_date, $user_profile];

    if ($this->config->get('greetings')) {
      $user_greeting[] = $this->t('@custom_greetings', ['@custom_greetings' => $this->config->get('greetings')]);
    }

    return [
      '#theme' => 'simple_salutation_block',
      '#user_greeting' => $user_greeting,
      '#cache' => [
        'contexts' => [
          'user',
        ]
      ],
    ];
  }


}
