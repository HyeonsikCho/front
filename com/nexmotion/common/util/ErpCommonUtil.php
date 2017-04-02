<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/define/common_config.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/common_lib/CommonUtil.php');

class ErpCommonUtil extends CommonUtil {
    /**
     * @brief 기본가격, 요율, 적용금액을 이용해 신규가격을 계산
     * 신규금액 = (기본금액 * 요율) + 기본가격 + 적용금액
     *
     * @param $basic_price = 기본가격
     * @param $rate        = 요율
     * @param $aplc_price  = 적용금액
     *
     * @return 계산된 가격
     */
    function getNewPrice($basic_price, $rate, $aplc_price) {
        $basic_price = doubleval($basic_price);
        $rate = doubleval($rate);
        $rate = ($rate / 100.0);
        $aplc_price = doubleval($aplc_price);

        $new_price = (($basic_price * $rate) + $basic_price) + $aplc_price;

        return ceil($new_price);
    }

    /**
     * @brief 가격 구분 배열을 내림차순으로 정렬하고
     * 인덱스를 숫자로 변경하는 함수
     *
     * @param $dvs_arr = 가격 구분 배열(수량/평량)
     *
     * @return 내림차순으로 정렬된 배열
     */
    function sortDvsArr($dvs_arr) {
        $temp_arr = array();

        if (count($dvs_arr) > 1) {
            unset($dvs_arr[NULL]);
        }

        foreach ($dvs_arr as $key => $val) {
            if (is_numeric($key)) {
                $temp_arr[$key] = $val;
            } else {
                $key = doubleval($key);
                $temp_arr[$key] = $val;
            }
        }

        ksort($temp_arr);
        $dvs_arr = $temp_arr;

        unset($temp_arr);

        $dvs_arr_count = count($dvs_arr);

        $j = 0;
        foreach ($dvs_arr as $key => $val) {
            $temp_arr[$j++] = $val;
        }

        return $temp_arr;
    }

    /**
     * @brief 각 가격정보 검색결과를 가격검색 및 제목생성에
     * 사용할 수 있도록 가공하는 함수
     *
     * @detail 반환되는 배열은 mpcode 배열과 info 배열이다
     * mpcode 배열은 가격검색에 사용되고
     * info 배열은 엑셀 가격정보 셀 생성에 사용된다
     *
     * @param $rs           = 검색결과
     * @param $info_fld_arr = $rs에서 info로 생성할 필드명
     *
     * @return 가공된 배열
     */
    function makeTotalInfoArr($rs, $info_fld_arr) {
        $ret = array(
            "mpcode" => array(), // 종이 맵핑코드 배열, 가격검색시 사용
            "info"   => array()  // 종이 정보 배열, 제목 생성시 사용
        );

        $info_fld_arr_count = count($info_fld_arr);

        $i = 0;
        while ($rs && !$rs->EOF) {
            for ($j = 0; $j < $info_fld_arr_count; $j++) {
                $info_fld = $info_fld_arr[$j];
                $ret["info"][$i][$info_fld] = $rs->fields[$info_fld];
            }

            $ret["mpcode"][$i++] = $rs->fields["mpcode"];

            $rs->MoveNext();
        }

        return $ret;
    }

    /**
     * @brief 계열 배열에서 사용할 값만 추출해서 반환
     * 전부 선택되어있거나 하나도 선택되어있지 않다면 빈값을 반환
     *
     * @detail 파라미터 형태는
     * $affil["46"] = "true", $affil["GUK"] = "false" 형태로 전달된다
     *
     * @param $affil = 계열 배열
     *
     * @return 사용할 계열조건
     */
    function getUseAffil($affil) {
        $affil_46  = $affil["46"];
        $affil_guk = $affil["GUK"];

        if ($affil_46 === "true" && $affil_guk === "true") {
            return "";
        }

        if ($affil_46 === "false" && $affil_guk === "false") {
            return "";
        }

        if ($affil_46 === "true") {
            return $affil_46;
        }

        if ($affil_guk === "true") {
            return $affil_guk;
        }
    }

    /**
     * @brief 사용하는 계열 파라미터 식으로 변환해서 반환
     *
     * @param $conn = 디비 커넥션, 이스케이프용
     * @param $dao  = dao 객체, 이스케이프용
     * @param $fb   = 폼빈 객체
     * @param $util = 유틸 객체
     *
     * @return 계열 파라미터
     */
    function getUseAffilParam($conn, $dao, $fb) {
        $affil_46  = $fb->form("affil_fs");
        $affil_guk = $fb->form("affil_guk");
        $affil_spc = $fb->form("affil_spc");

        $arr = array(); 

        if ($affil_46 !== null) {
            array_push($arr, "46");
        }
        if ($affil_guk !== null) {
            array_push($arr, "국");
        }
        if ($affil_spc !== null) {
            array_push($arr, "별");
        }

        $arr = $dao->parameterArrayEscape($conn, $arr);

        return $this->arr2delimStr($arr);
    }

    /**
     * @brief 페이지에 따른 seqno 범위 계산
     * 대용량 페이징에서 사용한다
     *
     * @param $last_seqno = 마지막 seqno
     * @param $list_size  = 목록 크기
     * @param $page       = 현재 페이지값
     *
     * @return seqno 범위 배열
     */
    function calcSeqnoRange($last_seqno, $list_size, $page) {

        $end_seqno   = $last_seqno - ($list_size * ($page - 1));
        $start_seqno = $end_seqno - $list_size;

        if ($start_seqno <= 0) {
            $start_seqno = 1;
        }

        if ($end_seqno < $start_seqno) {
            return false;
        }

        return array("start" => $start_seqno,
                     "end"   => $end_seqno);
    }
}
?>
