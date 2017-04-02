<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MemberCommonDAO.php');

class MemberJoinDAO extends MemberCommonDAO {
    function __construct() {
    }

    /**
     * @brief 회원 아이디 중복 검사
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectIdOverCheck($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = "\nSELECT  COUNT(member_id) AS cnt";
        $query .= "\n  FROM  member ";
        $query .= "\n WHERE  member_id = " . $param["member_id"];
        $query .= "\n   AND  cpn_admin_seqno = " . $param["sell_site"];

        return $conn->Execute($query);
    } 

    /**
     * @brief 가상계좌 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectVirtBa($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = "\nSELECT  virt_ba_admin_seqno";
        $query .= "\n  FROM  virt_ba_admin ";
        $query .= "\n WHERE  bank_name = " . $param["bank_name"];
        $query .= "\n   AND  state = 'N'";

        return $conn->Execute($query);
    } 

    /**
     * @brief 회원정보 등록
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertMemberInfo($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param["first_join_date"] = date("Y-m-d H:i:s");

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = " \n INSERT INTO member(";
        $query .= " \n      cpn_admin_seqno";
        $query .= " \n     ,member_id";
        $query .= " \n     ,member_name";
        $query .= " \n     ,office_nick";
        $query .= " \n     ,passwd";
        $query .= " \n     ,zipcode";
        $query .= " \n     ,addr";
        $query .= " \n     ,addr_detail";
        $query .= " \n     ,mailing_yn";
        $query .= " \n     ,sms_yn";
        $query .= " \n     ,tel_num";
        $query .= " \n     ,cell_num";
        $query .= " \n     ,birth";
        $query .= " \n     ,first_join_date";
        $query .= " \n     ,member_dvs";
        $query .= " \n     ,mail";
        $query .= " \n     ,member_typ";
        $query .= " \n     ,onefile_etprs_yn";
        $query .= " \n     ,card_pay_yn";
        $query .= " \n     ,certi_yn";
        $query .= " \n     ,dlvr_friend_yn";
        $query .= " \n     ,new_yn";
        $query .= " \n     ,grade";
        $query .= " \n     ,nc_release_resp";
        $query .= " \n     ,bl_release_resp";
        $query .= " \n     ,withdraw_dvs";
        $query .= " \n     ,dlvr_dvs";
        $query .= " \n     ,aprvl_yn";
        $query .= " \n     ,auto_grade_yn";
        $query .= " \n     ,state";
        $query .= " \n ) VALUES (";
        $query .= " \n      %s";
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
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n     ,%s";
        $query .= " \n )";

        $query  = sprintf($query, $param["cpn_admin_seqno"]
                                , $param["member_id"]
                                , $param["member_name"]
                                , $param["office_nick"]
                                , $param["passwd"]
                                , $param["zipcode"]
                                , $param["addr"]
                                , $param["addr_detail"]
                                , $param["mailing_yn"]
                                , $param["sms_yn"]
                                , $param["tel_num"]
                                , $param["cell_num"]
                                , $param["birth"]
                                , $param["first_join_date"]
                                , $param["member_dvs"]
                                , $param["mail"]
                                , $param["member_typ"]
                                , $param["onefile_etprs_yn"]
                                , $param["card_pay_yn"]
                                , $param["certi_yn"]
                                , $param["dlvr_friend_yn"]
                                , $param["new_yn"]
                                , $param["grade"]
                                , $param["nc_release_resp"]
                                , $param["bl_release_resp"]
                                , $param["withdraw_dvs"]
                                , $param["dlvr_dvs"]
                                , $param["aprvl_yn"]
                                , $param["auto_grade_yn"]
                                , $param["state"]);

        $resultSet = $conn->Execute($query);
 
        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }
}
?>
