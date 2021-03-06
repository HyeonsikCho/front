<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/BusinessRegistrationDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();
$check = "등록에 성공했습니다.";

$fb = new FormBean();
$dao = new BusinessRegistrationDAO();
$conn->StartTrans();

$param = array();
$param["crn"] = $fb->form("crn");
$param["corp_name"] = $fb->form("corp_name");
$param["repre_name"] = $fb->form("repre_name");
$param["bc"] = $fb->form("bc");
$param["tob"] = $fb->form("tob");
$param["tel_num"] = $fb->form("tel_num");
$param["zipcode"] = $fb->form("zipcode");
$param["addr"] = $fb->form("addr");
$param["addr_detail"] = $fb->form("addr_detail");
$param["mng_name"] = $fb->form("mng_name");
$param["posi"] = $fb->form("posi");
$param["mail"] = $fb->form("mail");
$param["member_seqno"] = $fb->session("org_member_seqno");

$result = $dao->insertRegistration($conn, $param);

if (!$result) 
    $check = "등록에 실패했습니다.";

echo $check;
$conn->CompleteTrans();
$conn->close();
?>
