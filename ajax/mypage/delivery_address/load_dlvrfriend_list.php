<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/common_info.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/MemberDlvrDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new MemberDlvrDAO();

$param = array();
$param["search_txt"] = $fb->form("search_txt");
$rs = $dao->selectDlvrMainList($conn, $param);

echo makeDlvrMainList($rs);
$conn->Close();
?>
