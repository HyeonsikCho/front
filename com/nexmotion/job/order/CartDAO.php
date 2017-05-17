<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/OrderCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/order/CartHTML.php');

class CartDAO extends OrderCommonDAO {
    function __construct() {
    }

    /**
     * @brief 카테고리 옵션 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 분류코드
     * $param["sell_site"] = 판매채널
     * $param["basic_yn"] = 기본여부
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 쿼리실행결과
     */
    function selectCateOptInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $except_arr = array("mpcode" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query .= "\n SELECT  A.opt_name";
        $query .= "\n        ,A.depth1";
        $query .= "\n        ,A.depth2";
        $query .= "\n        ,A.depth3";
        $query .= "\n        ,B.mpcode";
        //$query .= "\n        ,C.sell_price";

        $query .= "\n   FROM  prdt_opt       AS A";
        $query .= "\n        ,cate_opt       AS B";
        //$query .= "\n        ,cate_opt_price AS C";

        $query .= "\n  WHERE  A.prdt_opt_seqno = B.prdt_opt_seqno";
        //$query .= "\n    AND  B.mpcode = C.cate_opt_mpcode";
        $query .= "\n    AND  B.cate_sortcode = %s";
        $query .= "\n    AND  B.basic_yn = %s";
        //$query .= "\n    AND  C.cpn_admin_seqno = %s";
        /*
        if ($this->blankParameterCheck($param, "amt")) {
            $query .= "\n    AND  " . $param["amt"] . " <= (C.amt + 0)";
        }
        */
        if ($this->blankParameterCheck($param, "mpcode")) {
            $query .= "\n    AND  B.mpcode IN (";
            $query .= $param["mpcode"];
            $query .= ')';
        }
        /*
        if ($this->blankParameterCheck($param, "amt")) {
            $query .= "\n  LIMIT  1";
        }
        */

        $query  = sprintf($query, $param["cate_sortcode"]
                                , $param["basic_yn"]);
                                //, $param["cpn_admin_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 카테고리 후공정 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 분류코드
     * $param["sell_site"] = 판매채널
     * $param["basic_yn"] = 기본여부
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 쿼리실행결과
     */
    function selectCateAfterInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $except_arr = array("mpcode" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query .= "\n SELECT  A.after_name";
        $query .= "\n        ,A.depth1";
        $query .= "\n        ,A.depth2";
        $query .= "\n        ,A.depth3";
        $query .= "\n        ,B.mpcode";

        $query .= "\n   FROM  prdt_after AS A";
        $query .= "\n        ,cate_after AS B";

        $query .= "\n  WHERE  A.prdt_after_seqno = B.prdt_after_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";
        $query .= "\n    AND  B.basic_yn = %s";
        if ($this->blankParameterCheck($param, "mpcode")) {
            $query .= "\n    AND  B.mpcode IN (";
            $query .= $param["mpcode"];
            $query .= ')';
        }

        $query  = sprintf($query, $param["cate_sortcode"]
                                , $param["basic_yn"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 주문_공통 테이블에 데이터 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertOrderCommon($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $except_arr = array("group_seqno" => true);
        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = " \n INSERT INTO order_common (";
        $query .= " \n      order_regi_date";
        $query .= " \n     ,member_seqno";
        $query .= " \n     ,cpn_admin_seqno";
        $query .= " \n     ,order_num";
        $query .= " \n     ,order_state";
        $query .= " \n     ,order_detail";
        $query .= " \n     ,mono_yn";
        $query .= " \n     ,claim_yn";
        $query .= " \n     ,title";
        $query .= " \n     ,sell_price";
        $query .= " \n     ,grade_sale_price";
        $query .= " \n     ,add_opt_price";
        $query .= " \n     ,add_after_price";
        $query .= " \n     ,event_price";
        $query .= " \n     ,expec_weight";
        $query .= " \n     ,cate_sortcode";
        $query .= " \n     ,opt_use_yn";
        $query .= " \n     ,del_yn";
        $query .= " \n     ,group_seqno";
        $query .= " \n     ,amt";
        $query .= " \n     ,amt_unit_dvs";
        $query .= " \n     ,page_cnt";
        $query .= " \n     ,count";
        $query .= " \n ) VALUES (";
        $query .= " \n      now()";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n )";

        $query  = sprintf($query, $param["member_seqno"]
                                , $param["cpn_admin_seqno"]
                                , $param["order_num"]
                                , $param["order_state"]
                                , $param["order_detail"]
                                , $param["mono_yn"]
                                , $param["claim_yn"]
                                , $param["title"]
                                , $param["sell_price"]
                                , $param["grade_sale_price"]
                                , $param["add_opt_price"]
                                , $param["add_after_price"]
                                , $param["event_price"]
                                , $param["expec_weight"]
                                , $param["cate_sortcode"]
                                , $param["opt_use_yn"]
                                , $param["del_yn"]
                                , $param["group_seqno"]
                                , $param["amt"]
                                , $param["amt_unit_dvs"]
                                , $param["page_cnt"]
                                , $param["count"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 주문_상세 테이블에 데이터 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertOrderDetail($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["table"] = "order_detail";
        $temp["col"]["order_common_seqno"] = $param["order_common_seqno"];

        $temp["col"]["cate_beforeside_print_mpcode"] =
                                            $param["cate_bef_print_mpcode"];
        $temp["col"]["cate_aftside_print_mpcode"] =
                                            $param["cate_aft_print_mpcode"];
        $temp["col"]["cate_beforeside_add_print_mpcode"] =
                                            $param["cate_bef_add_print_mpcode"];
        $temp["col"]["cate_aftside_add_print_mpcode"] =
                                            $param["cate_aft_add_print_mpcode"];

        $temp["col"]["cate_paper_mpcode"] = $param["cate_paper_mpcode"];
        
        $temp["col"]["typ"] = $param["typ"];
        $temp["col"]["page_amt"] = $param["page_amt"];

        $temp["col"]["cut_size_wid"]  = $param["cut_size_wid"];
        $temp["col"]["cut_size_vert"] = $param["cut_size_vert"];
        $temp["col"]["work_size_wid"]  = $param["work_size_wid"];
        $temp["col"]["work_size_vert"] = $param["work_size_vert"];
        $temp["col"]["tomson_size_wid"]  = $param["tomson_size_wid"];
        $temp["col"]["tomson_size_vert"] = $param["tomson_size_vert"];

        $temp["col"]["print_purp_dvs"] = $param["print_purp_dvs"];
        $temp["col"]["del_yn"] = 'N';

        $temp["col"]["spc_dscr"]             = $param["spc_dscr"];
        $temp["col"]["sell_price"]           = $param["sell_price"];
        $temp["col"]["grade_sale_price"]     = $param["grade_sale_price"];
        $temp["col"]["add_after_price"]      = $param["add_after_price"];
        $temp["col"]["cp_price"]             = $param["cp_price"];
        $temp["col"]["pay_price"]            = $param["pay_price"];
        $temp["col"]["order_detail"]         = $param["order_detail"];
        $temp["col"]["mono_yn"]              = $param["mono_yn"];
        $temp["col"]["stan_name"]            = $param["stan_name"];
        $temp["col"]["amt"]                  = $param["amt"];
        $temp["col"]["count"]                = $param["count"];
        $temp["col"]["expec_weight"]         = $param["expec_weight"];
        $temp["col"]["amt_unit_dvs"]         = $param["amt_unit_dvs"];
        $temp["col"]["after_use_yn"]         = $param["after_use_yn"];
        $temp["col"]["cate_sortcode"]        = $param["cate_sortcode"];
        $temp["col"]["tot_tmpt"]             = $param["tot_tmpt"];
        $temp["col"]["side_dvs"]             = $param["side_dvs"];
        $temp["col"]["state"]                = $param["state"];
        $temp["col"]["order_detail_dvs_num"] = $param["order_detail_dvs_num"];
        $temp["col"]["print_tmpt_name"]      = $param["print_tmpt_name"];

        $temp["col"]["tomson_yn"] = $param["tomson_yn"];
        $temp["col"]["typset_way"] = $param["typset_way"];

        return $this->insertData($conn, $temp);
    }

    /**
     * @brief 주문_상세_책자 테이블에 데이터 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertOrderDetailBrochure($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["table"] = "order_detail_brochure";
        $temp["col"]["order_common_seqno"] = $param["order_common_seqno"];

        $temp["col"]["cate_beforeside_print_mpcode"] =
                                            $param["cate_bef_print_mpcode"];
        $temp["col"]["cate_aftside_print_mpcode"] =
                                            $param["cate_aft_print_mpcode"];
        $temp["col"]["cate_beforeside_add_print_mpcode"] =
                                            $param["cate_bef_add_print_mpcode"];
        $temp["col"]["cate_aftside_add_print_mpcode"] =
                                            $param["cate_aft_add_print_mpcode"];

        $temp["col"]["cate_paper_mpcode"] = $param["cate_paper_mpcode"];
        
        $temp["col"]["typ"] = $param["typ"];
        $temp["col"]["page_amt"] = $param["page_amt"];

        $temp["col"]["cut_size_wid"]  = $param["cut_size_wid"];
        $temp["col"]["cut_size_vert"] = $param["cut_size_vert"];
        $temp["col"]["work_size_wid"]  = $param["work_size_wid"];
        $temp["col"]["work_size_vert"] = $param["work_size_vert"];
        $temp["col"]["tomson_size_wid"]  = $param["tomson_size_wid"];
        $temp["col"]["tomson_size_vert"] = $param["tomson_size_vert"];

        $temp["col"]["cut_front_wing_size_wid"] =
                                            $param["cut_front_wing_size_wid"];
        $temp["col"]["cut_front_wing_size_vert"] =
                                            $param["cut_front_wing_size_vert"];
        $temp["col"]["work_front_wing_size_wid"] =
                                            $param["work_front_wing_size_wid"];
        $temp["col"]["work_front_wing_size_vert"] =
                                            $param["work_front_wing_size_vert"];

        $temp["col"]["cut_rear_wing_size_wid"] =
                                             $param["cut_rear_wing_size_wid"];
        $temp["col"]["cut_rear_wing_size_vert"] =
                                             $param["cut_rear_wing_size_vert"];
        $temp["col"]["work_rear_wing_size_wid"] =
                                             $param["work_rear_wing_size_wid"];
        $temp["col"]["work_rear_wing_size_vert"] =
                                             $param["work_rear_wing_size_vert"];

        $temp["col"]["seneca_size"] = $param["seneca_size"];
        $temp["col"]["print_purp_dvs"] = $param["print_purp_dvs"];
        $temp["col"]["del_yn"] = 'N';

        $temp["col"]["spc_dscr"]             = $param["spc_dscr"];
        $temp["col"]["sell_price"]           = $param["sell_price"];
        $temp["col"]["grade_sale_price"]     = $param["grade_sale_price"];
        $temp["col"]["add_after_price"]      = $param["add_after_price"];
        $temp["col"]["cp_price"]             = $param["cp_price"];
        $temp["col"]["pay_price"]            = $param["pay_price"];
        $temp["col"]["order_detail"]         = $param["order_detail"];
        $temp["col"]["mono_yn"]              = $param["mono_yn"];
        $temp["col"]["stan_name"]            = $param["stan_name"];
        $temp["col"]["amt"]                  = $param["amt"];
        $temp["col"]["amt_unit_dvs"]         = $param["amt_unit_dvs"];
        $temp["col"]["expec_weight"]         = $param["expec_weight"];
        $temp["col"]["after_use_yn"]         = $param["after_use_yn"];
        $temp["col"]["cate_sortcode"]        = $param["cate_sortcode"];
        $temp["col"]["tot_tmpt"]             = $param["tot_tmpt"];
        $temp["col"]["side_dvs"]             = $param["side_dvs"];
        $temp["col"]["state"]                = $param["state"];
        $temp["col"]["order_detail_dvs_num"] = $param["order_detail_dvs_num"];
        $temp["col"]["print_tmpt_name"]      = $param["print_tmpt_name"];

        return $this->insertData($conn, $temp);
    }

    /**
     * @brief 주문_옵션_내역 테이블에 데이터 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertOrderOptHistory($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["table"] = "order_opt_history";

        $temp["col"]["order_common_seqno"] = $param["order_common_seqno"];

        $temp["col"]["opt_name"] = $param["opt_name"];
        $temp["col"]["depth1"]   = $param["depth1"];
        $temp["col"]["depth2"]   = $param["depth2"];
        $temp["col"]["depth3"]   = $param["depth3"];

        $temp["col"]["price"] = $param["price"];
        $temp["col"]["basic_yn"] = $param["basic_yn"];

        return $this->insertData($conn, $temp);
    }

    /**
     * @brief 주문_후공정_내역 테이블에 데이터 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertOrderAfterHistory($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["table"] = "order_after_history";

        $temp["col"]["order_detail_dvs_num"] = $param["order_detail_dvs_num"];
        $temp["col"]["order_common_seqno"]   = $param["order_common_seqno"];

        $temp["col"]["after_name"] = $param["after_name"];
        $temp["col"]["depth1"] = $param["depth1"];
        $temp["col"]["depth2"] = $param["depth2"];
        $temp["col"]["depth3"] = $param["depth3"];

        $temp["col"]["price"]    = $param["price"];
        $temp["col"]["basic_yn"] = $param["basic_yn"];
        $temp["col"]["seq"]      = $param["seq"];
        $temp["col"]["detail"]   = $param["detail"];

        return $this->insertData($conn, $temp);
    }

    /**
     * @brief 각 에러단계에 따라서 주문관련 데이터 삭제
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return option html
     */
    function deleteOrderData($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $except_arr = array("table" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = "DELETE";
        $query .= "  FROM %s";
        $query .= " WHERE order_common_seqno = %s";

        $query  = sprintf($query, $param["table"]
                                , $param["order_common_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 주문_후공정_내역 데이터 삭제
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return option html
     */
    function deleteOrderAfterHistory($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $order_detail_dvs_num = $this->arr2paramStr($conn,
                                                    $param["order_detail_dvs_num"]);

        $query  = "DELETE";
        $query .= "  FROM order_after_history";
        $query .= " WHERE order_detail_dvs_num IN (%s)";

        $query  = sprintf($query, $order_detail_dvs_num);

        return $conn->Execute($query);
    }

    /**
     * @brief 주문_상세_건수_파일 데이터 삭제
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return option html
     */
    function deleteOrderDetailCountFile($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $order_detail_seqno = $this->arr2paramStr($conn,
                                                  $param["order_detail_seqno"]);

        $query  = "DELETE";
        $query .= "  FROM order_detail_count_file";
        $query .= " WHERE order_detail_seqno IN (%s)";

        $query  = sprintf($query, $order_detail_seqno);

        return $conn->Execute($query);
    }

    /**
     * @brief 주문공통 데이터 삭제
     *
     * @param $conn  = connection identifier
     * @param $param = 삭제조건 파라미터
     *
     * @return option html
     */
    function deleteOrderCommon($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n DELETE FROM order_common";
        $query .= "\n WHERE order_common_seqno = %s";
        $query .= "\n   AND member_seqno       = %s";

        $query  = sprintf($query, $param["order_common_seqno"]
                                , $param["member_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 인쇄 면구분, 총도수 검색
     *
     * @param $conn   = connection identifier
     * @param $mpcode = 카테고리 인쇄 맵핑코드
     *
     * @return option html
     */
    function selectPrintTmptInfo($conn, $mpcode) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $query  = "\n SELECT  A.side_dvs";
        $query .= "\n        ,A.beforeside_tmpt";
        $query .= "\n        ,A.aftside_tmpt";
        $query .= "\n        ,A.add_tmpt";
        $query .= "\n        ,A.tot_tmpt";
        $query .= "\n   FROM  prdt_print AS A";
        $query .= "\n        ,cate_print AS B";
        $query .= "\n  WHERE  A.prdt_print_seqno = B.prdt_print_seqno";
        $query .= "\n    AND  B.mpcode IN (%s)";

        $query  = sprintf($query, $mpcode);

        $rs = $conn->Execute($query);

        return $rs;
    }

    /**
     * @brief 가격 계산방식이 모두이거나 계산형인 카테고리 검색
     *
     * @param $conn          = connection identifer
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return 검색결과
     */
    function selectCateInfo($conn, $cate_sortcode) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $cate_sortcode = $this->parameterEscape($conn, $cate_sortcode);

        $query  = "\n SELECT  flattyp_yn";
        $query .= "\n        ,tmpt_dvs";
        $query .= "\n        ,typset_way";
        $query .= "\n   FROM  cate";
        $query .= "\n  WHERE  sortcode = %s";

        $query  = sprintf($query, $cate_sortcode);

        $rs = $conn->Execute($query);

        return $rs->fields;
    }

    /**
     * @brief 주문_상세_건수_파일 데이터 입력
     * 건수만큼 주문_상세_일련번호만 채워서 입력함
     *
     * @param $conn  = connection identifer
     * @param $param = 입력값 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertOrderDetailCountFile($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }
        
        $count            = $param["count"];
        $order_detail_num = $param["order_detail_num"];
        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n INSERT INTO order_detail_count_file (";
        $query .= "\n      order_detail_seqno";
        $query .= "\n     ,seq";
        $query .= "\n     ,order_detail_file_num";
        $query .= "\n     ,state";
        $query .= "\n )";
        $query .= "\n VALUES";

        $value_base = "\n (%s, '%s', '%s', %s),";
        $temp = '';
        for ($i = 0; $i < $count; $i++) {
            $seq = str_pad(strval($i + 1), 2, '0', STR_PAD_LEFT);

            $temp .= sprintf($value_base, $param["order_detail_seqno"]
                                        , $seq
                                        , $order_detail_num . $seq
                                        , $param["state"]);
        }

        $query .= substr($temp, 0, -1);

        return $conn->Execute($query);
    }

    /**
     * @brief 카테고리 대분류 별명 검색
     *
     * @param $conn          = connection identifer
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return 검색결과
     */
    function selectCateNick($conn, $cate_sortcode) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $query  = "\n SELECT nick";
        $query .= "\n   FROM cate";
        $query .= "\n  WHERE sortcode   = %s";
        $query .= "\n    AND cate_level = '1'";

        $query  = sprintf($query, $cate_sortcode);

        $rs = $conn->Execute($query);

        return $rs->fields["nick"];
    }

    /**
     * @brief 판매채널 별명 검색
     *
     * @param $conn  = connection identifer
     * @param $seqno = 회사관리일련번호
     *
     * @return 검색결과
     */
    function selectCpnAdminNick($conn, $seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $query  = "\n SELECT nick";
        $query .= "\n   FROM cpn_admin";
        $query .= "\n  WHERE cpn_admin_seqno = %s";

        $query  = sprintf($query, $seqno);

        $rs = $conn->Execute($query);

        return $rs->fields["nick"];
    }

    /**
     * @brief 시퀀스 증가용 마지막 주문번호 검색
     *
     * @param $conn  = connection identifer
     *
     * @return 마지막 숫자
     */
    function selectOrderCommonLastNum($conn) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $today = date("Y-m-d");

        $query  = "\n   SELECT order_num";
        $query .= "\n     FROM order_common";
        $query .= "\n    WHERE '%s 00:00:00' <= order_regi_date";
        $query .= "\n      AND order_regi_date <= '%s 23:59:59'";
        $query .= "\n ORDER BY order_common_seqno DESC";
        $query .= "\n    LIMIT 1";

        $query  = sprintf($query, $today, $today);

        $rs = $conn->Execute($query);

        if ($rs->EOF) {
            $last_num = 1;
        } else {
            $last_num = intval(substr($rs->fields["order_num"], 11)) + 1;
        }

        return $last_num;
    }
}
?>
