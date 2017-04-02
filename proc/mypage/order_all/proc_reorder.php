<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/order_status.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderInfoDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$orderDAO = new OrderInfoDAO();
$conn->StartTrans();
$conn->debug = 1;
$check = 1;
/**
 * @brief 해당하는 주문공통 SELECT
 *        이벤트로 구매한 상품은 주문불가
 *
 */
$param = array();
$param["order_seqno"] = $fb->form("seqno");
$order_result = $orderDAO->selectOrderRow($conn, $param);
$title = "";
if ($order_result) {

    $title = $order_result->fields["title"];
    //이벤트 상품은 재주문 불가
    if ($order_result->fields["event_yn"] == "Y") {

        echo "2";
        exit;
    }

} else {

    $check = 0;
}

/*삭제 예정 -> 재주문시 주문대기로 간다면 필요없는 영역*/
/**
 * @brief 회원 일련번호로 회원등급 SELECT
 *        회원등급으로 구매할인율 SELECT
 *        구매할인율로 판매가격 계산
 */
$param = array();
$param["member_seqno"] = $order_result->fields["member_seqno"];
$member_result = $orderDAO->selectMemberInfo($conn, $param);

$param = array();
$param["grade"] = $member_result->fields["grade"];
$grade_result = $orderDAO->selectGradeRate($conn, $param);
$sale_rate = $grade_result->fields["sales_sale_rate"];

/**
 * @brief 결제 금액
 */
$pay_price = 
    (int)$order_result->fields["sell_price"] * (int)(100- $sale_rate) / 100;
$param["pay_price"] = $pay_price;

/**
 * @brief 선입금 금액
 */
$prepay_price = 
    (int)$member_result->fields["prepay_price"] - $pay_price;

/**
 * @brief 회원 선입금 처리
 *        예외회원이 아니고 선입금이 부족한경우 :
 *                  -> 주문부족금액과 선입금  UPDATE
 *        예외회원이거나 선입금이 부족하지 않은경우 :
 *                  -> 선입금만  UPDATE
 */
$param = array();
$param["member_seqno"] = $order_result->fields["member_seqno"];
$param["price"] = $prepay_price;
$param["type"] = "";
$state_dvs = "310";

if ($member_result->fields["member_typ"] != "예외회원") {

    //선입금액이 부족할때
    if ($prepay_price < 0) {

        $param["price"] = (int)$member_result->fields["order_lack_price"] + $pay_price - (int)$member_result->fields["prepay_price"];
        $param["type"] = "lack";
        $state_dvs = "210";

    }
}
/*삭제 예정 여기까지*/

/**
 * @brief 주문공통 재주문 INSERT
 */
$insert_param = array();
//$insert_param["pay_price"] = $pay_price; =>재주문 시 새주문 상태가 주문대기이면 필요 없음
//$insert_param["state"] = $state_dvs; =>재주문 시 새주문 상태가 주문대기이면 필요 없음
$insert_param["pay_price"] = 0;
$insert_param["state"] = OrderStatus::STATUS_PROC["주문"]["대기"];
$result = $orderDAO->insertReorder($conn, $order_result, $insert_param);
$order_seqno = $conn->insert_ID();
if (!$result) $check = 0;

/**
 * @brief 회원 선입금 UPDATE
 *        재주문 후 선입금 UPDATE
 *
 */
$result = $orderDAO->updateMemberPrepay($conn, $param);
if (!$result) $check = 0;

/**
 * @brief 재주문을 위한 SELECT
 */
//재주문을 위한 공통 파라미터
$param = array();
$param["order_seqno"] = $fb->form("seqno");
$param["reorder_seqno"] = $order_seqno;

//주문 파일 결과
$file_result = $orderDAO->selectOrderFileSet($conn, $param);
if (!$file_result) $check = 0;

//주문 상세 결과
$detail_result = $orderDAO->selectOrderDetailSet($conn, $param);
if (!$detail_result) $check = 0;

//주문 후공정 결과
$after_result = $orderDAO->selectOrderAfterSet($conn, $param);
if (!$after_result) $check = 0;

//주문 옵션 결과
$opt_result = $orderDAO->selectOrderOptSet($conn, $param);
if (!$opt_result) $check = 0;

/**
 * @brief 재주문을 위한 데이터 INSERT
 */
//주문번호 SELECT
$param = array();
$param["order_seqno"] = $order_seqno;
$result = $orderDAO->selectOrderNum($conn, $param);
if (!$result) $check = 0;

//재주문을 위한 공통 파라미터
$param = array();
$param["reorder_seqno"] = $order_seqno;
$param["order_num"] = $result->fields["order_num"];

//재주문 파일 INSERT
$result = $orderDAO->insertOrderFile($conn, $file_result, $param);
if (!$result) $check = 0;

//재주문 상세 INSERT
$result = $orderDAO->insertOrderDetail($conn, $detail_result, $param);
if (!$result) $check = 0;

//재주문 후공정 INSERT
$result = $orderDAO->insertOrderAfter($conn, $after_result, $param);
if (!$result) $check = 0;

//재주문 옵션 INSERT
$result = $orderDAO->insertOrderOpt($conn, $opt_result, $param);
if (!$result) $check = 0;

echo $check . "♪♭§" . $title;


$conn->CompleteTrans();
$conn->Close();
?>
