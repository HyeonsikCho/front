<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/BusinessRegistrationDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();
$check = "삭제에 성공했습니다.";

$fb = new FormBean();
$dao = new BusinessRegistrationDAO();
$conn->StartTrans();

$param = array();
$param["admin_licenseeregi_seqno"] = $fb->form("seq");

$result = $dao->deleteRegistration($conn, $param);
if (!$result) 
    $check = "삭제에 실패했습니다.";

echo $check;
$conn->CompleteTrans();
$conn->close();
?>
