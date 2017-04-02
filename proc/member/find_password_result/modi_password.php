<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/member/MemberFindPwDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/PasswordEncrypt.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new MemberFindPwDAO();

$check = 1;
$conn->StartTrans();

$member_seqno = $fb->form("member_seqno");
$passwd = $fb->form("passwd");

$passwd = password_hash($passwd, PASSWORD_DEFAULT);

$param = array();
$param["member_seqno"] = $member_seqno;
$param["passwd"] = $passwd;

$rs = $dao->updateMemberPw($conn, $param);

if (!$rs) {
    $check = 0;
}

$conn->CompleteTrans();
$conn->Close();
echo $check;
?>
