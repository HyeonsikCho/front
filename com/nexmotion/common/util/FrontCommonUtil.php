<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/define/common_config.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/common_lib/CommonUtil.php');

class FrontCommonUtil extends CommonUtil {
    /**
     * @brief 카테고리 대중소 분류코드 반환
     *
     * @detail 카테고리 중, 소 분류코드가 무조건 001로 시작한다고
     * 장담할 수 없으므로 DB검색으로 가장 작은값을 먼저 가져옴
     *
     * @param $conn = connection identifier
     * @param $dao  = 카테고리 분류코드 검색용 dao
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return array(
     *             "sortcode_t" => 카테고리 대분류 분류코드,
     *             "sortcode_m" => 카테고리 중분류 분류코드,
     *             "sortcode_b" => 카테고리 소분류 분류코드
     *         )
     */
    function getTMBCateSortcode($conn, $dao, $cate_sortcode) {
        $sortcode_t = false;
        $sortcode_m = false;
        $sortcode_b = false;

        if (strlen($cate_sortcode) === 3) {
            $sortcode_t = $cate_sortcode;
            $sortcode_m = $dao->selectCateSortcode($conn, $sortcode_t);
            $sortcode_b = $dao->selectCateSortcode($conn, $sortcode_m);
        }
        if (strlen($cate_sortcode) === 6) {
            $sortcode_t = substr($cate_sortcode, 0, 3);
            $sortcode_m = $cate_sortcode;
            $sortcode_b = $dao->selectCateSortcode($conn, $sortcode_m);
        }
        if (strlen($cate_sortcode) === 9) {
            $sortcode_t = substr($cate_sortcode, 0, 3);
            $sortcode_m = substr($cate_sortcode, 0, 6);
            $sortcode_b = $cate_sortcode;
        }

        return array("sortcode_t" => $sortcode_t,
                     "sortcode_m" => $sortcode_m,
                     "sortcode_b" => $sortcode_b);
    }

    /**
     * @brief 옵션/후공정 depth1, 2, 3을 붙여서 하나로 만듬
     *
     * @detail 추가 옵션/후공정 팝업에서 사용
     *
     * @param $info  = 옵션/후공정 정보
     *
     * @return 옵션/후공정 풀네임
     */
    function getOptAfterFullName($info) {
        $depth1 = $info["depth1"];
        $depth2 = $info["depth2"];
        $depth3 = $info["depth3"];

        $dvs = '';

        if ($depth1 !== '-') {
            $dvs = $depth1;
        }
        if ($depth2 !== '-') {
            $dvs .= ' ' . $depth2;
        }
        if ($depth3 !== '-') {
            $dvs .= ' ' . $depth3;
        }

        return $dvs;
    }

    /**
     * @brief 사이드 메뉴 주문상태 요약 배열 생성
     *
     * @detail $ret["200"] = 입금대기
     * $ret["300"] = 접수
     * $ret["400"] = 조판
     * $ret["600"] = 출력
     * $ret["700"] = 인쇄
     * $ret["800"] = 후공정
     * $ret["900"] = 입고
     * $ret["950"] = 출고
     * $ret["010"] = 배송 
     * $ret["020"] = 완료
     *
     * @param $rs = 검색결과
     *
     * @return 주문상태 + 진행상태값
     */
    function makeOrderSummaryArr($rs) {
        $ret = array(
            "입금대기" => 0,
            "접수"     => 0,
            "제작"     => 0,
            "입출고"   => 0,
            "배송"     => 0,
            "완료"     => 0
        );

        while ($rs && !$rs->EOF) {
            $state_name  = $rs->fields["dvs"];
            $state_count = intval($rs->fields["state_count"]);

            switch ($state_name) {
            // 입금대기
            case "입금" :
                $ret["입금대기"] += $state_count;
                break;
            // 접수
            case "접수" :
                $ret["접수"] += $state_count;
                break;
            // 제작
            case "조판" :
            case "출력" :
            case "인쇄" :
            case "후공정" :
                $ret["제작"] += $state_count;
                break;
            // 입출고
            case "입고" :
            case "출고" :
                $ret["입출고"] += $state_count;
                break;
            // 배송
            case "배송" :
                $ret["배송"] += $state_count;
                break;
            // 완료
            case "구매확정" :
                $ret["완료"] += $state_count;
                break;
            }

            $rs->MoveNext();
        }

        return $ret;
    }

    /**
     * @brief 잘못 된 접근시
     *
     * @param $title = alert 내용
     *
     * @return 계산된 값
     */
    function errorGoBack($title = "") {
        echo "<script>";
        echo "    alert('" . $title . "');";
        echo "    history.go(-1);";
        echo "</script>";
    }

    /** 2016-11-20 주석처리
     * @brief 넘어온 정보로 인쇄 맵핑코드 검색
     *
     * @param $conn = connection identifer
     * @param $dao  = 검색을 수행할 dao객체
     * @param $fb   = 넘어온 값을 가져올 FormBean 객체
     * @param $dvs  = 영역구분값(all 때문에 전달받음)
     *
     * @return 맵핑코드 배열
    function getPrintMpcode($conn, $dao, $fb, $dvs) {
        $ret = array();

        $flag = $fb["flag"];
        $cate_sortcode = $fb["cate_sortcode"];

        $bef_print_name     = $fb[$dvs . "_bef_print_name"];
        $bef_add_print_name = $fb[$dvs . "_bef_add_print_name"];
        $aft_print_name     = $fb[$dvs . "_aft_print_name"];
        $aft_add_print_name = $fb[$dvs . "_aft_add_print_name"];
        $print_purp         = $fb[$dvs . "_print_purp"];

        $param = array();
        $param["cate_sortcode"] = $cate_sortcode;
        $param["purp_dvs"]      = $print_purp;

        // 낱장형 일 때 검색
        if ($flag === 'Y') {
            $param["name"] = $bef_print_name;
            $bef_mpcode = $dao->selectCatePrintMpcode($conn, $param);

            $ret["bef"]     = $bef_mpcode;
            $ret["bef_add"] = '0';
            $ret["aft"]     = '0';
            $ret["aft_add"] = '0';

            //print_r($ret);

            return $ret;
        }

        // 전면 인쇄 맵핑코드
        $param["name"]     = $bef_print_name;
        $param["side_dvs"] = "전면";
        $bef_mpcode = $dao->selectCatePrintMpcode($conn, $param);
        // 전면 추가 인쇄 맵핑코드
        $param["name"]     = $bef_add_print_name;
        $param["side_dvs"] = "전면추가";
        $bef_add_mpcode = $dao->selectCatePrintMpcode($conn, $param);
        // 후면 인쇄 맵핑코드
        $param["name"]     = $aft_print_name;
        $param["side_dvs"] = "후면";
        $aft_mpcode = $dao->selectCatePrintMpcode($conn, $param);
        // 후면 추가 인쇄 맵핑코드
        $param["name"]     = $aft_add_print_name;
        $param["side_dvs"] = "후면추가";
        $aft_add_mpcode = $dao->selectCatePrintMpcode($conn, $param);

        $ret["bef"]     = $bef_mpcode;
        $ret["bef_add"] = $bef_add_mpcode;
        $ret["aft"]     = $aft_mpcode;
        $ret["aft_add"] = $aft_add_mpcode;

        return $ret;
    }
     */

    /**
     * @brief 종이 정보 문자열 생성
     * 구분값이 '-' 일 경우 빼고 생성
     *
     * @param $arr = 종이 정보 배열
     *
     * @return 종이 정보 문자열
     */
    function makePaperInfoStr($arr) {
        $name        = $arr["name"];
        $dvs         = $arr["dvs"];
        $color       = $arr["color"];
        $basisweight = $arr["basisweight"];

        $ret = '';
        if (!empty($name)) {
            $ret = $name;
        }
        if ($dvs !== '-') {
            $ret .= " " . $dvs;
        }
        if ($color !== '-') {
            $ret .= " " . $color;
        }
        if ($basisweight !== "0g") {
            $ret .= " " . $basisweight;
        }

        return trim($ret);
    }

    /**
     * @brief 문자열 길이 자르기
     *
     * @param $str = 원본문자열
     * @param $start = 추출할 문자열 시작 위치
     * @param $end = 추출할 문자열 끝 위치
     * @param $tail = 뒤에 붙일 문자열
     *
     * @return 다듬어진 문자열
     */
    function str_cut($str, $start = 0, $end, $tail = "..") {
    
        if (!$str) return "";

        if (strlen($str) > $end)
            return mb_substr($str, $start, $end, 'UTF-8') . $tail;
        else 
            return $str;
    }

    /**
     * @brief 묶음배송용 식별값 생성
     *
     * @return 식별값
     */
    function makeBunDlvrOrderNum() {
        $t = strval(ceil(microtime(true) * 1000.0));
        $r = rand(0, 9);

        return $t . $r;
    }

    /**
     * @brief 주문번호 생성
     *
     * @detail 주문번호 양식은 다음과 같다
     * 채널(3)날짜(YYMMDD)품목(2)일련번호(5)
     * GPT160921NC00001
     *
     * @param $conn  = db connection
     * @param $dao   = dao 객체
     * @param $param = 검색용 파라미터
     *
     * @return 주문번호
     */
    function makeOrderNum($conn, $dao, $cate_sortcode) {
        $sell_site     = $dao->selectCpnAdminNick($conn, $_SESSION["sell_site"]);
        $cate_sortcode = substr($cate_sortcode, 0, 3);
        $cate_nick     = $dao->selectCateNick($conn, $cate_sortcode);
        $last_num      = $dao->selectOrderCommonLastNum($conn);
        $last_num      = str_pad($last_num, 5, '0', STR_PAD_LEFT);

        $form = "%s%s%s%s";
        $form = sprintf($form, $sell_site
                             , date("ymd")
                             , $cate_nick
                             , $last_num);
        return $form;
    }
}
?>
