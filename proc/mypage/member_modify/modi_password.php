<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/MemberInfoDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new MemberInfoDAO();

$check = 1;

$member_seqno = $fb->form("member_seqno");
$exi_passwd = $fb->form("exi_passwd");
$new_passwd = $fb->form("new_passwd");

$param = array();
$param["member_seqno"] = $member_seqno;

$rs = $dao->selectMemberPw($conn, $param);

$pw = $rs->fields["passwd"];

if (password_verify($exi_passwd, $pw) === false) {
    echo "2";
    exit;
}

$new_passwd = password_hash($new_passwd, PASSWORD_DEFAULT);

$conn->StartTrans();

$param = array();
$param["member_seqno"] = $member_seqno;
$param["passwd"] = $new_passwd;

$rs = $dao->updateMemberPw($conn, $param);

if (!$rs) {
    $check = 0;
}

$conn->CompleteTrans();
$conn->Close();
echo $check;
?>
