<?php
/**
 * @filesource Gcms/View.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Gcms;

/**
 * View base class สำหรับ GCMS.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\View
{

  /**
   * ouput เป็น HTML.
   *
   * @param string|null $template HTML Template ถ้าไม่กำหนด (null) จะใช้ index.html
   * @return string
   */
  public function renderHTML($template = null)
  {
    // เนื้อหา
    parent::setContents(array(
      // url สำหรับกลับไปหน้าก่อนหน้า
      '/{BACKURL(\?([a-zA-Z0-9=&\-_@\.]+))?}/e' => '\Gcms\View::back',
      /* ภาษา */
      '/{LNG_([^}]+)}/e' => '\Kotchasan\Language::get(array(1=>"$1"))',
      /* ภาษา ที่ใช้งานอยู่ */
      '/{LANGUAGE}/' => \Kotchasan\Language::name()
    ));
    return parent::renderHTML($template);
  }
}