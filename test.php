#! /usr/local/bin/php -f
<?php
function http_digest_parse($txt) {
	$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
	$data = array();
	preg_match_all('@(\w+)=(?:(?:\'([^\']+)\'|"([^"]+)")|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
	foreach ($matches as $m) {
		$data[$m[1]] = $m[2]?$m[2]:($m[3]?$m[3]:$m[4]);
		unset($needed_parts[$m[1]]);
	}
	return $needed_parts ? false : $data;
}
function is_auth() {
	$users = array('jmnote'=>'P@ssw0rd', 'guest'=>'guest');
	if(empty($_SERVER['PHP_AUTH_DIGEST']))return false; // 오류
	$data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']);
	if($data === false)return false; // 오류
	global $username;
	$username = $data['username'];
	if(!isset($users[$username])) return false; // 아이디 틀림
	$ha1 = md5($username.':'.$data['realm'].':'.$users[$username]);
	$ha2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
	$response = md5($ha1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$ha2);
	if($data['response'] != $response) return false; // 패스워드 틀림
	return true;
}
if( !is_auth() ) {
	$realm = 'Digest Auth Test';
	header('HTTP/1.1 401 Unauthorized');
	header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
	echo '<meta charset="utf-8">';
	echo '로그인이 필요합니다.';
	exit;
}
echo '<meta charset="utf-8">';
echo "$username 님 반갑습니다.";
