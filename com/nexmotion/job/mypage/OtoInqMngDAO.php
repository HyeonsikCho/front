<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/MemberCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/html/mypage/OtoInqList.php");

/**
 * @file OtoInqMngDAO.php
 *
 * @brief 회원 - 회원관리 - 1대1문의리스트 DAO
 */
class OtoInqMngDAO extends MemberCommonDAO {

    function __construct() {
    }

    /**
     * @brief 1:1문의리스트
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOtoInquireList($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);
        $title = substr($param["title"], 1, -1);

        if ($dvs == "COUNT") {
            $query  ="\nSELECT  COUNT(*) AS cnt ";
        } else {
            $query  = "\nSELECT  T1.* ";
            $query .= "\n       ,T2.office_nick ";
            $query .= "\n       ,T2.member_name ";
        }
        $query .= "\n  FROM  ( ";
        $query .= "\n           SELECT  A.regi_date AS inq_date ";
        $query .= "\n                  ,A.member_seqno ";
        $query .= "\n                  ,A.inq_typ ";
        $query .= "\n                  ,A.title ";
        $query .= "\n                  ,B.regi_date AS reply_date ";
        $query .= "\n                  ,C.name ";
        $query .= "\n                  ,A.answ_yn ";
        $query .= "\n                  ,A.oto_inq_seqno ";
        $query .= "\n                  ,A.group_seqno ";
        $query .= "\n            FROM  oto_inq AS A ";
        $query .= "\n       LEFT JOIN  oto_inq_reply AS B ";
        $query .= "\n              ON  A.oto_inq_seqno = B.oto_inq_seqno ";
        $query .= "\n       LEFT JOIN  empl AS C ";
        $query .= "\n              ON  B.empl_seqno = C.empl_seqno ) AS T1 ";
        $query .= "\n         ,member AS T2 ";
        $query .= "\n WHERE  T1.member_seqno = T2.member_seqno ";

        //사내닉네임(회원명) -> 일련번호로 변형 해서 검색
        if ($this->blankParameterCheck($param ,"member_seqno")) {
            $query .="\n    AND  T1.member_seqno = $param[member_seqno] ";
        }

        //구분
        if ($this->blankParameterCheck($param ,"group_seqno")) {
            $query .="\n    AND  T1.group_seqno = $param[group_seqno] ";
        }

        //문의일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .="\n   AND  T1.inq_date > '" . $from;
            $query .=" 00:00:00'";
        }
        if ($this->blankParameterCheck($param ,"to")) {
            $to = substr($param["to"], 1, -1);
            $query .="\n   AND  T1.inq_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        //상태
        if ($this->blankParameterCheck($param ,"answ_yn")) {
            $query .="\n   AND  T1.answ_yn = $param[answ_yn] ";
        }

        //구분
        if ($this->blankParameterCheck($param ,"inq_typ")) {
            $query .="\n   AND  T1.inq_typ = $param[inq_typ] ";
        }

        //제목
        if ($this->blankParameterCheck($param ,"title")) {
            $query .="\n   AND  T1.title like '%" . $title . "%' ";
        }

        $query .="\n ORDER BY T1.oto_inq_seqno DESC ";

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($dvs == "SEQ") {
            $query .= "\nLIMIT ". $s_num . ", " . $list_num;
        }
        return $conn->Execute($query);
    }

    /**
     * @brief 1:1문의 요약리스트
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOtoInquireSummary($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\nSELECT  T1.* ";
        $query .= "\n       ,T2.office_nick ";
        $query .= "\n       ,T2.member_name ";
        $query .= "\n  FROM  ( ";
        $query .= "\n           SELECT  A.regi_date AS inq_date ";
        $query .= "\n                  ,A.member_seqno ";
        $query .= "\n                  ,A.inq_typ ";
        $query .= "\n                  ,A.title ";
        $query .= "\n                  ,B.regi_date AS reply_date ";
        $query .= "\n                  ,C.name ";
        $query .= "\n                  ,A.answ_yn ";
        $query .= "\n                  ,A.oto_inq_seqno ";
        $query .= "\n            FROM  oto_inq AS A ";
        $query .= "\n       LEFT JOIN  oto_inq_reply AS B ";
        $query .= "\n              ON  A.oto_inq_seqno = B.oto_inq_seqno ";
        $query .= "\n       LEFT JOIN  empl AS C ";
        $query .= "\n              ON  B.empl_seqno = C.empl_seqno ) AS T1 ";
        $query .= "\n         ,member AS T2 ";
        $query .= "\n WHERE  T1.member_seqno = T2.member_seqno ";

        //사내닉네임(회원명) -> 일련번호로 변형 해서 검색
        if ($this->blankParameterCheck($param ,"member_seqno")) {
            $query .="\n    AND  T2.member_seqno = $param[member_seqno] ";
        }

        $query .= "\n ORDER BY T1.oto_inq_seqno DESC ";
        $query .= "\n LIMIT 0, 5";

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
     * @brief 답변내용
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOtoReplyCont($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\nSELECT  B.name ";
        $query .= "\n       ,C.depar_name ";
        $query .= "\n       ,A.cont ";
        $query .= "\n       ,A.oto_inq_reply_seqno ";
        $query .= "\n  FROM  oto_inq_reply AS A ";
        $query .= "\n       ,empl AS B ";
        $query .= "\n       ,depar_admin AS C ";
        $query .= "\n WHERE  A.empl_seqno = B.empl_seqno ";
        $query .= "\n   AND  B.depar_code = C.depar_code ";

        //일련번호
        if ($this->blankParameterCheck($param ,"oto_inq_seqno")) {
            $query .="\n    AND A.oto_inq_seqno = $param[oto_inq_seqno] ";
        }

        return $conn->Execute($query);
    }

    /**
     * @brief 1:1문의 요청
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertOtoInq($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\nINSERT INTO  oto_inq ( ";
        $query .= "\n         title ";
        $query .= "\n       , inq_typ ";
        if ($param["tel_num"])
            $query .= "\n       , tel_num ";
        if ($param["cell_num"])
            $query .= "\n       , cell_num ";
        $query .= "\n       , cont ";
        if ($param["mail"])
            $query .= "\n       , mail ";
        $query .= "\n       , answ_yn ";
        $query .= "\n       , answ_mail_yn ";
        $query .= "\n       , regi_date ";
        $query .= "\n       , member_seqno ";
        $query .= "\n       , group_seqno ";
        $query .= "\n) VALUES ( ";
        $query .= "\n         %s ";
        $query .= "\n       , %s ";
        if ($param["tel_num"])
            $query .= "\n   ," . $param["tel_num"];
        if ($param["cell_num"])
            $query .= "\n   ," . $param["cell_num"];
        $query .= "\n       , %s ";
        if ($param["mail"])
            $query .= "\n   ," . $param["mail"];
        $query .= "\n       , 'N' ";
        $query .= "\n       , 'N' ";
        $query .= "\n       , SYSDATE() ";
        $query .= "\n       , %s ";
        $query .= "\n       , %s ";
        $query .= "\n) ";

        $query = sprintf( $query
                        , $param["title"]
                        , $param["inq_typ"]
                        , $param["cont"]
                        , $param["member_seqno"]
                        , $param["group_seqno"]);

        $resultSet = $conn->Execute($query);

        if ($resultSet === FALSE) {
            return false;
        } else {
            return $conn->Insert_ID("oto_inq", "oto_inq_seqno");
        }
    }
}
