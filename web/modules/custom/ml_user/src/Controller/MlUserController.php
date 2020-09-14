<?php

namespace Drupal\ml_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Class MlUserController
 *
 * @package Drupal\ml_user\Controller
 */
class MlUserController extends ControllerBase {

  /**
   * Prompt User to Log In or create an account.
   *
   * @param string $context
   *   Used for refining the message.
   *
   * @return array
   *   Rendered array.
   */
  public function promptUser($context) {

    switch ($context) {
      case 'media_add':
        $messageContext = $this->t('add content');
        break;
      default:
        $messageContext = $this->t('perform this action');
    }

    $build = [
      'header' => [
        '#markup' => $this->t('Ooops, looks like you need an account to @context_msg', [
          '@context_msg' => $messageContext,
        ]),
      ],
    ];

    $build['prompt'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#items' => [
        $this->t('Please'),
        Link::fromTextAndUrl($this->t('Log In'), Url::fromRoute('user.login')),
        $this->t('OR'),
        Link::fromTextAndUrl($this->t('Create an account'), Url::fromRoute('user.register')),
      ],
      '#attributes' => ['class' => 'prompt-account'],
      '#wrapper_attributes' => ['class' => 'container'],
    ];

    return $build;
  }
}
