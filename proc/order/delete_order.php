<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/Template.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/CartDAO.php");

$frontUtil = new FrontCommonUtil();

if ($is_login === false) {
    $frontUtil->errorGoBack("로그인 상태가 아닙니다.");
    exit;
}

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new CartDAO();

$member_seqno = $fb->session("org_member_seqno");

$fb = $fb->getForm();

$seq_arr = $fb["seq"];
$count_seq_arr = count($seq_arr);

$param = array();
$param["member_seqno"] = $member_seqno;

//$conn->debug = 1;
for ($i = 0; $i < $count_seq_arr; $i++) {
    $order_common_seqno = $seq_arr[$i];

    $param["order_common_seqno"] = $order_common_seqno;
    $info_rs = $dao->selectOrderInfo($conn, $param);

    $conn->StartTrans();

    $param["table"] = "order_opt_history";
    $dao->deleteOrderData($conn, $param);

    $param["table"] = "order_dlvr";
    $dao->deleteOrderData($conn, $param);

    while ($info_rs && !$info_rs->EOF) {
        $fields = $info_rs->fields;

        $s_detail_seqno   = $fields["s_detail_seqno"];
        $s_detail_dvs_num = $fields["s_detail_dvs_num"];
        $b_detail_dvs_num = $fields["b_detail_dvs_num"];

        $order_detail_seqno_arr = array();
        $order_detail_dvs_num_arr = array();

        if (empty($s_detail_seqno) === false) {
            $order_detail_seqno_arr[] = $s_detail_seqno;
        }

        if (empty($s_detail_dvs_num) === false) {
            $order_detail_dvs_num_arr[] = $s_detail_dvs_num;
        }

        if (empty($b_detail_dvs_num) === false) {
            $order_detail_dvs_num_arr[] = $b_detail_dvs_num;
        }

        if (count($order_detail_seqno_arr) > 0) {
            $param["order_detail_seqno"] = $order_detail_seqno_arr;
            $dao->deleteOrderDetailCountFile($conn, $param);
        }

        if (count($order_detail_dvs_num_arr) > 0) {
            $param["order_detail_dvs_num"] = $order_detail_dvs_num_arr;
            $dao->deleteOrderAfterHistory($conn, $param);
        }
        
        $info_rs->MoveNext();
    }

    $param["table"] = "order_detail";
    $dao->deleteOrderData($conn, $param);

    $param["table"] = "order_detail_brochure";
    $dao->deleteOrderData($conn, $param);

    $param["table"] = "order_file";
    $dao->deleteOrderData($conn, $param);

    if ($conn->HasFailedTrans() === true) {
        $conn->FailTrans();
        $conn->RollbackTrans();
        goto ERR;
    }

    $conn->CompleteTrans();

    $delete_ret = $dao->deleteOrderCommon($conn, $param);

    if ($delete_ret === false) {
        goto ERR;
    }
}

echo 'T';
$conn->Close();
exit;

ERR:
    echo 'F';
    $conn->Close();
    exit;
?>
