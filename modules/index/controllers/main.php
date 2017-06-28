<?php
/**
 * @filesource modules/index/controllers/main.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Main;

use \Kotchasan\Http\Request;
use \Kotchasan\Template;

/**
 * Controller หลัก สำหรับแสดงหน้าเว็บไซต์
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * หน้าหลักเว็บไซต์
   *
   * @param Request $request
   * @return string
   */
  public function execute(Request $request)
  {
    // โมดูลจาก URL ถ้าไม่มีใช้ default (dashboard)
    $module = $request->request('module', 'dashboard')->toString();
    if (preg_match('/^([a-z]+)([\/\-]([a-z]+))?$/i', $module, $match)) {
      if (empty($match[3])) {
        $owner = 'index';
        $module = $match[1];
      } else {
        $owner = $match[1];
        $module = $match[3];
      }
    } else {
      // ถ้าไม่ระบุ module มาแสดงหน้า dashboard
      $owner = 'index';
      $module = 'dashboard';
    }
    // ตรวจสอบหน้าที่เรียก
    if (is_file(APP_PATH.'modules/'.$owner.'/controllers/'.$module.'.php')) {
      // หน้าที่เรียก
      include APP_PATH.'modules/'.$owner.'/controllers/'.$module.'.php';
      $className = ucfirst($owner).'\\'.ucfirst($module).'\Controller';
    } else {
      // หน้า default ถ้าไม่พบหน้าที่เรียก
      include APP_PATH.'modules/index/controllers/dashboard.php';
      $className = 'Index\Dashboard\Controller';
    }
    $controller = new $className;
    // tempalate
    $template = Template::create('', '', 'main');
    $template->add(array(
      '/{CONTENT}/' => $controller->render($request)
    ));
    // ข้อความ title bar
    $this->title = $controller->title();
    // เมนูที่เลือก
    $this->menu = $controller->menu();
    return $template->render();
  }
}
