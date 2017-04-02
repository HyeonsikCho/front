<?
/*
 *
 * Copyright (c) 2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 2016/09/02 엄준현 수정(클레임 선택일 때 관리버튼 안나오도록 수정)
 * 2016/11/15 엄준현 수정(결제금액 부분 로직 수정)
 *============================================================================
 *
 */

include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderInfoDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$util = new FrontCommonUtil();
$dao = new OrderInfoDAO();
$session = $fb->getSession();
$order_common_seqno = $fb->form("order_common_seqno");

$param = array();
$param["order_common_seqno"] = $order_common_seqno;
$rs = $dao->selectOrderCommon($conn, $param);
$fields = $rs->fields;
unset($rs);

// 후공정 검색결과
$param["order_seqno"] = $order_common_seqno;
$aft_rs = $dao->selectOrderAfterSet($conn, $param);
// 옵션 검색결과
$opt_rs = $dao->selectOrderOptSet($conn, $param);
// 파일 검색결과
$file_rs = $dao->selectOrderFileSet($conn, $param);

$member_dvs = $fb->session("member_dvs");

$colspan = "9";
if ($member_dvs == "기업") {
    $colspan = "10";
}

if (empty($fb->form("colspan")) === false) {
    $colspan = $fb->form("colspan");
}

$param = array();
$param["conn"]               = $conn;
$param["dao"]                = $dao;
$param["colspan"]            = $colspan;
$param["file_path"]          = $fields["file_path"];
$param["save_file_name"]     = $fields["save_file_name"];
$param["title"]              = $fields["title"];
$param["amt"]                = doubleval($fields["amt"]);
$param["count"]              = $fields["count"];
$param["amt_unit_dvs"]       = $fields["amt_unit_dvs"];

$param["sell_price"]       = $fields["sell_price"];
$param["pay_price"]        = $fields["pay_price"];
$param["grade_sale_price"] = $fields["grade_sale_price"];
$param["event_price"]      = $fields["event_price"];
$param["use_point_price"]  = $fields["use_point_price"];
$param["add_after_price"]  = $fields["add_after_price"];
$param["add_opt_price"]    = $fields["add_opt_price"];
$param["dlvr_price"]       = $fields["dlvr_price"];

$param["expec_weight"]       = $fields["expec_weight"];
$param["order_detail"]       = $fields["order_detail"];
$param["dlvr_way"]           = $fields["dlvr_way"];
$param["zipcode"]            = $fields["zipcode"];
$param["addr"]               = $fields["addr"];
$param["addr_detail"]        = $fields["addr_detail"];
$param["order_common_seqno"] = $order_common_seqno;
$param["order_state"]        = $fields["order_state"];
$param["order_state_arr"]    = $session["state_arr"];
$param["btn_flag"]           = true;

// 클레임 선택일 때 관리버튼 출력 안되도록 하는 부분
$refer = $_SERVER["HTTP_REFERER"];
$refer = explode('/', $refer);
$refer = $refer[count($refer) - 1];

if ($refer === "claim_select.html" ||
        $refer === "cart.html" ||
        $refer === "sheet.html") {
    $param["btn_flag"] = false;
}

echo makeOrderdetail($param, $util, $opt_rs, $aft_rs, $file_rs);
$conn->close();
?>
