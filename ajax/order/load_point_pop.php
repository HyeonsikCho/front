<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/doc/order/SheetPopup.php");

$frontUtil = new FrontCommonUtil();

if ($is_login === false) {
    $frontUtil->errorGoBack("로그인 상태가 아닙니다.");
    exit;
}

$fb = new FormBean();

$pay_price = $fb->form("pay_price");

$session = $fb->getSession();
unset($fb);

$own_point = $session["own_point"];

echo pointPopup($own_point, $pay_price);

exit;
?>
