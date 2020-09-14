<?php

namespace Drupal\ml_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block for top header.
 *
 * @Block(
 *   id = "header_top",
 *   admin_label = @Translation("Header Top"),
 *   category = @Translation("ML Core")
 * )
 */
class HeaderTopBlock extends BlockBase implements ContainerFactoryPluginInterface {

  const NODE_BUNDLE_MEDIA = 'media';

  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $account) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $build['#cache'] = [
      'contexts' => [
        'user',
      ],
      'max-age' => 0,
    ];

    return [
        '#theme' => 'header_top',
        '#fb_url' => 'https://www.facebook.com/twerklist',
        '#sitename' => $this->configFactory->get('system.site')->get('name'),
        '#add_media_link' => $this->getAddMediaLink(),
        '#user_items' => $this->getUserItems(),
      ] + $build;

  }

  /**
   * User specific items.
   *
   * @return array
   *   Items dependent on weather the user is logged in or not.
   */
  private function getUserItems() {
    if ($this->account->isAnonymous()) {
      return [
        Link::fromTextAndUrl($this->t('Log In'), Url::fromRoute('user.login')),
        $this->t('or'),
        Link::fromTextAndUrl($this->t('Register'), Url::fromRoute('user.register')),
      ];
    }
    else {
      return [
        $this->t('Hi'),
        Link::fromTextAndUrl($this->account->getDisplayName(), Url::fromRoute('user.page')),
        ['#markup' => '<span class="separator"></span>'],
        Link::fromTextAndUrl($this->t('Settings'), Url::fromRoute('entity.user.edit_form', ['user' => $this->account->id()])),
        Link::fromTextAndUrl($this->t('Log out'), Url::fromRoute('user.logout')),
      ];
    }
  }

  /**
   * Gets the link to Node add media page.
   *
   * @return \Drupal\Core\Link
   *   Either the Media add page link or the user registration page.
   */
  private function getAddMediaLink() {
    $linkText = $this->t('Add a video or image');
    if (!$this->account->isAnonymous()) {
      return Link::fromTextAndUrl($linkText, Url::fromRoute('node.add', ['node_type' => self::NODE_BUNDLE_MEDIA]));
    }

    return Link::fromTextAndUrl($linkText, Url::fromRoute('ml_user.account_required', ['context' => 'media_add']));
  }

}
