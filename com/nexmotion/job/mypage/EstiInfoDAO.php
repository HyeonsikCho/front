<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/estimate_list/EstiListHTML.php');

/**
 * @file EstiInfoDAO.php
 *
 * @brief 마이페이지 - 견적관리 - 견적리스트 DAO
 */

class EstiInfoDAO extends MypageCommonDAO {
    
    function __construct() {
    }
 
    /**
     * @brief 견적리스트 조건검색
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectEstiListCond($conn, $param) {
     
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);

        if ($dvs == "COUNT") {
            $query  = "\n SELECT  COUNT(A.esti_seqno) AS cnt";
        } else {
            $query .= "\n SELECT  A.esti_seqno ";
            $query .= "\n        ,A.req_date ";
            $query .= "\n        ,A.title ";
            $query .= "\n        ,A.paper ";
            $query .= "\n        ,A.print_tmpt ";
            $query .= "\n        ,A.size ";
            $query .= "\n        ,A.after ";
            $query .= "\n        ,A.amt ";
            $query .= "\n        ,A.inq_cont ";
            $query .= "\n        ,A.regi_date ";
            $query .= "\n        ,A.etc ";
            $query .= "\n        ,A.memo ";
            $query .= "\n        ,A.esti_price ";
            $query .= "\n        ,A.supply_price ";
            $query .= "\n        ,A.vat ";
            $query .= "\n        ,A.sale_price ";
            $query .= "\n        ,A.req_date ";
            $query .= "\n        ,A.answ_cont ";
            $query .= "\n        ,A.count ";
            $query .= "\n        ,A.state ";
            $query .= "\n        ,A.expec_order_date ";
        }
        $query .= "\n   FROM  esti AS A ";
        $query .= "\n  WHERE  1=1";

        if ($this->blankParameterCheck($param ,"member_seqno")) {
            $query .= "\n    AND  A.member_seqno     = ";
            $query .= $param["member_seqno"];
        }
        if ($this->blankParameterCheck($param ,"group_seqno")) {
            $query .= "\n    AND  A.group_seqno     = ";
            $query .= $param["group_seqno"];
        }
        if ($this->blankParameterCheck($param ,"state")) {
            $query .= "\n    AND  A.state     = ";
            $query .= $param["state"];
        }
        if ($this->blankParameterCheck($param ,"from")) {
            $val = substr($param["search_cnd"], 1, -1);
            $from = substr($param["from"], 1, -1);
            $query .= "\n    AND  A.$val > '" . $from;
            $query .=" 00:00:00'";
        }
        if ($this->blankParameterCheck($param ,"to")) {
            $val = substr($param["search_cnd"], 1, -1);
            $to = substr($param["to"], 1, -1);
            $query .= "\n    AND  A.$val <= '" . $to;
            $query .=" 23:59:59'";
        }
        if ($this->blankParameterCheck($param ,"title")) {
            $title = substr($param["title"], 1, -1);
            $query .= "\n    AND  A.title LIKE '%" . $title . "%'";
        }
        
        $query .= "\n ORDER BY esti_seqno DESC ";

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if (!$dvs) { 
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;
        }

        return $conn->Execute($query);
    }
  
    /**
     * @brief 견적리스트 상세보기
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectEstiView($conn, $param) {
     
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
            
        $query .= "\n SELECT  A.esti_seqno ";
        $query .= "\n        ,A.req_date ";
        $query .= "\n        ,A.title ";
        $query .= "\n        ,A.paper ";
        $query .= "\n        ,A.print_tmpt ";
        $query .= "\n        ,A.size ";
        $query .= "\n        ,A.after ";
        $query .= "\n        ,A.amt ";
        $query .= "\n        ,A.inq_cont ";
        $query .= "\n        ,A.regi_date ";
        $query .= "\n        ,A.etc ";
        $query .= "\n        ,A.memo ";
        $query .= "\n        ,A.esti_price ";
        $query .= "\n        ,A.supply_price ";
        $query .= "\n        ,A.vat ";
        $query .= "\n        ,A.sale_price ";
        $query .= "\n        ,A.req_date ";
        $query .= "\n        ,A.answ_cont ";
        $query .= "\n        ,A.count ";
        $query .= "\n        ,A.state ";
        $query .= "\n        ,A.expec_order_date ";
        $query .= "\n        ,B.esti_file_seqno as user_file_seq ";
        $query .= "\n        ,B.file_path as user_file_path ";
        $query .= "\n        ,B.save_file_name as user_save_file_name ";
        $query .= "\n        ,B.origin_file_name as user_origin_file_name ";
        $query .= "\n        ,B.size as user_file_size ";
        $query .= "\n        ,C.admin_esti_file_seqno ";
        $query .= "\n        ,C.file_path as admin_file_path ";
        $query .= "\n        ,C.save_file_name as admin_save_file_name ";
        $query .= "\n        ,C.origin_file_name as admin_origin_file_name ";
        $query .= "\n  FROM  esti A ";
        $query .= "\n        LEFT JOIN esti_file B";
        $query .= "\n        ON A.esti_seqno = B.esti_seqno";
        $query .= "\n        LEFT JOIN admin_esti_file C";
        $query .= "\n        ON A.esti_seqno = C.esti_seqno";

        if ($this->blankParameterCheck($param ,"esti_seqno")) {
            $query .= "\n    WHERE A.esti_seqno = ";
            $query .= $param["esti_seqno"];
        }

        return $conn->Execute($query);
    }
 
    /**
     * @brief 견적리스트 삭제
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function deleteEstiList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $seqno = substr($param["esti_seqno"], 1, -1);
 
        $query  = "\n DELETE ";
        $query .= "\n   FROM esti ";
        $query .= "\n  WHERE esti_seqno in ($seqno)";

        $resultSet = $conn->Execute($query);

        if ($resultSet === FALSE) {
            $errorMessage = "데이터 삭제에 실패 하였습니다.";
            return false;
        } else {
            return true;
        } 
    }

    /**
     * @brief 인쇄정보 카테고리 중분류 
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCate($conn) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }
 
        $query  = "\n SELECT DISTINCT B.cate_name ";
        $query .= "\n       ,A.cate_sortcode ";
        $query .= "\n   FROM prdt_print_info AS A";
        $query .= "\n       ,cate AS B";
        $query .= "\n  WHERE A.cate_sortcode = B.sortcode";

        return $conn->Execute($query);
    }

    /**
     * @brief 견적 수정
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateEstiList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
 
        $query  = "\nUPDATE  esti ";
        $query .= "\n   SET  memo = %s ";
        $query .= "\n       ,answ_cont = %s ";
        $query .= "\n       ,supply_price = %s ";
        $query .= "\n       ,vat = %s ";
        $query .= "\n       ,sale_price = %s ";
        $query .= "\n       ,esti_price = %s ";
        $query .= "\n       ,expec_order_date = %s ";
        $query .= "\n       ,state = %s ";
        $query .= "\n WHERE  esti_seqno = %s ";

        $query = sprintf($query, $param["memo"],
                         $param["answ_cont"],
                         $param["supply_price"],
                         $param["vat"],
                         $param["sale_price"],
                         $param["esti_price"],
                         $param["expec_order_date"],
                         $param["state"],
                         $param["esti_seqno"]);

        $resultSet = $conn->Execute($query);

        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        } 
    }

    /**
     * @brief 견적 상태 수정
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateEstiState($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
 
        $query  = "\nUPDATE  esti ";
        $query .= "\n   SET  state = %s ";
        $query .= "\n WHERE  esti_seqno = %s ";

        $query = sprintf($query, $param["state"],
                         $param["esti_seqno"]);

        $resultSet = $conn->Execute($query);

        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        } 
    }

    /**
     * @brief 견적요청
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertEsti($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
 
        $query  = "\nINSERT INTO  esti ( ";
        $query .= "\n         title ";
        $query .= "\n       , inq_cont ";
        $query .= "\n       , state ";
        $query .= "\n       , regi_date ";
        $query .= "\n       , req_date ";
        $query .= "\n       , member_seqno ";
        $query .= "\n       , group_seqno ";
        $query .= "\n) VALUES ( ";
        $query .= "\n         %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n) ";

        $query = sprintf( $query
                        , $param["title"]
                        , $param["inq_cont"]
                        , $param["state"]
                        , 'SYSDATE()'
                        , 'SYSDATE()'
                        , $param["member_seqno"]
                        , $param["group_seqno"]);

        $resultSet = $conn->Execute($query);

        if ($resultSet === FALSE) {
            return false;
        } else {
            return $conn->Insert_ID("esti", "esti_seqno");
        } 
    }


    /**
     * @brief 견적문의시 첨부파일 설정
     *
     * @param $conn  = connection identifier
     * @param $param = 파라미터
     *
     * @return 검색결과
     */
    function updateEstiFile($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
 
        $query  = "\nUPDATE  esti_file ";
        $query .= "\n   SET  esti_seqno= %s ";
        $query .= "\n WHERE  esti_file_seqno = %s ";

        $query = sprintf($query, $param["esti_seqno"],
                         $param["esti_file_seqno"]);

        $resultSet = $conn->Execute($query);

        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        } 
    }

    /**
     * @brief 사용자 견적 파일 리스트 
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectEstiFileList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }
 
        $query .= "\n SELECT  esti_file_seqno";
        $query .= "\n        ,file_path";
        $query .= "\n        ,save_file_name";
        $query .= "\n        ,origin_file_name";
        $query .= "\n        ,size";
        $query .= "\n        ,esti_seqno";
        $query .= "\n   FROM  esti_file";
        $query .= "\n  WHERE  esti_file_seqno = %s";

        $query = sprintf($query, $param["esti_file_seqno"]);

        return $conn->Execute($query);
    }
}
?>
