<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderInfoDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$orderDAO = new OrderInfoDAO();

$param = array();
$param["order_seqno"] = $fb->form("order_seqno");
$result = $orderDAO->selectOrderMemo($conn, $param);

$memo = htmlspecialchars_decode($result->fields["memo"], ENT_QUOTES);

echo $memo;
?>
