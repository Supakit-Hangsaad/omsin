<?php
/**
 * @filesource modules/index/controllers/menu.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Menu;

/**
 * รายการเมนู
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller
{
  /**
   * รายการเมนู (Backend)
   *
   * @var array
   */
  public $menus;

  /**
   * Controller สำหรับการโหลดเมนู
   *
   * @param array $login
   * @return \static
   */
  public static function init($login)
  {
    $obj = new static;
    // โหลดเมนู
    $obj->menus = \Index\Menu\Model::getMenus($login);
    return $obj;
  }

  /**
   * แสดงผลเมนู
   *
   * @param string $select
   * @return string
   */
  public function render($select)
  {
    return \Kotchasan\Menu::render($this->menus, $select);
  }
}