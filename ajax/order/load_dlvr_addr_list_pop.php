<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/SheetDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/doc/order/SheetPopup.php");

$frontUtil = new FrontCommonUtil();

if ($is_login === false) {
    $frontUtil->errorGoBack("로그인 상태가 아닙니다.");
    exit;
}

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new SheetDAO();

$session = $fb->getSession();
$fb = $fb->getForm();

$param = array();
$param["member_seqno"] = $session["org_member_seqno"];
//$param["basic_yn"] = 'N';

$rs = $dao->selectMemberDlvr($conn, $param);

echo addressListPopup($rs);

exit;
?>
