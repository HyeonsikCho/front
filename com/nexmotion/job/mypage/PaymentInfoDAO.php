<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/MypageCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/html/mypage/PaymentInfoHtml.php");

/**
 * @file OtoInqMngDAO.php
 *
 * @brief 마이페이지 - 결제정보
 */
class PaymentInfoDAO extends MypageCommonDAO {

    function __construct() {
    }

    /**
     * @brief 결재내역 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectPaymentList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);
        $seqno = substr($param["member_seqno"], 1, -1);

        if ($type === "COUNT") {
            $query  = "\n  SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n  SELECT  A.deal_date";
            $query .= "\n         ,A.dvs";
            $query .= "\n         ,A.depo_way";
            $query .= "\n         ,B.title";
            $query .= "\n         ,A.depo_price";
            $query .= "\n         ,A.pay_price";
        }
        $query .= "\n    FROM  member_pay_history AS A";
        $query .= "\n         ,order_common AS B";
        $query .= "\n   WHERE  A.order_num = B.order_num";
        $query .= "\n     AND  A.member_seqno IN (" . $seqno . ")";

        //구분
        if ($this->blankParameterCheck($param ,"dvs")) {
            $query .= "\n     AND  A.dvs = " . $param["dvs"];
        }

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .= "\n     AND  A.deal_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {
            $to = substr($param["to"], 1, -1);
            $query .= "\n     AND  A.deal_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($type == "SEQ") {
            $query .= "\nORDER BY A.deal_date DESC";
            $query .= "\n   LIMIT ". $s_num . ", " . $list_num;
        }

        return $conn->Execute($query);
    }

    /**
     * @brief 거래내역 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectTransactionalInfoList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);
        $seqno = substr($param["member_seqno"], 1, -1);

        if ($type === "COUNT") {
            $query  = "\n  SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n  SELECT  A.deal_date";
            $query .= "\n         ,A.order_num";
            $query .= "\n         ,B.order_detail";
            $query .= "\n         ,B.amt";
            $query .= "\n         ,B.amt_unit_dvs";
            $query .= "\n         ,B.count";
            $query .= "\n         ,B.title";
            $query .= "\n         ,A.sell_price";
            $query .= "\n         ,A.sale_price";
            $query .= "\n         ,A.pay_price";
        }
        $query .= "\n    FROM  member_pay_history AS A";
        $query .= "\n         ,order_common AS B";
        $query .= "\n   WHERE  A.order_num = B.order_num";
        $query .= "\n     AND  A.member_seqno IN (" . $seqno . ")";

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .= "\n     AND  A.deal_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {
            $to = substr($param["to"], 1, -1);
            $query .= "\n     AND  A.deal_date <= '" . $to;
            $query .=" 23:59:59'";
        }  
  
        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($type == "SEQ") {
            $query .= "\nORDER BY A.deal_date DESC";
            $query .= "\n   LIMIT ". $s_num . ", " . $list_num;
        }

        return $conn->Execute($query);
    }


    /**
     * @brief 거래내역 총매출액,에누리,순매출액,입금액,일일잔액
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectTransactionPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }
        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);
        $seqno = substr($param["member_seqno"], 1, -1);
        $query  = "\n  SELECT SUM(A.sell_price) AS sell_price";
        $query .= "\n        ,SUM(A.sale_price) AS sale_price";
        $query .= "\n        ,SUM(A.pay_price) AS pay_price";
        $query .= "\n    FROM  member_pay_history AS A";
        $query .= "\n         ,order_common AS B";
        $query .= "\n   WHERE  A.order_num = B.order_num";
        $query .= "\n     AND  A.member_seqno =" . $seqno;

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .= "\n     AND  A.deal_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {
            $to = substr($param["to"], 1, -1);
            $query .= "\n     AND  A.deal_date <= '" . $to;
            $query .=" 23:59:59'";
        }  

        return $conn->Execute($query);
    }

    /**
     * @brief 거래내역 입금액,일일잔액
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectDepoBalancePrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $type = substr($param["type"], 1, -1);
        $seqno = substr($param["member_seqno"], 1, -1);
        if ($type == "BALANCE") {
            $query .= "\n  SELECT prepay_bal";
        } else {
            $query  = "\n  SELECT SUM(depo_price) AS depo_price";
        }
        $query .= "\n    FROM  member_pay_history";
        $query .= "\n   WHERE  member_seqno =" . $seqno;

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .= "\n     AND  deal_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {
            $to = substr($param["to"], 1, -1);
            $query .= "\n     AND  deal_date <= '" . $to;
            $query .=" 23:59:59'";
        }  

        if ($type == "BALANCE") {
            $query .= "\nORDER BY deal_date DESC";
            $query .= "\n   LIMIT 1";
        }
  
        return $conn->Execute($query);
    }

    /**
     * @brief 해당 회원의 사업자 정보 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectLicenseInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param["col"]  = " corp_name";
        $param["col"] .= ",crn";
        $param["col"] .= ",repre_name";
        $param["col"] .= ",addr";
        $param["col"] .= ",addr_detail";
        $param["col"] .= ",bc";
        $param["col"] .= ",tob";
        $param["table"] = "licensee_info";
        $param["where"]["member_seqno"] = $param["member_seqno"];

        $rs = $dao->selectData($conn, $param);

        return $rs->fields;
    }
}
?>
