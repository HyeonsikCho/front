<?
/*
 * Copyright (c) 2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 2016-11-15 엄준현 생성(기획인쇄물 문어발 때문에 분리)
 *============================================================================
 *
 */
include_once($_SERVER["DOCUMENT_ROOT"] . "/product/info/CommonInfo.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/product_info_class.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/message.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/prdt_default_info.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/html/product/QuickEstimate.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductSheetCutDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");

class SheetCutInfoAD extends CommonInfo {
    // 확정형
    var $sell_price        = null;
    var $grade_sale_rate   = null;
    var $grade_sale_price  = null;
    var $member_sale_rate  = null;
    var $member_sale_price = null;
    var $sale_price        = null;
    // 계산형
    var $paper_price       = null;
    var $print_price       = null;
    var $output_price      = null;
    // 공통
    var $tax               = null;
    var $supply_price      = null;
    var $flattyp_yn        = null;

    /**
     * @brief 클래스 생성자
     *
     * @detail $flag_arr에 들어가는 flag는 아래와 같다
     * $flag_arr["pos_yn"]      = 사이즈 자리수 노출여부
     * $flag_arr["mix_yn"]      = 책자형 여부
     * $flag_arr["size_typ_yn"] = 사이즈명 종류 노출여부
     *
     * @param &$conn         = db 커넥션
     * @param &$template     = 주문페이지에 값을 표현할 템플릿 객체
     * @param $cate_sortcode = 카테고리 분류코드
     * @param $dvs           = 고유값을 만들기위한 구분값
     * @param $sell_site     = 판매채널
     * @param $flag_arr      = 플래그 여부 배열
     */
    function __construct(&$conn,
                         &$template,
                         $cate_sortcode,
                         $dvs,
                         $sell_site,
                         $flag_arr) {
        $this->conn          = $conn;
        $this->template      = $template;
        $this->cate_sortcode = $cate_sortcode;
        $this->dvs           = $dvs;
        $this->sell_site     = $sell_site;
        $this->affil_yn      = $flag_arr["affil_yn"];
        $this->pos_yn        = $flag_arr["pos_yn"];
        $this->mix_yn        = $flag_arr["mix_yn"];
        $this->size_typ_yn   = $flag_arr["size_typ_yn"];

        $this->init();
    }

    /**
     * @brief 정보를 초기화 하는 함수
     */
    function init() {
        $dao = new ProductSheetCutDAO();
        $util = new FrontCommonUtil();

        $price_info_arr = array();
        $param = array();

        $conn        = $this->conn;
        $template    = $this->template;
        $sortcode_b  = $this->cate_sortcode;
        $dvs         = $this->dvs;
        $sell_site   = $this->sell_site;
        $affil_yn    = $this->affil_yn;
        $pos_yn      = $this->pos_yn;
        $mix_yn      = $this->mix_yn;
        $size_typ_yn = $this->size_typ_yn;
        $is_login    = empty($_SESSION["id"]) ? false : true;

        $prefix = '';
        if (empty($dvs) === false) {
            $prefix = $dvs . '_';
        }

        //-2 제품별 카테고리 정보 생성
        if ($mix_yn === true) {
            $cate_bot = $dao->selectMixCateHtml($conn, $sortcode_b);
            $template->reg($prefix . "cate_bot", $cate_bot); 
        }

        //-1 카테고리 독판여부, 수량단위 검색
        $cate_info_arr = $dao->selectCateInfo($conn, $sortcode_b);
        $mono_dvs   = $cate_info_arr["mono_dvs"];
        $amt_unit   = $cate_info_arr["amt_unit"];
        $tmpt_dvs   = $cate_info_arr["tmpt_dvs"];
        $flattyp_yn = $cate_info_arr["flattyp_yn"];
        unset($cate_info_arr);
        $template->reg($prefix . "mono_dvs", makeMonoDvsOption($mono_dvs)); 
        $template->reg($prefix . "tmpt_dvs", $tmpt_dvs); 
        $template->reg($prefix . "flattyp_yn", $flattyp_yn); 

        $mono_dvs = ($mono_dvs === '1' || $mono_dvs === '2') ? '0' : '1';

        // 가격 테이블명 검색
        $price_tb  = $dao->selectPriceTableName($conn, $mono_dvs, $sell_site);

        //0 종이 정보 생성(##사이즈의 계열에 따라서 처리)
        $param["cate_sortcode"] = $sortcode_b;
        $paper = $dao->selectCatePaperHtml($conn, $param, $price_info_arr);
        $template->reg($prefix . "paper", $paper["info"]); 

        //1 인쇄도수 정보 생성
        if ($mono_dvs === '1') {
            $param["affil"] = $price_info_arr["affil"];
        }
        $print_tmpt = $dao->selectCatePrintTmptHtml($conn,
                                                    $param,
                                                    $price_info_arr);
        if ($tmpt_dvs === '0') {
            $tmpt = $print_tmpt["단면"] . $print_tmpt["양면"];
            $template->reg($prefix . "print_tmpt", $tmpt); 
        } else {
            $template->reg($prefix . "bef_print_tmpt", $print_tmpt["전면"]); 
            $template->reg($prefix . "aft_print_tmpt", $print_tmpt["후면"]); 
            $template->reg($prefix . "bef_add_print_tmpt", $print_tmpt["전면추가"]); 
            $template->reg($prefix . "aft_add_print_tmpt", $print_tmpt["후면추가"]); 
        }
        $template->reg($prefix . "print_purp", $print_tmpt["purp_dvs"]); 

        unset($print_tmpt);
        unset($param);

        //2 사이즈 사이즈 명만 검색
        $param["cate_sortcode"] = $sortcode_b;
        $size = $dao->selectCateStanNameHtml($conn,
                                             $param,
                                             $price_info_arr,
                                             $pos_yn,
                                             $affil_yn,
                                             $size_typ_yn);
        $template->reg($prefix . "size", $size); 

        //3 재단, 작업사이즈간 차이 정보 생성
        $size_gap = " _gap%s";
        $val = ProductInfoClass::SIZE_GAP[$sortcode_b];
        if (empty($val)) {
            $val = $price_info_arr["size_gap"];
        }
        $val = $price_info_arr["size_gap"];
        $template->reg($prefix . "size_gap", sprintf($size_gap, $val));

        //4 사이즈 명으로 사이즈 유형 및 실제 맵핑코드 검색
        $temp = array(); // 맵핑코드 가져오기용
        $param["stan_name"] = $price_info_arr["stan_mpcode"];
        $size_typ = $dao->selectCateStanTypHtml($conn,
                                                $param,
                                                $temp,
                                                $pos_yn,
                                                $affil_yn,
                                                $size_typ_yn);

        $price_info_arr["stan_mpcode"] = $temp["stan_mpcode"];
        unset($temp);

        $template->reg($prefix . "size_typ", $size_typ); 

        if ($mix_yn === false) {
            //5 옵션 체크박스 생성
            $param["cate_sortcode"] = $sortcode_b;
            $param["dvs"]           = $dvs;
            $opt = $dao->selectCateOptHtml($conn, $param);
            $template->reg($prefix . "opt", $opt["html"]); 

            //6 옵션 가격 레이어 생성
            $template->reg($prefix . "add_opt", ''); 
            if (empty($opt["info_arr"]) === false) {
                $add_opt = $opt["info_arr"]["name"];
                $add_opt = $dao->parameterArrayEscape($conn, $add_opt);
                $add_opt = $util->arr2delimStr($add_opt);

                $param["opt_name"] = $add_opt;
                $param["opt_idx"]  = $opt["info_arr"]["idx"];
                $add_opt = $dao->selectCateAddOptInfoHtml($conn, $param);
                unset($param);
                $template->reg($prefix . "add_opt", $add_opt); 
            }
        }

        //7 후공정 체크박스 생성
        $param["cate_sortcode"] = $sortcode_b;
        $param["dvs"]           = $dvs;

        $after = $dao->selectCateAfterHtml($conn, $param);
        $template->reg($prefix . "after", $after["html"]); 
        unset($param);

        //8 기본 후공정 내역에 표시할 정보 생성
        $template->reg($prefix . "basic_after", ''); 
        if (empty($after["info_arr"]["basic"]) === false) {
            $basic_after = $after["info_arr"]["basic"];
            $basic_after = $util->arr2delimStr($basic_after, '|');
            $template->reg($prefix . "basic_after", $basic_after); 
        }

        //9 추가 후공정 가격 레이어 생성
        $template->reg($prefix . "after", ''); 
        $template->reg($prefix . "add_after", ''); 
        if (empty($after["info_arr"]["add"]) === false) {
            $add_after = $after["info_arr"]["add"];
            $template->reg($prefix . "after", $after["html"]); 

            $param["cate_sortcode"] = $sortcode_b;
            $param["dvs"]           = $dvs;


            $add_after_html = '';
            foreach ($add_after as $after_name) {
                $param["size"]       = $price_info_arr["size_name"];
                $param["after_name"] = $after_name;

                $add_after_html .= $dao->selectCateAddAfterInfoHtml($conn,
                                                                    $param);
                unset($param["affil"]);
                unset($param["subpaper"]);
            }
            unset($param);
            $template->reg($prefix . "add_after", $add_after_html); 
        }

        //11 지질느낌 검색
        $paper_sense = $dao->selectPaperDscr($conn, $price_info_arr["paper_mpcode"]);
        $template->reg($prefix . "paper_sense", $paper_sense); 

        //12 수량 정보 생성
        $param["table_name"]    = $price_tb;
        $param["cate_sortcode"] = $sortcode_b;
        $param["paper_mpcode"]  = $price_info_arr["paper_mpcode"];
        $param["stan_mpcode"]   = $price_info_arr["stan_mpcode"];
        $param["amt_unit"]      = $amt_unit;
        $amt = $dao->selectCateAmtHtml($conn, $param, $price_info_arr);
        unset($param);
        $template->reg($prefix . "amt", $amt); 
        $template->reg($prefix . "amt_unit", $amt_unit); 

        //13 기준가격(정상판매가) 검색, 부가세 계산
        $param["table_name"]           = $price_tb;
        $param["cate_sortcode"]        = $sortcode_b;
        $param["paper_mpcode"]         = $price_info_arr["paper_mpcode"];
        $param["bef_print_mpcode"]     = $price_info_arr["print_mpcode"];
        $param["bef_add_print_mpcode"] = '0';
        $param["aft_print_mpcode"]     = '0';
        $param["aft_add_print_mpcode"] = '0';
        $param["stan_mpcode"]          = $price_info_arr["stan_mpcode"];
        $param["amt"]                  = $price_info_arr["amt"];

        $sell_price   = 0;
        $paper_price  = 0;
        $print_price  = 0;
        $output_price = 0;
        $tax          = 0;

        $page = 2;
        $page_dvs = "표지";

        if ($mono_dvs === '0') {
            $price_rs = $dao->selectPrdtPlyPrice($conn, $param);

            $page = $price_rs["page"];
            $page_dvs = $price_rs["page_dvs"];
            
            $sell_price = doubleval($price_rs["new_price"]);
            $sell_price = $util->ceilVal($sell_price);

            $print_price = $sell_price;
        } else {
            // 계산형 들어가면 그 때 구현
        }

        unset($param);
        $template->reg($prefix . "paper_price" , $paper_price); 
        $template->reg($prefix . "print_price" , $print_price); 
        $template->reg($prefix . "output_price", $output_price); 
        $template->reg($prefix . "sell_price"  , number_format($sell_price));

        $template->reg($prefix . "page"    , $page); 
        $template->reg($prefix . "page_dvs", $page_dvs); 

        //14 회원등급 할인, 회원 할인 정보 생성
        if ($is_login) {
            $param["cate_sortcode"] = $sortcode_b;
            $param["member_seqno"]  = $_SESSION["member_seqno"];
            $member_sale_rate = $dao->selectCateMemberSaleRate($conn, $param);
            unset($param);
        } else {
            $dscr = "로그인시 할인받으실 수 있는 금액입니다.";
            $grade_sale_rate  = 0;
            $grade_sale       = 0;
            $member_sale_rate = 0;
            $member_sale      = 0;

            $sale_price = $sell_price;
        }
        unset($param);

        $grade = empty($_SESSION["grade"]) ? '10' : $_SESSION["grade"];

        $param["cate_sortcode"] = $sortcode_b;
        $param["grade"]         = $grade;
        $grade_sale_rate = $dao->selectGradeSaleRate($conn, $param);
        $grade_sale = $util->calcPrice($grade_sale_rate, $sell_price);

        $sale_price   = $sell_price + $grade_sale;
        $member_sale  = $util->calcPrice($member_sale_rate, $sale_price);
        $sale_price  += $member_sale;

        $arr = array(
            "dscr"             => $dscr,
            "rate"             => $grade_sale_rate,
            "member_sale_rate" => $member_sale_rate,
            "grade"            => $grade,
            "price"            => $util->ceilVal($grade_sale + $member_sale)
        );
                                                 
        $grade_sale_html = makeGradeSaleDl($arr);
        unset($arr);

        $template->reg($prefix . "member_sale_rate", $member_sale_rate); 
        $template->reg($prefix . "grade_sale"     , $grade_sale_html); 
        $template->reg($prefix . "grade_sale_rate", $grade_sale_rate); 

        //15 이벤트 할인 정보 생성
        $param["dscr"]  = NO_EVENT;
        $template->reg($prefix . "event_sale", makeEventSaleDl($param)); 
        unset($param);

        //16 결제금액 계산
        $sale_price = $util->ceilVal($sale_price);
        $template->reg($prefix . "sale_price", number_format($sale_price)); 

        // 공급가 계산
        //$tax = $sale_price - ceil($sale_price / 1.1);
        $tax = $util->ceilVal($sale_price / 11);
        $template->reg($prefix . "tax", number_format($tax));
        // 부가세 계산
        $supply_price = $sale_price - $tax;
        $template->reg($prefix . "supply_price", number_format($supply_price));

        $this->sell_price        = $sell_price;
        $this->grade_sale_rate   = $grade_sale_rate;
        $this->grade_sale_price  = $grade_sale;
        $this->member_sale_rate  = $member_sale_rate;
        $this->member_sale_price = $member_sale;
        $this->sale_price        = $sale_price;
        $this->paper_price       = $paper_price;
        $this->print_price       = $print_price;
        $this->output_price      = $output_price;
        $this->tax               = $tax;
        $this->supply_price      = $supply_price;
        $this->flattyp_yn        = $flattyp_yn;

        //17 견적서 html 생성
        if ($mix_yn === false) {
            $param["esti_output"] = $output_price;
            $param["esti_print"]  = $print_price;
            $param["esti_opt"]    = $opt_price;
            $param["esti_sell_price"] = $sell_price;
            $param["esti_sale_price"] = $sale_price;
            $template->reg("quick_esti", getQuickEstimateHtml(
                                             $param,
                                             $util,
                                             ProductInfoClass::AFTER_ARR
                                         )); 
        }

        //18 재질미리보기 정보 생성
        $param["name"]  = $price_info_arr["paper_name"];
        $param["dvs"]   = $price_info_arr["paper_dvs"];
        $param["color"] = $price_info_arr["paper_color"];

        $rs = $dao->selectPaperPreviewInfo($conn, $param);
        $rs = $rs->fields;

        $save_file_arr = explode('.', $rs["save_file_name"]);

        $zoom = $rs["file_path"] . DIRECTORY_SEPARATOR . $rs["save_file_name"];
        $thumb = $rs["file_path"] . DIRECTORY_SEPARATOR .
                 $save_file_arr[0] . "_400_313." . $save_file_arr[1];

        $template->reg("preview_org", $zoom);
        $template->reg("preview_thumb", $thumb);
    }
}
?>
