<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderInfoDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$orderDAO = new OrderInfoDAO();
$conn->StartTrans();
$check = 1;

$param = array();
$param["order_seqno"] = $fb->form("order_seqno");
$param["memo"] = $fb->form("memo");

$result = $orderDAO->updateOrderMemo($conn, $param);
if(!$result) $check = 0;

echo $check;
$conn->CompleteTrans();
$conn->close();
?>
