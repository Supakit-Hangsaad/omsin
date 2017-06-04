<?php
/**
 * @filesource Kotchasan/Curl.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Kotchasan;

/**
 * Curl Class
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.1
 */
class Curl
{
  /**
   * URL of the session
   *
   * @var string
   */
  protected $url;
  /**
   * พารามิเตอร์ CURLOPT
   *
   * @var array
   */
  protected $options = array();
  /**
   * HTTP headers
   *
   * @var array
   */
  protected $headers = array();
  /**
   * ตัวแปรสำหรับเก็บ Error ที่มาจาก cURL
   * สำเร็จ คืนค่า false
   * ไม่สำเร็จ คืนค่าข้อความ error
   *
   * @var boolean|string
   */
  protected $error = false;

  /**
   * Construct
   *
   * @param string $url URL ที่ใช้ในการส่ง request
   * @throws \ErrorException ถ้าไม่รองรับ cURL
   */
  public function __construct($url)
  {
    if (!extension_loaded('curl')) {
      throw new \ErrorException('cURL library is not loaded');
    }
    $this->url = $url;
    // default parameter
    $this->headers = array(
      'Connection' => 'keep-alive',
      'Keep-Alive' => '300',
      'Accept-Charset' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
      'Accept-Language' => 'en-us,en;q=0.5'
    );
    $this->options = array(
      CURLOPT_TIMEOUT => 30,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FAILONERROR => true,
      CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36',
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
    );
  }

  /**
   * คืนค่า Error จากการ execute
   *
   * @return string
   * */
  function error()
  {
    return $this->error;
  }

  /**
   * DELETE
   *
   * @param array $params
   * @return $this
   */
  public function delete($params)
  {
    if (is_array($params)) {
      $this->options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
      $this->options[CURLOPT_POSTFIELDS] = http_build_query($params, NULL, '&');
    }
    return $this;
  }

  /**
   * GET
   *
   * @param array $params
   * @return $this
   */
  public function get($params = array())
  {
    $this->options[CURLOPT_CUSTOMREQUEST] = 'GET';
    $this->options[CURLOPT_HTTPGET] = true;
    if (is_array($params)) {
      $this->url .= (strpos($this->url, '?') === false ? '?' : '&').http_build_query($params, NULL, '&');
    }
    return $this;
  }

  /**
   * HEAD
   *
   * @param array $params
   * @return $this
   */
  public function head($params = array())
  {
    $this->options[CURLOPT_CUSTOMREQUEST] = 'HEAD';
    $this->options[CURLOPT_NOBODY] = true;
    if (is_array($params)) {
      $this->options[CURLOPT_POSTFIELDS] = http_build_query($params, NULL, '&');
    }
    return $this;
  }

  /**
   * POST
   *
   * @param array $params
   * @return $this
   */
  public function post($params = array())
  {
    $this->options[CURLOPT_CUSTOMREQUEST] = 'POST';
    $this->options[CURLOPT_POST] = true;
    if (is_array($params)) {
      $this->options[CURLOPT_POSTFIELDS] = http_build_query($params, NULL, '&');
    }
    return $this;
  }

  /**
   * PUT
   *
   * @param array $params
   * @return $this
   */
  public function put($params = array())
  {
    $this->options[CURLOPT_CUSTOMREQUEST] = 'PUT';
    if (is_array($params)) {
      $this->options[CURLOPT_POSTFIELDS] = http_build_query($params, NULL, '&');
    }
    return $this;
  }

  /**
   * กำหนด referer
   *
   * @param string $referrer
   * @return $this
   */
  public function referer($referrer)
  {
    $this->options[CURLOPT_REFERER] = $referrer;
    return $this;
  }

  /**
   * ใช้งาน PROXY
   *
   * @param string $url
   * @param int $port
   * @param string $username
   * @param string $password
   * @return $this
   */
  public function httpproxy($url = '', $port = 80, $username = null, $password = null)
  {
    $this->options[CURLOPT_HTTPPROXYTUNNEL] = true;
    $this->options[CURLOPT_PROXY] = $url.':'.$port;
    if ($username !== null && $password !== null) {
      $this->options[CURLOPT_PROXYUSERPWD] = $username.':'.$password;
    }
    return $this;
  }

  /**
   * Login สำหรับการส่งแบบ HTTP
   *
   * @param string $username
   * @param string $password
   * @param string $type any (default), digest, basic, digest_ie, negotiate, ntlm, ntlm_wb, anysafe, only
   * @return $this
   */
  public function httpauth($username = '', $password = '', $type = 'any')
  {
    $this->options[CURLOPT_HTTPAUTH] = constant('CURLAUTH_'.strtoupper($type));
    $this->options[CURLOPT_USERPWD] = $username.':'.$password;
    return $this;
  }

  /**
   * กำหนด Header
   *
   * @param array $headers
   * @return $this
   */
  public function setHeaders($headers)
  {
    foreach ($headers as $key => $value) {
      $this->headers[$key] = $value;
    }
    return $this;
  }

  /**
   * กำหนด Options
   *
   * @param array $options
   * @return $this
   */
  public function setOptions($options)
  {
    foreach ($options as $key => $value) {
      $this->options[$key] = $value;
    }
    return $this;
  }

  /**
   * ประมวลผล cURL
   *
   * @return string
   */
  public function execute()
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->url);
    if (!empty($this->headers)) {
      $headers = array();
      foreach ($this->headers as $key => $value) {
        $headers[] = $key.': '.$value;
      }
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    foreach ($this->options as $key => $value) {
      curl_setopt($ch, $key, $value);
    }
    $response = curl_exec($ch);
    if (!$response) {
      $this->error = curl_error($ch).' ['.curl_errno($ch).']';
    }
    curl_close($ch);
    return $response;
  }
}
