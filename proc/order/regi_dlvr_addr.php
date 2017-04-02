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

$dlvr_name   = $fb["dlvr_name"];
$recei       = $fb["recei"];
$tel_num     = $fb["tel_num"];
$cell_num    = $fb["cell_num"];
$zipcode     = $fb["zipcode"];
$addr        = $fb["addr"];
$addr_detail = $fb["addr_detail"];

$param = array();
$param["member_seqno"] = $session["org_member_seqno"];
$param["dlvr_name"]    = $dlvr_name;
$param["recei"]        = $recei;
$param["tel_num"]      = $tel_num;
$param["cell_num"]     = $cell_num;
$param["zipcode"]      = $zipcode;
$param["addr"]         = $addr;
$param["addr_detail"]  = $addr_detail;
$param["basic_yn"]     = 'N';
// dlvr_dvs, doro_yn 확인 필요

$insert_ret = $dao->insertMemberDlvr($conn, $param);

if ($insert_ret === false) {
    echo 'F';
    exit;
}

echo 'T';

exit;
?>
