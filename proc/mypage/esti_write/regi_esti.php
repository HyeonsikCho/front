<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/EstiInfoDAO.php"); 

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();
$conn->StartTrans();

$fb = new FormBean();
$dao = new EstiInfoDAO();
$check = "견적을 등록하였습니다.";

//견적 등록
$param = array();
$param["title"] = $fb->form("title");
$param["inq_cont"] = $fb->form("inq_cont");
$param["member_seqno"] = $fb->session("org_member_seqno");
$param["group_seqno"] = $fb->session("member_seqno");
$param["state"] = "견적대기";

$insID = $dao->insertEsti($conn, $param);

//User 견적 파일 설정
$seqno = $conn->Insert_ID("esti");
$param_file = array();
$param_file["esti_seqno"] = $seqno;
$param_file["esti_file_seqno"] = $fb->form("esti_file_seqno");
$modiFile = $dao->updateEstiFile($conn, $param_file);

if (!$insID) {
    if (!$modiFile) {
        $check = "견적등록을 실패하였습니다.";
        $conn->CompleteTrans();
        $conn->Close();
        echo $check;
        exit;
    }
}

$conn->CompleteTrans();
$conn->Close();
echo $check;
?>

