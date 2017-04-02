<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/SheetDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/order_status.php");

$frontUtil = new FrontCommonUtil();
$err_line = 0;
$msg = "";

if ($is_login === false) {
    $err_line = __LINE__;
    $msg = "NO_LOGIN";
    goto ERR;
}

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new SheetDAO();

$session = $fb->getSession();
$fb = $fb->getForm();

$form_use_point = intval($fb["use_point"]);
$form_prepay_price = intval($fb["prepay_price"]);

$own_point = intval($session["own_point"]);
$prepay_price = intval($session["prepay_price"]);

// 가격 쪽 파라미터 확인
$seq_arr = $fb["seq"];

$param = array();
$param["member_seqno"] = $session["org_member_seqno"];
$param["order_state"]  = OrderStatus::STATUS_PROC["주문"]["대기"];
$param["order_common_seqno"] = $dao->arr2paramStr($conn, $seq_arr);
$order_rs = $dao->selectCartOrderList($conn, $param);

while ($order_rs && !$order_rs->EOF) {
    $fields = $order_rs->fields;

    $seqno = $fields["order_common_seqno"];
    $rs_basic_price      = intval($fields["basic_price"]);
    $rs_grade_sale_price = intval($fields["grade_sale_price"]);
    $rs_event_price      = intval($fields["event_price"]);
    $rs_add_after_price  = intval($fields["add_after_price"]);

    $rs_basic_price += $rs_add_after_price;

    $form_basic_price      = intval($fb["basic_price_" . $seqno]);
    $form_grade_sale_price = intval($fb["grade_sale_price_" . $seqno]);
    $form_event_price      = intval($fb["event_price_" . $seqno]);

    if ($rs_basic_price !== $form_basic_price ||
            $rs_grade_sale_price !== $form_grade_sale_price ||
            $rs_event_price !== $form_event_price) {
        $err_line = __LINE__;
        $msg = "ERR";
        goto ERR;
    }

    $order_rs->MoveNext();
}

// 현재 자신이 가진 포인트보다 값이 큰지 확인
if ($own_point < $form_use_point) {
    $err_line = __LINE__;
    $msg = "ERR";
    goto ERR;
}

// 현재 자신이 가진 선입금액보다 값이 큰지 확인
if ($prepay_price < $form_prepay_price) {
    $err_line = __LINE__;
    $msg = "ERR";
    goto ERR;
}

// 작업파일 업로드 개수 확인
unset($param);

$file_seqno_arr = $fb["work_file_seqno"];
$count_file_seqno_arr = count($file_seqno_arr);

$temp = array();
$k = 0;
for ($i = 0; $i < $count_file_seqno_arr; $i++) {
    $file_seqno = explode('|', $file_seqno_arr[$i]);
    $count_file_seqno = count($file_seqno);

    for ($j = 0; $j < $count_file_seqno; $j++) {
        $temp[$k++] = $file_seqno[$j];
    }
}

$param["member_seqno"] = $session["org_member_seqno"];

$count_seq_arr = count($seq_arr);
for ($i = 0; $i < $count_seq_arr; $i++) {
    $param["order_seqno"] = $seq_arr[$i];
    $param["file_seqno"]  = $temp[$i];

    $cnt = $dao->selectOrderFileCount($conn, $param);

    if ($cnt === '0') {
        $err_line = __LINE__;
        $msg = "NO_FILE";
        goto ERR;
    }
}

echo "PASS";
exit;

ERR:
    echo $err_line;
    echo $msg;
    exit;
?>
