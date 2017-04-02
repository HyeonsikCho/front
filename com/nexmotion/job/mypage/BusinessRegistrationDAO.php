<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/MemberCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/html/mypage/BusinessRegistrationList.php");

/**
 * @file BusinessRegistrationDAO.php
 *
 * @brief My페이지 > 관리사업자등록증 DAO
 */
class BusinessRegistrationDAO extends MemberCommonDAO {

    function __construct() {
    }

    /**
     * @brief My페이지 > 관리사업자등록증 DAO
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectBusinessRegistrationList($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);

        if ($dvs == "COUNT") {
            $query  = "\nSELECT  COUNT(*) AS cnt ";
        } else {
            $query  = "\nSELECT  admin_licenseeregi_seqno ";
            $query .= "\n       ,crn ";
            $query .= "\n       ,corp_name ";
            $query .= "\n       ,repre_name ";
            $query .= "\n       ,bc ";
            $query .= "\n       ,tob ";
            $query .= "\n       ,tel_num ";
            $query .= "\n       ,zipcode ";
            $query .= "\n       ,addr ";
            $query .= "\n       ,addr_detail ";
            $query .= "\n       ,mng_name ";
            $query .= "\n       ,posi ";
            $query .= "\n       ,mail ";
            $query .= "\n       ,member_seqno ";
        }
        $query .= "\n  FROM  admin_licenseeregi ";
        $query .= "\n WHERE  1 = 1 ";

        //일련번호로 변형 해서 검색
        if ($this->blankParameterCheck($param ,"member_seqno")) {
            $query .="\n    AND  member_seqno = $param[member_seqno] ";
        }
 
        if ($this->blankParameterCheck($param ,"admin_licenseeregi_seqno")) {
            $query .="\n    AND  admin_licenseeregi_seqno = $param[admin_licenseeregi_seqno] ";
        }
       
        $query .="\n ORDER BY admin_licenseeregi_seqno DESC ";

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($dvs == "SEQ") { 
            $query .= "\nLIMIT ". $s_num . ", " . $list_num;
        }

        return $conn->Execute($query);
    }

    /**
     * @brief 문의내용
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOtoInqCont($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\nSELECT  A.title ";
        $query .= "\n       ,A.inq_typ ";
        $query .= "\n       ,B.member_name ";
        $query .= "\n       ,B.office_nick ";
        $query .= "\n       ,A.tel_num ";
        $query .= "\n       ,A.cell_num ";
        $query .= "\n       ,A.mail ";
        $query .= "\n       ,A.cont ";
        $query .= "\n       ,A.answ_yn ";
        $query .= "\n       ,A.answ_mail_yn ";
        $query .= "\n  FROM  oto_inq AS A ";
        $query .= "\n       ,member AS B ";
        $query .= "\n WHERE  A.member_seqno = B.member_seqno ";

        //일련번호
        if ($this->blankParameterCheck($param ,"oto_inq_seqno")) {
            $query .="\n    AND A.oto_inq_seqno = $param[oto_inq_seqno] ";
        }

        return $conn->Execute($query);
    }

    /**
     * @brief 관리사업자등록증 신규등록
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertRegistration($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
 
        $query  = "\nINSERT INTO admin_licenseeregi ( ";
        $query .= "\n         crn ";
        $query .= "\n       , corp_name ";
        $query .= "\n       , repre_name ";
        $query .= "\n       , bc ";
        $query .= "\n       , tob ";
        $query .= "\n       , tel_num ";
        $query .= "\n       , zipcode ";
        $query .= "\n       , addr ";
        $query .= "\n       , addr_detail ";
        $query .= "\n       , mng_name ";
        $query .= "\n       , posi ";
        $query .= "\n       , mail ";
        $query .= "\n       , member_seqno ";
        $query .= "\n) VALUES ( ";
        $query .= "\n         %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n) ";
            
        $query = sprintf( $query
                        , $param["crn"]
                        , $param["corp_name"]
                        , $param["repre_name"]
                        , $param["bc"]
                        , $param["tob"]
                        , $param["tel_num"]
                        , $param["zipcode"]
                        , $param["addr"]
                        , $param["addr_detail"]
                        , $param["mng_name"]
                        , $param["posi"]
                        , $param["mail"]
                        , $param["member_seqno"]);

        $resultSet = $conn->Execute($query);

        if ($resultSet === FALSE) {
            return false;
        } else {
            return $conn->Insert_ID("admin_licenseeregi", "admin_licenseeregi_seqno");
        } 
    }


    /**
     * @brief 관리사업자등록증 수정
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateRegistration($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
 
        $query  = "\nUPDATE admin_licenseeregi set ";
        $query .= "\n         crn = %s ";
        $query .= "\n       , corp_name = %s ";
        $query .= "\n       , repre_name  = %s ";
        $query .= "\n       , bc = %s ";
        $query .= "\n       , tob = %s ";
        $query .= "\n       , tel_num = %s ";
        $query .= "\n       , zipcode = %s ";
        $query .= "\n       , addr = %s ";
        $query .= "\n       , addr_detail = %s ";
        $query .= "\n       , mng_name = %s ";
        $query .= "\n       , posi = %s ";
        $query .= "\n       , mail = %s ";
        $query .= "\nWHERE admin_licenseeregi_seqno = %s ";
            
        $query = sprintf( $query
                        , $param["crn"]
                        , $param["corp_name"]
                        , $param["repre_name"]
                        , $param["bc"]
                        , $param["tob"]
                        , $param["tel_num"]
                        , $param["zipcode"]
                        , $param["addr"]
                        , $param["addr_detail"]
                        , $param["mng_name"]
                        , $param["posi"]
                        , $param["mail"]
                        , $param["admin_licenseeregi_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 관리사업자등록증 삭제 
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function deleteRegistration($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
 
        $query  = "\nDELETE FROM admin_licenseeregi ";
        $query .= "\nWHERE admin_licenseeregi_seqno = %s ";
            
        $query = sprintf( $query
                        , $param["admin_licenseeregi_seqno"]);

        return $conn->Execute($query);
    }
}
