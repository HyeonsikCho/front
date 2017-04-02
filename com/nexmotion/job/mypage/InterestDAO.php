<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/OrderCommonDAO.php');

class InterestDAO extends OrderCommonDAO {
    function __construct() {
    }

    /**
     * @brief 각 에러단계에 따라서 관심상품 데이터 삭제
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return option html
     */
    function deleteInterestPrdtData($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $except_arr = array("table" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = "DELETE";
        $query .= "  FROM %s";
        $query .= " WHERE interest_prdt_seqno = %s";

        $query  = sprintf($query, $param["table"]
                                , $param["interest_prdt_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 관심_상품_후공정_내역 데이터 삭제
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return option html
     */
    function deleteInterestPrdtAfterHistory($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $interest_prdt_detail_dvs_num = $this->arr2paramStr($conn,
                                        $param["interest_prdt_detail_dvs_num"]);

        $query  = "DELETE";
        $query .= "  FROM interest_prdt_after_history";
        $query .= " WHERE interest_prdt_detail_dvs_num IN (%s)";

        $query  = sprintf($query, $interest_prdt_detail_dvs_num);

        return $conn->Execute($query);
    }

    /**
     * @brief 관심_상품 테이블에 데이터 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertInterestPrdt($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = " \n INSERT INTO interest_prdt (";
        $query .= " \n      regi_date";
        $query .= " \n     ,member_seqno";
        $query .= " \n     ,cpn_admin_seqno";
        $query .= " \n     ,order_detail";
        $query .= " \n     ,mono_yn";
        $query .= " \n     ,title";
        $query .= " \n     ,expec_weight";
        $query .= " \n     ,cate_sortcode";
        $query .= " \n     ,opt_use_yn";
        $query .= " \n     ,amt";
        $query .= " \n     ,amt_unit_dvs";
        $query .= " \n     ,count";
        $query .= " \n     ,page_cnt";
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
        $query .= " \n )";

        $query  = sprintf($query, $param["member_seqno"]
                                , $param["cpn_admin_seqno"]
                                , $param["order_detail"]
                                , $param["mono_yn"]
                                , $param["title"]
                                , $param["expec_weight"]
                                , $param["cate_sortcode"]
                                , $param["opt_use_yn"]
                                , $param["amt"]
                                , $param["amt_unit_dvs"]
                                , $param["count"]
                                , $param["page_cnt"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 관심_상품_상세 테이블에 데이터 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertInterestPrdtDetail($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["table"] = "interest_prdt_detail";
        $temp["col"]["interest_prdt_seqno"] = $param["interest_prdt_seqno"];

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

        $temp["col"]["spc_dscr"]             = $param["spc_dscr"];
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
        $temp["col"]["print_tmpt_name"]      = $param["print_tmpt_name"];
        $temp["col"]["interest_prdt_detail_dvs_num"] =
                                         $param["interest_prdt_detail_dvs_num"];
        $temp["col"]["side_dvs"]             = $param["side_dvs"];
        $temp["col"]["tomson_yn"]            = $param["tomson_yn"];

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
    function insertInterestPrdtDetailBrochure($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["table"] = "interest_prdt_detail_brochure";
        $temp["col"]["interest_prdt_seqno"] = $param["interest_prdt_seqno"];

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

        $temp["col"]["spc_dscr"]             = $param["spc_dscr"];
        $temp["col"]["order_detail"]         = $param["order_detail"];
        $temp["col"]["mono_yn"]              = $param["mono_yn"];
        $temp["col"]["stan_name"]            = $param["stan_name"];
        $temp["col"]["amt"]                  = $param["amt"];
        $temp["col"]["amt_unit_dvs"]         = $param["amt_unit_dvs"];
        $temp["col"]["after_use_yn"]         = $param["after_use_yn"];
        $temp["col"]["cate_sortcode"]        = $param["cate_sortcode"];
        $temp["col"]["tot_tmpt"]             = $param["tot_tmpt"];
        $temp["col"]["print_tmpt_name"]      = $param["print_tmpt_name"];
        $temp["col"]["interest_prdt_detail_dvs_num"] =
                                         $param["interest_prdt_detail_dvs_num"];
        $temp["col"]["side_dvs"]             = $param["side_dvs"];

        return $this->insertData($conn, $temp);
    }

    /**
     * @brief 관심_상품_후공정_내역 테이블에 데이터 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertInterestPrdtAfterHistory($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["table"] = "interest_prdt_after_history";

        $temp["col"]["interest_prdt_detail_dvs_num"] =
                                         $param["interest_prdt_detail_dvs_num"];

        $temp["col"]["after_name"] = $param["after_name"];
        $temp["col"]["depth1"] = $param["depth1"];
        $temp["col"]["depth2"] = $param["depth2"];
        $temp["col"]["depth3"] = $param["depth3"];

        $temp["col"]["basic_yn"] = $param["basic_yn"];
        $temp["col"]["seq"]      = $param["seq"];
        $temp["col"]["detail"]   = $param["detail"];

        return $this->insertData($conn, $temp);
    }

    /**
     * @brief 관심_상품_옵션_내역 테이블에 데이터 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertInterestPrdtOptHistory($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["table"] = "interest_prdt_opt_history";

        $temp["col"]["interest_prdt_seqno"] = $param["interest_prdt_seqno"];

        $temp["col"]["opt_name"] = $param["opt_name"];
        $temp["col"]["depth1"] = $param["depth1"];
        $temp["col"]["depth2"] = $param["depth2"];
        $temp["col"]["depth3"] = $param["depth3"];

        $temp["col"]["basic_yn"] = $param["basic_yn"];
        $temp["col"]["detail"]   = $param["detail"];

        return $this->insertData($conn, $temp);
    }

    /**
     * @brief 관심상품 테이블 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return option html
    function selectWishList($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);

        if ($dvs === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n SELECT  A.regi_date";
            $query .= "\n        ,A.interest_prdt_seqno";
            $query .= "\n        ,A.title";
            $query .= "\n        ,A.order_detail";
            $query .= "\n        ,A.amt";
            $query .= "\n        ,A.count";
            $query .= "\n        ,A.expec_weight";
            $query .= "\n        ,A.cate_sortcode";
        }    
        $query .= "\n   FROM  interest_prdt A";
        $query .= "\n       , cate B";
        $query .= "\n  WHERE  A.member_seqno = " . $param["member_seqno"];
        $query .= "\n    AND  A.cate_sortcode = B.sortcode";
        
        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .="\n     AND  A.regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {
            $to = substr($param["to"], 1, -1);
            $query .="\n     AND  A.regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        //상품정보
        if ($this->blankParameterCheck($param ,"order_detail")) {
            $query .="\n     AND  A.order_detail like '%".$param["order_detail"]."%'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($dvs == "SEQ") {
            $query .= "\n ORDER BY interest_prdt_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;
        }

        $rs = $conn->Execute($query);

        return $rs;
    }
     */

    /**
     * @brief 기입력된 관심상품 있는지 체크
     *
     * @param $conn = db 커넥션
     * @param $param = 검색조건 파라미터
     *
     * @return 결과가 없으면 false, 있으면 true
     */
    function selectInterestPrdtCate($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  1";
        $query .= "\n   FROM  interest_prdt A";
        $query .= "\n  WHERE  A.cate_sortcode = " . $param["cate_sortcode"];
        $query .= "\n    AND  A.member_seqno  = " . $param["member_seqno"];
        $query .= "\n  LIMIT  1";

        $rs = $conn->Execute($query);

        return $rs->EOF ? false : true;
    }
}
?>
