<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/ProductCommonDAO.php');

class ProductSheetCutDAO extends ProductCommonDAO {
    function __construct() {
    }

    /**
     * @brief 카테고리 규격명만 검색
     *
     * @param $conn           = connection identifier
     * @param $param          = 검색조건 파라미터
     * @param $price_info_arr = 가격검색용 정보저장 배열
     * @param $affil_yn       = 사이즈별 계열 표시 여부
     * @param $pos_yn         = 사이즈별 자리수 표시 여부
     * @param $size_typ_yn    = 사이즈 타입명 노출여부
     *
     * @return option html
     */
    function selectCateStanNameHtml($conn,
                                    $param,
                                    &$price_info_arr,
                                    $pos_yn,
                                    $affil_yn,
                                    $size_typ_yn) {
        $cate_sortcode = $param["cate_sortcode"];

        $default_arr = ProductDefaultSel::DEFAULT_SEL[$cate_sortcode];

        $default = $default_arr["size"];
        if (empty($default) === true) {
            $default = $param["default"];
        }
        if (empty($default) === true) {
            $default = true;
        }

        $pos_num_arr = 0;
        if ($pos_yn === true) {
            $pos_num_arr = PrdtDefaultInfo::POSITION_NUMBER[$cate_sortcode];
        }

        $except_arr = array("cate_mpcode" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = "\n SELECT  DISTINCT A.name";
        $query .= "\n        ,A.name AS mpcode";
        $query .= "\n        ,A.work_wid_size";
        $query .= "\n        ,A.work_vert_size";
        $query .= "\n        ,A.cut_wid_size";
        $query .= "\n        ,A.cut_vert_size";
        $query .= "\n        ,A.affil";

        $query .= "\n   FROM  prdt_stan AS A";
        $query .= "\n        ,cate_stan AS B";

        $query .= "\n  WHERE  A.prdt_stan_seqno = B.prdt_stan_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";

        $query  = sprintf($query, $param["cate_sortcode"]);

        $rs = $conn->Execute($query);

        return makeCateSizeOption($rs,
                                  $default,
                                  $pos_num_arr,
                                  $price_info_arr,
                                  $affil_yn,
                                  $size_typ_yn);
    }

    /**
     * @brief 카테고리 규격명으로 사이즈 종류랑 맵핑코드 검색
     *
     * @param $conn        = db connection
     * @param $param       = 검색조건 파라미터
     * @param $temp        = 실제 규격 맵핑코드 가져오기 위한 임시배열
     * @param $pos_yn      = 사이즈별 자리수 표시 여부
     * @param $affil_yn    = 사이즈별 계열 표시 여부
     * @param $size_typ_yn = 사이즈 타입명 노출여부
     *
     * @return option html
     */
    function selectCateStanTypHtml($conn,
                                   $param,
                                   &$temp,
                                   $pos_yn,
                                   $affil_yn,
                                   $size_typ_yn) {
        $cate_sortcode = $param["cate_sortcode"];

        $pos_num_arr = 0;
        if ($pos_yn === true) {
            $pos_num_arr = PrdtDefaultInfo::POSITION_NUMBER[$cate_sortcode];
        }

        $except_arr = array("cate_mpcode" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = "\n SELECT  A.typ AS name";
        $query .= "\n        ,B.mpcode";

        $query .= "\n   FROM  prdt_stan AS A";
        $query .= "\n        ,cate_stan AS B";

        $query .= "\n  WHERE  A.prdt_stan_seqno = B.prdt_stan_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";
        $query .= "\n    AND  A.name          = %s";

        /*
        if ($this->blankParameterCheck($param, "cate_mpcode")) {
            $query .= "\n    AND  B.mpcode IN (";
            $query .= $param["cate_mpcode"];
            $query .= ')';
        }
        */

        $query  = sprintf($query, $param["cate_sortcode"]
                                , $param["stan_name"]);

        $rs = $conn->Execute($query);

        return makeCateSizeOption($rs,
                                  true,
                                  $pos_num_arr,
                                  $temp,
                                  $affil_yn,
                                  $size_typ_yn);
    }
}
?>
