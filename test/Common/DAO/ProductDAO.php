<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/common/MakeCommonHtml.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/CommonDAO.php');

/*! 공통 DAO Class */
class ProductDAO extends CommonDAO {

    function __construct() {
    }

    /////////////////종이
    /**
     * @brief 상품 확정형 가격 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 중분류코드
     * $param["paper_mpcode"] = 카테고리 종이 맵핑코드
     * $param["print_mpcode"] = 카테고리 인쇄 맵핑코드
     * $param["stan_mpcode"] = 카테고리 규격 맵핑코드
     * $param["amt"] = 수량
     * $param["table_name"] = 가격 테이블명
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 신규가격
     */
    function selectPrdtPlyPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }
        $temp = array();
        $temp["col"]   = "new_price";
        $temp["table"] = $param["table_name"];
        $temp["where"]["cate_sortcode"]     = $param["cate_sortcode"];
        $temp["where"]["cate_paper_mpcode"] = $param["paper_mpcode"];
        $temp["where"]["cate_print_mpcode"] = $param["print_mpcode"];
        $temp["where"]["cate_stan_mpcode"]  = $param["stan_mpcode"];
        $temp["where"]["amt"]               = $param["amt"];
		$rs = $this->selectData($conn, $temp);
        return $rs->fields["new_price"];
    }

    function selectCatePrintMpcode($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  B.mpcode";

        $query .= "\n   FROM  prdt_print AS A";
        $query .= "\n        ,cate_print AS B";

        $query .= "\n  WHERE  A.prdt_print_seqno = B.prdt_print_seqno";
        $query .= "\n    AND  B.cate_sortcode = %s";
        $query .= "\n    AND  A.name = %s";
        if ($this->blankParameterCheck($param, "purp")) {
            $query .= "\n AND  A.purp_dvs = " . $param["purp"];
        }
        if ($this->blankParameterCheck($param, "side_dvs")) {
            $query .= "\n AND  A.side_dvs = " . $param["side_dvs"];
        }

        $query  = sprintf($query, $param["cate_sortcode"]
                                , $param["tmpt"]);

        return $conn->Execute($query);
    }


//////////////////////////옵션
/**
     * @brief 카테고리 옵션 맵핑코드/기준수량 검색
     *
     * @detail $param["cate_sortcode"] = 카테고리 분류코드
     * $param["opt_name"] = 후공정명
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateOptInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  B.mpcode";
        $query .= "\n   FROM  prdt_opt AS A";
        $query .= "\n        ,cate_opt AS B";
        $query .= "\n  WHERE  A.prdt_opt_seqno = B.prdt_opt_seqno";
        $query .= "\n    AND  A.opt_name = %s";
        $query .= "\n    AND  B.cate_sortcode = %s";

        $query  = sprintf($query, $param["name"]
                                , $param["cate_sortcode"]);

        return $conn->Execute($query);
    }

/**
     * @brief 옵션 가격 검색
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateOptPriceList($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  A.cate_opt_price_seqno AS price_seqno";
        $query .= "\n        ,A.amt";
        $query .= "\n        ,A.basic_price";
        $query .= "\n        ,A.sell_rate";
        $query .= "\n        ,A.sell_aplc_price";
        $query .= "\n        ,A.sell_price ";

        $query .= "\n   FROM  cate_opt_price AS A";

        $query .= "\n  WHERE  1 = 1";

        if ($this->blankParameterCheck($param, "opt_mpcode")) {
            $query .= "\n    AND  A.cate_opt_mpcode = ";
            $query .= $param["opt_mpcode"];
        }
        if ($this->blankParameterCheck($param, "sell_site")) {
            $query .= "\n    AND  A.cpn_admin_seqno   = ";
            $query .= $param["sell_site"];
        }
        if ($this->blankParameterCheck($param, "price_seqno")) {
            $query .= "\n    AND  A.cate_opt_price_seqno = ";
            $query .= $param["price_seqno"];
        }
		if ($this->blankParameterCheck($param, "amt")) {
            $query .= "\n    AND  A.amt >= ";
            $query .= $param["amt"];
			$query .= "\n    order by A.amt desc limit 1";
        }

		$rs = $conn->Execute($query);

		if($rs->fields['sell_price'] != '') {
			return $rs;
		} else {
			$query  = "\n SELECT  A.cate_opt_price_seqno AS price_seqno";
			$query .= "\n        ,A.amt";
			$query .= "\n        ,A.basic_price";
			$query .= "\n        ,A.sell_rate";
			$query .= "\n        ,A.sell_aplc_price";
			$query .= "\n        ,A.sell_price ";

	        $query .= "\n   FROM  cate_opt_price AS A";

	        $query .= "\n  WHERE  1 = 1";

	        if ($this->blankParameterCheck($param, "opt_mpcode")) {
	            $query .= "\n    AND  A.cate_opt_mpcode = ";
	            $query .= $param["opt_mpcode"];
	        }
	        if ($this->blankParameterCheck($param, "sell_site")) {
	            $query .= "\n    AND  A.cpn_admin_seqno   = ";
	            $query .= $param["sell_site"];
	        }
	        if ($this->blankParameterCheck($param, "price_seqno")) {
	            $query .= "\n    AND  A.cate_opt_price_seqno = ";
	            $query .= $param["price_seqno"];
	        }
			if ($this->blankParameterCheck($param, "amt")) {
	            $query .= "\n    AND  A.amt < ";
	            $query .= $param["amt"];
				$query .= "\n    order by A.amt asc limit 1";
	        }
		}
        return $conn->Execute($query);
    }

///////////////////////////////// 후공정
 /**
     * @brief 후공정 가격 검색
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateAftPriceList($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  A.cate_after_price_seqno AS price_seqno";
        $query .= "\n        ,A.amt";
        $query .= "\n        ,A.basic_price";
        $query .= "\n        ,A.sell_rate";
        $query .= "\n        ,A.sell_aplc_price";
        $query .= "\n        ,A.sell_price ";

        $query .= "\n   FROM  cate_after_price AS A";

        $query .= "\n  WHERE  1 = 1";

        if ($this->blankParameterCheck($param, "after_mpcode")) {
            $query .= "\n    AND  A.cate_after_mpcode = ";
            $query .= $param["after_mpcode"];
        }
        if ($this->blankParameterCheck($param, "sell_site")) {
            $query .= "\n    AND  A.cpn_admin_seqno   = ";
            $query .= $param["sell_site"];
        }
        if ($this->blankParameterCheck($param, "price_seqno")) {
            $query .= "\n    AND  A.cate_after_price_seqno = ";
            $query .= $param["price_seqno"];
        }
		if ($this->blankParameterCheck($param, "amt")) {
            $query .= "\n    AND  A.amt >= ";
            $query .= $param["amt"];
			$query .= "\n    order by A.amt desc limit 1";
        }
		//$conn->debug=1;
        $rs = $conn->Execute($query);

		if($rs->fields['sell_price'] != '') {
			return $rs;
		} else {
			$query  = "\n SELECT  A.cate_after_price_seqno AS price_seqno";
			$query .= "\n        ,A.amt";
			$query .= "\n        ,A.basic_price";
			$query .= "\n        ,A.sell_rate";
			$query .= "\n        ,A.sell_aplc_price";
			$query .= "\n        ,A.sell_price ";

			$query .= "\n   FROM  cate_after_price AS A";

			$query .= "\n  WHERE  1 = 1";

			if ($this->blankParameterCheck($param, "after_mpcode")) {
			    $query .= "\n    AND  A.cate_after_mpcode = ";
			    $query .= $param["after_mpcode"];
			}
			if ($this->blankParameterCheck($param, "sell_site")) {
			    $query .= "\n    AND  A.cpn_admin_seqno   = ";
			    $query .= $param["sell_site"];
			}
			if ($this->blankParameterCheck($param, "price_seqno")) {
			    $query .= "\n    AND  A.cate_after_price_seqno = ";
			    $query .= $param["price_seqno"];
			}
			if ($this->blankParameterCheck($param, "amt")) {
	            $query .= "\n    AND  A.amt < ";
	            $query .= $param["amt"];
				$query .= "\n    order by A.amt asc limit 1";
	        }
		}

		return $conn->Execute($query);
    }


/**
     * @brief 카테고리 후공정 정보 검색
     *
     * @param $conn  = connection identifier
     * @param $dvs   = 정보 구분
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCateAftInfo($conn, $dvs, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        if ($dvs === "AFTER_NAME") {
            $query  = "\n SELECT  DISTINCT after_name";
        } else if ($dvs === "DEPTH1") {
            $query  = "\n SELECT  DISTINCT depth1";
        } else if ($dvs === "DEPTH2") {
            $query  = "\n SELECT  DISTINCT depth2";
        } else if ($dvs === "DEPTH3") {
        $query  = "\n SELECT  DISTINCT depth3";
        //카운트
        } else if ($dvs === "COUNT") {
            $query  = "\nSELECT  COUNT(*) AS cnt";
        } else if ($dvs === "SEQ") {
            $query  = "\n SELECT  A.after_name";
            $query .= "\n        ,A.depth1";
            $query .= "\n        ,A.depth2";
            $query .= "\n        ,A.depth3";
            $query .= "\n        ,A.crtr_unit";
            $query .= "\n        ,B.mpcode";
            $query .= "\n        ,B.basic_yn";
            $query .= "\n        ,B.cate_after_seqno";
            $query .= "\n        ,A.crtr_unit";
        }

        $query .= "\n   FROM  prdt_after AS A";
        $query .= "\n        ,cate_after AS B";

        $query .= "\n  WHERE  A.prdt_after_seqno = B.prdt_after_seqno";

        if ($this->blankParameterCheck($param, "cate_sortcode")) {
            $query .= "\n    AND  B.cate_sortcode = ";
            $query .= $param["cate_sortcode"];
        }
        //카테고리 분류코드 LIKE
        if ($this->blankParameterCheck($param ,"cate_sortcode_like")) {
            $val = substr($param["cate_sortcode_like"], 1, -1);
            $query .= "\n   AND  cate_sortcode LIKE '" . $val. "%'";
        }
        if ($this->blankParameterCheck($param, "basic_yn")) {
            $query .= "\n    AND  B.basic_yn = ";
            $query .= $param["basic_yn"];
        }
        if ($this->blankParameterCheck($param, "after_name")) {
            $query .= "\n    AND  A.after_name = ";
            $query .= $param["after_name"];
        }
        if ($this->blankParameterCheck($param, "depth1")) {
            $query .= "\n    AND  A.depth1 = ";
            $query .= $param["depth1"];
        }
        if ($this->blankParameterCheck($param, "depth2")) {
            $query .= "\n    AND  A.depth2 = ";
            $query .= $param["depth2"];
        }
        if ($this->blankParameterCheck($param, "depth3")) {
            $query .= "\n    AND  A.depth3 = ";
            $query .= $param["depth3"];
        }
        if ($this->blankParameterCheck($param ,"seqno")) {
            $query .= "\n   AND  cate_after_seqno = $param[seqno]";
        }

        if ($dvs === "SEQ" || $dvs === "COUNT") {

            if ($this->blankParameterCheck($param ,"search_txt")) {
                $val = substr($param["search_txt"], 1, -1);
                $query .= "\n   AND  after_name LIKE '%" . $val . "%'";
            }

            if ($this->blankParameterCheck($param ,"sorting")) {
                $sorting = substr($param["sorting"], 1, -1);
                $query .= "\n ORDER BY " . $sorting;

                if ($this->blankParameterCheck($param ,"sorting_type")) {
                    $sorting_type = substr($param["sorting_type"], 1, -1);
                    $query .= " " . $sorting_type;
                }
            }

            if ($this->blankParameterCheck($param ,"s_num")) {
                $s_num = substr($param["s_num"], 1, -1);
            }

            if ($this->blankParameterCheck($param ,"list_num")) {
                $list_num = substr($param["list_num"], 1, -1);
            }

            if ($this->blankParameterCheck($param ,"s_num") &&
                $this->blankParameterCheck($param ,"list_num") &&
                $dvs != "COUNT") {
                $query .= "\nLIMIT ". $s_num . ", " . $list_num;
            }
        }

        return $conn->Execute($query);
    }

}
?>
