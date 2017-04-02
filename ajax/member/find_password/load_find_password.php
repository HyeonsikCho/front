<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/member/MemberFindPwDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new MemberFindPwDAO();

$search_cnd = $fb->form("search_cnd");

$arr = array();
$arr["cell_num"] = $fb->form("cell_num");
$arr["mail"] = $fb->form("mail");

$param = array();
$param["member_name"] = $fb->form("member_name");
$param["member_id"] = $fb->form("member_id");
$param["search_cnd"] = $search_cnd;
$param["search_txt"] = $arr[$search_cnd];

$rs = $dao->selectFindPw($conn, $param);

echo $rs->fields["member_seqno"];
$conn->Close();
?>
