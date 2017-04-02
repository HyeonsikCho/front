<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/MemberDlvrDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();
$check = "삭제하였습니다.";

$fb = new FormBean();
$dao = new MemberDlvrDAO();
$conn->StartTrans();

$arr = array();
$arr = explode("&", $fb->form("seq"));
$member_dlvr_seqno = "";

for($i=0; $i<count($arr); $i++) {
    
    $seq = array();
    $seq = explode("=", $arr[$i]);

    $member_dlvr_seqno .= ",".$seq[1];
}

$param = array();
$param["member_dlvr_seqno"] = substr($member_dlvr_seqno, 1);
$result = $dao->deleteDlvr($conn, $param);

if (!$result) {
    $check = "삭제에 실패했습니다.";
    break;
}

echo $check;
$conn->CompleteTrans();
$conn->close();
?>
