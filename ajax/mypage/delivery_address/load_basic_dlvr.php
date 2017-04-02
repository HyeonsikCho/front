<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/MemberDlvrDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();
$fb = new FormBean();
$dao = new MemberDlvrDAO();

$param = array();
$param["seqno"] = $fb->session("org_member_seqno");
$rs = $dao->selectBasicDlvr($conn, $param);

$rs_dlvr = $dao->selectMemberDlvrDvs($conn, $param);
echo $rs->fields["addr"]."♪".$rs->fields["addr_detail"] . "♪" . $rs_dlvr->fields["dlvr_dvs"]  . " " . 
$rs_dlvr->fields["dlvr_code"];
$conn->close();
?>

