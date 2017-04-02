<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/ServiceCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/service/FileListHtml.php');

class FileListDAO extends ServiceCommonDAO {
    function __construct() {
    }

    /**
     * @brief 공유자료실 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectFileList($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);

        if ($dvs === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\nSELECT  A.title ";
            $query .= "\n       ,A.cont ";
            $query .= "\n       ,A.regi_date ";
            $query .= "\n       ,A.hits ";
            $query .= "\n       ,A.share_library_seqno ";
            $query .= "\n       ,B.member_name ";
        }
        $query .= "\n  FROM  share_library AS A ";
        $query .= "\n       ,member AS B ";
        $query .= "\n WHERE  A.member_seqno = B.member_seqno";

        //검색조건 별 검색
        if ($this->blankParameterCheck($param ,"search_txt")) {
            $search_cnd = substr($param["search_cnd"], 1, -1);
            $search_txt = substr($param["search_txt"], 1, -1);
            $query .= "\n    AND  $search_cnd LIKE '%$search_txt%' ";
        }

        //일련번호 검색
        if ($this->blankParameterCheck($param ,"seqno")) {
            $query .= "\n    AND  share_library_seqno = $param[seqno]";
        }
 
        //이전 일련번호 검색
        if ($this->blankParameterCheck($param ,"pre_seqno")) {
            $query .= "\n    AND  share_library_seqno < $param[pre_seqno]";
        }
 
        //다음 일련번호 검색
        if ($this->blankParameterCheck($param ,"next_seqno")) {
            $query .= "\n    AND  share_library_seqno > $param[next_seqno]";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);
        
        if ($dvs === "SEQ") { 
            $query .= "\n ORDER BY share_library_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;
        } else if ($dvs === "pre") {
            $query .= "\n ORDER BY share_library_seqno DESC ";
            $query .= "\n LIMIT 1";
        } else if ($dvs === "next") {
            $query .= "\n LIMIT 1";
        }

        return $conn->Execute($query);
    }
}
?>
