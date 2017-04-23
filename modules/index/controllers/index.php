<?php
/**
 * @filesource index/controllers/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Index;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Template;
use \Gcms\Gcms;
use \Kotchasan\Http\Response;

/**
 * Controller หลัก สำหรับแสดง backend ของ GCMS
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

  /**
   * หน้าหลักเว็บไซต์ (index.html)
   *
   * @param Request $request
   */
  public function index(Request $request)
  {
    // ตัวแปรป้องกันการเรียกหน้าเพจโดยตรง
    define('MAIN_INIT', 'indexhtml');
    // session cookie
    $request->initSession();
    // ตรวจสอบการ login
    Login::create();
    // กำหนด skin ให้กับ template
    Template::init(self::$cfg->skin);
    // View
    Gcms::$view = new \Gcms\View;
    if ($login = Login::isMember()) {
      // โหลดเมนู
      $menu = \Index\Menu\Controller::init();
      // Controller หลัก
      $main = new \Index\Main\Controller;
      $bodyclass = 'mainpage';
    } else {
      // forgot, login, register
      $main = new \Index\Welcome\Controller;
      $bodyclass = 'loginpage';
    }
    // เนื้อหา
    Gcms::$view->setContents(array(
      // main template
      '/{MAIN}/' => $main->execute(self::$request),
      // title
      '/{TITLE}/' => $main->title(),
      // class สำหรับ body
      '/{BODYCLASS}/' => $bodyclass
    ));
    if ($login) {
      Gcms::$view->setContents(array(
        // ID สมาชิก
        '/{LOGINID}/' => $login['id'],
        // แสดงชื่อคน Login
        '/{LOGINNAME}/' => $login['name'],
        // เมนู
        '/{MENUS}/' => $menu->render($main->menu())
      ));
    }
    // ส่งออก เป็น HTML
    $response = new Response;
    $response->withContent(Gcms::$view->renderHTML())->send();
  }
}