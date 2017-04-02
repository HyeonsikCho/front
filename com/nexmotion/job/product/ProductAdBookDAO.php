<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/ProductCommonDAO.php');

class ProductAdBookDAO extends ProductCommonDAO {
    function __construct() {
    }

    /**
     * @brief 책자 제본 html 생성
     *
     * @param $conn  = connection identifier
     * @param $dvs   = 가져올 정보 구분값
     * @param $param = 검색조건 파라미터
     * @param &$price_info_arr = 가격검색용 정보저장 배열
     *
     * @return option html
     */
    function selectBindingHtml($conn, $dvs, $param, &$price_info_arr) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $default = $param["default"];

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT ";
        if ($dvs === "depth1") {
            $query .= "DISTINCT A.depth1";
        } else if ($dvs === "depth2") {
            $query .= " A.depth2";
            $query .= ",B.mpcode";
        }

        $query .= "\n   FROM  prdt_after AS A";
        $query .= "\n        ,cate_after AS B";

        $query .= "\n  WHERE  A.prdt_after_seqno = B.prdt_after_seqno";
        $query .= "\n    AND  A.after_name    = '제본'";
        $query .= "\n    AND  B.cate_sortcode = %s";
        if ($this->blankParameterCheck($param, "depth1")) {
            $query .= "\n    AND  A.depth1 = " . $param["depth1"];
        }
        if ($this->blankParameterCheck($param, "depth2")) {
            $query .= "\n    AND  A.depth2 = " . $param["depth2"];
        }
        if ($this->blankParameterCheck($param, "size")) {
            $query .= "\n    AND  B.size = " . $param["size"];
        }

        $query  = sprintf($query, $param["cate_sortcode"]);

        $rs = $conn->Execute($query);

        $temp = array(
            "dvs"     => $dvs,
            "default" => $default
        );

        return makeBindingOptionHtml($rs, $temp, $price_info_arr);
    }
}
?>
