<?php
/**
 * @filesource Gcms/Login.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Gcms;

use \Kotchasan\Model;
use \Kotchasan\Language;
use \Kotchasan\Http\Request;

/**
 * คลาสสำหรับตรวจสอบการ Login
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Login extends \Kotchasan\Login implements \Kotchasan\LoginInterface
{

  /**
   * ฟังก์ชั่นตรวจสอบสมาชิกกับฐานข้อมูล
   *
   * @param string $username
   * @param string $password
   * @return array|string คืนค่าข้อมูลสมาชิก (array) ไม่พบคืนค่าข้อความผิดพลาด (string)
   */
  public static function checkMember($username, $password)
  {
    $where = array();
    foreach (self::$cfg->login_fields as $field) {
      $where[] = array($field, $username);
    }
    // model
    $model = new Model;
    $query = $model->db()->createQuery()
      ->select()
      ->from('user')
      ->where($where, 'OR')
      ->order('status DESC')
      ->toArray();
    $login_result = null;
    foreach ($query->execute() as $item) {
      if ($item['password'] == sha1($password.$item[reset(self::$cfg->login_fields)])) {
        $login_result = $item;
        break;
      }
    }
    if ($login_result === null) {
      // user หรือ password ไม่ถูกต้อง
      self::$login_input = isset($item) ? 'password' : 'username';
      return isset($item) ? Language::replace('Incorrect :name', array(':name' => Language::get('Password'))) : Language::get('not a registered user');
    } elseif (!empty($login_result['ban'])) {
      // ติดแบน
      self::$login_input = 'username';
      return Language::get('Members were suspended');
    } else {
      return $login_result;
    }
  }

  /**
   * ฟังก์ชั่นตรวจสอบการ login และบันทึกการเข้าระบบ
   *
   * @param string $username
   * @param string $password
   * @return string|array เข้าระบบสำเร็จคืนค่าแอเรย์ข้อมูลสมาชิก, ไม่สำเร็จ คืนค่าข้อความผิดพลาด
   */
  public function checkLogin($username, $password)
  {
    // ตรวจสอบสมาชิกกับฐานข้อมูล
    $login_result = self::checkMember($username, $password);
    if (is_string($login_result)) {
      return $login_result;
    } else {
      // model
      $model = new Model;
      // ip ที่ login
      $ip = self::$request->getClientIp();
      // current session
      $session_id = session_id();
      // อัปเดทการเยี่ยมชม
      if ($session_id != $login_result['session_id']) {
        $login_result['visited'] ++;
        $model->db()->createQuery()
          ->update('user')
          ->set(array(
            'session_id' => $session_id,
            'visited' => $login_result['visited'],
            'lastvisited' => time(),
            'ip' => $ip
          ))
          ->where((int)$login_result['id'])
          ->execute();
      }
    }
    return $login_result;
  }

  /**
   * ฟังก์ชั่นส่งอีเมล์ลืมรหัสผ่าน
   */
  public function forgot(Request $request)
  {
    // ค่าที่ส่งมา
    $username = $request->post('login_username')->url();
    if (empty($username)) {
      if ($request->post('action')->toString() === 'forgot') {
        self::$login_message = Language::get('Please fill in');
      }
    } else {
      self::$text_username = $username;
      // ชื่อฟิลด์สำหรับตรวจสอบอีเมล์
      $field = reset(self::$cfg->login_fields);
      // ค้นหาอีเมล์
      $model = new Model;
      $table = $model->getTableName('user');
      $search = $model->db()->first($table, array(array($field, $username), array('fb', '0')));
      if ($search === false) {
        self::$login_message = Language::get('not a registered user');
      } else {
        // สุ่มรหัสผ่านใหม่
        $password = \Kotchasan\Text::rndname(6);
        // ข้อมูลอีเมล์
        $replace = array(
          '/%PASSWORD%/' => $password,
          '/%EMAIL%/' => $search->$field
        );
        // send mail
        $err = \Gcms\Email::send(3, 'member', $replace, $search->$field);
        if (!$err->error()) {
          // อัปเดทรหัสผ่านใหม่
          $model->db()->update($table, (int)$search->id, array('password' => sha1($password.$search->$field)));
          // คืนค่า
          self::$login_message = Language::get('Your message was sent successfully');
          self::$request = $request->withQueryParams(array('action' => 'login'));
        } else {
          self::$login_message = $err->getErrorMessage();
        }
      }
    }
  }
}
