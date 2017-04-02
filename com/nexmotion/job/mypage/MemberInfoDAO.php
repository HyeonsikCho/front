<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/MemberInfoHTML.php');

class MemberInfoDAO extends MypageCommonDAO {
 
    function __construct() {
    }

    /**
     * @brief 회원 가입 정보
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMemberJoinInfo($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\nSELECT mail ";
        $query .= "\n      ,birth ";
        $query .= "\n      ,tel_num ";
        $query .= "\n      ,cell_num ";
        $query .= "\n      ,zipcode ";
        $query .= "\n      ,addr ";
        $query .= "\n      ,addr_detail ";
        $query .= "\n      ,sms_yn ";
        $query .= "\n      ,mailing_yn ";
        $query .= "\n  FROM member ";
        $query .= "\n WHERE member_seqno = " . $param["member_seqno"];

        return $conn->Execute($query);
    }

    /**
     * @brief 회원 비밀번호 체크
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMemberPw($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\nSELECT passwd ";
        $query .= "\n  FROM member ";
        $query .= "\n WHERE member_seqno = " . $param["member_seqno"];

        return $conn->Execute($query);
    }

    /**
     * @brief 회원정보 회원탈퇴
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateMemberWithdraw($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param["final_modi_date"] = date("Y-m-d H:i:s");

        $withdraw_dvs = "";
        if ($param["withdraw_dvs"]) {
            $withdraw_dvs = $param["withdraw_dvs"];
        } else {
            $withdraw_dvs = 3;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = "\n    UPDATE  member ";
        $query .= "\n       SET  withdraw_dvs = %s ";
        $query .= "\n           ,final_modi_date = %s ";
        $query .= "\n           ,own_point = NULL ";
        $query .= "\n     WHERE  member_seqno = %s ";

        $query = sprintf($query, $withdraw_dvs,
                         $param["final_modi_date"],
                         $param["member_seqno"]);

        $resultSet = $conn->Execute($query);
 
        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 기업에 속한 회원 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCoPerMember($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\nSELECT  member_name ";
        $query .= "\n       ,member_id ";
        $query .= "\n       ,tel_num ";
        $query .= "\n       ,cell_num ";
        $query .= "\n       ,mail ";
        $query .= "\n       ,member_seqno ";
        $query .= "\n  FROM  member";
        $query .= "\n WHERE  group_id = " . $param["member_seqno"];
        $query .= "\n   AND  withdraw_dvs = 1";

        return $conn->Execute($query);
    }

    /**
     * @brief 가입정보 변경
     *
     * @param $conn  = connection identifier
     *
     * @return 검색결과
     */
    function updateMemberJoinInfo($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param["final_modi_date"] = date("Y-m-d H:i:s");

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = "\n    UPDATE  member ";
        $query .= "\n       SET  mail = %s ";
        $query .= "\n           ,birth = %s ";
        $query .= "\n           ,tel_num = %s ";
        $query .= "\n           ,cell_num = %s ";
        $query .= "\n           ,zipcode = %s ";
        $query .= "\n           ,addr = %s ";
        $query .= "\n           ,addr_detail = %s ";
        $query .= "\n           ,mailing_yn = %s ";
        $query .= "\n           ,sms_yn = %s ";
        $query .= "\n           ,final_modi_date = %s ";
        $query .= "\n     WHERE  member_seqno = %s ";

        $query = sprintf($query, $param["mail"],
                         $param["birth"],
                         $param["tel_num"],
                         $param["cell_num"],
                         $param["zipcode"],
                         $param["addr"],
                         $param["addr_detail"],
                         $param["mailing_yn"],
                         $param["sms_yn"],
                         $param["final_modi_date"],
                         $param["member_seqno"]);

        $resultSet = $conn->Execute($query);
 
        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 기업 주문담당자 회원정보 등록
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertCoOrderMng($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param["first_join_date"] = date("Y-m-d H:i:s");

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = " \n INSERT INTO member(";
        $query .= " \n      group_id";
        $query .= " \n     ,group_name";
        $query .= " \n     ,member_id";
        $query .= " \n     ,member_name";
        $query .= " \n     ,office_nick";
        $query .= " \n     ,member_dvs";
        $query .= " \n     ,posi";
        $query .= " \n     ,passwd";
        $query .= " \n     ,tel_num";
        $query .= " \n     ,cell_num";
        $query .= " \n     ,first_join_date";
        $query .= " \n     ,mail";
        $query .= " \n     ,withdraw_dvs";
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
        $query .= " \n     ,1";
        $query .= " \n )";

        $query  = sprintf($query, $param["group_id"]
                                , $param["group_name"]
                                , $param["member_id"]
                                , $param["office_nick"]
                                , $param["member_name"]
                                , $param["member_dvs"]
                                , $param["posi"]
                                , $param["passwd"]
                                , $param["tel_num"]
                                , $param["cell_num"]
                                , $param["first_join_date"]
                                , $param["mail"]);

        $resultSet = $conn->Execute($query);
 
        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }
 
    /**
     * @brief 기업에 속한 회원 상세정보 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCoPerMemberInfo($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\nSELECT  member_name ";
        $query .= "\n       ,posi ";
        $query .= "\n       ,member_id ";
        $query .= "\n       ,tel_num ";
        $query .= "\n       ,cell_num ";
        $query .= "\n       ,mail ";
        $query .= "\n  FROM  member";
        $query .= "\n WHERE  member_seqno = " . $param["member_seqno"];

        return $conn->Execute($query);
    }

    /**
     * @brief 기업에 속한 회원 가입정보 수정
     *
     * @param $conn  = connection identifier
     *
     * @return 검색결과
     */
    function updateCoOrderMng($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param["final_modi_date"] = date("Y-m-d H:i:s");

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = "\n    UPDATE  member ";
        $query .= "\n       SET  mail = %s ";
        $query .= "\n           ,member_name = %s ";
        $query .= "\n           ,passwd = %s ";
        $query .= "\n           ,tel_num = %s ";
        $query .= "\n           ,cell_num = %s ";
        $query .= "\n           ,final_modi_date = %s ";
        $query .= "\n     WHERE  member_seqno = %s ";

        $query = sprintf($query, $param["mail"],
                         $param["member_name"],
                         $param["passwd"],
                         $param["tel_num"],
                         $param["cell_num"],
                         $param["final_modi_date"],
                         $param["member_seqno"]);

        $resultSet = $conn->Execute($query);
 
        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 쿠폰 내역 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCpList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);
        $today = date("Y-m-d H:i:s", time());

        if ($type === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\nSELECT  A.cp_name";
            $query .= "\n       ,A.val";
            $query .= "\n       ,A.unit";
            $query .= "\n       ,A.max_sale_price";
            $query .= "\n       ,A.min_order_price";
            $query .= "\n       ,B.use_deadline";
            $query .= "\n       ,B.issue_date";
            $query .= "\n       ,B.use_yn";
        }
        $query .= "\n  FROM  cp AS A";
        $query .= "\n       ,cp_issue AS B";
        $query .= "\n WHERE  A.cp_seqno = B.cp_seqno";
        $query .= "\n   AND  B.member_seqno = " . $param["seqno"];
        $query .= "\n   AND  B.use_yn = 'N'";
        $query .= "\n   AND  B.use_able_start_date <= '" . $today . "'";

        //상태
        if ($this->blankParameterCheck($param ,"state")) {
            
            $state = substr($param["state"], 1, -1);

            //미사용 사용가능한 쿠폰
            if ($state == 1) {
                $query .= "\n       AND B.use_able_start_date <= '";
                $query .= $today . "'";
                $query .= "\n       AND (B.use_deadline >= '" . $today . "'";
                $query .= "\n        OR B.use_deadline is NULL)";

            //기한 만료된 쿠폰
            } else  {
                $query .= "\n       AND (B.use_able_start_date > '";
                $query .= $today . "'";
                $query .= "\n        OR B.use_deadline < '" . $today . "')";
            }
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($type == "SEQ") {

            $query .= "\n ORDER BY B.cp_issue_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;

        }

        return $conn->Execute($query);
    }

    /**
     * @brief 포인트 내역 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectPointList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);

        if ($type === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n    SELECT  regi_date";
            $query .= "\n           ,point";
            $query .= "\n           ,rest_point";
            $query .= "\n           ,order_price";
            $query .= "\n           ,dvs";
            $query .= "\n           ,order_num";
        }
        $query .= "\n      FROM  member_point_history";
        $query .= "\n     WHERE  member_seqno = " . $param["seqno"];

        //구분
        if ($this->blankParameterCheck($param ,"dvs")) {

            $query .= "\n    AND  dvs = " . $param["dvs"];

        }

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {

            $from = substr($param["from"], 1, -1);

            $query .="\n     AND  regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {

            $to = substr($param["to"], 1, -1);

            $query .="\n     AND  regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($type == "SEQ") {

            $query .= "\n ORDER BY member_point_history_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;

        }

        return $conn->Execute($query);
    }

    /**
     * @brief 참여중인 이벤트 건수
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectJoinEventCount($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\nSELECT  COUNT(*) AS cnt ";
        $query .= "\n  FROM  member AS A ";
        $query .= "\n       ,order_common AS B";
        $query .= "\n WHERE  A.member_seqno = B.member_seqno";
        $query .= "\n   AND  B.event_yn = 'Y'";
        $query .= "\n   AND  A.member_seqno = " . $param["member_seqno"];

        return $conn->Execute($query);
    }

    /**
     * @brief 이벤트 내역 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectEventList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);
        $today = date("Y-m-d H:i:s", time());

        if ($type === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\nSELECT  member_event_seqno ";
            $query .= "\n       ,event_typ ";
            $query .= "\n       ,prdt_name ";
            $query .= "\n       ,bnf ";
            $query .= "\n       ,regi_date ";
        }
        $query .= "\n  FROM  member_event ";
        $query .= "\n WHERE  member_seqno = " . $param["seqno"];

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);

            $query .="\n     AND  regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {
            $to = substr($param["to"], 1, -1);

            $query .="\n     AND  regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        //이벤트명
        if ($this->blankParameterCheck($param ,"event_typ")) {
            $val = substr($param["event_typ"], 1, -1);
            $query .="\n  AND  event_typ LIKE '%" . $val . "%'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);
        $query .= "\n ORDER BY regi_date DESC ";
        $query .= "\n LIMIT ". $s_num . ", " . $list_num;

        return $conn->Execute($query);
    } 
 
    /**
     * @brief 카테고리 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateTable($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $sortcode = substr($param["cate_sortcode"], 1, -1);
        $type = substr($param["type"], 1, -1);
 
        if ($type === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\nSELECT  cate_name";
            $query .= "\n       ,sortcode";
        }
        $query .= "\n  FROM  cate";
        $query .= "\n WHERE  cate_level = " . $param["cate_level"];
        $query .= "\n   AND  sortcode LIKE '" . $sortcode . "%'";
        $query .= "\nORDER BY sortcode";

        return $conn->Execute($query);
    }

    /**
     * @brief 카테고리 별 회원등급 할인정보 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateGradeInfo($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션어택방지
        $param = $this->parameterArrayEscape($conn, $param);

        if ($type === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n   SELECT  B.rate";
        }
        $query .= "\n     FROM  member_grade_policy AS A";
        $query .= "\nLEFT JOIN  grade_sale_price AS B";
        $query .= "\n       ON  A.grade = B.grade";
        $query .= "\n    WHERE  1 = 1";

        if ($this->blankParameterCheck($param, "cate_sortcode")) {
            $query .= "\n      AND  B.cate_sortcode = ";
            $query .= $param["cate_sortcode"];
        }

        if ($this->blankParameterCheck($param, "grade")) {
            $query .= "\n      AND  A.grade = ";
            $query .= $param["grade"];
        }
        $query .= "\nORDER BY B.cate_sortcode";

        return $conn->Execute($query);
    }

    /**
     * @brief 선입금 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectPrepaymentList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);
        $seqno = substr($param["member_seqno"], 1, -1);

        if ($type === "COUNT") {
            $query  = "\n  SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n  SELECT  A.deal_date";
            $query .= "\n         ,A.depo_way";
            $query .= "\n         ,A.depo_price";
            $query .= "\n         ,A.state";
            $query .= "\n         ,A.deal_num";
        }
        $query .= "\n    FROM  member_pay_history AS A";
        $query .= "\n   WHERE  A.member_seqno IN (" . $seqno . ")";
        $query .= "\n     AND  A.dvs = '입금'";

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .= "\n     AND  A.deal_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {
            $to = substr($param["to"], 1, -1);
            $query .= "\n     AND  A.deal_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($type == "SEQ") {
            $query .= "\nORDER BY A.deal_date DESC";
            $query .= "\n   LIMIT ". $s_num . ", " . $list_num;
        }

        return $conn->Execute($query);
    }
}
?>
