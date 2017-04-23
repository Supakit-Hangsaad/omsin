<?php
/**
 * @filesource index/controllers/welcome.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Welcome;

use \Kotchasan\Http\Request;

/**
 * หน้าแรกสุดก่อนเข้าระบบ
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * forgot, login register
   *
   * @param Request $request
   * @return string
   */
  public function execute(Request $request)
  {
    $action = $request->get('action')->toString();
    $action = in_array($action, array('register', 'forgot')) ? $action : 'login';
    $view = \Index\Welcome\View::$action($request);
    $this->title = $view->title;
    return $view->content;
  }
}