<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/eventmall/EventmallDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new EventmallDAO();

$fb = new FormBean();
$sortcode = $fb->form("sortcode");
$rs = $dao->selectPopularList($conn, $sortcode);

$html = makePopularListHTML($conn, $rs);
$conn->Close();

echo $html;
exit;
?>
