<?php
/**
 * @filesource Gcms/Gcms.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Gcms;

use \Kotchasan\Language;

/**
 * GCMS utility class
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Gcms extends \Kotchasan\KBase
{
  /**
   * View
   *
   * @var \Gcms\View
   */
  public static $view;

  /**
   * อ่านภาษาที่ติดตั้งตามลำดับการตั้งค่า
   *
   * @return array
   */
  public static function installedLanguage()
  {
    $languages = array();
    foreach (self::$cfg->languages as $item) {
      $languages[$item] = $item;
    }
    foreach (Language::installedLanguage() as $item) {
      $languages[$item] = $item;
    }
    return array_keys($languages);
  }

  /**
   * คืนค่าลิงค์รูปแบบโทรศัพท์
   *
   * @param string $phone_number
   * @return string
   */
  public static function showPhone($phone_number)
  {
    if (preg_match('/^([0-9\-\s]{9,})(.*)$/', $phone_number, $match)) {
      return '<a href="tel:'.trim($match[1]).'">'.$phone_number.'</a>';
    }
    return $phone_number;
  }
}