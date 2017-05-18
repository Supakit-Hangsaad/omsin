<?php
/**
 * @filesource index/models/fblogin.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Fblogin;

use \Kotchasan\Http\Request;
use \Kotchasan\Language;

/**
 * Facebook Login
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  public function chklogin(Request $request)
  {
    // session, token
    if ($request->initSession() && $request->isSafe()) {
      // สุ่มรหัสผ่านใหม่
      $password = uniqid();
      // ข้อมูลที่ส่งมา
      $save = array(
        'username' => $request->post('id')->number(),
        'name' => trim($request->post('first_name')->topic().' '.$request->post('last_name')->topic()),
      );
      // db
      $db = $this->db();
      // table
      $user_table = $this->getTableName('user');
      // ตรวจสอบสมาชิกกับ db
      $search = $db->createQuery()
        ->from('user')
        ->where(array('username', $save['username']))
        ->toArray()
        ->first('id', 'username', 'name', 'visited', 'fb', 'status');
      if ($search === false) {
        $save['status'] = 0;
        $save['fb'] = 1;
        $save['visited'] = 0;
        $save['password'] = sha1($password.$save['username']);
        $save['lastvisited'] = time();
        $save['create_date'] = time();
        $save['id'] = $db->insert($user_table, $save);
      } elseif ($search['fb'] == 1) {
        // facebook เคยเยี่ยมชมแล้ว อัปเดทการเยี่ยมชม
        $save = $search;
        $save['visited'] ++;
        $save['lastvisited'] = time();
        $save['ip'] = $request->getClientIp();
        $save['password'] = sha1($password.$search['username']);
        $db->update($user_table, $save['id'], $save);
      } else {
        // ไม่สามารถ login ได้ เนื่องจากมี email อยู่ก่อนแล้ว
        $save = false;
        $ret['alert'] = Language::replace('This :name already exist', array(':name' => Language::get('Username')));
        $ret['isMember'] = 0;
      }
      if (is_array($save)) {
        // clear
        $request->removeToken();
        // login
        $save['password'] = $password;
        $_SESSION['login'] = $save;
        // คืนค่า
        $ret['isMember'] = 1;
        $ret['alert'] = Language::replace('Welcome %s, login complete', array('%s' => $save['name']));
      }
      // คืนค่าเป็น json
      echo json_encode($ret);
    }
  }
}