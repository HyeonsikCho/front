<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/CscenterCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/cscenter/ContactHtml.php');

class ContactDAO extends CscenterCommonDAO {
 
    /**
     * @brief 각부서연락처 > 부서조회
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectContactPart($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    SELECT  depar_admin_seqno ";
        $query .= "\n           ,high_depar_code ";
        $query .= "\n           ,depar_code ";
        $query .= "\n           ,depar_name ";
        $query .= "\n           ,eng_name ";
        $query .= "\n           ,eng_abb ";
        $query .= "\n           ,depar_mail ";
        $query .= "\n           ,expo_yn ";
        $query .= "\n           ,depar_level ";
        $query .= "\n           ,cpn_admin_seqno ";
        $query .= "\n      FROM  depar_admin ";
//        $query .= "\n     WHERE  depar_level <> 1";
        $query .= "\n     WHERE  1=1";
        $query .= "\n       AND  expo_yn = 'Y'";

        //일련번호 검색
        if ($this->blankParameterCheck($param ,"cpn_admin_seqno")) {
            $query .= "\n   AND cpn_admin_seqno = " . $param["cpn_admin_seqno"];
        }
 
        $query .= "\n ORDER BY depar_code ASC ";

        return $conn->Execute($query);
    }
 
    /**
     * @brief 각부서별 팀원조회
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectContactPartMember($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    SELECT  A.empl_seqno ";
        $query .= "\n           ,A.name ";
        $query .= "\n           ,A.tel_num ";
        $query .= "\n           ,A.photo_path ";
        $query .= "\n           ,B.posi_name ";
        $query .= "\n           ,ifnull(C.sex,1) sex ";
        $query .= "\n      FROM  empl A LEFT JOIN posi_admin B ";
        $query .= "\n           ON A.posi_code = B.posi_code ";
        $query .= "\n           LEFT JOIN empl_human_info C ";
        $query .= "\n           ON A.empl_seqno = C.empl_seqno ";

        if ($this->blankParameterCheck($param ,"depar_code")) {
            $query .= "\n   WHERE depar_code = " . $param["depar_code"];
        }
 
        $query .= "\n ORDER BY B.posi_code ASC, C.sex ASC ";

        return $conn->Execute($query);
    }


}
