<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/CscenterCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/cscenter/NoticeHtml.php');

class NoticeDAO extends CscenterCommonDAO {

    /**
     * @brief 공지사항 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectNoticeList($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);

        //if ($dvs === "COUNT") {
        //    $query  = "\n SELECT  COUNT(*) AS cnt";
        //} else {
            $query  = "\n    SELECT  SQL_CALC_FOUND_ROWS";
            $query .= "\n            A.title";
            $query .= "\n           ,A.dvs";
            $query .= "\n           ,A.regi_date";
            $query .= "\n           ,A.hits";
            $query .= "\n           ,A.cont";
            $query .= "\n           ,A.notice_seqno";
        //}
        $query .= "\n      FROM  notice A";
        $query .= "\n     WHERE  1 = 1";

        //검색조건 별 검색
        if ($this->blankParameterCheck($param ,"search_txt")) {
            $search_txt = substr($param["search_txt"], 1, -1);
            $query .= "\n    AND  A.title LIKE '%$search_txt%' ";
        }

        //일련번호 검색
        if ($this->blankParameterCheck($param ,"seqno")) {
            $query .= "\n    AND  A.notice_seqno = $param[seqno]";
        }

        //일반 구분 리스트
/*
        if ($this->blankParameterCheck($param ,"noti_dvs")) {
            $query .= "\n    AND  A.dvs = $param[noti_dvs]";
        }
*/
        //긴급, 호환성문제 구분 리스트
        if ($dvs === "noti") {
            $query .= "\n    AND  (A.dvs = 1 OR A.dvs = 2)";
        }

        //이전 일련번호 검색
        if ($this->blankParameterCheck($param ,"pre_seqno")) {
            $query .= "\n    AND  A.notice_seqno < $param[pre_seqno]";
        }

        //다음 일련번호 검색
        if ($this->blankParameterCheck($param ,"next_seqno")) {
            $query .= "\n    AND  A.notice_seqno > $param[next_seqno]";
        }

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $query .="\n     AND  A.regi_date > $param[from] ";
        }

        if ($this->blankParameterCheck($param ,"to")) {
            $query .="\n     AND  A.regi_date <= $param[to] ";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($dvs === "SEQ") {
            $query .= "\n ORDER BY A.notice_seqno DESC, A.dvs DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;
        } else if ($dvs === "pre") {
            $query .= "\n ORDER BY A.notice_seqno DESC ";
            $query .= "\n LIMIT 1";
        } else if ($dvs === "next") {
            $query .= "\n LIMIT 1";
        }

        return $conn->Execute($query);
    }


    /**
     * @brief 공지사항 요약 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectNoticeSummary($conn) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $query  = "\n    SELECT  A.title";
        $query .= "\n           ,A.dvs";
        $query .= "\n           ,A.regi_date";
        $query .= "\n           ,A.hits";
        $query .= "\n           ,A.cont";
        $query .= "\n           ,A.notice_seqno";
        $query .= "\n      FROM  notice A";
        $query .= "\n     WHERE  1 = 1";
        $query .= "\n ORDER BY A.notice_seqno DESC ";
        $query .= "\n LIMIT 0, 5 ";

        return $conn->Execute($query);
    }


    /**
     * @brief 공지사항 뷰
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectNoticeView($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);

        if ($dvs === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n    SELECT  A.title";
            $query .= "\n           ,A.dvs";
            $query .= "\n           ,A.regi_date";
            $query .= "\n           ,A.hits";
            $query .= "\n           ,A.cont";
            $query .= "\n           ,A.notice_seqno";
            $query .= "\n           ,B.file_path";
            $query .= "\n           ,B.save_file_name";
            $query .= "\n           ,B.origin_file_name";
        }
        $query .= "\n      FROM  notice A";
        $query .= "\n            LEFT JOIN notice_file B ";
        $query .= "\n            ON A.notice_seqno = B.notice_seqno ";

        //일련번호 검색
        if ($this->blankParameterCheck($param ,"seqno")) {
            $query .= "\n WHERE  A.notice_seqno = $param[seqno]";
        }

        return $conn->Execute($query);
    }

}
?>
