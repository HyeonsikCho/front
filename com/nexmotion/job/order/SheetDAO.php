<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/OrderCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/order/SheetHTML.php');

class SheetDAO extends OrderCommonDAO {
    /**
     * @brief 카테고리 낱장여부 검색
     *
     * @param $conn  = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return 검색결과
     */
    function selectCateFlattypYn($conn, $cate_sortcode) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"] = "flattyp_yn";
        $temp["table"] = "cate";
        $temp["where"]["sortcode"] = $cate_sortcode;

        $rs = $this->selectData($conn, $temp);

        return $rs->fields["flattyp_yn"];
    }

    /**
     * @brief 운영체제에 따른 프로그램 정보 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectProTyp($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["table"] = "pro_typ";
        $temp["where"]["oper_sys"] = $param["os"];
        if ($this->blankParameterCheck($param, "pro")) {
            $temp["col"] = "pro_ver";
            $temp["where"]["pro"] = $param["pro"];
        } else {
            $temp["col"] = "DISTINCT pro";
        }

        $rs = $this->selectData($conn, $temp);

        return $rs;
    }

    /**
     * @brief 회원 주소 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMemberDlvr($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"]  = " dlvr_name";
        $temp["col"] .= ",recei";
        $temp["col"] .= ",tel_num";
        $temp["col"] .= ",cell_num";
        $temp["col"] .= ",zipcode";
        $temp["col"] .= ",addr";
        $temp["col"] .= ",addr_detail";
        $temp["table"] = "member_dlvr";
        $temp["where"]["member_seqno"] = $param["member_seqno"];

        if($param["basic_yn"] != NULL) {
            $temp["where"]["basic_yn"] = $param["basic_yn"];
        }
        $rs = $this->selectData($conn, $temp);

        return $rs;
    }

    /**
     * @brief 회원 주소 검색
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMembInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $query  = "\n SELECT  member_name, tel_num, cell_num, zipcode, addr, addr_detail";
        $query .= "\n   FROM  member";
        $query .= "\n  WHERE  member_seqno = '%s'";

        $query  = sprintf($query, $param["member_seqno"]);

        $rs = $conn->Execute($query);

        return $rs;
    }

	    /**
     * @brief 직배사용여부 체크
     */
    function selectDirectDlvrInfo($conn, $member_seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $member_seqno = $this->parameterEscape($conn, $member_seqno);

        $query  = "\n SELECT  direct_dlvr_yn";
        $query .= "\n   FROM  member";
        $query .= "\n  WHERE  member_seqno = %s";

        $query  = sprintf($query, $member_seqno);

        $rs = $conn->Execute($query);

        return $rs->fields;
    }

    /**
     * @brief 회사 주소 검색
     *
     * @param $conn  = connection identifier
     * @param $sell_site = 판매채널 일련번호
     *
     * @return 검색결과
     */
    function selectCpnAdmin($conn, $sell_site) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["col"]  = " sell_site      AS name";
        $temp["col"] .= ",repre_num      AS tel_num";
        $temp["col"] .= ",repre_cell_num AS cell_num";
        $temp["col"] .= ",zipcode";
        $temp["col"] .= ",addr";
        $temp["col"] .= ",addr_detail";
        $temp["table"] = "cpn_admin";
        $temp["where"]["cpn_admin_seqno"] = $sell_site;

        $rs = $this->selectData($conn, $temp);

        return $rs->fields;
    }

    /**
     * @brief 회원 배송주소 입력
     *
     * @param $conn  = connection identifier
     * @param $param = 입력값 파라미터
     *
     * @return 검색결과
     */
    function insertMemberDlvr($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n INSERT INTO member_dlvr(";
        $query .= "\n     member_seqno,";
        $query .= "\n     dlvr_name,";
        $query .= "\n     recei,";
        $query .= "\n     tel_num,";
        $query .= "\n     cell_num,";
        $query .= "\n     zipcode,";
        $query .= "\n     addr,";
        $query .= "\n     addr_detail,";
        $query .= "\n     regi_date,";
        $query .= "\n     basic_yn";
        $query .= "\n ) VALUES (";
        $query .= "\n     %s,";
        $query .= "\n     %s,";
        $query .= "\n     %s,";
        $query .= "\n     %s,";
        $query .= "\n     %s,";
        $query .= "\n     %s,";
        $query .= "\n     %s,";
        $query .= "\n     %s,";
        $query .= "\n     now(),";
        $query .= "\n     %s";
        $query .= "\n )";

        $query  = sprintf($query, $param["member_seqno"]
                                , $param["dlvr_name"]
                                , $param["recei"]
                                , $param["tel_num"]
                                , $param["cell_num"]
                                , $param["zipcode"]
                                , $param["addr"]
                                , $param["addr_detail"]
                                , $param["basic_yn"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 주문_파일 row 수 검색
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return row 수
     */
    function selectOrderFileCount($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT COUNT(1) AS cnt";
        $query .= "\n   FROM order_file AS A";
        $query .= "\n  WHERE  A.order_file_seqno   = %s";
        $query .= "\n    AND  A.order_common_seqno = %s";
        $query .= "\n    AND  A.member_seqno       = %s";

        $query  = sprintf($query, $param["file_seqno"]
                                , $param["order_seqno"]
                                , $param["member_seqno"]);

        $rs = $conn->Execute($query);

        return $rs->fields["cnt"];
    }

    /**
     * @brief 주문에 묶인 작업파일 검색
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건
     *
     * @return row 수
     */
    function selectOrderFileList($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $except_arr = array("order_common_seqno" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = "\n SELECT  A.order_file_seqno";
        $query .= "\n        ,A.file_path";
        $query .= "\n        ,A.save_file_name";
        $query .= "\n        ,A.origin_file_name";
        $query .= "\n        ,A.size";
        $query .= "\n   FROM  order_file AS A";
        $query .= "\n  WHERE  A.order_common_seqno IN (%s)";
        $query .= "\n    AND  A.dvs = %s";

        $query  = sprintf($query, $param["order_common_seqno"]
                                , $param["dvs"]);

        $rs = $conn->Execute($query);

        return $rs;
    }

    /**
     * @brief 주문_파일 정보 입력
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertOrderFile($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $temp = array();
        $temp["table"]                     = "order_file";
        $temp["col"]["dvs"]                = $param["dvs"];
        $temp["col"]["file_path"]          = $param["file_path"];
        $temp["col"]["save_file_name"]     = $param["save_file_name"];
        $temp["col"]["origin_file_name"]   = $param["origin_file_name"];
        $temp["col"]["size"]               = $param["size"];
        $temp["col"]["order_common_seqno"] = $param["order_common_seqno"];
        $temp["col"]["member_seqno"]       = $param["member_seqno"];

        return $this->insertData($conn, $temp);
    }

    /**
     * @brief 주문_파일 정보 수정
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateOrderFile($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n UPDATE  order_file";
        $query .= "\n    SET  save_file_name = %s";
        $query .= "\n        ,origin_file_name = %s";
        $query .= "\n        ,size = %s";
        $query .= "\n  WHERE  order_common_seqno = %s";
        $query .= "\n    AND  member_seqno = %s";

        $query  = sprintf($query, $param["save_file_name"]
                                , $param["origin_file_name"]
                                , $param["size"]
                                , $param["order_common_seqno"]
                                , $param["member_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 주문의 카테고리 분류코드 검색
     *
     * @param $conn      = connection identifier
     * @param $seqno_arr = 주문_공통_일련번호 배열
     *
     * @return 쿼리 실행결과
     */
    function selectOrderCateSortcode($conn, $seqno_arr) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $seqno = $this->arr2paramStr($conn, $seqno_arr);

        $query  = "\n SELECT  A.cate_sortcode";
        $query .= "\n   FROM  order_common AS A";
        $query .= "\n  WHERE  A.order_common_seqno IN (%s)";

        $query  = sprintf($query, $seqno);

        return $conn->Execute($query);
    }

    /**
     * @brief 사용 가능한 쿠폰 일련번호 검색
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 쿼리 실행결과
     */
    function selectValidCpSeqno($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  A.cp_seqno";

        $query .= "\n   FROM  cp_cate  AS A";
        $query .= "\n        ,cp_issue AS B";

        $query .= "\n  WHERE  A.cp_seqno = B.cp_seqno";
        $query .= "\n    AND  A.cp_cate_sortcode = %s";
        $query .= "\n    AND  B.member_seqno = %s";

        $query  = sprintf($query, $param["cate_sortcode"]
                                , $param["member_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 쿠폰 정보 검색
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 쿼리 실행결과
     */
    function selectValidCpInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $except_arr = array("cp_seqno" => true);

        $param = $this->parameterArrayEscape($conn, $param, $except_arr);

        $query  = "\n SELECT  cp_seqno";
        $query .= "\n        ,cp_name";
        $query .= "\n        ,val";
        $query .= "\n        ,unit";
        $query .= "\n        ,max_sale_price";
        $query .= "\n        ,public_period_start_date";
        $query .= "\n        ,cp_extinct_date";
        $query .= "\n        ,min_order_price";

        $query .= "\n   FROM  cp AS A";

        $query .= "\n  WHERE  A.cpn_admin_seqno = %s";
        $query .= "\n    AND  A.cp_seqno IN (%s)";

        $query  = sprintf($query, $param["sell_site"]
                                , $param["cp_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 택배비 조회시 도서지역인지 확인
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 쿼리 실행결과
     */
    function selectParcelCostPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $zipcode = $param['zipcode'];

        $query  = "\n SELECT  price";
        $query .= "\n   FROM  cjparcel_islands AS A";
        $query .= "\n  WHERE  A.zipcode = %s";

        $query  = sprintf($query, $param['zipcode']);

        return $conn->Execute($query);
    }

    /**
     * @brief 택배비 조회시 도서지역인지 확인
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 쿼리 실행결과
     */
    function selectAutobikeCostPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $zipcode = $param['zipcode'];

        $query  = "\n SELECT  autobike, damas, rabo, 1ton";
        $query .= "\n   FROM  autobike_dlvr_cost ";
        $query .= "\n  WHERE  new_zipcode = " . $zipcode;
        $query .= "\n ORDER BY autobike DESC ";

        return $conn->Execute($query);
    }


    /**
     * @brief 택배비 조회시 도서지역인지 확인
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 쿼리 실행결과
     */
    function selectIslandParcelCost($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $first_zipcode = substr($param['zipcode'], 1, 1);

        $query  = "\n SELECT  distinct price ";
        $query  .= "\n FROM  CJparcel_islands ";
        $query .= "\n  WHERE  new_zipcode = '%s'";

        $query  = sprintf($query, $param['zipcode']);

        return $conn->Execute($query);
    }


    /**
     * @brief 선택 된 주문 정보 확인
     *
     * @param $conn = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 쿼리 실행결과
     */
    function selectProductList($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $query  = "\n SELECT  A.order_detail";
        $query .= "\n        ,A.title";
        $query .= "\n        ,A.order_common_seqno";
        $query .= "\n        ,B.amt   AS s_amt";
        $query .= "\n        ,B.count AS s_count";
        $query .= "\n        ,C.amt   AS b_amt";
        $query .= "\n   FROM order_common AS A";
        $query .= "\n   LEFT OUTER JOIN order_detail AS B";
        $query .= "\n     ON A.order_common_seqno = B.order_common_seqno";
        $query .= "\n   LEFT OUTER JOIN order_detail_brochure AS C";
        $query .= "\n     ON A.order_common_seqno = C.order_common_seqno";
        $query .= "\n  WHERE  A.order_common_seqno IN (%s)";

        $query  = sprintf($query, $param['order_common_seqno']);

        return $conn->Execute($query);
    }
}
?>
