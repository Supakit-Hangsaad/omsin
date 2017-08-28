<?php
/**
 * @filesource modules/index/controllers/dashboard.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Dashboard;

use \Kotchasan\Http\Request;
use \Kotchasan\Login;
use \Kotchasan\Html;

/**
 * module=dashboard
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * Dashboard
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // สมาชิก
    if ($login = Login::isMember()) {
      // ข้อความ title bar
      $this->title = self::$cfg->web_title.' - '.self::$cfg->web_description;
      // เลือกเมนู
      $this->menu = 'dashboard';
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-home">{LNG_Home}</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h2 class="icon-dashboard">{LNG_Dashboard}</h2>'
      ));
      $section->add('a', array(
        'id' => "ierecord",
        'href' => WEB_URL.'index.php?module=ierecord',
        'title' => "{LNG_Recording} {LNG_Income}/{LNG_Expense}",
        'class' => 'icon-edit notext'
      ));
      // แสดงฟอร์ม
      $section->appendChild(createClass('Index\Dashboard\View')->render($request, $login));
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}