<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/MemberInfoDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new MemberInfoDAO();

$check = 1;

$conn->StartTrans();

$param = array();
$param["group_id"] = $fb->session("org_member_seqno");
$param["group_name"] = $fb->session("member_name");
$param["office_nick"] = $fb->session("member_name") . "(" . $fb->form("member_name") . ")";
$param["member_name"] = $fb->form("member_name");
$param["member_id"] = $fb->form("member_id");
$param["member_dvs"] = "기업개인";
$param["posi"] = $fb->form("posi");
$param["passwd"] = password_hash($fb->form("passwd"), PASSWORD_DEFAULT);
$param["tel_num"] = $fb->form("tel_num");
$param["cell_num"] = $fb->form("cell_num");
$param["mail"] = $fb->form("mail");

$rs = $dao->insertCoOrderMng($conn, $param);

if (!$rs) {
    $check = 0;
}

$conn->CompleteTrans();
$conn->Close();
echo $check;
?>
