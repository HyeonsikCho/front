<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/CscenterCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/cscenter/FaqHtml.php');

class FaqDAO extends CscenterCommonDAO {
 
    /**
     * @brief FAQ 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectFaqList($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);

        if ($dvs === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n    SELECT  A.faq_seqno";
            $query .= "\n           ,A.sort";
            $query .= "\n           ,A.title";
            $query .= "\n           ,A.cont";
            $query .= "\n           ,A.regi_date";
        }
        $query .= "\n      FROM  faq A";
        $query .= "\n     WHERE  1 = 1";

        //검색조건 별 검색
        if ($this->blankParameterCheck($param ,"cont")) {
            $search_txt = substr($param["cont"], 1, -1);
            $query .= "\n    AND  (A.cont LIKE '%$search_txt%' ";
            $query .= "\n     OR  A.title LIKE '%$search_txt%') ";
        }

        //FAQ 타입 별 검색
        if ($this->blankParameterCheck($param ,"type")) {
            $query .= "\n    AND  A.sort = $param[type]";
        }
 
        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($dvs === "SEQ") { 
            $query .= "\n ORDER BY A.faq_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;
        }

        return $conn->Execute($query);
    }


    /**
     * @brief FAQ Top10 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectFaqTopList($conn, $param) {
        
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);
            
        $query  = "\n    SELECT  A.faq_seqno";
        $query .= "\n           ,A.sort";
        $query .= "\n           ,A.title";
        $query .= "\n           ,A.cont";
        $query .= "\n           ,A.regi_date";
        $query .= "\n           ,A.hits";
        $query .= "\n      FROM  faq A";
        $query .= "\n     WHERE  1 = 1";

        //검색조건 별 검색
        if ($this->blankParameterCheck($param ,"cont")) {
            $search_txt = substr($param["cont"], 1, -1);
            $query .= "\n    AND  A.cont LIKE '%$search_txt%' ";
            $query .= "\n     OR  A.title LIKE '%$search_txt%' ";
        }

        $query .= "\n ORDER BY A.hits DESC, A.faq_seqno DESC ";
        $query .= "\n LIMIT 0, 10 ";

        return $conn->Execute($query);
    }
}
?>
