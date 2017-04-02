<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/MemberDlvrHTML.php');

class MemberDlvrDAO extends MypageCommonDAO {
 
    /**
     * @brief 배송지 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectDlvrList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);

        if ($type === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n    SELECT  member_dlvr_seqno ";
            $query .= "\n           ,dlvr_name ";
            $query .= "\n           ,regi_date ";
            $query .= "\n           ,recei ";
            $query .= "\n           ,tel_num ";
            $query .= "\n           ,cell_num ";
            $query .= "\n           ,ifnull(cell_num, tel_num) tel ";
            $query .= "\n           ,zipcode ";
            $query .= "\n           ,addr ";
            $query .= "\n           ,addr_detail ";
            $query .= "\n           ,member_seqno ";
            $query .= "\n           ,basic_yn ";
        }
        $query .= "\n      FROM  member_dlvr ";
        $query .= "\n     WHERE  member_seqno = " . $param["seqno"];

        //구분
        if ($this->blankParameterCheck($param ,"category")
                    && $this->blankParameterCheck($param ,"searchkey")) {
            $key = substr($param["category"], 1, -1);
            $search = substr($param["searchkey"], 1, -1);
            $query .= "\n    AND " . $key . " like '%" . $search . "%'";
        }

        if ($this->blankParameterCheck($param ,"seq")) {
            $query .="\n   AND  member_dlvr_seqno = $param[seq] ";
        }

        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .="\n   AND  regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {
            $to = substr($param["to"], 1, -1);
            $query .="\n   AND  regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($type == "SEQ") {
            $query .= "\n ORDER BY member_dlvr_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;
        }

        return $conn->Execute($query);
    }

   /**
     * @brief 배송유형
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMemberDlvrDvs($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    SELECT  dlvr_dvs ";
        $query .= "\n           ,dlvr_code ";
        $query .= "\n      FROM  member";
        $query .= "\n     WHERE  member_seqno = " . $param["seqno"];

        return $conn->Execute($query);
    }


   /**
     * @brief 기본배송지
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectBasicDlvr($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    SELECT  member_dlvr_seqno ";
        $query .= "\n           ,dlvr_name ";
        $query .= "\n           ,regi_date ";
        $query .= "\n           ,recei ";
        $query .= "\n           ,tel_num ";
        $query .= "\n           ,ifnull(cell_num, tel_num) tel ";
        $query .= "\n           ,zipcode ";
        $query .= "\n           ,addr ";
        $query .= "\n           ,addr_detail ";
        $query .= "\n           ,member_seqno ";
        $query .= "\n           ,basic_yn ";
        $query .= "\n      FROM  member_dlvr";
        $query .= "\n     WHERE  member_seqno = " . $param["seqno"];
        $query .= "\n       AND  basic_yn = 'Y'";

        return $conn->Execute($query);
    }

    /**
     * @brief 나의배송지 신규등록
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertDlvr($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
 
        $query  = "\nINSERT INTO member_dlvr ( ";
        $query .= "\n         dlvr_name ";
        $query .= "\n       , recei ";
        $query .= "\n       , tel_num ";
        $query .= "\n       , cell_num ";
        $query .= "\n       , zipcode ";
        $query .= "\n       , addr ";
        $query .= "\n       , addr_detail ";
        $query .= "\n       , basic_yn ";
        $query .= "\n       , regi_date ";
        $query .= "\n       , member_seqno ";
        $query .= "\n) VALUES ( ";
        $query .= "\n         %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , 'N' ";
        $query .= "\n       , SYSDATE() ";
        $query .= "\n       , %s ";
        $query .= "\n) ";
            
        $query = sprintf( $query
                        , $param["dlvr_name"]
                        , $param["recei"]
                        , $param["tel_num"]
                        , $param["cell_num"]
                        , $param["zipcode"]
                        , $param["addr"]
                        , $param["addr_detail"]
                        , $param["member_seqno"]);

        $resultSet = $conn->Execute($query);

        if ($resultSet === FALSE) {
            return false;
        } else {
            return $conn->Insert_ID("admin_licenseeregi", "admin_licenseeregi_seqno");
        } 
    }


    /**
     * @brief 나의배송지 수정
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateDlvr($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
 
        $query  = "\nUPDATE member_dlvr set ";
        $query .= "\n         dlvr_name = %s ";
        $query .= "\n       , recei = %s ";
        $query .= "\n       , tel_num = %s ";
        $query .= "\n       , cell_num = %s ";
        $query .= "\n       , zipcode = %s ";
        $query .= "\n       , addr = %s ";
        $query .= "\n       , addr_detail = %s ";
        $query .= "\nWHERE member_dlvr_seqno = %s ";
            
        $query = sprintf( $query
                        , $param["dlvr_name"]
                        , $param["recei"]
                        , $param["tel_num"]
                        , $param["cell_num"]
                        , $param["zipcode"]
                        , $param["addr"]
                        , $param["addr_detail"]
                        , $param["member_dlvr_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 나의 배송지 삭제 
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function deleteDlvr($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $seqnoes = substr($param["member_dlvr_seqno"], 1, -1);
 
        $query  = "\nDELETE FROM member_dlvr ";
        $query .= "\nWHERE member_dlvr_seqno in ( %s )";
            
        $query = sprintf( $query
                        , $seqnoes);

        return $conn->Execute($query);
    }


    /**
     * @brief 기본배송지 수정
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateBasicDlvr($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $preQuery  = "\nUPDATE member_dlvr set ";
        $preQuery .= "\n       basic_yn = 'N' ";
        $preQuery .= "\nWHERE member_seqno = %s ";
    
        $preQuery = sprintf( $preQuery
                            , $param["member_seqno"]);

        if ($conn->Execute($preQuery)) {
            $query  = "\nUPDATE member_dlvr set ";
            $query .= "\n         basic_yn = 'Y' ";
            $query .= "\nWHERE member_dlvr_seqno = %s ";
                
            $query = sprintf( $query
                            , $param["member_dlvr_seqno"]);
        
            return $conn->Execute($query);
        } 
        
        return false;
    }

    /*
     * 모든 메인 업체 리스트 보기
     * $conn : DB Connection
     * return : resultSet 
     */ 
    function selectDlvrMainList($conn, $param) {

        if (!$this->connectionCheck($conn)) return false; 
        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    SELECT  A.addr";
        $query .= "\n           ,A.addr_detail";
        $query .= "\n           ,A.tel_num";
        $query .= "\n           ,B.office_nick";  
        $query .= "\n           ,B.member_seqno";  
        $query .= "\n           ,C.dlvr_friend_main_seqno";  
        $query .= "\n      FROM  member_dlvr A";
        $query .= "\n           ,member B";
        $query .= "\n           ,dlvr_friend_main C";
        $query .= "\n     WHERE  A.member_seqno = B.member_seqno";
        $query .= "\n       AND  B.member_seqno = C.member_seqno";
        $query .= "\n       AND  A.basic_yn = 'Y'";
        $query .= "\n       AND  B.dlvr_friend_yn = 'Y'";
        $query .= "\n       AND  B.dlvr_friend_main = 'Y'";
        $query .= "\n       AND  C.state = '2'";

        //업체명 검색
        if ($this->blankParameterCheck($param, "search_txt")) {
            $query .= "\n       AND B.office_nick LIKE '%" . substr($param["search_txt"], 1,-1) . "%'";
        }

        if ($this->blankParameterCheck($param ,"sort") && $this->blankParameterCheck($param,"sort_type")) {
    
            $param["sort"] = substr($param["sort"], 1, -1);
            $param["sort_type"] = substr($param["sort_type"], 1, -1); 
            
            if ($param["sort"] == "member_name") {

                $query .= "\n  ORDER BY  B.member_name " . $param["sort_type"];

            } else if ($param["sort"] == "addr") {

                $query .= "\n  ORDER BY  A.addr " . $param["sort_type"];
            }
        }

        $result = $conn->Execute($query);
        return $result;
    }

    /**
     * @brief 회원배송친구 등록
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateMemberDlvrFriend($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
 
        $query  = "\nUPDATE member set ";
        $query .= "\n       dlvr_friend_main = %s";
        $query .= "\n WHERE member_seqno = %s ";
            
        $query = sprintf( $query
                        , $param["dlvr_friend_main"]
                        , $param["member_seqno"]);

        return $conn->Execute($query);
    }


}
?>
