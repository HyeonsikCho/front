<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/CommonDAO.php');

class MypageCommonDAO extends CommonDAO {

    function __construct() {
    }

    /**
     * @brief 개인 기업 일련번호 가져옴
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function getBuPerSeqno($conn, $param) {
     
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n SELECT  member_seqno ";
        $query .= "\n   FROM  member ";
        $query .= "\n  WHERE  group_id =" . $param["member_seqno"];
        $query .= "\n    AND  withdraw_dvs = 1";

        return $conn->Execute($query);
    }

    /**
     * @brief 주문에 해당하는 후공정 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderAfter($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  after_name";
        $query .= "\n           ,depth1";
        $query .= "\n           ,depth2";
        $query .= "\n           ,depth3";
        $query .= "\n      FROM  order_after_history";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    //주문 수
    function selectOrderStatusCount($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $member_seqno = substr($param["member_seqno"], 1, -1);

        $query .= "\nSELECT  COUNT(*) AS cnt ";
        $query .= "\n  FROM  order_common AS A ";
        $query .= "\n WHERE  member_seqno IN(" . $member_seqno . ")";
 
        //제외 
        if ($this->blankParameterCheck($param ,"not")) {
            $query .= "\n   AND  A.order_state != " . $param["not"];
        }

        //상태
        if ($this->blankParameterCheck($param ,"state_min")) {
            $query .= "\n      AND (" . $param["state_min"] . " + 0) <= A.order_state";
            $query .= "\n      AND A.order_state <= (" . $param["state_max"] . " + 0)";
        }
 
        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .="\n       AND A.order_regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {

            $to = substr($param["to"], 1, -1);

            $query .="\n       AND A.order_regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        return $conn->Execute($query);
    }

    //마이페이지 기업 메인 주문리스트 쿼리
    function selectMainBusiOrderList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);
        $member_seqno = substr($param["member_seqno"], 1, -1);

        if ($dvs === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n SELECT A.order_regi_date";
            $query .= "\n       ,A.order_num";
            $query .= "\n       ,A.member_seqno";
            $query .= "\n       ,A.title";
            $query .= "\n       ,A.order_detail";
            $query .= "\n       ,A.amt";
            $query .= "\n       ,A.amt_unit_dvs";
            $query .= "\n       ,A.count";
            $query .= "\n       ,A.pay_price";
            $query .= "\n       ,A.order_state";
            $query .= "\n       ,A.order_common_seqno";
            $query .= "\n       ,B.member_name";
        }
        $query .= "\n  FROM  order_common AS A ";
        $query .= "\n       ,member AS B ";
        $query .= "\n WHERE  A.member_seqno = B.member_seqno";
        $query .= "\n   AND  A.member_seqno IN(" . $member_seqno . ")";
 
        //제외 
        if ($this->blankParameterCheck($param ,"not")) {
            $query .= "\n   AND  A.order_state != " . $param["not"];
        }
 
        //메인 상태 검색
        if ($this->blankParameterCheck($param ,"state_min")) {
            $query .= "\n      AND (" . $param["state_min"] . " + 0) <= A.order_state";
            $query .= "\n      AND A.order_state <= (" . $param["state_max"] . " + 0)";
        }

        //상태
        if ($this->blankParameterCheck($param ,"order_state")) {
            $query .= "\n   AND  A.order_state = " . $param["order_state"];
        }
 
        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .="\n       AND A.order_regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {

            $to = substr($param["to"], 1, -1);

            $query .="\n       AND A.order_regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $query .= "\n ORDER BY A.order_common_seqno DESC ";

        return $conn->Execute($query);
    }
 
    //마이페이지 기업을 제외한 메인 주문리스트 쿼리
    function selectMainOrderList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);
        $member_seqno = substr($param["member_seqno"], 1, -1);

        if ($dvs === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n SELECT A.order_regi_date";
            $query .= "\n       ,A.order_num";
            $query .= "\n       ,A.member_seqno";
            $query .= "\n       ,A.title";
            $query .= "\n       ,A.order_detail";
            $query .= "\n       ,A.amt";
            $query .= "\n       ,A.amt_unit_dvs";
            $query .= "\n       ,A.count";
            $query .= "\n       ,A.pay_price";
            $query .= "\n       ,A.order_state";
            $query .= "\n       ,A.order_common_seqno";
        }
        $query .= "\n  FROM  order_common AS A ";
        $query .= "\n WHERE  A.member_seqno IN(" . $member_seqno . ")";
 
        //제외 
        if ($this->blankParameterCheck($param ,"not")) {
            $query .= "\n   AND  A.order_state != " . $param["not"];
        }

        //상태
        if ($this->blankParameterCheck($param ,"order_state")) {
            $query .= "\n   AND  A.order_state = " . $param["order_state"];
        }
  
        //메인 상태 검색
        if ($this->blankParameterCheck($param ,"state_min")) {
            $query .= "\n      AND (" . $param["state_min"] . " + 0) <= A.order_state";
            $query .= "\n      AND A.order_state <= (" . $param["state_max"] . " + 0)";
        }

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .="\n       AND A.order_regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {

            $to = substr($param["to"], 1, -1);

            $query .="\n       AND A.order_regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $query .= "\n ORDER BY A.order_common_seqno DESC ";

        return $conn->Execute($query);
    }

    /**
     * @brief 기업을 주문 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectBusiOrderList($conn, $param) {

        if ($this->connectionCheck($conn) === false) { return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);
        $member_seqno = substr($param["member_seqno"], 1, -1);

        if ($dvs === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n   SELECT A.order_regi_date";
            $query .= "\n         ,A.order_num";
            $query .= "\n         ,A.member_seqno";
            $query .= "\n         ,A.title";
            $query .= "\n         ,A.order_detail";
            $query .= "\n         ,A.amt";
            $query .= "\n         ,A.amt_unit_dvs";
            $query .= "\n         ,A.count";
            $query .= "\n         ,A.pay_price";
            $query .= "\n         ,A.order_state";
            $query .= "\n         ,A.order_common_seqno";
            $query .= "\n         ,C.member_name";
            $query .= "\n         ,SUM(B.dlvr_price) AS dlvr_price";
        }
        $query .= "\n     FROM order_common AS A";
        $query .= "\nLEFT JOIN order_dlvr AS B";
        $query .= "\n       ON A.order_common_seqno = B.order_common_seqno";
        $query .= "\nLEFT JOIN member AS C";
        $query .= "\n       ON A.member_seqno = C.member_seqno";
        $query .= "\n    WHERE A.member_seqno IN(" . $member_seqno . ")";
        $query .= "\n      AND B.tsrs_dvs = '수신' ";

        //인쇄물제목 검색
        if ($this->blankParameterCheck($param ,"title")) {
            $title = substr($param["title"], 1, -1);
            $query .= "\n      AND A.title LIKE '%" . $title . "%'";
        }

        //배송종류
        if ($this->blankParameterCheck($param ,"dlvr_way")) {
            $search_txt = substr($param["dlvr_way"], 1, -1);
            $query .= "\n      AND B.dlvr_way = " . $param["dlvr_way"];
        }

        //상태
        if ($this->blankParameterCheck($param ,"state")) {
            $query .= "\n      AND A.order_state = " . $param["state"];
        }
        if ($this->blankParameterCheck($param ,"state_min")) {
            $query .= "\n      AND (" . $param["state_min"] . " + 0) <= A.order_state";
            $query .= "\n      AND A.order_state <= (" . $param["state_max"] . " + 0)";
        }

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .="\n       AND A.order_regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {

            $to = substr($param["to"], 1, -1);

            $query .="\n       AND A.order_regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($dvs != "COUNT") {
            $query .= "\n GROUP BY A.order_common_seqno";
            $query .= "\n ORDER BY A.order_common_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;
        }

        return $conn->Execute($query);
    }

    /**
     * @brief 기업을 제외한 주문 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);
        $member_seqno = substr($param["member_seqno"], 1, -1);

        if ($dvs === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n   SELECT A.order_regi_date";
            $query .= "\n         ,A.order_num";
            $query .= "\n         ,A.member_seqno";
            $query .= "\n         ,A.title";
            $query .= "\n         ,A.order_detail";
            $query .= "\n         ,A.amt";
            $query .= "\n         ,A.amt_unit_dvs";
            $query .= "\n         ,A.count";
            $query .= "\n         ,A.pay_price";
            $query .= "\n         ,A.order_state";
            $query .= "\n         ,A.order_common_seqno";
            $query .= "\n         ,C.member_name";
            $query .= "\n         ,SUM(B.dlvr_price) AS dlvr_price";
        }
        $query .= "\n     FROM order_common AS A";
        $query .= "\nLEFT JOIN order_dlvr AS B";
        $query .= "\n       ON A.order_common_seqno = B.order_common_seqno";
        $query .= "\nLEFT JOIN member AS C";
        $query .= "\n       ON A.member_seqno = C.member_seqno";
        $query .= "\n    WHERE A.member_seqno IN(" . $member_seqno . ")";
        $query .= "\n      AND B.tsrs_dvs = '수신' ";

        //인쇄물제목 검색
        if ($this->blankParameterCheck($param ,"title")) {
            $title = substr($param["title"], 1, -1);
            $query .= "\n      AND A.title LIKE '%" . $title . "%'";
        }

        //배송종류
        if ($this->blankParameterCheck($param ,"dlvr_way")) {
            $search_txt = substr($param["dlvr_way"], 1, -1);
            $query .= "\n      AND B.dlvr_way = " . $param["dlvr_way"];
        }

        //상태
        if ($this->blankParameterCheck($param ,"state")) {
            $query .= "\n      AND A.order_state = " . $param["state"];
        }
        if ($this->blankParameterCheck($param ,"state_min")) {
            $query .= "\n      AND (" . $param["state_min"] . " + 0) <= A.order_state";
            $query .= "\n      AND A.order_state <= (" . $param["state_max"] . " + 0)";
        }

        //등록일
        if ($this->blankParameterCheck($param ,"from")) {
            $from = substr($param["from"], 1, -1);
            $query .="\n       AND A.order_regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {

            $to = substr($param["to"], 1, -1);

            $query .="\n       AND A.order_regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($dvs != "COUNT") {
            $query .= "\n GROUP BY A.order_common_seqno";
            $query .= "\n ORDER BY A.order_common_seqno DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;
        }

        return $conn->Execute($query);
    }

    /**
     * @breif 주문공통 테이블 검색, 주문상세정보중 공통정보 검색
     *
     * @param $conn = db connection
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderCommon($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n   SELECT C.file_path";
        $query .= "\n         ,C.save_file_name";
        $query .= "\n         ,A.title";
        $query .= "\n         ,A.amt";
        $query .= "\n         ,A.count";
        $query .= "\n         ,A.amt_unit_dvs";
        $query .= "\n         ,A.event_price";
        $query .= "\n         ,A.use_point_price";
        $query .= "\n         ,A.sell_price";
        $query .= "\n         ,A.pay_price";
        $query .= "\n         ,A.grade_sale_price";
        $query .= "\n         ,A.add_after_price";
        $query .= "\n         ,A.add_opt_price";
        $query .= "\n         ,A.order_state";
        $query .= "\n         ,A.order_detail";
        $query .= "\n         ,A.expec_weight";
        $query .= "\n         ,D.dlvr_way";
        $query .= "\n         ,D.zipcode";
        $query .= "\n         ,D.addr";
        $query .= "\n         ,D.addr_detail";
        $query .= "\n         ,SUM(D.dlvr_price) AS dlvr_price";
        $query .= "\n     FROM order_common AS A";
        $query .= "\nLEFT JOIN cate_photo AS C";
        $query .= "\n       ON A.cate_sortcode = C.cate_sortcode";
        $query .= "\n      AND C.seq = '1'";
        $query .= "\nLEFT JOIN order_dlvr AS D ";
        $query .= "\n       ON A.order_common_seqno = D.order_common_seqno";
        $query .= "\n      AND D.tsrs_dvs = '수신'";
        $query .= "\n    WHERE A.order_common_seqno = " . $param["order_common_seqno"];

        return $conn->Execute($query);
    }

    /**
     * @breif 주문상세 테이블 검색, 주문상세정보 검색
     *
     * @param $conn = db connection
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderDetail($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $except_arr = array("table" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = "\n   SELECT A.order_detail";
        $query .= "\n         ,A.state";
        $query .= "\n         ,A.grade_sale_price";
        $query .= "\n         ,A.amt";
        $query .= "\n         ,A.amt_unit_dvs";
        $query .= "\n         ,A.pay_price";
        $query .= "\n         ,A.%s_seqno AS seqno";
        if ($param["table"] === "order_detail") {
            $query .= "\n         ,A.count";
            $query .= "\n         ,A.expec_weight";
        }
        $query .= "\n         ,A.order_detail_dvs_num";
        $query .= "\n     FROM %s AS A";
        $query .= "\n    WHERE A.order_common_seqno = %s";

        $query  = sprintf($query, $param["table"]
                                , $param["table"]
                                , $param["order_common_seqno"]);

        return $conn->Execute($query);
    }
}
?>
