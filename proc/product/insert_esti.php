<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductEstiDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/CartDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/prdt_default_info.php");

if ($is_login === false) {
    echo "로그인이 필요합니다.";
    exit;
}

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$util = new FrontCommonUtil();
$dao = new ProductEstiDAO();
$cartDAO = new CartDAO();
$fb = new FormBean();

$session = $fb->getSession();
$fb = $fb->getForm();

/*
echo "<pre>";
$conn->debug = 1;
*/

// insert 실패시 에러메세지
$err_msg = '';

//@ 제목, 빈값이면 뒤로가기
$title = $fb["title"];
if (empty($title)) {
    $err_msg = "인쇄물제목을  입력해주세요.";
    exit;
}

$state_arr = $session["state_arr"];

$common_param = array();
$common_param["title"]           = $title;
$common_param["order_state"]     = $state_arr["접수보류"];
$common_param["mono_yn"]         = 'Y';
$common_param["claim_yn"]        = 'N';
$common_param["del_yn"]          = 'N';
$common_param["receipt_dvs"]     = "Manual";
$common_param["member_seqno"]    = $session["org_member_seqno"];
$common_param["cpn_admin_seqno"] = $session["sell_site"];
$common_param["group_seqno"]     = "null";
if ($session["member_dvs"] === "기업" ||
        $session["member_dvs"] === "기업개인") {
    $common_param["group_seqno"] = $session["member_seqno"];
}
$common_param["opt_use_yn"] = 'Y';
if (empty($fb["opt_add"])) {
    $common_param["opt_use_yn"] = 'N';
}
// 더미값
$common_param["sell_price"]       = null;
$common_param["grade_sale_price"] = null;
$common_param["add_opt_price"]    = null;
$common_param["add_after_price"]  = null;
$common_param["event_price"]      = null;
$common_param["expec_weight"]     = null;

// 제품 공통구분값, 책자형 제품인지 구분
$common_prdt_dvs = $fb["common_prdt_dvs"];
// 책자형인지 판단
$is_booklet = false;
if (empty($common_prdt_dvs) === false) {
    $prefix = $common_prdt_dvs . '_';
    $common_cate_sortcode = $fb[$prefix . "cate_sortcode"];
    $common_amt           = $fb[$prefix . "amt"];

    $is_booklet = true;

    $cate_info = $dao->selectCateInfo($conn, $common_cate_sortcode);
    $flattyp_yn = $cate_info["flattyp_yn"];
    unset($cate_info);

    $common_param["cate_sortcode"] = $common_cate_sortcode;
    $common_param["order_detail"]  = $fb[$prefix . "order_detail"];
    $common_param["flattyp_yn"]    = $flattyp_yn;

    $common_param["amt"]           = $common_amt;
    $common_param["amt_unit_dvs"]  = $fb[$prefix . "amt_unit"];
    $common_param["count"]         = '1';

    $common_param["page_cnt"]      = intval($fb[$prefix . "sheet_count"]);

    // 주문_번호
    $order_num = $util->makeOrderNum($conn,
                                     $cartDAO,
                                     $common_cate_sortcode);
    $common_param["order_num"] = $order_num;
}

$dvs_arr = explode('|', $fb["prdt_dvs"]);
$dvs_arr_count = count($dvs_arr);

$detail_param_arr = null;
$param = array();
for ($i = 0; $i < $dvs_arr_count; $i++) {
    $dvs = $dvs_arr[$i];
    $prefix = $dvs . '_';
    $typ = $fb[$prefix . "typ"];

    $cate_sortcode = $fb[$prefix . "cate_sortcode"];

    //@ 카테고리 분류코드 없는경우 비정상 접근
    if (empty($cate_sortcode) === true) {
        $location .= "/main/main.html";
        $err_line = __LINE__;
        goto SUCCESS;
    }

    $cate_info = $dao->selectCateInfo($conn, $cate_sortcode);
    $flattyp_yn = $cate_info["flattyp_yn"];

    unset($param);
    // 수량_단위_구분
    $amt_unit_dvs = $fb[$prefix . "amt_unit"];

    //@ 수량_단위_구분 없는경우 비정상 접근
    if (empty($amt_unit_dvs) === true) {
        $location .= "/main/main.html";
        $err_line = __LINE__;
        goto SUCCESS;
    }
    // 수량
    $amt = $fb[$prefix . "amt"];
    if (empty($amt)) {
        $amt = $common_amt;
    }
    // 건수
    $count = 1;
    if (empty($fb[$prefix . "count"]) === false) {
        $count = intval($fb[$prefix . "count"]);
    }

    $param["cate_sortcode"] = $cate_sortcode;
    $param["tmpt_dvs"]      = $tmpt_dvs;
    $param["util"]          = $util;

    $detail_param_arr[$i]["paper_info"]            =
        $fb[$prefix . "paper_info"];
    $detail_param_arr[$i]["beforeside_print_info"] =
        $fb[$prefix . "bef_print_info"];
    $detail_param_arr[$i]["aftside_print_info"]    =
        $fb[$prefix . "aft_print_info"];
    $detail_param_arr[$i]["print_purp_info"]       =
        $fb[$prefix . "print_purp_info"];
    $detail_param_arr[$i]["stan_info"]             =
        $fb[$prefix . "size_name"];
    $detail_param_arr[$i]["amt_info"]              = $fb[$prefix . "amt_info"];
    $detail_param_arr[$i]["count"]                 = $fb[$prefix . "count"];
    $detail_param_arr[$i]["page_info"]             = $fb[$prefix . "page_info"];

    $detail_param_arr[$i]["work_size_wid"]  = $fb[$prefix . "work_wid_size"];
    $detail_param_arr[$i]["work_size_vert"] = $fb[$prefix . "work_vert_size"];
    $detail_param_arr[$i]["cut_size_wid"]   = $fb[$prefix . "cut_wid_size"];
    $detail_param_arr[$i]["cut_size_vert"]  = $fb[$prefix . "cut_vert_size"];

    $detail_param_arr[$i]["flattyp_yn"]  = $flattyp_yn;
    $detail_param_arr[$i]["dvs"]         = $dvs;

    if ($is_booklet === false) {
        $common_cate_sortcode = $cate_sortcode;
        $temp = $detail_param_arr[$i];

        $common_param["cate_sortcode"] = $cate_sortcode;
        $common_param["order_detail"]  = $fb[$prefix . "order_detail"];

        $common_param["flattyp_yn"]    = $flattyp_yn;
        $common_param["amt"]           = $amt;
        $common_param["amt_unit_dvs"]  = $amt_unit_dvs;
        $common_param["count"]         = $count;

        if (empty($fb[$prefix . "sheet_count"]) === true) {
            $amt = doubleval($fb[$prefix . "amt"]);
        } else {
            $amt = doubleval($fb[$prefix . "sheet_count"]);
        }

        $common_param["page_cnt"] = $amt;

        // 주문번호
        $order_num = $util->makeOrderNum($conn,
                                         $cartDAO,
                                         $cate_sortcode);
        $common_param["order_num"] = $order_num;
        unset($temp);
    }
}

$insert_ret = $cartDAO->insertOrderCommon($conn, $common_param);

if ($insert_ret === false) {
    $err_line = __LINE__;
    $err_msg = "공통 데이터 입력에 실패했습니다.";
    $conn->FailTrans();
    $conn->RollbackTrans();
    goto ERR;
}

$order_common_seqno = $conn->Insert_ID("order_common");

// 에러났을 경우 데이터 삭제할 때 사용
$detail_dvs_num_arr = array();
$detail_seqno_arr = array();

$after_basic_param_arr = array();
$after_add_param_arr = array();

$conn->StartTrans();
$detail_param_arr_count = count($detail_param_arr);
for ($i = 0; $i < $detail_param_arr_count; $i++) {
    $detail_param = $detail_param_arr[$i];

    $dvs        = $detail_param["dvs"];
    $prefix     = $dvs . '_';

    $flattyp_yn = $detail_param["flattyp_yn"];

    $detail_param["order_common_seqno"] = $order_common_seqno;

    $detail_num = str_pad(strval($i + 1), 2, '0', STR_PAD_LEFT);
    // 주문_상세_번호
    $detail_num = $order_num . $detail_num;

    //! 낱장여부에 따라 주문_상세 or 주문_상세_책장 입력
    $detail_dvs_num = null;
    if ($flattyp_yn === 'Y') {
        $detail_dvs_num = 'S' . $detail_num;
        $detail_param["esti_detail_dvs_num"] = $detail_dvs_num;
        $dao->insertEstiDetail($conn, $detail_param);

        $detail_seqno = $conn->Insert_ID("esti_detail");

        $detail_seqno_arr[] = $detail_seqno;

        //! 주문_상세_건수_파일
        unset($param);
        $param["count"] = intval($count);
        $param["esti_detail_seqno"]  = $detail_seqno;
        $param["order_detail_num"]   = $detail_dvs_num;
        $param["state"]              = $state_arr["접수보류"];
        $dao->insertOrderDetailCountFile($conn, $param);

        if ($conn->HasFailedTrans() === true) {
            $err_line = __LINE__;
            $err_msg = "주문 상세 데이터 입력에 실패했습니다.";
            $conn->FailTrans();
            $conn->RollbackTrans();
            goto ORDER_DETAIL_FILE_ERR;
        }
    } else {
        $detail_dvs_num = 'B' . $detail_num;
        $detail_param["esti_detail_dvs_num"] = $detail_dvs_num;
        $dao->insertEstiDetailBrochure($conn, $detail_param);

        $detail_seqno = $conn->Insert_ID();

        $detail_seqno_arr[] = $detail_seqno;
    }

    $detail_dvs_num_arr[$i] = $detail_dvs_num;

    if ($conn->HasFailedTrans() === true) {
        $err_line = __LINE__;
        $err_msg = "견적 상세 데이터 입력에 실패했습니다.";
        $conn->FailTrans();
        $conn->RollbackTrans();
        goto ESTI_DETAIL_ERR;
    }

    //! 주문_후공정_내역
    $after_en_arr = ProductInfoClass::AFTER_ARR;

    // 기본 후공정 검색
    unset($param);
    $param["cate_sortcode"] = $cate_sortcode;
    $param["basic_yn"]      = 'Y';

    $default_after_rs = $cartDAO->selectCateAfterInfo($conn, $param);

    while ($default_after_rs && !$default_after_rs->EOF) {
        $name = $default_after_rs->fields["after_name"];
        $depth1 = $default_after_rs->fields["depth1"];
        $depth2 = $default_after_rs->fields["depth2"];
        $depth3 = $default_after_rs->fields["depth3"];

        $after_en = $after_en_arr[$name];

        $info   = $fb[$prefix . $after_en . "_info"];

        $after_basic_param_arr[$i][$name]["order_detail_dvs_num"] = $detail_dvs_num;
        $after_basic_param_arr[$i][$name]["after_name"] = $name;
        $after_basic_param_arr[$i][$name]["depth1"]   = $depth1;
        $after_basic_param_arr[$i][$name]["depth2"]   = $depth2;
        $after_basic_param_arr[$i][$name]["depth3"]   = $depth3;
        $after_basic_param_arr[$i][$name]["price"]    = 0;
        $after_basic_param_arr[$i][$name]["basic_yn"] = 'Y';
        $after_basic_param_arr[$i][$name]["seq"]      = null;
        $after_basic_param_arr[$i][$name]["detail"]   = $info;

        $default_after_rs->MoveNext();
    }

    unset($default_after_rs);

    // 추가 후공정 검색
    $after_ko_arr = $fb[$prefix . "chk_after"];
    $count_after_ko_arr = count($after_ko_arr);

    $after_add_mpcode = "";
    $after_add_info_arr = array();

    for ($j = 0; $j < $count_after_ko_arr; $j++) {
        $after_ko = $after_ko_arr[$j];
        $after_en = $after_en_arr[$after_ko];

        $mpcode = $fb[$prefix . $after_en . "_val"];
        $info   = $fb[$prefix . $after_en . "_info"];

        if (empty($mpcode) === true /*|| empty($price) === true*/) {
            $err_line = __LINE__;
            $err_msg = "주문 후공정 내역 데이터 입력에 실패했습니다.";
            $conn->FailTrans();
            $conn->RollbackTrans();
            goto AFT_ERR;
        }

        $after_add_info_arr[$mpcode]["info"]  = $info;
        $after_add_info_arr[$mpcode]["price"] = null;

        $after_add_mpcode .= $dao->parameterEscape($conn, $mpcode) . ',';
    }

    if (empty($after_add_mpcode) === false) {
        $param["mpcode"]   = substr($after_add_mpcode, 0, -1);
        $param["basic_yn"] = 'N';

        $add_after_rs = $cartDAO->selectCateAfterInfo($conn, $param);

        $after_add_param_arr[] = makeAfterAddParam($add_after_rs,
                                                   $after_add_info_arr,
                                                   $detail_dvs_num);
    }
}
$conn->CompleteTrans();

$conn->StartTrans();
$after_basic_param_arr_count = count($after_basic_param_arr);
for ($i = 0; $i < $after_basic_param_arr_count; $i++) {
    $after_basic_param = $after_basic_param_arr[$i];

    foreach ($after_basic_param as $param) {
        $param["order_common_seqno"] = $order_common_seqno;
        $after_name = $param["after_name"];

        $cartDAO->insertOrderAfterHistory($conn, $param);

        if ($conn->HasFailedTrans() === true) {
            $err_line = __LINE__;
            $err_msg = "주문 후공정 내역 데이터 입력에 실패했습니다.";
            $conn->FailTrans();
            $conn->RollbackTrans();
            goto AFT_ERR;
        }
    }
}
$conn->CompleteTrans();

$conn->StartTrans();
$after_add_param_arr_count = count($after_add_param_arr);
for ($i = 0; $i < $after_add_param_arr_count; $i++) {
    $after_add_param = $after_add_param_arr[$i];
    $after_add_param_count = count($after_add_param);

    for ($j = 0; $j < $after_add_param_count; $j++) {
        $param = $after_add_param[$j];
        $param["order_common_seqno"] = $order_common_seqno;

        $cartDAO->insertOrderAfterHistory($conn, $param);

        if ($conn->HasFailedTrans() === true) {
            $err_line = __LINE__;
            $err_msg = "주문 후공정 내역 데이터 입력에 실패했습니다.";
            $conn->FailTrans();
            $conn->RollbackTrans();
            goto AFT_ERR;
        }
    }
}
$conn->CompleteTrans();

/*
echo "</pre>";
goto OPT_ERR;
exit;
*/

goto SUCCESS;

exit;

OPT_ERR:
    $param["table"] = "order_opt_history";
    $param["order_common_seqno"] = $order_common_seqno;
    $ret = $cartDAO->deleteOrderData($conn, $param);
AFT_ERR:
    unset($param);
    $param["order_detail_dvs_num"] = $detail_dvs_num_arr;
    $ret = $cartDAO->deleteOrderAfterHistory($conn, $param);
ORDER_DETAIL_FILE_ERR:
    $param["order_detail_seqno"] = $detail_seqno_arr;
    $ret = $cartDAO->deleteOrderDetailCountFile($conn, $param);
ESTI_DETAIL_ERR:
    $param["table"] = "esti_detail";
    $param["order_common_seqno"] = $order_common_seqno;
    $ret = $cartDAO->deleteOrderData($conn, $param);
    $param["table"] = "esti_detail_brochure";
    $param["order_common_seqno"] = $order_common_seqno;
    $ret = $cartDAO->deleteOrderData($conn, $param);
ERR:
    $param["table"] = "order_common";
    $param["order_common_seqno"] = $order_common_seqno;
    $ret = $cartDAO->deleteOrderData($conn, $param);
    $conn->CompleteTrans();
    $conn->Close();
    
    $err_msg = $err_line . ':' . $err_msg;
    echo "{\"success\" : false, \"err_msg\" : \"$err_msg\"}";
    exit;
SUCCESS:
    echo "{\"success\" : true, \"order_num\" : \"$order_num\", \"seqno\" : \"$order_common_seqno\"}";
    $conn->CompleteTrans();
    $conn->Close();
    exit;

/******************************************************************************
 * 함수 영역
 *****************************************************************************/

/**
 * @brief 추가 후공정 검색결과 테이블 입력 정보배열 생성
 *
 * @param $rs = 검색결과
 * @param $info_arr = 후공정 설명정보 배열
 * @param $order_common_seqno = 주문공통_일련번호
 *
 * @return 생성된 배열
 */
function makeAfterAddParam($rs, $info_arr, $detail_dvs_num) {
    $ret = array();

    $i = 0;
    while ($rs && !$rs->EOF) {
        $name = $rs->fields["after_name"];
        $depth1 = $rs->fields["depth1"];
        $depth2 = $rs->fields["depth2"];
        $depth3 = $rs->fields["depth3"];
        $mpcode = $rs->fields["mpcode"];

        $info = $info_arr[$mpcode];

        $ret[$i]["order_detail_dvs_num"] = $detail_dvs_num;
        $ret[$i]["after_name"] = $name;
        $ret[$i]["depth1"]     = $depth1;
        $ret[$i]["depth2"]     = $depth2;
        $ret[$i]["depth3"]     = $depth3;
        $ret[$i]["price"]      = $info["price"];
        $ret[$i]["detail"]     = $info["info"];
        $ret[$i]["seq"]        = $i + 1;
        $ret[$i++]["basic_yn"] = 'N';

        $rs->MoveNext();
    }

    return $ret;
}
?>
