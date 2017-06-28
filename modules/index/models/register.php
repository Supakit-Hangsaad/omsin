<?php
/**
 * @filesource modules/index/models/register.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Register;

use \Kotchasan\Http\Request;
use \Kotchasan\Language;
use \Gcms\Email;
use \Kotchasan\Validator;

/**
 * ลงทะเบียนสมาชิกใหม่
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * module=register
   *
   * @param Request $request
   */
  public function submit(Request $request)
  {
    // session, token
    if ($request->initSession() && $request->isSafe()) {
      $ret = array();
      // รับค่าจากการ POST
      $save = array(
        'username' => $request->post('register_email')->url(),
        'name' => $request->post('register_name')->topic(),
      );
      // ชื่อตาราง user
      $table = $this->getTableName('user');
      // database connection
      $db = $this->db();
      // username
      if (empty($save['username'])) {
        $ret['ret_register_email'] = 'Please fill in';
      } elseif (!Validator::email($save['username'])) {
        $ret['ret_register_email'] = Language::replace('Incorrect :name', array(':name' => Language::get('Email')));
      } else {
        // ตรวจสอบ username ซ้ำ
        $search = $db->first($table, array('username', $save['username']));
        if ($search) {
          $ret['ret_register_email'] = Language::replace('This :name already exist', array(':name' => Language::get('Email')));
        }
      }
      // password
      $password = $request->post('register_password')->topic();
      if (mb_strlen($password) < 4) {
        // รหัสผ่านต้องไม่น้อยกว่า 4 ตัวอักษร
        $ret['ret_register_password'] = 'Please fill in';
      } else {
        $save['password'] = sha1($password.$save['username']);
      }
      // name
      if (empty($save['name'])) {
        $ret['ret_register_name'] = 'Please fill in';
      }
      if (empty($ret)) {
        // บันทึก user
        $save['create_date'] = time();
        $save['status'] = 0;
        $save['fb'] = 0;
        $id = $db->insert($table, $save);
        // ส่งอีเมล์
        $replace = array(
          '/%NAME%/' => $save['name'],
          '/%EMAIL%/' => $save['username'],
          '/%PASSWORD%/' => $password
        );
        Email::send(2, 'member', $replace, $save['username']);
        // คืนค่า
        $ret['alert'] = Language::replace('Register successfully, We have sent complete registration information to :email', array(':email' => $save['username']));
        $ret['location'] = 'index.php?action=login';
        // clear
        $request->removeToken();
      }
      // คืนค่าเป็น JSON
      if (!empty($ret)) {
        echo json_encode($ret);
      }
    }
  }
}
