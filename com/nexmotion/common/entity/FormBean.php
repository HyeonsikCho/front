<?
ini_set("session.gc_probability", 1);
ini_set("session.gc_divisor", 1);
ini_set("session.gc_maxlifetime", 43200);

class FormBean {

	var $fb; //form
	var $ss; //session
	var $sessionpath = "/tmp";

	function __construct() {
		$this->initFormBean();
	}

	function initFormBean() {
		global $_POST, $_GET, $_SESSION;

		session_start();

		$this->fb = array();
		$this->ss = array();

		while ( list($key, $val) = each($_GET) ) $this->fb[$key] = $val;
		while ( list($key, $val) = each($_POST) ) $this->fb[$key] = $val;
		while ( list($key, $val) = each($_SESSION) ) $this->ss[$key] = $val;

		reset($_GET);
		reset($_POST);
		reset($_SESSION);
	}

	function base64decode() {
		while ( list($key, $val) = each($this->fb) ) $this->fb[$key] = base64_decode($val);
		reset($this->fb);
	}

	function overwritePHPSession() {
		global $_SESSION;
		$sdata = "";
		$file = sprintf("%s/sess_%s", $this->sessionpath, $this->form("PHPSESSID"));
		$fd = fopen($file, "r");
		if ( !$fd ) return;
		while ( !feof($fd) ) {
			$sdata .= fgets($fd, 8192);
		}
		fclose($fd);
		session_decode($sdata);
		while ( list($key, $val) = each($_SESSION) ) $this->ss[$key] = $val;
		reset($_SESSION);
	}

	function printForm() {
		echo "<pre>";
		print_r($this->fb);
		echo "</pre>";
	}

	function getForm() {
		return $this->fb;
	}

	function getSession() {
		return $this->ss;
	}

	function form($key) {
		return $this->fb[$key];
	}

	function session($key) {
		return $this->ss[$key];
	}

	function addSession($key, $val) {
		$_SESSION[$key] = $val;
		$this->ss[$key] = $val;
	}

	function removeSession($key) {
		$_SESSION[$key] = "";
		$this->ss[$key] = "";
	}

	function removeAllSession() {
		session_destroy();
		$this->ss = "";
	}

	function formLike($str) {

		if ( !$str ) return "";

		$len = strlen($str);
		$ret = "";

		while ( list($key, $val) = each($this->fb)) {

			$pos = strpos($key, $str);

			if ( $pos !== false && $pos == 0 ) {
				$key = substr($key, $pos + $len);
				$ret[$key] = $val;
			}
		}

		reset($this->fb);

		return $ret;

	}

	function formLikeNTAG($str) {

		if ( !$str ) return "";

		$len = strlen($str);
		$ret = "";

		while ( list($key, $val) = each($this->fb)) {

			$pos = strpos($key, $str);

			if ( $pos !== false && $pos == 0 ) {
				$key = substr($key, $pos + $len);

				//$val = str_replace("\"", "&quot;", $val);
				//$val = str_replace("'", "&#039;", $val);
				$ret[$key] = $val;
			}
		}

		reset($this->fb);

		return $ret;

	}

	//post, get 으로 넘어온 데이터를 반환
	function getdata() {
		while ( list($key, $val) = each($this->fb) ) {
			    $getmsg .= $key . "=" . $val . "&";
		}

		return $getmsg;
	}

	//post, get 으로 넘어온 데이터를 반환(urlencode)
	function getdataURLEncode() {
		while ( list($key, $val) = each($fb->fb) ) {
			$getmsg .= $key . "=" . urlencode($val) . "&";
		}

		return $getmsg;
	}

}
?>
