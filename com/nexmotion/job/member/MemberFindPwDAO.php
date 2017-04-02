<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MemberCommonDAO.php');

class MemberFindPwDAO extends MemberCommonDAO {
    function __construct() {
    }
 
    /**
     * @brief 비밀번호 찾기
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectFindPw($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $search_cnd = substr($param["search_cnd"], 1, -1);

        $query  = "\nSELECT  member_seqno ";
        $query .= "\n  FROM  member ";
        $query .= "\n WHERE  member_name = $param[member_name] ";
        $query .= "\n   AND  member_id = $param[member_id] ";
        $query .= "\n   AND  $search_cnd = $param[search_txt]";

        return $conn->Execute($query);
    }

    /**
     * @brief 비밀번호 찾기 결과 정보
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectFindPwInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $search_cnd = substr($param["search_cnd"], 1, -1);

        $query  = "\nSELECT  member_id ";
        $query .= "\n  FROM  member ";
        $query .= "\n WHERE  1 = 1 ";

        //일련번호 검색
        if ($this->blankParameterCheck($param ,"seqno")) {
            $query .= "\n   AND  member_seqno = $param[seqno]";
        }
 
        return $conn->Execute($query);
    }
}
?>
