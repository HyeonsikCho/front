<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/SheetDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/doc/order/SheetPopup.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_lib/CommonUtil.php");

$frontUtil = new FrontCommonUtil();

if ($is_login === false) {
    $frontUtil->errorGoBack("로그인 상태가 아닙니다.");
    exit;
}

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$commonUtil = new CommonUtil();

$fb = new FormBean();
$dao = new SheetDAO();

$sell_site    = $fb->session("sell_site");
$member_seqno = $fb->session("org_member_seqno");

$fb = $fb->getForm();

$param = array();
$param["order_common_seqno"] = str_replace('|', ',', $fb["seq"]);

//$conn->debug = 1;
$rs = $dao->selectProductList($conn, $param);
echo productListPopup($rs, $fb["to"], $fb["selected"]);
$conn->Close();
exit;
?>
