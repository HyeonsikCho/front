<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/ClaimInfoHTML.php');

class ClaimInfoDAO extends MypageCommonDAO {

    function __construct() {
    }

    /**
     * @brief 클레임 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectClaimList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);
        $member_seqno = substr($param["member_seqno"], 1, -1);

        if ($type === "COUNT") {
            $query  = "\nSELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\nSELECT A.regi_date";
            $query .= "\n      ,B.order_num";
            $query .= "\n      ,B.title AS order_title";
            $query .= "\n      ,A.title AS claim_title";
            $query .= "\n      ,A.dvs";
            $query .= "\n      ,A.state";
            $query .= "\n      ,A.order_common_seqno";
            $query .= "\n      ,A.order_claim_seqno";
        }
        $query .= "\n  FROM order_claim AS A";
        $query .= "\n      ,order_common AS B";
        $query .= "\n WHERE A.order_common_seqno = B.order_common_seqno";
        $query .= "\n   AND B.member_seqno IN(" . $member_seqno . ")";


        //인쇄물제목 검색
        if ($this->blankParameterCheck($param ,"title")) {
            $title = substr($param["title"], 1, -1);
            $query .= "\n   AND A.title LIKE '%" . $title . "%'";
        }

        //상태
        if ($this->blankParameterCheck($param ,"state")) {
            $query .= "\n   AND A.state = " . $param["state"];
        }

        //사고유형
        if ($this->blankParameterCheck($param ,"dvs")) {
            $query .= "\n   AND A.dvs = " . $param["dvs"];
        }

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .="\n    AND A.regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {
            $to = substr($param["to"], 1, -1);
            $query .="\n    AND A.regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($type == "SEQ") {
            $query .= "\n ORDER BY A.order_claim_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;
        }

        return $conn->Execute($query);
    }

    /**
     * @brief 클레임 갯수 COUNT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectClaimCount($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    SELECT  COUNT(*) AS cnt";
        $query .= "\n      FROM  order_claim";
        $query .= "\n     WHERE  order_common_seqno = " . $param["order_common_seqno"];

        return $conn->Execute($query);
    }

    /**
     * @brief 클레임 보기
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectClaimView($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n   SELECT A.order_common_seqno";
        $query .= "\n         ,A.title AS claim_title";
        $query .= "\n         ,A.dvs";
        $query .= "\n         ,A.sample_origin_file_name";
        $query .= "\n         ,A.cust_cont";
        $query .= "\n         ,B.name AS empl_name";
        $query .= "\n         ,A.state";
        $query .= "\n         ,A.occur_price";
        $query .= "\n         ,A.cust_burden_price";
        $query .= "\n         ,A.mng_cont";
        $query .= "\n     FROM order_claim AS A";
        $query .= "\nLEFT JOIN empl AS B";
        $query .= "\n       ON A.empl_seqno = B.empl_seqno";
        $query .= "\n    WHERE A.order_claim_seqno = " . $param["order_claim_seqno"];

        return $conn->Execute($query);
    }
 
    /**
     * @brief 클레임댓글 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectClaimComment($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $query  = "\n    SELECT  comment";
        $query .= "\n           ,regi_date";
        $query .= "\n           ,cust_yn";
        $query .= "\n      FROM  order_claim_comment";
        $query .= "\n     WHERE  order_claim_seqno = ";
        $query .= $param["claim_seqno"];
        $query .= "\n  ORDER BY regi_date ASC";
 
        return $conn->Execute($query);

    }
}
?>
