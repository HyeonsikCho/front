<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/ProductCommonDAO.php');

class ProductEstiDAO extends ProductCommonDAO {
    function __construct() {
    }

    /**
     * @brief 견적_파일 정보 입력
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertEstiFile($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["table"]                     = "order_file";
        $temp["col"]["dvs"]                = $param["dvs"];
        $temp["col"]["file_path"]          = $param["file_path"];
        $temp["col"]["save_file_name"]     = $param["save_file_name"];
        $temp["col"]["origin_file_name"]   = $param["origin_file_name"];
        $temp["col"]["size"]               = $param["size"];
        $temp["col"]["order_common_seqno"] = $param["order_common_seqno"];
        $temp["col"]["member_seqno"]       = $param["member_seqno"];

        return $this->insertData($conn, $temp);
    }

    /**
     * @brief 견적_상세 테이블에 데이터 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertEstiDetail($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["table"] = "esti_detail";
        $temp["col"]["order_common_seqno"] = $param["order_common_seqno"];

        $temp["col"]["paper_info"]            = $param["paper_info"];
        $temp["col"]["beforeside_print_info"] = $param["beforeside_print_info"];
        $temp["col"]["aftside_print_info"]    = $param["aftside_print_info"];
        $temp["col"]["print_purp_info"]       = $param["print_purp_info"];
        $temp["col"]["stan_info"]             = $param["stan_info"];
        $temp["col"]["amt_info"]              = $param["amt_info"];
        $temp["col"]["work_size_wid"]         = $param["work_size_wid"];
        $temp["col"]["work_size_vert"]        = $param["work_size_vert"];
        $temp["col"]["cut_size_wid"]          = $param["cut_size_wid"];
        $temp["col"]["cut_size_vert"]         = $param["cut_size_vert"];
        $temp["col"]["count"]                 = $param["count"];
        $temp["col"]["esti_detail_dvs_num"]   = $param["esti_detail_dvs_num"];

        return $this->insertData($conn, $temp);
    }

    /**
     * @brief 견적_상세 테이블에 데이터 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 데이터 파라미터
     *
     * @return 쿼리실행결과
     */
    function insertEstiDetailBrochure($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["table"] = "esti_detail_brochure";
        $temp["col"]["order_common_seqno"] = $param["order_common_seqno"];

        $temp["col"]["paper_info"]            = $param["paper_info"];
        $temp["col"]["beforeside_print_info"] = $param["beforeside_print_info"];
        $temp["col"]["aftside_print_info"]    = $param["aftside_print_info"];
        $temp["col"]["print_purp_info"]       = $param["print_purp_info"];
        $temp["col"]["stan_info"]             = $param["stan_info"];
        $temp["col"]["amt_info"]              = $param["amt_info"];
        $temp["col"]["work_size_wid"]         = $param["work_size_wid"];
        $temp["col"]["work_size_vert"]        = $param["work_size_vert"];
        $temp["col"]["cut_size_wid"]          = $param["cut_size_wid"];
        $temp["col"]["cut_size_vert"]         = $param["cut_size_vert"];
        $temp["col"]["page_info"]             = $param["page_info"];
        $temp["col"]["esti_detail_dvs_num"]   = $param["esti_detail_dvs_num"];

        return $this->insertData($conn, $temp);
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
        $query .= "\n      esti_detail_seqno";
        $query .= "\n     ,seq";
        $query .= "\n     ,order_detail_file_num";
        $query .= "\n     ,state";
        $query .= "\n )";
        $query .= "\n VALUES";

        $value_base = "\n (%s, '%s', '%s', %s),";
        $temp = '';
        for ($i = 0; $i < $count; $i++) {
            $seq = str_pad(strval($i + 1), 2, '0', STR_PAD_LEFT);

            $temp .= sprintf($value_base, $param["esti_detail_seqno"]
                                        , $seq
                                        , $order_detail_num . $seq
                                        , $param["state"]);
        }

        $query .= substr($temp, 0, -1);

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

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n UPDATE  order_common AS A";
        $query .= "\n    SET  A.oper_sys        = %s";
        $query .= "\n        ,A.order_regi_date = now()";
        $query .= "\n  WHERE  A.order_common_seqno = %s";

        $query  = sprintf($query, $param["oper_sys"]
                                , $param["order_common_seqno"]);

        return $conn->Execute($query);
    }
}
?>
