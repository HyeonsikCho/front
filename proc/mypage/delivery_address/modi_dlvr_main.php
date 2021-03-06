<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/MemberDlvrDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();
$check = "기본배송지를 변경했습니다.";

$fb = new FormBean();
$dao = new MemberDlvrDAO();
$conn->StartTrans();

$param = array();
$param["member_dlvr_seqno"] = $fb->form("seq");
$param["member_seqno"] = $fb->session("org_member_seqno");
$result = $dao->updateBasicDlvr($conn, $param);

if (!$result)
    $check = "기본배송지 변경에 실패했습니다.";

echo $check;
$conn->CompleteTrans();
$conn->close();
?>
