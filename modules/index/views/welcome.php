<?php
/**
 * @filesource index/views/welcome.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Welcome;

use \Kotchasan\Http\Request;
use \Kotchasan\Language;
use \Kotchasan\Template;
use \Kotchasan\Login;

/**
 * Login, Forgot, Register
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

  public static function login(Request $request)
  {
    // template
    $template = Template::create('', '', 'login');
    $template->add(array(
      '/{TOKEN}/' => $request->createToken(),
      '/{EMAIL}/' => Login::$text_username,
      '/{PASSWORD}/' => Login::$text_password,
      '/{MESSAGE}/' => Login::$login_message,
      '/{CLASS}/' => empty(Login::$login_message) ? 'hidden' : (empty(Login::$login_input) ? 'message' : 'error')
    ));
    return (object)array(
        'content' => $template->render(),
        'title' => self::$cfg->web_description.' - '.Language::get('Login with an existing account')
    );
  }

  public static function forgot(Request $request)
  {
    // template
    $template = Template::create('', '', 'forgot');
    $template->add(array(
      '/{TOKEN}/' => $request->createToken(),
      '/{EMAIL}/' => Login::$text_username,
      '/{MESSAGE}/' => Login::$login_message,
      '/{CLASS}/' => empty(Login::$login_message) ? 'hidden' : (empty(Login::$login_input) ? 'message' : 'error')
    ));
    return (object)array(
        'content' => $template->render(),
        'title' => self::$cfg->web_description.' - '.Language::get('Get new password')
    );
  }

  public static function register(Request $request)
  {
    // template
    $template = Template::create('', '', 'register');
    $template->add(array(
      '/{Terms of Use}/' => '<a href="{WEBURL}index.php?module=terms">{LNG_Terms of Use}</a>',
      '/{Privacy Policy}/' => '<a href="{WEBURL}index.php?module=policy">{LNG_Privacy Policy}</a>',
      '/{TOKEN}/' => $request->createToken(),
    ));
    return (object)array(
        'content' => $template->render(),
        'title' => self::$cfg->web_description.' - '.Language::get('Register')
    );
  }
}