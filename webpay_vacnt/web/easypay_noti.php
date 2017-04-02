<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/CommonDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$frontUtil = new FrontCommonUtil();
$dao = new CommonDAO();
   
/* -------------------------------------------------------------------------- */
/* ::: 노티수신                                                               */
/* -------------------------------------------------------------------------- */
$result_msg = "";

/*
$r_res_cd         = $_POST[ "res_cd"         ];  // 응답코드
$r_res_msg        = $_POST[ "res_msg"        ];  // 응답 메시지
$r_cno            = $_POST[ "cno"            ];  // PG거래번호
$r_memb_id        = $_POST[ "memb_id"        ];  // 가맹점 ID
$r_amount         = $_POST[ "amount"         ];  // 총 결제금액
$r_order_no       = $_POST[ "order_no"       ];  // 주문번호
$r_noti_type      = $_POST[ "noti_type"      ];  // 노티구분
$r_auth_no        = $_POST[ "auth_no"        ];  // 승인번호
$r_tran_date      = $_POST[ "tran_date"      ];  // 승인일시
$r_bank_cd        = $_POST[ "bank_cd"        ];  // 은행코드
$r_bank_nm        = $_POST[ "bank_nm"        ];  // 은행명
$r_account_no     = $_POST[ "account_no"     ];  // 계좌번호
$r_deposit_nm     = $_POST[ "deposit_nm"     ];  // 입금자명
$r_expire_date    = $_POST[ "expire_date"    ];  // 계좌사용만료일
$r_cash_res_cd    = $_POST[ "cash_res_cd"    ];  // 현금영수증 결과코드
$r_cash_res_msg   = $_POST[ "cash_res_msg"   ];  // 현금영수증 결과메시지
$r_cash_auth_no   = $_POST[ "cash_auth_no"   ];  // 현금영수증 승인번호
$r_cash_tran_date = $_POST[ "cash_tran_date" ];  // 현금영수증 승인일시
$r_escrow_yn      = $_POST[ "escrow_yn"      ];  // 에스크로 사용유무
$r_pay_type       = $_POST[ "pay_type"       ];  // 결제수단
$r_auth_cno       = $_POST[ "auth_cno"       ];  // 인증거래번호
$r_tlf_sno        = $_POST[ "tlf_sno"        ];  // 채번거래번호
$r_account_type   = $_POST[ "account_type"   ];  // 채번계좌 타입 US AN 1 (V-일반형, F-고정형)
$r_reserve1       = $_POST[ "reserve1"       ];  // 가맹점 필드1
$r_reserve2       = $_POST[ "reserve2"       ];  // 가맹점 필드2
$r_reserve3       = $_POST[ "reserve3"       ];  // 가맹점 필드3
$r_reserve4       = $_POST[ "reserve4"       ];  // 가맹점 필드4
$r_reserve5       = $_POST[ "reserve5"       ];  // 가맹점 필드5
$r_reserve6       = $_POST[ "reserve6"       ];  // 가맹점 필드6
*/

$r_amount = intval($fb->form("amount"));  // 총 결제금액
$r_account_no = $fb->form("account_no");  // 계좌번호

$ret = false;

if ($r_res_cd == "0000") {

    /* ---------------------------------------------------------------------- */
    /* ::: 가맹점 DB 처리                                                     */
    /* ---------------------------------------------------------------------- */
    /* DB처리 성공 시 : res_cd=0000, 실패 시 : res_cd=5001                    */
    /* ---------------------------------------------------------------------- */
    $member_seqno = $dao->selectDepositMember($conn, $r_account_no)->fields["member_seqno"];

    $prepay_price = $dao->selectPrepayPrice($conn, $member_seqno)->fields["prepay_price"];

    $conn->StartTrans();

    $param = array();
    $param["member_seqno"]  = $member_seqno;
    $param["order_num"]     = '';
    $param["dvs"]           = "입금";
    $param["sell_price"]    = '0';
    $param["sale_price"]    = '0';
    $param["pay_price"]     = '0';
    $param["depo_price"]    = $r_amount;
    $param["depo_way"]      = "가상계좌";
    $param["exist_prepay"]  = $prepay_price;
    $param["prepay_bal"]    = $prepay_bal;
    $param["state"]         = "결제완료";
    $param["deal_num"]      = '';
    $param["prepay_use_yn"] = '-';

    unset($param);
    $param["prepay_price"] = $prepay_price + $r_amount;
    $param["member_seqno"] = $member_seqno;

    $ret = $dao->updateMemberPrepay($conn, $param);

    // DB처리 성공 시
    if ($ret === false) {
        goto RESULT;
    }

    // 발행_대상_금액 테이블 입력or수정
    $rs = $dao->selectPublicOrderPrice($conn, $member_seqno);

    if ($rs->EOF) {
        unset($param);
        $param["public_object_price"]   = $r_amount;
        $param["unissued_object_price"] = '0';
        $param["member_seqno"]          = $member_seqno;

        $ret = $dao->insertPublicObjectPrice($conn, $param);

    } else {
        $public_price  = intval($rs->fields["public_object_price"]);
        $public_price += $public_price + $r_amount;

        unset($param);
        $param["public_object_price"]   = $public_price;
        $param["unissued_object_price"] = '0';
        $param["member_seqno"]          = $member_seqno;

        $ret = $dao->updatePublicObjectPrice($conn, $param);
    }

    if ($ret === false) {
        goto RESULT;
    }

    $conn->CompleteTrans();
    exit;
} else {
    echo "결제 된 계좌 없음";
    exit;
}

/* -------------------------------------------------------------------------- */
/* ::: 노티 처리결과 처리                                                     */
/* -------------------------------------------------------------------------- */
RESULT:
    $conn->FailTrans();
    $conn->RollbackTrans();
    
    if ($ret) {         
        echo "res_cd=0000" . chr(31) . "res_msg=SUCCESS";
    // DB처리 실패
    } else {  
        echo "res_cd=5001" . chr(31) . "res_msg=FAIL";
    }

    exit;
?>
