<?php
/**
 * @filesource index/controllers/loader.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Loader;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Template;
use \Gcms\Gcms;

/**
 * Controller สำหรับโหลดข้อมูลด้วย GLoader
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

  /**
   * มาจากการเรียกด้วย GLoader
   *
   * @param Request $request
   */
  public function index(Request $request)
  {
    // session, referer
    if ($request->initSession() && $request->isReferer()) {
      // ตัวแปรป้องกันการเรียกหน้าเพจโดยตรง
      define('MAIN_INIT', 'indexhtml');
      // ตรวจสอบการ login
      Login::create();
      // กำหนด skin ให้กับ template
      Template::init(self::$cfg->skin);
      // View
      Gcms::$view = new \Gcms\View;
      // โมดูลจาก URL ถ้าไม่มีใช้ default (dashboard)
      $module = $request->post('module', 'dashboard')->toString();
      if (preg_match('/^([a-z]+)([\/\-]([a-z]+))?$/i', $module, $match)) {
        if (empty($match[3])) {
          $owner = 'index';
          $module = $match[1];
        } else {
          $owner = $match[1];
          $module = $match[3];
        }
      } else {
        // หน้า default ถ้าไม่ระบุ module มา
        $owner = 'index';
        $module = 'dashboard';
      }
      // ตรวจสอบหน้าที่เรียก
      if (is_file(APP_PATH.'modules/'.$owner.'/controllers/'.$module.'.php')) {
        // หน้าที่เรียก (Admin)
        include APP_PATH.'modules/'.$owner.'/controllers/'.$module.'.php';
        $className = ucfirst($owner).'\\'.ucfirst($module).'\Controller';
      } else {
        // หน้า default ถ้าไม่พบหน้าที่เรียก
        include APP_PATH.'modules/index/controllers/dashboard.php';
        $className = 'Index\Dashboard\Controller';
      }
      $controller = new $className;
      // เนื้อหา
      Gcms::$view->setContents(array(
        '/{CONTENT}/' => $controller->render($request)
      ));
      // output เป็น HTML
      $ret = array(
        'detail' => Gcms::$view->renderHTML(Template::load('', '', 'loader')),
        'menu' => $controller->menu(),
        'topic' => $controller->title(),
        'to' => $request->post('to', 'scroll-to')->filter('a-z0-9_')
      );
      // คืนค่า JSON
      echo json_encode($ret);
    }
  }
}