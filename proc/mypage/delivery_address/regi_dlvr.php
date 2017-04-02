<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/MemberDlvrDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();
$check = "등록에 성공했습니다.";

$fb = new FormBean();
$dao = new MemberDlvrDAO();
$conn->StartTrans();

$param = array();
$param["dlvr_name"] = $fb->form("dlvr_name");
$param["recei"] = $fb->form("recei");
$param["tel_num"] = $fb->form("tel_num");
$param["cell_num"] = $fb->form("cell_num");
$param["zipcode"] = $fb->form("zipcode");
$param["addr"] = $fb->form("addr");
$param["addr_detail"] = $fb->form("addr_detail");
$param["member_seqno"] = $fb->session("org_member_seqno");

$result = $dao->insertDlvr($conn, $param);

if (!$result) 
    $check = "등록에 실패했습니다.";

echo $check;
$conn->CompleteTrans();
$conn->close();
?>
