<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/MypageCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/OrderInfoHTML.php');

/**
 * @file OtoInqMngDAO.php
 *
 * @brief 마이페이지 - 메인
 */
class MainDAO extends MypageCommonDAO {

    function __construct() {
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
        $member_seqno = substr($param["member_seqno"], 1, -1);

        $query  = "\nSELECT  T1.* ";
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
        $query .="\n    AND  T2.member_seqno IN(" . $member_seqno . ")";
        $query .= "\n ORDER BY T1.oto_inq_seqno DESC ";
        $query .= "\n LIMIT 0, 5";

        return $conn->Execute($query);
    }

    /**
     * @brief 견적리스트 요약
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectEstiSummary($conn, $param) {
     
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $member_seqno = substr($param["member_seqno"], 1, -1);

        $query .= "\n SELECT  esti_seqno ";
        $query .= "\n        ,title ";
        $query .= "\n        ,state ";
        $query .= "\n        ,regi_date ";
        $query .= "\n   FROM  esti ";
        $query .= "\n  WHERE  member_seqno IN(" . $member_seqno . ")";
        $query .= "\n ORDER BY esti_seqno DESC ";
        $query .= "\n LIMIT 0, 5";

        return $conn->Execute($query);
    }
}
