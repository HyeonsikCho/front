<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/member/MemberJoinDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new MemberJoinDAO();

$param = array();
$param["member_id"] = $fb->form("member_id");
$param["sell_site"] = $fb->session("sell_site");

$rs = $dao->selectIdOverCheck($conn, $param);

$cnt = $rs->fields["cnt"];

if ($cnt == 0) {
    echo "true";
} else {
    echo "false";
}
$conn->Close();
?>
