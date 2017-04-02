<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/MemberInfoDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new MemberInfoDAO();
$check = 1;

$conn->StartTrans();

//회원 탈퇴신청
$param = array();
$param["member_seqno"] = $fb->form("seqno");

$rs = $dao->updateMemberWithdraw($conn, $param);

if (!$rs) {
    $check = 0;
}

//회원탈퇴 입력
$param = array();
$param["table"] = "member_withdraw";
$param["col"]["withdraw_code"] = $fb->form("withdraw_code");
$param["col"]["withdraw_dvs"] = "본인탈퇴";
$param["col"]["reason"] = $fb->form("reason");
$param["col"]["withdraw_date"] = date("Y-m-d H:i:s");
$param["col"]["member_seqno"] = $fb->form("seqno");

$rs = $dao->insertData($conn, $param);

if (!$rs) {
    $check = 0;
}

//회원 쿠폰 삭제
$param = array();
$param["table"] = "cp_issue";
$param["prk"] = "member_seqno";
$param["prkVal"] = $fb->form("seqno");

$rs = $dao->deleteData($conn, $param);

if (!$rs) {
    $check = 0;
}

//회원 포인트 내역 삭제
$param = array();
$param["table"] = "member_point_history";
$param["prk"] = "member_seqno";
$param["prkVal"] = $fb->form("seqno");

$rs = $dao->deleteData($conn, $param);

if (!$rs) {
    $check = 0;
}

//회원 포인트 요청 삭제
$param = array();
$param["table"] = "member_point_req";
$param["prk"] = "member_seqno";
$param["prkVal"] = $fb->form("seqno");

$rs = $dao->deleteData($conn, $param);

if (!$rs) {
    $check = 0;
}

echo $check;
$conn->CompleteTrans();
$conn->close();
?>
