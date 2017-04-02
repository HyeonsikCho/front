<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/MypageCommonDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/mypage/OrderInfoHTML.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/util/FrontCommonUtil.php');

class OrderInfoDAO extends MypageCommonDAO {

    /**
     * @brief 주문취소시 UPDATE
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateOrderState($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    UPDATE  order_common";
        $query .= "\n       SET  order_state = %s";
        $query .= "\n           ,del_yn = 'Y'";
        $query .= "\n           ,eraser = %s";
        $query .= "\n     WHERE  order_common_seqno = %s";

        $query  = sprintf($query, $param["order_state"]
                                , $param["member_name"]
                                , $param["order_seqno"]);

        $result = $conn->Execute($query);

        if ($result === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 주문취소시 주문상세 상태값 UPDATE
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateOrderDetailState($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $table_name = $param["table_name"];

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    UPDATE  %s";
        $query .= "\n       SET  state = %s";
        $query .= "\n           ,del_yn = 'Y'";
        $query .= "\n     WHERE  order_common_seqno = %s";

        $query  = sprintf($query, $table_name
                                , $param["order_state"]
                                , $param["order_seqno"]);

        $result = $conn->Execute($query);

        if ($result === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 주문취소시 주문상세파일 상태값 UPDATE
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateOrderDetailCountFileState($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    UPDATE  order_detail_count_file";
        $query .= "\n       SET  state = %s";
        $query .= "\n     WHERE  order_detail_seqno = %s";

        $query  = sprintf($query, $param["order_state"]
                                , $param["order_detail_seqno"]);

        $result = $conn->Execute($query);

        if ($result === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 회원선입금 UPDATE(주문취소/ 재주문)
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateMemberPrepay($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $type = substr($param["type"], 1, -1);

        $query  = "\n    UPDATE  member";

        if ($type == "lack") {
            $query .= "\n       SET  prepay_price = 0";
            $query .= "\n           ,order_lack_price = ";
            $query .= $param["price"];

        } else {
            $query .= "\n       SET  prepay_price = " . $param["price"];
            $query .= "\n           ,own_point    = " . $param["point"];
        }

        $query .= "\n     WHERE  member_seqno = " . $param["member_seqno"];

        $result = $conn->Execute($query);

        if ($result === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 회원 이름 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMemberName($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  member_name";
        $query .= "\n      FROM  member";
        $query .= "\n     WHERE  member_seqno = %s";

        $query  = sprintf($query, $param["member_seqno"]);

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 주문상세 일련번호 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderDetailSeqno($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $table_name = $param["table_name"];

        $param = $this->parameterArrayEscape($conn, $param);
        $query .= "\n    SELECT  %s_seqno AS detail_seqno";
        $query .= "\n      FROM  %s";
        $query .= "\n     WHERE  order_common_seqno = %s";
        $query  = sprintf($query, $table_name
                                , $table_name
                                , $param["order_seqno"]);

        $result = $conn->Execute($query);

        return $result;
    }

    /**
     * @brief 주문 번호 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderNum($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $query .= "\n    SELECT  order_num";
        $query .= "\n      FROM  order_common";
        $query .= "\n     WHERE  order_common_seqno = " . $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;
    }

    /**
     * @brief 회원 선입금 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMemberPrepay($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  prepay_price";
        $query .= "\n           ,own_point";
        $query .= "\n           ,member_name";
        $query .= "\n      FROM  member";
        $query .= "\n     WHERE  member_seqno = %s";

        $query  = sprintf($query, $param["member_seqno"]);

        $result = $conn->Execute($query);

        return $result;

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

    /**
     * @brief 주문취소를 위한 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderInfo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  pay_price";
        $query .= "\n           ,use_point_price";
        $query .= "\n           ,member_seqno";
        $query .= "\n           ,order_state";
        $query .= "\n           ,order_num";
        $query .= "\n           ,order_common_seqno";
        $query .= "\n      FROM  order_common";
        $query .= "\n     WHERE  order_common_seqno = %s";

        $query  = sprintf($query, $param["order_seqno"]);

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 재주문을 위한 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
    function selectOrderRow($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT";
        $query .= "\n            order_state";
        $query .= "\n           ,oper_sys";
        $query .= "\n           ,pro";
        $query .= "\n           ,pro_ver";
        $query .= "\n           ,req_cont";
        $query .= "\n           ,basic_price";
        $query .= "\n           ,grade_sale_price";
        $query .= "\n           ,event_price";
        $query .= "\n           ,use_point_price";
        $query .= "\n           ,sell_price";
        $query .= "\n           ,cp_price";
        $query .= "\n           ,pay_price";
        $query .= "\n           ,order_regi_date";
        //$query .= "\n           ,stan_name";
        $query .= "\n           ,amt";
        $query .= "\n           ,amt_unit_dvs";
        $query .= "\n           ,member_seqno";
        $query .= "\n           ,mono_yn";
        $query .= "\n           ,claim_yn";
        $query .= "\n           ,order_detail";
        $query .= "\n           ,title";
        $query .= "\n           ,expec_weight";
        $query .= "\n           ,count";
        $query .= "\n           ,bun_group";
        $query .= "\n           ,receipt_regi_date";
        $query .= "\n           ,memo";
        $query .= "\n           ,cpn_admin_seqno";
        $query .= "\n           ,del_yn";
        $query .= "\n           ,point_use_yn";
        $query .= "\n           ,owncompany_img_use_yn";
        $query .= "\n           ,cate_sortcode";
        //$query .= "\n           ,after_use_yn";
        $query .= "\n           ,opt_use_yn";
        //$query .= "\n           ,print_tmpt_name";
        $query .= "\n           ,prdt_basic_info";
        $query .= "\n           ,prdt_add_info";
        $query .= "\n           ,prdt_price_info";
        $query .= "\n           ,bun_yn";
        $query .= "\n           ,prdt_pay_info";
 //       $query .= "\n           ,dlvr_way";
 //       $query .= "\n           ,dlvr_pay_way";
        $query .= "\n           ,pay_way";
 //       $query .= "\n           ,dlvr_price";
        $query .= "\n           ,add_after_price";
        $query .= "\n           ,add_opt_price";
        $query .= "\n           ,expenevid_req_yn";
        $query .= "\n           ,expenevid_dvs";
        $query .= "\n           ,expenevid_num";
        $query .= "\n           ,event_yn";
        $query .= "\n      FROM  order_common";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;
    }
     */

    /**
     * @brief 데이터 삽입 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"]["컬럼명"] = "데이터" (다중)<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
    function insertReorder($conn, $result, $param) {

        $param = $this->parameterArrayEscape($conn, $param);

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $frontUtil = new FrontCommonUtil();
        $order_num = $frontUtil->makeOrderNum();

        $query  = "\n INSERT INTO order_common (";
        $query .= "\n                          order_num";
        $query .= "\n                         ,order_state";
        $query .= "\n                         ,oper_sys";
        $query .= "\n                         ,pro";
        $query .= "\n                         ,pro_ver";
        $query .= "\n                         ,req_cont";
        $query .= "\n                         ,basic_price";
        $query .= "\n                         ,grade_sale_price";
        $query .= "\n                         ,event_price";
        $query .= "\n                         ,use_point_price";
        $query .= "\n                         ,sell_price";
        $query .= "\n                         ,cp_price";
        $query .= "\n                         ,pay_price";
        $query .= "\n                         ,order_regi_date";
        //$query .= "\n                         ,stan_name";
        $query .= "\n                         ,amt";
        $query .= "\n                         ,amt_unit_dvs";
        $query .= "\n                         ,member_seqno";
        $query .= "\n                         ,mono_yn";
        $query .= "\n                         ,claim_yn";
        $query .= "\n                         ,order_detail";
        $query .= "\n                         ,title";
        $query .= "\n                         ,expec_weight";
        $query .= "\n                         ,count";
        $query .= "\n                         ,bun_group";
        $query .= "\n                         ,memo";
        $query .= "\n                         ,cpn_admin_seqno";
        $query .= "\n                         ,del_yn";
        $query .= "\n                         ,point_use_yn";
        $query .= "\n                         ,owncompany_img_use_yn";
        $query .= "\n                         ,cate_sortcode";
        //$query .= "\n                         ,after_use_yn";
        $query .= "\n                         ,opt_use_yn";
        //$query .= "\n                         ,print_tmpt_name";
        $query .= "\n                         ,prdt_basic_info";
        $query .= "\n                         ,prdt_add_info";
        $query .= "\n                         ,prdt_price_info";
        $query .= "\n                         ,bun_yn";
        $query .= "\n                         ,prdt_pay_info";
//        $query .= "\n                         ,dlvr_way";
//        $query .= "\n                         ,dlvr_pay_way";
//        $query .= "\n                         ,dlvr_price";
        $query .= "\n                         ,add_after_price";
        $query .= "\n                         ,add_opt_price";
        $query .= "\n                         ,expenevid_req_yn";
        $query .= "\n                         ,expenevid_dvs";
        $query .= "\n                         ,expenevid_num";
        $query .= "\n                         ,event_yn";
        $query .= "\n                         ,pay_way";
        $query .= "\n) VALUES (";
        $query .= "\n           '" . $order_num . "'";
        $query .= "\n          ," . $param["state"];
        $query .= "\n          ,'" . $result->fields["oper_sys"] . "'";
        $query .= "\n          ,'" . $result->fields["pro"] . "'";
        $query .= "\n          ,'" . $result->fields["pro_ver"] . "'";
        $query .= "\n          ,'" . $result->fields["req_cont"] . "'";
        $query .= "\n          ,'" . $result->fields["basic_price"] . "'";
        $query .= "\n          ,'0'";
        $query .= "\n          ,'0'";
        $query .= "\n          ,'0'";
        $query .= "\n          ,'" . $result->fields["sell_price"] . "'";
        $query .= "\n          ,'0'";
        $query .= "\n          ," . $param["pay_price"];
        $query .= "\n          ,'" . date("Y-m-d H:i:s", time()) . "'";
        //$query .= "\n          ,'" . $result->fields["stan_name"] . "'";
        $query .= "\n          ,'" . $result->fields["amt"] . "'";
        $query .= "\n          ,'" . $result->fields["amt_unit_dvs"] . "'";
        $query .= "\n          ,'" . $result->fields["member_seqno"] . "'";
        $query .= "\n          ,'" . $result->fields["mono_yn"] . "'";
        $query .= "\n          ,'N'";
        $query .= "\n          ,'" . $result->fields["order_detail"] . "'";
        $query .= "\n          ,'" . $result->fields["title"] . "'";
        $query .= "\n          ,'" . $result->fields["expec_weight"] . "'";
        $query .= "\n          ,'" . $result->fields["count"] . "'";
        $query .= "\n          ,'" . $result->fields["bun_group"] . "'";
        $query .= "\n          ,'" . $result->fields["memo"] . "'";
        $query .= "\n          ,'" . $result->fields["cpn_admin_seqno"] . "'";
        $query .= "\n          ,'N'";
        $query .= "\n          ,'N'";
        $query .= "\n          ,'" . $result->fields["owncompany_img_use_yn"];
        $query .= "'";
        $query .= "\n          ,'" . $result->fields["cate_sortcode"] . "'";
        //$query .= "\n          ,'" . $result->fields["after_use_yn"] . "'";
        $query .= "\n          ,'" . $result->fields["opt_use_yn"] . "'";
        //$query .= "\n          ,'" . $result->fields["print_tmpt_name"] . "'";
        $query .= "\n          ,'" . $result->fields["prdt_basic_info"] . "'";
        $query .= "\n          ,'" . $result->fields["prdt_add_info"] . "'";
        $query .= "\n          ,'" . $result->fields["prdt_price_info"] . "'";
        $query .= "\n          ,'" . $result->fields["bun_yn"] . "'";
        $query .= "\n          ,'" . $result->fields["prdt_pay_info"] . "'";
//        $query .= "\n          ,'" . $result->fields["dlvr_way"] . "'";
//        $query .= "\n          ,'" . $result->fields["dlvr_pay_way"] . "'";
//        $query .= "\n          ,'" . $result->fields["dlvr_price"] . "'";
        $query .= "\n          ,'" . $result->fields["add_after_price"] . "'";
        $query .= "\n          ,'" . $result->fields["add_opt_price"] . "'";
        $query .= "\n          ,'N'";
        $query .= "\n          ,'" . $result->fields["expenevid_dvs"] . "'";
        $query .= "\n          ,'" . $result->fields["expenevid_num"] . "'";
        $query .= "\n          ,'N'";
        $query .= "\n          ,'" . $result->fields["pay_way"] . "'";
        $query .= "\n )";

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            return false;
        } else {
            return true;
        }
    }
     */

    /**
     * @brief 주문파일을 위한 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderFileSet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  dvs";
        $query .= "\n           ,file_path";
        $query .= "\n           ,save_file_name";
        $query .= "\n           ,origin_file_name";
        $query .= "\n           ,size";
        $query .= "\n           ,order_file_seqno";
        $query .= "\n      FROM  order_file";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 재주문 주문파일을 INSERT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertOrderFile($conn, $result, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $check = TRUE;

        while ($result && !$result->EOF) {

            $query  = "\n INSERT INTO order_file (";
            $query .= "\n                          dvs";
            $query .= "\n                         ,file_path";
            $query .= "\n                         ,save_file_name";
            $query .= "\n                         ,origin_file_name";
            $query .= "\n                         ,size";
            $query .= "\n                         ,order_common_seqno";
            $query .= "\n) VALUES (";
            $query .= "\n            '" . $result->fields["dvs"] . "'";
            $query .= "\n           ,'" . $result->fields["file_path"] . "'";
            $query .= "\n           ,'" . $result->fields["save_file_name"];
            $query .= "'";
            $query .= "\n           ,'" . $result->fields["origin_file_name"];
            $query .= "'";
            $query .= "\n           ,'" . $result->fields["size"] . "'";
            $query .= "\n           , " . $param["reorder_seqno"];
            $query .= "\n )";

            $resultSet = $conn->Execute($query);
            if ($resultSet === false) {
                $check = false;
            }

            $result->moveNext();
        }

        return $check;

    }

    /**
     * @brief 주문상세를 위한 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderDetailSet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  typ";
        $query .= "\n           ,page_amt";
        $query .= "\n           ,cate_beforeside_print_mpcode";
        $query .= "\n           ,cate_beforeside_add_print_mpcode";
        $query .= "\n           ,cate_aftside_print_mpcode";
        $query .= "\n           ,cate_aftside_add_print_mpcode";
        $query .= "\n           ,cate_paper_mpcode";
        $query .= "\n           ,spc_dscr";
        $query .= "\n           ,order_detail_dvs_num";
        $query .= "\n           ,work_size_wid";
        $query .= "\n           ,work_size_vert";
        $query .= "\n           ,cut_size_wid";
        $query .= "\n           ,cut_size_vert";
        $query .= "\n           ,tomson_size_wid";
        $query .= "\n           ,tomson_size_vert";
        $query .= "\n           ,cut_front_wing_size_wid";
        $query .= "\n           ,cut_front_wing_size_vert";
        $query .= "\n           ,work_front_wing_size_wid";
        $query .= "\n           ,work_front_wing_size_vert";
        $query .= "\n           ,cut_rear_wing_size_wid";
        $query .= "\n           ,cut_rear_wing_size_vert";
        $query .= "\n           ,work_rear_wing_size_wid";
        $query .= "\n           ,work_rear_wing_size_vert";
        $query .= "\n           ,seneca_size";
        $query .= "\n      FROM  order_detail";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 재주문 주문상세 INSERT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertOrderDetail($conn, $result, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $order_num = substr($param["order_num"], 1, -1);

        $check = TRUE;
        $idx = 1;

        while ($result && !$result->EOF) {

            $query  = "\n INSERT INTO order_detail (";
            $query .= "\n                          cate_beforeside_print_mpcode";
            $query .= "\n                         ,cate_beforeside_add_print_mpcode";
            $query .= "\n                         ,cate_aftside_print_mpcode";
            $query .= "\n                         ,cate_aftside_add_print_mpcode";
            $query .= "\n                         ,typ";
            $query .= "\n                         ,page_amt";
            $query .= "\n                         ,cate_paper_mpcode";
            $query .= "\n                         ,spc_dscr";
            $query .= "\n                         ,order_detail_dvs_num";
            $query .= "\n                         ,work_size_wid";
            $query .= "\n                         ,work_size_vert";
            $query .= "\n                         ,cut_size_wid";
            $query .= "\n                         ,cut_size_vert";
            $query .= "\n                         ,tomson_size_wid";
            $query .= "\n                         ,tomson_size_vert";
            $query .= "\n                         ,cut_front_wing_size_wid";
            $query .= "\n                         ,cut_front_wing_size_vert";
            $query .= "\n                         ,work_front_wing_size_wid";
            $query .= "\n                         ,work_front_wing_size_vert";
            $query .= "\n                         ,cut_rear_wing_size_wid";
            $query .= "\n                         ,cut_rear_wing_size_vert";
            $query .= "\n                         ,work_rear_wing_size_wid";
            $query .= "\n                         ,work_rear_wing_size_vert";
            $query .= "\n                         ,seneca_size";
            $query .= "\n                         ,order_detail_num";
            $query .= "\n                         ,order_common_seqno";
            $query .= "\n                         ,del_yn";
            $query .= "\n) VALUES (";
            $query .= "\n          '" . $result->fields["cate_beforeside_print_mpcode"] . "'";
            $query .= "\n         ,'" . $result->fields["cate_beforeside_add_print_mpcode"] . "'";
            $query .= "\n         ,'" . $result->fields["cate_aftside_print_mpcode"] . "'";
            $query .= "\n         ,'" . $result->fields["cate_aftside_add_print_mpcode"] . "'";
            $query .= "\n         ,'" . $result->fields["typ"] . "'";
            $query .= "\n         ,'" . $result->fields["page_amt"] . "'";
            $query .= "\n         ,'" . $result->fields["cate_paper_mpcode"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["spc_dscr"] . "'";
            $query .= "\n         ,'" . $result->fields["detail_num"] . "'";
            $query .= "\n         ,'" . $result->fields["work_size_wid"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["work_size_vert"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["cut_size_wid"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["cut_size_vert"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["tomson_size_wid"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["tomson_size_vert"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["cut_front_wing_size_wid"] . "'";
            $query .= "\n         ,'" . $result->fields["cut_front_wing_size_vert"] . "'";
            $query .= "\n         ,'" . $result->fields["work_front_wing_size_wid"] . "'";
            $query .= "\n         ,'" . $result->fields["work_front_wing_size_vert"] . "'";
            $query .= "\n         ,'" . $result->fields["cut_rear_wing_size_wid"] . "'";
            $query .= "\n         ,'" . $result->fields["cut_rear_wing_size_vert"] . "'";
            $query .= "\n         ,'" . $result->fields["work_rear_wing_size_wid"] . "'";
            $query .= "\n         ,'" . $result->fields["work_rear_wing_size_vert"] . "'";
            $query .= "\n         ,'" . $result->fields["seneca_size"] . "'";
            $query .= "\n         ,'" . $order_num . $idx . "'";
            $query .= "\n         , " . $param["reorder_seqno"];
            $query .= "\n         , 'N'";
            $query .= "\n )";

            $resultSet = $conn->Execute($query);
            if ($resultSet === false) {
                $check = false;
            }

            $result->moveNext();
            $idx++;
        }

        return $check;

    }

    /**
     * @brief 재주문을 위한 후공정 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderAfterSet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  basic_yn";
        $query .= "\n           ,depth1";
        $query .= "\n           ,depth2";
        $query .= "\n           ,depth3";
        $query .= "\n           ,price";
        $query .= "\n           ,after_name";
        $query .= "\n           ,seq";
        $query .= "\n           ,detail";
        $query .= "\n      FROM  order_after_history";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 재주문 후공정 INSERT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertOrderAfter($conn, $result, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $check = TRUE;

        while ($result && !$result->EOF) {

            $query  = "\n INSERT INTO ";
            $query .= "\n order_after_history (";
            $query .= "\n                       basic_yn";
            $query .= "\n                      ,depth1";
            $query .= "\n                      ,depth2";
            $query .= "\n                      ,depth3";
            $query .= "\n                      ,price";
            $query .= "\n                      ,after_name";
            $query .= "\n                      ,seq";
            $query .= "\n                      ,detail";
            $query .= "\n                      ,order_common_seqno";
            $query .= "\n) VALUES (";
            $query .= "\n            '" . $result->fields["basic_yn"] . "'";
            $query .= "\n           ,'" . $result->fields["depth1"] . "'";
            $query .= "\n           ,'" . $result->fields["depth2"] . "'";
            $query .= "\n           ,'" . $result->fields["depth3"] . "'";
            $query .= "\n           ,'" . $result->fields["price"] . "'";
            $query .= "\n           ,'" . $result->fields["after_name"] . "'";
            $query .= "\n           ,'" . $result->fields["seq"] . "'";
            $query .= "\n           ,'" . $result->fields["detail"] . "'";
            $query .= "\n           , " . $param["reorder_seqno"];
            $query .= "\n )";


            $resultSet = $conn->Execute($query);
            if ($resultSet === false) {
                $check = false;
            }

            $result->moveNext();
        }

        return $check;

    }

    /**
     * @brief 재주문을 위한 옵션 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderOptSet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  basic_yn";
        $query .= "\n           ,opt_name";
        $query .= "\n           ,depth1";
        $query .= "\n           ,depth2";
        $query .= "\n           ,depth3";
        $query .= "\n           ,price";
        $query .= "\n      FROM  order_opt_history";
        $query .= "\n     WHERE  order_common_seqno = ";
        $query .= $param["order_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 재주문 옵션 INSERT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function insertOrderOpt($conn, $result, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $check = TRUE;

        while ($result && !$result->EOF) {

            $query  = "\n INSERT INTO ";
            $query .= "\n order_opt_history (";
            $query .= "\n                       basic_yn";
            $query .= "\n                      ,depth1";
            $query .= "\n                      ,depth2";
            $query .= "\n                      ,depth3";
            $query .= "\n                      ,price";
            $query .= "\n                      ,opt_name";
            $query .= "\n                      ,order_common_seqno";
            $query .= "\n) VALUES (";
            $query .= "\n            '" . $result->fields["basic_yn"] . "'";
            $query .= "\n           ,'" . $result->fields["depth1"] . "'";
            $query .= "\n           ,'" . $result->fields["depth2"] . "'";
            $query .= "\n           ,'" . $result->fields["depth3"] . "'";
            $query .= "\n           ,'" . $result->fields["price"] . "'";
            $query .= "\n           ,'" . $result->fields["opt_name"] . "'";
            $query .= "\n           , " . $param["reorder_seqno"];
            $query .= "\n )";

            $resultSet = $conn->Execute($query);
            if ($resultSet === false) {
                $check = false;
            }

            $result->moveNext();
        }

        return $check;

    }


    /**
     * @brief 주문 메모 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectOrderMemo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  cust_memo";
        $query .= "\n      FROM  order_common";
        $query .= "\n     WHERE  order_common_seqno = %s";
        $query .= "\n       AND  member_seqno = %s";

        $query  = sprintf($query, $param["order_seqno"]
                                , $param["member_seqno"]);

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 주문메모 UPDATE
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateOrderMemo($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $query  = "\n    UPDATE  order_common";
        $query .= "\n       SET  cust_memo = " . $param["cust_memo"];
        $query .= "\n     WHERE  order_common_seqno = " . $param["order_seqno"];

        $result = $conn->Execute($query);

        if ($result === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 회원 등급 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMemberInfo($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  grade";
        $query .= "\n           ,prepay_price";
        $query .= "\n           ,order_lack_price";
        $query .= "\n           ,member_typ";
        $query .= "\n      FROM  member";
        $query .= "\n     WHERE  member_seqno = ";
        $query .= $param["member_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 등급별 할인율 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectGradeRate($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  sales_sale_rate";
        $query .= "\n      FROM  member_grade_policy";
        $query .= "\n     WHERE  grade = ";
        $query .= $param["grade"];

        $result = $conn->Execute($query);

        return $result;

    }

    /**
     * @brief 관심상품 리스트
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectPrdtList($conn, $param) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $dvs = substr($param["dvs"], 1, -1);

        if ($dvs === "COUNT") {
            $query  = "\n SELECT  COUNT(*) AS cnt";
        } else {
            $query  = "\n    SELECT  A.regi_date";
            $query .= "\n           ,A.cate_sortcode";
            $query .= "\n           ,B.cate_name";
            $query .= "\n           ,A.interest_prdt_seqno";
            /*
            $query .= "\n           ,A.order_detail";
            $query .= "\n           ,A.amt";
            $query .= "\n           ,A.count";
            $query .= "\n           ,A.expec_weight";
            $query .= "\n           ,B.file_path";
            $query .= "\n           ,B.save_file_name";
            */

        }
        $query .= "\n      FROM  interest_prdt A";
        $query .= "\n           ,cate B";
        //$query .= "\n           ,cate_photo B";
        $query .= "\n     WHERE A.cate_sortcode = B.sortcode";
        $query .= "\n       AND A.member_seqno = " . $param["seqno"];
        //$query .= "\n       AND B.seq = '1'";

        //주문상세 검색
        if ($this->blankParameterCheck($param ,"order_detail")) {
            $detail = substr($param["order_detail"], 1, -1);
            $query .= "\n    AND  A.order_detail LIKE '%" . $detail . "%'";
        }

        //등록일
        /*
        if ($this->blankParameterCheck($param ,"from")) {

            $from = substr($param["from"], 1, -1);

            $query .="\n     AND  A.regi_date >= '" . $from;
            $query .=" 00:00:00'";
        }

        if ($this->blankParameterCheck($param ,"to")) {

            $to = substr($param["to"], 1, -1);

            $query .="\n     AND  A.regi_date <= '" . $to;
            $query .=" 23:59:59'";
        }
        */

        $s_num = substr($param["s_num"], 1, -1);
        $list_num = substr($param["list_num"], 1, -1);

        if ($dvs == "SEQ") {

            $query .= "\n ORDER BY A.regi_date DESC ";
            $query .= "\n LIMIT ". $s_num . ", " . $list_num;

        }

        return $conn->Execute($query);
    }

    /**
     * @brief 관심상품에 해당하는 후공정 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
    function selectPrdtAfter($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  after_name";
        $query .= "\n           ,depth1";
        $query .= "\n           ,depth2";
        $query .= "\n           ,depth3";
        $query .= "\n      FROM  interest_prdt_after_history";
        $query .= "\n     WHERE  interest_prdt_seqno = ";
        $query .= $param["prdt_seqno"];

        $result = $conn->Execute($query);

        return $result;
    }
     */

    /**
     * @brief 관심상품을 정보 SELECT
     *        위한 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
    function selectPrdt($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT";
        $query .= "\n            member_seqno";
        $query .= "\n           ,order_detail";
        $query .= "\n           ,mono_yn";
        $query .= "\n           ,req_cont";
        $query .= "\n           ,title";
        $query .= "\n           ,stan_name";
        $query .= "\n           ,amt";
        $query .= "\n           ,count";
        $query .= "\n           ,expec_weight";
        $query .= "\n           ,amt_unit_dvs";
        $query .= "\n           ,memo";
        $query .= "\n           ,owncompany_img_use_yn";
        $query .= "\n           ,pay_way";
        $query .= "\n           ,cate_sortcode";
        $query .= "\n           ,after_use_yn";
        $query .= "\n           ,opt_use_yn";
        $query .= "\n           ,print_tmpt_name";
        $query .= "\n           ,cpn_admin_seqno";
        $query .= "\n      FROM  interest_prdt";
        $query .= "\n     WHERE  interest_prdt_seqno = ";
        $query .= $param["prdt_seqno"];

        $result = $conn->Execute($query);

        return $result;
    }
     */

    /**
     * @brief 관심상품을 장바구니에 INSERT
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"]["컬럼명"] = "데이터" (다중)<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
    function insertShb($conn, $result) {

        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $frontUtil = new FrontCommonUtil();
        $order_num = $frontUtil->makeOrderNum();

        $query  = "\n INSERT INTO order_common (";
        $query .= "\n             order_num";
        $query .= "\n            ,member_seqno";
        $query .= "\n            ,order_detail";
        $query .= "\n            ,mono_yn";
        $query .= "\n            ,req_cont";
        $query .= "\n            ,title";
        $query .= "\n            ,stan_name";
        $query .= "\n            ,amt";
        $query .= "\n            ,count";
        $query .= "\n            ,expec_weight";
        $query .= "\n            ,amt_unit_dvs";
        $query .= "\n            ,memo";
        $query .= "\n            ,owncompany_img_use_yn";
        $query .= "\n            ,pay_way";
        $query .= "\n            ,cate_sortcode";
        $query .= "\n            ,after_use_yn";
        $query .= "\n            ,opt_use_yn";
        $query .= "\n            ,print_tmpt_name";
        $query .= "\n            ,cpn_admin_seqno";
        $query .= "\n            ,order_regi_date";
        $query .= "\n            ,order_state";
        $query .= "\n) VALUES (";
        $query .= "\n           '" . $order_num . "'";
        $query .= "\n          ,'" . $result->fields["member_seqno"] . "'";
        $query .= "\n          ,'" . $result->fields["order_detail"] . "'";
        $query .= "\n          ,'" . $result->fields["mono_yn"] . "'";
        $query .= "\n          ,'" . $result->fields["req_cont"] . "'";
        $query .= "\n          ,'" . $result->fields["title"] . "'";
        $query .= "\n          ,'" . $result->fields["stan_name"] . "'";
        $query .= "\n          ,'" . $result->fields["amt"] . "'";
        $query .= "\n          ,'" . $result->fields["count"] . "'";
        $query .= "\n          ,'" . $result->fields["expec_weight"] . "'";
        $query .= "\n          ,'" . $result->fields["amt_unit_dvs"] . "'";
        $query .= "\n          ,'" . $result->fields["memo"] . "'";
        $query .= "\n          ,'" . $result->fields["owncompany_img_use_yn"];
        $query .= "'";
        $query .= "\n          ,'" . $result->fields["pay_way"] . "'";
        $query .= "\n          ,'" . $result->fields["cate_sortcode"] . "'";
        $query .= "\n          ,'" . $result->fields["after_use_yn"] . "'";
        $query .= "\n          ,'" . $result->fields["opt_use_yn"] . "'";
        $query .= "\n          ,'" . $result->fields["print_tmpt_name"] . "'";
        $query .= "\n          ,'" . $result->fields["cpn_admin_seqno"] . "'";
        $query .= "\n          ,'" . date("Y-m-d H:i:s", time()) . "'";
        $query .= "\n          ,'110'";
        $query .= "\n )";

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            return false;
        } else {
            return true;
        }
    }
     */

    /**
     * @brief 관심상품 후공정 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
    function selectPrdtAfterSet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  basic_yn";
        $query .= "\n           ,depth1";
        $query .= "\n           ,depth2";
        $query .= "\n           ,depth3";
        $query .= "\n           ,price";
        $query .= "\n           ,after_name";
        $query .= "\n           ,seq";
        $query .= "\n           ,detail";
        $query .= "\n      FROM  interest_prdt_after_history";
        $query .= "\n     WHERE  interest_prdt_seqno = ";
        $query .= $param["prdt_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }
     */

    /**
     * @brief 장바구니에 관심상품 옵션 정보 INSERT
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
    function insertShbAfter($conn, $result, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $check = TRUE;

        while ($result && !$result->EOF) {

            $query  = "\n INSERT INTO ";
            $query .= "\n order_after_history (";
            $query .= "\n                       basic_yn";
            $query .= "\n                      ,depth1";
            $query .= "\n                      ,depth2";
            $query .= "\n                      ,depth3";
            $query .= "\n                      ,price";
            $query .= "\n                      ,after_name";
            $query .= "\n                      ,seq";
            $query .= "\n                      ,detail";
            $query .= "\n                      ,order_common_seqno";
            $query .= "\n) VALUES (";
            $query .= "\n            '" . $result->fields["basic_yn"] . "'";
            $query .= "\n           ,'" . $result->fields["depth1"] . "'";
            $query .= "\n           ,'" . $result->fields["depth2"] . "'";
            $query .= "\n           ,'" . $result->fields["depth3"] . "'";
            $query .= "\n           ,'" . $result->fields["price"] . "'";
            $query .= "\n           ,'" . $result->fields["after_name"] . "'";
            $query .= "\n           ,'" . $result->fields["seq"] . "'";
            $query .= "\n           ,'" . $result->fields["detail"] . "'";
            $query .= "\n           , " . $param["shb_seqno"];
            $query .= "\n )";


            $resultSet = $conn->Execute($query);
            if ($resultSet === false) {
                $check = false;
            }

            $result->moveNext();
        }

        return $check;

    }
     */

    /**
     * @brief 관심상품 옵션 정보 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
    function selectPrdtOptSet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  basic_yn";
        $query .= "\n           ,depth1";
        $query .= "\n           ,depth2";
        $query .= "\n           ,depth3";
        $query .= "\n           ,price";
        $query .= "\n           ,opt_name";
        $query .= "\n      FROM  interest_prdt_opt_history";
        $query .= "\n     WHERE  interest_prdt_seqno = ";
        $query .= $param["prdt_seqno"];

        $result = $conn->Execute($query);

        return $result;

    }
     */

    /**
     * @brief 장바구니에 관심상품 옵션 INSERT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
    function insertShbOpt($conn, $result, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $check = TRUE;

        while ($result && !$result->EOF) {

            $query  = "\n INSERT INTO ";
            $query .= "\n order_opt_history (";
            $query .= "\n                       basic_yn";
            $query .= "\n                      ,depth1";
            $query .= "\n                      ,depth2";
            $query .= "\n                      ,depth3";
            $query .= "\n                      ,price";
            $query .= "\n                      ,opt_name";
            $query .= "\n                      ,order_common_seqno";
            $query .= "\n) VALUES (";
            $query .= "\n            '" . $result->fields["basic_yn"] . "'";
            $query .= "\n           ,'" . $result->fields["depth1"] . "'";
            $query .= "\n           ,'" . $result->fields["depth2"] . "'";
            $query .= "\n           ,'" . $result->fields["depth3"] . "'";
            $query .= "\n           ,'" . $result->fields["price"] . "'";
            $query .= "\n           ,'" . $result->fields["opt_name"] . "'";
            $query .= "\n           , " . $param["shb_seqno"];
            $query .= "\n )";

            $resultSet = $conn->Execute($query);
            if ($resultSet === false) {
                $check = false;
            }

            $result->moveNext();
        }

        return $check;

    }
     */

    /**
     * @brief 관심상품 주문상세 SELECT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
    function selectPrdtDetailSet($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query .= "\n    SELECT  cate_paper_mpcode";
        $query .= "\n           ,typ";
        $query .= "\n           ,page_amt";
        $query .= "\n           ,cate_beforeside_print_mpcode";
        $query .= "\n           ,cate_beforeside_add_print_mpcode";
        $query .= "\n           ,cate_aftside_print_mpcode";
        $query .= "\n           ,cate_aftside_add_print_mpcode";
        $query .= "\n           ,spc_dscr";
        $query .= "\n           ,detail_num";
        //$query .= "\n           ,order_detail_num";
        $query .= "\n           ,work_size_wid";
        $query .= "\n           ,work_size_vert";
        $query .= "\n           ,cut_size_wid";
        $query .= "\n           ,cut_size_vert";
        $query .= "\n           ,tomson_size_wid";
        $query .= "\n           ,tomson_size_vert";
        $query .= "\n           ,cut_front_wing_size_wid";
        $query .= "\n           ,cut_front_wing_size_vert";
        $query .= "\n           ,work_front_wing_size_wid";
        $query .= "\n           ,work_front_wing_size_vert";
        $query .= "\n           ,cut_rear_wing_size_wid";
        $query .= "\n           ,cut_rear_wing_size_vert";
        $query .= "\n           ,work_rear_wing_size_wid";
        $query .= "\n           ,work_rear_wing_size_vert";
        $query .= "\n           ,seneca_size";
        $query .= "\n      FROM  interest_prdt_detail";
        $query .= "\n     WHERE  interest_prdt_seqno = ";
        $query .= $param["prdt_seqno"];

        $result = $conn->Execute($query);

        return $result;
    }
     */

    /**
     * @brief 거 주문상세 INSERT
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
    function insertShbDetail($conn, $result, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);
        $order_num = substr($param["order_num"], 1, -1);

        $check = TRUE;
        $idx = 1;

        while ($result && !$result->EOF) {

            $query  = "\n INSERT INTO order_detail (";
            $query .= "\n                          cate_beforeside_print_mpcode";
            $query .= "\n                         ,cate_beforeside_add_print_mpcode";
            $query .= "\n                         ,cate_aftside_print_mpcode";
            $query .= "\n                         ,cate_aftside_add_print_mpcode";
            $query .= "\n                         ,typ";
            $query .= "\n                         ,page_amt";
            $query .= "\n                         ,cate_paper_mpcode";
            $query .= "\n                         ,spc_dscr";
            $query .= "\n                         ,detail_num";
            $query .= "\n                         ,work_size_wid";
            $query .= "\n                         ,work_size_vert";
            $query .= "\n                         ,cut_size_wid";
            $query .= "\n                         ,cut_size_vert";
            $query .= "\n                         ,tomson_size_wid";
            $query .= "\n                         ,tomson_size_vert";
            $query .= "\n                         ,cut_front_wing_size_wid";
            $query .= "\n                         ,cut_front_wing_size_vert";
            $query .= "\n                         ,work_front_wing_size_wid";
            $query .= "\n                         ,work_front_wing_size_vert";
            $query .= "\n                         ,cut_rear_wing_size_wid";
            $query .= "\n                         ,cut_rear_wing_size_vert";
            $query .= "\n                         ,work_rear_wing_size_wid";
            $query .= "\n                         ,work_rear_wing_size_vert";
            $query .= "\n                         ,seneca_size";
            $query .= "\n                         ,order_detail_num";
            $query .= "\n                         ,order_common_seqno";
            $query .= "\n) VALUES (";
            $query .= "\n          '" . $result->fields["cate_beforeside_print_mpcode"] . "'";
            $query .= "\n         ,'" . $result->fields["cate_beforeside_add_print_mpcode"] . "'";
            $query .= "\n         ,'" . $result->fields["cate_aftside_print_mpcode"] . "'";
            $query .= "\n         ,'" . $result->fields["cate_aftside_add_print_mpcode"] . "'";
            $query .= "\n         ,'" . $result->fields["typ"] . "'";
            $query .= "\n         ,'" . $result->fields["page_amt"] . "'";
            $query .= "\n         ,'" . $result->fields["cate_paper_mpcode"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["spc_dscr"] . "'";
            $query .= "\n         ,'" . $result->fields["detail_num"] . "'";
            $query .= "\n         ,'" . $result->fields["work_size_wid"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["work_size_vert"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["cut_size_wid"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["cut_size_vert"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["tomson_size_wid"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["tomson_size_vert"];
            $query .= "'";
            $query .= "\n         ,'" . $result->fields["cut_front_wing_size_wid"] . "'";
            $query .= "\n         ,'" . $result->fields["cut_front_wing_size_vert"] . "'";
            $query .= "\n         ,'" . $result->fields["work_front_wing_size_wid"] . "'";
            $query .= "\n         ,'" . $result->fields["work_front_wing_size_vert"] . "'";
            $query .= "\n         ,'" . $result->fields["cut_rear_wing_size_wid"] . "'";
            $query .= "\n         ,'" . $result->fields["cut_rear_wing_size_vert"] . "'";
            $query .= "\n         ,'" . $result->fields["work_rear_wing_size_wid"] . "'";
            $query .= "\n         ,'" . $result->fields["work_rear_wing_size_vert"] . "'";
            $query .= "\n         ,'" . $result->fields["seneca_size"] . "'";

            $query .= "\n         ,'" . $order_num . $idx . "'";
            $query .= "\n         , " . $param["shb_seqno"];
            $query .= "\n )";

            $resultSet = $conn->Execute($query);
            if ($resultSet === false) {
                $check = false;
            }

            $result->moveNext();
            $idx++;
        }

        return $check;

    }
     */
}
?>
