<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/OrderCommonDAO.php');

class CompleteDAO extends OrderCommonDAO {
    function __construct() {
    }

    /**
     * @brief 회원_포인트_내역 테이블 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 입력값 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertMemberPointHistory($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n INSERT INTO member_point_history (";
        $query .= "\n      regi_date";
        $query .= "\n     ,dvs";
        $query .= "\n     ,point";
        $query .= "\n     ,rest_point";
        $query .= "\n     ,order_price";
        $query .= "\n     ,order_num";
        $query .= "\n     ,member_seqno";
        $query .= "\n     ,member_grade";
        $query .= "\n ) VALUES (";
        $query .= "\n      now()";
        $query .= "\n     ,'사용'";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n )";

        $query  = sprintf($query, $param["point"]
                                , $param["rest_point"]
                                , $param["order_price"]
                                , $param["order_num"]
                                , $param["member_seqno"]
                                , $param["member_grade"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 주문_공통 테이블 값 수정
     *
     * @param $conn  = connection identifier
     * @param $param = 수정값 파라미터
     *
     * @return 쿼리실행결과
     */
    function updateOrderCommon($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $except_arr = array("prdt_basic_info" => true,
                            "prdt_add_info"   => true,
                            "prdt_price_info" => true,
                            "prdt_pay_info"   => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = "\n UPDATE  order_common AS A";
        $query .= "\n    SET  A.bun_group          = %s";
        $query .= "\n        ,A.owncompany_img_num = %s";
        $query .= "\n        ,A.oper_sys           = %s";
        $query .= "\n        ,A.cust_memo          = %s";
        $query .= "\n        ,A.pay_way            = %s";
        $query .= "\n        ,A.bun_yn             = %s";
        $query .= "\n        ,A.point_use_yn       = %s";
        $query .= "\n        ,A.use_point_price    = %s";
        $query .= "\n        ,A.pay_price          = %s";
        $query .= "\n        ,A.prdt_basic_info    = '%s'";
        $query .= "\n        ,A.prdt_add_info      = '%s'";
        $query .= "\n        ,A.prdt_pay_info      = '%s'";
        $query .= "\n        ,A.prdt_price_info    = '%s'";
        $query .= "\n        ,A.order_state        = %s";
        $query .= "\n        ,A.order_mng          = %s";
        $query .= "\n        ,A.file_upload_dvs    = %s";
        $query .= "\n        ,A.receipt_dvs        = %s";
        $query .= "\n        ,A.order_lack_price   = %s";
        $query .= "\n        ,A.dlvr_produce_dvs   = %s";
        $query .= "\n        ,A.order_regi_date    = now()";
        if ($this->blankParameterCheck($param, "depo_finish_date")) {
            $query .= "\n        ,A.depo_finish_date      = ";
            $query .= $param["depo_finish_date"];
        }
        $query .= "\n  WHERE  A.order_common_seqno = %s";

        $query  = sprintf($query, $param["bun_group"]
                                , $param["owncompany_img_num"]
                                , $param["oper_sys"]
                                , $param["cust_memo"]
                                , $param["pay_way"]
                                , $param["bun_yn"]
                                , $param["point_use_yn"]
                                , $param["use_point_price"]
                                , $param["pay_price"]
                                , $param["prdt_basic_info"]
                                , $param["prdt_add_info"]
                                , $param["prdt_pay_info"]
                                , $param["prdt_price_info"]
                                , $param["order_state"]
                                , $param["order_mng"]
                                , $param["file_upload_dvs"]
                                , $param["receipt_dvs"]
                                , $param["order_lack_price"]
                                , $param["dlvr_produce_dvs"]
                                , $param["order_common_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 주문_공통 주문 상태 값 수정
     *
     * @param $conn  = connection identifier
     * @param $param = 수정값 파라미터
     *
     * @return 쿼리실행결과
     */
    function updateOrderState($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n UPDATE  order_common AS A";
        $query .= "\n  WHERE  A.order_common_seqno = %s";

        $query  = sprintf($query, $param[""]
                                , $param["order_common_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 주문_배송 테이블 값 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 수정값 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertOrderDlvr($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n INSERT INTO order_dlvr (";
        $query .= "\n      tsrs_dvs";
        $query .= "\n     ,name";
        $query .= "\n     ,tel_num";
        $query .= "\n     ,cell_num";
        $query .= "\n     ,zipcode";
        $query .= "\n     ,addr";
        $query .= "\n     ,addr_detail";
        $query .= "\n     ,sms_yn";
        $query .= "\n     ,dlvr_way";
        $query .= "\n     ,dlvr_sum_way";
        $query .= "\n     ,dlvr_price";
        $query .= "\n     ,invo_cpn";
        $query .= "\n     ,order_common_seqno";
        $query .= "\n     ,bun_dlvr_order_num";
        $query .= "\n     ,bun_group";
        $query .= "\n     ,lump_count";
        $query .= "\n ) VALUES (";
        $query .= "\n      %s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n )";

        $query  = sprintf($query, $param["tsrs_dvs"]
                                , $param["name"]
                                , $param["tel_num"]
                                , $param["cell_num"]
                                , $param["zipcode"]
                                , $param["addr"]
                                , $param["addr_detail"]
                                , $param["sms_yn"]
                                , $param["dlvr_way"]
                                , $param["dlvr_sum_way"]
                                , $param["dlvr_price"]
                                , $param["invo_cpn"]
                                , $param["order_common_seqno"]
                                , $param["bun_dlvr_order_num"]
                                , $param["bun_group"]
                                , $param["lump_count"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 기본정보 생성에 필요한 정보 검색
     *
     * @detail 카테고리_이름, 인쇄도수, 수량, 건수
     * 특이사항, 고객메모, 사이즈
     *
     * @param $conn  = connection identifier
     * @param $param = 테이블명, 주문 공통 일련번호
     *
     * @return 쿼리실행결과
     */
    function selectOrderData($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $table = $param["table"];
        $seqno = $this->parameterEscape($conn, $param["seqno"]);

        $query .= "\n SELECT  B.cate_name";
        $query .= "\n        ,B.flattyp_yn";
        $query .= "\n        ,B.typset_way";
        $query .= "\n        ,A.cate_sortcode";
        $query .= "\n        ,A.amt";
        $query .= "\n        ,A.amt_unit_dvs";
        if ($table !== "order_common") {
            $query .= "\n        ,A.stan_name";
            $query .= "\n        ,A.print_tmpt_name";
            $query .= "\n        ,A.after_use_yn";
        }
        if ($table !== "order_detail_brochure") {
            $query .= "\n        ,A.count";
        }
        if ($table === "order_common") {
            $query .= "\n        ,A.title";
        }

        $query .= "\n   FROM  %s AS A";
        $query .= "\n        ,cate         AS B";

        $query .= "\n  WHERE  A.%s_seqno = %s";
        $query .= "\n    AND  A.cate_sortcode = B.sortcode";

        $query  = sprintf($query, $table
                                , $table
                                , $seqno);

        $rs = $conn->Execute($query);

        return $rs->fields;
    }

    /**
     * @brief 가격정보 생성에 필요한 정보 검색
     *
     * @detail 상품_금액, 추가_후공정_금액, 추가_옵션_금액
     *
     * @param $conn  = connection identifier
     * @param $seqno = 주문 공통 일련번호
     *
     * @return 쿼리실행결과
     */
    function selectOrderCommonPriceData($conn, $seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $seqno = $this->parameterEscape($conn, $seqno);

        $query .= "\n SELECT  A.sell_price";
        $query .= "\n        ,A.grade_sale_price";
        $query .= "\n        ,A.add_after_price";
        $query .= "\n        ,A.add_opt_price";

        $query .= "\n   FROM  order_common AS A";

        $query .= "\n  WHERE  A.order_common_seqno = %s";

        $query  = sprintf($query, $seqno);

        $rs = $conn->Execute($query);

        return $rs->fields;
    }

    /**
     * @brief 추가정보 생성에 필요한 정보 검색
     *
     * @detail 인쇄유형, 페이지, 종이
     *
     * @param $conn  = connection identifier
     * @param $seqno = 주문 공통 일련번호
     *
     * @return 쿼리실행결과
     */
    function selectOrderDetailData($conn, $seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $seqno = $this->parameterEscape($conn, $seqno);

        $query  = "\n SELECT  A.print_purp_dvs";
        $query .= "\n        ,A.page_amt";
        $query .= "\n        ,A.stan_name";
        $query .= "\n        ,A.print_tmpt_name";
        $query .= "\n        ,A.after_use_yn";
        $query .= "\n        ,CONCAT( B.name";
        $query .= "\n                ,' '";
        $query .= "\n                ,B.dvs";
        $query .= "\n                ,' '";
        $query .= "\n                ,B.color";
        $query .= "\n                ,' '";
        $query .= "\n                ,B.basisweight) AS paper";
        $query .= "\n   FROM  order_detail AS A";
        $query .= "\n        ,cate_paper   AS B";
        $query .= "\n  WHERE  A.order_common_seqno = %s";
        $query .= "\n    AND  A.cate_paper_mpcode = B.mpcode";

        $query  = sprintf($query, $seqno);

        $rs = $conn->Execute($query);

        return $rs;
    }

    /**
     * @brief 추가정보 생성에 필요한 정보 검색
     *
     * @detail 인쇄유형, 페이지, 종이
     *
     * @param $conn  = connection identifier
     * @param $seqno = 주문 공통 일련번호
     *
     * @return 쿼리실행결과
     */
    function selectOrderDetailBrochureData($conn, $seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $seqno = $this->parameterEscape($conn, $seqno);

        $query  = "\n SELECT  A.print_purp_dvs";
        $query .= "\n        ,A.print_tmpt_name";
        $query .= "\n        ,A.page_amt";
        $query .= "\n        ,A.after_use_yn";
        $query .= "\n        ,CONCAT( B.name";
        $query .= "\n                ,' '";
        $query .= "\n                ,B.dvs";
        $query .= "\n                ,' '";
        $query .= "\n                ,B.color";
        $query .= "\n                ,' '";
        $query .= "\n                ,B.basisweight) AS paper";
        $query .= "\n   FROM  order_detail_brochure AS A";
        $query .= "\n        ,cate_paper   AS B";
        $query .= "\n  WHERE  A.order_common_seqno = %s";
        $query .= "\n    AND  A.cate_paper_mpcode = B.mpcode";

        $query  = sprintf($query, $seqno);

        $rs = $conn->Execute($query);

        return $rs;
    }

    /**
     * @brief 추가정보 생성에 필요한 정보 검색
     *
     * @detail 후공정 정보
     *
     * @param $conn  = connection identifier
     * @param $param = 주문 공통 일련번호
     *
     * @return 쿼리실행결과
     */
    function selectOrderOptHistory($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }
        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  A.opt_name AS name";
        $query .= "\n        ,A.depth1";
        $query .= "\n        ,A.depth2";
        $query .= "\n        ,A.depth3";
        $query .= "\n        ,A.basic_yn";
        $query .= "\n   FROM  order_opt_history AS A";
        $query .= "\n  WHERE  A.order_common_seqno = %s";

        $query  = sprintf($query, $param["order_common_seqno"]);

        $rs = $conn->Execute($query);

        return $rs;
    }

    /**
     * @brief 수량_주문_상세_낱장 정보 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 입력정보 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertAmtOrderDetailSheet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"]   = "order_detail_count_file_seqno";
        $temp["table"] = "order_detail_count_file";
        $temp["where"]["order_detail_seqno"] = $param["order_detail_seqno"];

        $rs = $this->selectData($conn, $temp);

        unset($temp);
        $temp["table"] = "amt_order_detail_sheet";
        $temp["col"]["amt"]   = $param["amt"];
        $temp["col"]["state"] = $param["state"];

        $conn->StartTrans();

        while ($rs && !$rs->EOF) {
            $order_detail_count_file_seqno =
                $rs->fields["order_detail_count_file_seqno"];

            $temp["col"]["order_detail_count_file_seqno"] =
                $order_detail_count_file_seqno;
            $ret = $this->insertData($conn, $temp);

            if ($conn->HasFailedTrans() === true || $ret === false) {
                return false;
            }

            $rs->MoveNext();
        }

        $conn->CompleteTrans();

        return true;
    }

    /**
     * @brief 페이지_주문_상세_책자 정보 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 입력정보 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertPageOrderDetailBrochure($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["table"] = "page_order_detail_brochure";
        $temp["col"]["page"]  = $param["page"];
        $temp["col"]["state"] = $param["state"];
        $temp["col"]["order_detail_dvs_num"] = $param["order_detail_dvs_num"];

        return $this->insertData($conn, $temp);
    }

    /**
     * @brief 후공정_발주 정보 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 입력정보 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertAfterOp($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n INSERT INTO after_op (";
        $query .= "\n      regi_date";
        $query .= "\n     ,op_name";
        //$query .= "\n     ,seq";
        $query .= "\n     ,after_name";
        $query .= "\n     ,depth1";
        $query .= "\n     ,depth2";
        $query .= "\n     ,depth3";
        $query .= "\n     ,amt";
        $query .= "\n     ,amt_unit";
        //$query .= "\n     ,detail";
        $query .= "\n     ,op_typ";
        $query .= "\n     ,op_typ_detail";
        $query .= "\n     ,basic_yn";
        $query .= "\n     ,orderer";
        $query .= "\n     ,state";
        $query .= "\n     ,order_detail_dvs_num";
        $query .= "\n     ,order_common_seqno";
        $query .= "\n     ,extnl_brand_seqno";
        $query .= "\n ) VALUES (";
        $query .= "\n      now()";
        $query .= "\n     ,%s";
        //$query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        //$query .= "\n     ,%s";
        //$query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n )";

        $query  = sprintf($query, $param["op_name"]
                                //, $param["seq"]
                                , $param["after_name"]
                                , $param["depth1"]
                                , $param["depth2"]
                                , $param["depth3"]
                                , $param["amt"]
                                , $param["amt_unit"]
                                //, $param["detail"]
                                , "'자동발주'"//$param["op_typ"]
                                , "'자동발주프로그램'"//$param["op_typ_detail"]
                                , "'Y'"//$param["basic_yn"]
                                , $param["orderer"]
                                , $param["state"]
                                , $param["order_detail_dvs_num"]
                                , $param["order_common_seqno"]
                                , $param["extnl_brand_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 운영체제에 따른 주문담당자 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 입력정보 파라미터
     *
     * @return 쿼리실행결과
     */
    function selectOrderMngData($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $except_arr = array("oper_sys" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query .= "\n SELECT  B.name";

        $query .= "\n   FROM  member_mng AS A";
        $query .= "\n        ,empl AS B";

        $query .= "\n  WHERE  A.member_seqno = %s";
        $query .= "\n    AND  A.mng_dvs = '일반'";
        if ($param["oper_sys"] === "IBM") {
            $query .= "\n    AND  A.ibm_mng = B.empl_seqno";
        }
        if ($param["oper_sys"] === "MAC") {
            $query .= "\n    AND  A.mac_mng = B.empl_seqno";
        }

        $query  = sprintf($query, $param["member_seqno"]);

        $rs = $conn->Execute($query);

        $name = $rs->fields["name"];

        if (empty($name)) {
            $name = "미분류";
        }

        return $name;
    }

    /**
     * @brief 기본 후공정 외부 브랜드명 검색
     *
     * @param $conn = connection identifier
     * @param $name = 기본 후공정 depth1
     *
     * @return 쿼리실행결과
     */
    function selectExtnlBrand($conn, $name) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"] = "extnl_brand_seqno";
        $temp["table"] = "extnl_brand";
        $temp["where"]["name"] = $name;

        $rs = $this->selectData($conn, $temp);

        return $rs->fields["extnl_brand_seqno"];
    }

    /**
     * @brief 주문_파일 정보 검색
     *
     * @param $conn = connection identifier
     * @param $seqno = 주문 공통 일련번호
     *
     * @return 검색결과
     */
    function selectOrderFile($conn, $seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $seqno = $this->parameterEscape($conn, $seqno);

        $query  = "\n SELECT  A.file_path";
        $query .= "\n        ,A.save_file_name";
        $query .= "\n   FROM  order_file AS A";
        $query .= "\n  WHERE  A.order_common_seqno = %s";

        $query  = sprintf($query, $seqno);

        $rs = $conn->Execute($query);

        return $rs;
    }

    /**
     * @brief 주문_상세 테이블 기본/추가정보 수정
     *
     * @param $conn  = connection identifier
     * @param $param = 수정값 파라미터
     *
     * @return 쿼리실행결과
     */
    function updateOrderDetailInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $except_arr = array("basic_info" => true,
                            "add_info"   => true,
                            "table"      => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = "\n UPDATE  %s AS A";
        $query .= "\n    SET  A.%s_seqno = A.%s_seqno";
        if ($this->blankParameterCheck($param, "basic_info")) {
            $query .= "\n        ,A.prdt_basic_info = '";
            $query .= $param["basic_info"] . "'";
        }
        if ($this->blankParameterCheck($param, "add_info")) {
            $query .= "\n        ,A.prdt_add_info   = '";
            $query .= $param["add_info"] . "'";
        }
        if ($this->blankParameterCheck($param, "use_point_price")) {
            $query .= "\n        ,A.use_point_price = ";
            $query .= $param["use_point_price"];
        }
        if ($this->blankParameterCheck($param, "state")) {
            $query .= "\n        ,A.state = ";
            $query .= $param["state"];
        }
        if ($this->blankParameterCheck($param, "receipt_dvs")) {
            $query .= "\n        ,A.receipt_dvs = ";
            $query .= $param["receipt_dvs"];
        }
        if ($this->blankParameterCheck($param, "pay_price")) {
            $query .= "\n        ,A.pay_price = ";
            $query .= $param["pay_price"];
        }
        $query .= "\n  WHERE  A.%s_seqno = %s";

        $query  = sprintf($query, $param["table"]
                                , $param["table"]
                                , $param["table"]
                                , $param["table"]
                                , $param["seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 주문_상세_건수_파일 주문상태 수정
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return option html
     */
    function updateOrderDetailCountFile($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n UPDATE order_detail_count_file";
        $query .= "\n    SET state = %s";
        $query .= "\n  WHERE order_detail_seqno = %s";

        $query  = sprintf($query, $param["state"]
                                , $param["seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 발행_관리_임시 테이블 데이터 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 입력값 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertPublicAdminTemp($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $public_dvs = $param["public_dvs"];

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n INSERT INTO public_admin_temp (";
        $query .= "\n     member_seqno";
        $query .= "\n    ,public_dvs";
        $query .= "\n    ,req_year";
        $query .= "\n    ,req_mon";
        $query .= "\n    ,req_date";
        $query .= "\n    ,tel_num";
        $query .= "\n    ,print_title";
        $query .= "\n    ,public_state";
        $query .= "\n    ,tab_public";
        $query .= "\n    ,supply_price";
        $query .= "\n    ,pay_price";
        $query .= "\n    ,vat";
        $query .= "\n    ,member_name";
        $query .= "\n    ,unitprice";
        $query .= "\n    ,object_price";
        $query .= "\n    ,money_price";
        $query .= "\n    ,card_price";
        $query .= "\n    ,etc_price";
        $query .= "\n    ,order_num";
        if ($public_dvs === "현금영수증") {
            $query .= "\n    ,evid_dvs";
            $query .= "\n    ,cashreceipt_num";
        }
        if ($public_dvs === "세금계산서") {
            $query .= "\n    ,corp_name";
            $query .= "\n    ,repre_name";
            $query .= "\n    ,zipcode";
            $query .= "\n    ,crn";
            $query .= "\n    ,bc";
            $query .= "\n    ,tob";
            $query .= "\n    ,addr";
        }
        $query .= "\n ) VALUES (";
        $query .= "\n     %s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,now()";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        $query .= "\n    ,%s";
        if ($public_dvs === "현금영수증") {
            $query .= "\n    ," . $param["evid_dvs"];
            $query .= "\n    ," . $param["cashreceipt_num"];
        }
        if ($public_dvs === "세금계산서") {
            $query .= "\n    ," . $param["corp_name"];
            $query .= "\n    ," . $param["repre_name"];
            $query .= "\n    ," . $param["zipcode"];
            $query .= "\n    ," . $param["crn"];
            $query .= "\n    ," . $param["bc"];
            $query .= "\n    ," . $param["tob"];
            $query .= "\n    ," . $param["addr"];
        }
        $query .= "\n )";

        $query  = sprintf($query, $param["member_seqno"]
                                , $param["public_dvs"]
                                , $param["req_year"]
                                , $param["req_mon"]
                                , $param["tel_num"]
                                , $param["print_title"]
                                , $param["public_state"]
                                , $param["tab_public"]
                                , $param["supply_price"]
                                , $param["pay_price"]
                                , $param["vat"]
                                , $param["member_name"]
                                , $param["unitprice"]
                                , $param["object_price"]
                                , $param["money_price"]
                                , $param["card_price"]
                                , $param["etc_price"]
                                , $param["order_num"]);

        return $conn->Execute($query);
    }
}
