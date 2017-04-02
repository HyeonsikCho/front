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

$session = $fb->getSession();
$check = 1;

$param = array();
$param["order_seqno"] = $fb->form("seqno");

//주문 정보 select
$order_result = $orderDAO->selectOrderInfo($conn, $param);

if ($order_result) {

    if ($order_result->fields["order_state"] > 300) {

        $param = array();
        $param["member_seqno"] = $order_result->fields["member_seqno"];
        //회원 선입금 금액 select
        $member_result = $orderDAO->selectMemberPrepay($conn, $param);

        if ($member_result) {

            //회원 선입금 금액
            $prepay = $member_result->fields["prepay_price"];

            if ($prepay == "" || $prepay == NULL) {

                $prepay = "0";

            }

        } else {

            $check = 0;

        }

        $param["price"] = (int)$order_result->fields["pay_price"] 
                                + (int)$prepay;
        $result = $orderDAO->updateMemberPrepay($conn, $param);
        if(!$result) $check = 0;

    } else if ($order_result->fields["order_state"] == 120) {

        $check = 2;
        exit;

    }

} else {

    $check = 0;

}

$param = array();
$param["member_seqno"] = $session["org_member_seqno"];

//주문 테이블의 삭제자 이름을 위해 이름 SELECT
$result = $orderDAO->selectMemberName($conn, $param);
if (!$result) $check = 0;

$param = array();
$param["member_seqno"] = $fb->form("seqno");
$param["member_name"] = $result->fields["member_name"];

//주문취소 상태 변경 및 삭제자 이름 UPDATE
$result = $orderDAO->updateOrderState($conn, $param);
if (!$result) $check = 0;

echo $check;

$conn->CompleteTrans();
$conn->close();
?>
