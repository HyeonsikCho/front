<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderInfoDAO.php");

if ($is_login === false) {
    echo "<script>alert('로그인이 필요합니다.'); return false;</script>";
    exit;
}

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$orderDAO = new OrderInfoDAO();
$conn->StartTrans();

$session = $fb->getSession();
$check = 1;

$state_arr = $session["state_arr"];

$order_seqno = $fb->form("seqno");

$param = array();
$param["order_seqno"] = $order_seqno;

// 주문 정보 select
$order_result = $orderDAO->selectOrderInfo($conn, $param);

// 쿠폰 내역 검색 -- 추가필요

if (!$order_result->EOF) {
    $fields = $order_result->fields;
    $order_common_seqno = $fields["order_common_seqno"];

    $order_state = $fields["order_state"];

    $pay_price = intval($fields["pay_price"]);
    $use_point = intval($fields["use_point_price"]);

    if ($order_state === $state_arr["입금대기"] ||
            $order_state === $state_arr["재주문"] ||
            $order_state === $state_arr["입금완료"] ||
            $order_state === $state_arr["시안요청"] ||
            $order_state === $state_arr["시안확인완료"] ||
            $order_state === $state_arr["접수보류"] ||
            $order_state === $state_arr["접수완료"] ||
            $order_state === $state_arr["조판누락"]){
        // 취소가능
        $param = array();
        $param["member_seqno"] = $fields["member_seqno"];

        //회원 선입금 금액, 포인트 금액 select
        $member_result = $orderDAO->selectMemberPrepay($conn, $param);

        $prepay = $member_result->fields["prepay_price"];
        $point  = $member_result->fields["own_point"];
        unset($member_result);

        $prepay = empty($prepay) ? 0 : intval($prepay);
        $point  = empty($point)  ? 0 : intval($point);

        $sum_prepay = $pay_price + $prepay;
        $sum_point  = $use_price + $point;

        $param["price"] = $sum_prepay;
        $param["point"] = $sum_point;
        $result = $orderDAO->updateMemberPrepay($conn, $param);

        if(!$result) {
            $check = 0;
            goto ERR;
        }

        // 회원_결제_내역 입력
        unset($param);
        $param["member_seqno"]    = $fields["member_seqno"];
        $param["order_num"]       = $fields["order_num"];
        $param["dvs"]             = "환불";
        $param["sell_price"]      = '0';
        $param["sale_price"]      = '0';
        $param["pay_price"]       = '0';
        $param["depo_price"]      = $pay_price;
        $param["depo_way"]        = '-';
        $param["exist_prepay"]    = $prepay;
        $param["prepay_bal"]      = $sum_prepay;
        $param["state"]           = '-';
        $param["deal_num"]        = '-';
        $param["order_cancel_yn"] = 'Y';
        $param["prepay_use_yn"]   = '-';
        $result = $orderDAO->insertMemberPayHistory($conn, $param);

        if(!$result) {
            $check = 0;
            goto ERR;
        }

        // 회원_포인트_내역 입력

        // 주문상세 상태값 변경
        unset($param);
        $param["table_name"]  = "order_detail";
        $param["order_seqno"] = $order_seqno;
        $param["order_state"] = $state_arr["주문취소"];
        $result = $orderDAO->updateOrderDetailState($conn, $param);

        if(!$result) {
            $check = 0;
            goto ERR;
        }

        // 주문상세건수파일 상태변경
        $detail_rs = $orderDAO->selectOrderDetailSeqno($conn, $param);
        while ($detail_rs && !$detail_rs->EOF) {
            $detail_seqno = $detail_rs->fields["detail_seqno"];
            $param["order_detail_seqno"] = $detail_seqno;

            $result = $orderDAO->updateOrderDetailCountFileState($conn, $param);

            if(!$result) {
                $check = 0;
                goto ERR;
            }

            $detail_rs->MoveNext();
        }
        if(!$result) {
            $check = 0;
            goto ERR;
        }

        // 주문상세책자 상태값 변경
        $param["table_name"]  = "order_detail_brochure";
        $result = $orderDAO->updateOrderDetailState($conn, $param);

        if(!$result) {
            $check = 0;
            goto ERR;
        }

    } else if ($order_state ===  $state_arr["주문취소"]) {
        // 취소됨
        echo 2;
        exit;

    } else {
        // 취소불가
        echo 3;
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
$param["order_seqno"] = $order_seqno;
$param["order_state"] = $state_arr["주문취소"];
$param["member_name"] = $result->fields["member_name"];

//주문취소 상태 변경 및 삭제자 이름 UPDATE
$result = $orderDAO->updateOrderState($conn, $param);
if (!$result) $check = 0;

ERR:
    echo $check;
    
    $conn->CompleteTrans();
    $conn->Close();
    exit;
?>
