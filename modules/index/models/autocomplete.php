<?php
/**
 * @filesource modules/index/models/autocomplete.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Autocomplete;

use \Kotchasan\Http\Request;
use \Gcms\Login;

/**
 * ค้นหาสมาชิก สำหรับ autocomplete
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * ค้นหาจากตาราง category สำหรับ autocomplete
   * คืนค่าเป็น JSON
   *
   * @param Request $request
   */
  public function findCategory(Request $request)
  {
    if ($request->initSession() && $request->isReferer() && Login::isMember()) {
      $search = $request->post('name')->topic();
      $query = $this->db()->createQuery()
        ->select('category_id id', 'topic name')
        ->from('category')
        ->where(array(
          array('owner_id', $request->post('id')->toInt()),
          array('id', $request->post('typ')->toInt()),
        ))
        ->order('owner_id DESC', 'topic')
        ->limit($request->post('count')->toInt())
        ->toArray();
      if ($search != '') {
        $query->andWhere(array('topic', 'LIKE', '%'.$search.'%'));
      }
      $result = $query->execute();
      // คืนค่า JSON
      if (!empty($result)) {
        echo json_encode($result);
      }
    }
  }
}