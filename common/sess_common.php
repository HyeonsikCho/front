<?
session_start();
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
$fb = new FormBean();

$fb->addSession("sell_site", "1");

$is_login = false;
// 로그인 여부 체크
if (empty($fb->session("id")) === false) {
    $is_login = true;
}

$is_ba = "true";
// 가상계좌 여부
if ($is_login && empty($fb->session("bank_name")) === true) {
    $is_ba = "false";
}
?>
