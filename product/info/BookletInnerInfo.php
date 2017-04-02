<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/product/info/CommonInfo.php");

class BookletInnerInfo extends CommonInfo {
    var $sell_price       = null;
    var $grade_sale_rate  = null;
    var $grade_sale_price = null;
    var $sale_price       = null;
    var $paper_price      = null;
    var $print_price      = null;
    var $output_price     = null;
    var $opt_price        = null;

    /**
     * @brief 내지정보 클래스 생성자
     *
     * @param &$conn         = db 커넥션
     * @param &$template     = 주문페이지에 값을 표현할 템플릿 객체
     * @param $cate_sortcode = 카테고리 분류코드
     * @param $dvs           = 고유값을 만들기위한 구분값
     * @param $sell_site     = 판매채널
     */
    function __construct(&$conn,
                         &$template,
                         $cate_sortcode,
                         $dvs,
                         $sell_site,
                         $mix_yn = true) {
        $this->conn          = $conn;
        $this->template      = $template;
        $this->cate_sortcode = $cate_sortcode;
        $this->dvs           = $dvs;
        $this->sell_site     = $sell_site;
        $this->mix_yn        = $mix_yn;

        $this->init();
    }

    /**
     * @brief 정보를 초기화 하는 함수
     */
    function init() {
        include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
        include_once($_SERVER["DOCUMENT_ROOT"] . "/define/product_default_sel.php");
        include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/prdt_default_info.php");
        include_once($_SERVER["DOCUMENT_ROOT"] . "/common_lib/CommonUtil.php");
        include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/html/product/QuickEstimate.php");
        include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductAdBookDAO.php");
        include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");

        $dao = new ProductAdBookDAO();
        $commonUtil = new CommonUtil();
        $frontUtil = new FrontCommonUtil();

        $price_info_arr = array();
        $param = array();

        $conn       = $this->conn;
        $template   = $this->template;
        $sortcode_b = $this->cate_sortcode;
        //$sortcode_m = substr($sortcode_b, 0, 6);
        $dvs        = $this->dvs;
        $sell_site  = $this->sell_site;
        $mix_yn     = $this->mix_yn;

        $prefix = '';
        if (empty($dvs) === false) {
            $prefix = $dvs . '_';
        }

        //0 제품별 카테고리 정보 생성
        if ($mix_yn === true) {
            $cate_bot = $dao->selectMixCateHtml($conn, $sortcode_b);
            $template->reg($prefix . "cate_bot", $cate_bot); 
        }

        //1 기본 선택 정보 배열
        $default_sel_arr = ProductDefaultSel::DEFAULT_SEL[$sortcode_b];

        //2 사이즈 정보 생성
        $param["cate_sortcode"] = $sortcode_b;
        $size = $dao->selectCateSizeHtml($conn, $param, $price_info_arr, true);
        $template->reg($prefix . "size", $size); 
        $template->reg($prefix . "def_stan_mpcode", $price_info_arr["stan_mpcode"]); 
        $template->reg($prefix . "def_cut_wid"    , $price_info_arr["def_cut_wid"]); 
        $template->reg($prefix . "def_cut_vert"   , $price_info_arr["def_cut_vert"]); 

        //2-1 사이즈 자리수 정보 생성
        $pos_num = PrdtDefaultInfo::POSITION_NUMBER[$sortcode_b];
        $pos_num = $pos_num[$default_sel_arr["size"]];
        //$template->reg($prefix . "pos_num", $pos_num); 

        //3 재단, 작업사이즈간 차이 정보 생성
        $size_gap = " _gap%s";
        $val = ProductInfoClass::SIZE_GAP[$sortcode_b];
        $template->reg($prefix . "size_gap", sprintf($size_gap, $val));

        //4-1 제본 depth1 정보 생성
        $param["cate_sortcode"] = $sortcode_b;

        $binding_depth1 = $dao->selectBindingHtml($conn,
                                                  "depth1",
                                                  $param,
                                                  $price_info_arr);
        $template->reg($prefix . "binding_depth1", $binding_depth1); 

        //4-2 제본 depth2 정보 생성
        $param["depth1"] = $price_info_arr["binding_depth1"];

        $binding_depth2 = $dao->selectBindingHtml($conn,
                                                  "depth2",
                                                  $param,
                                                  $price_info_arr);

        unset($param);
        $template->reg($prefix . "binding_depth2", $binding_depth2); 

        //5 수량 정보 생성
        $amt_arr = PrdtDefaultInfo::AMT[$sortcode_b];
        $amt_arr_count = count($amt_arr);
        $amt = "";
        $amt_default = $default_sel_arr["amt"];
        for ($i = 0; $i < $amt_arr_count; $i++) {
            $val = $amt_arr[$i];
            $attr = "";
            if ($val === $amt_default) {
                $attr = "selected=\"selected\"";
                $price_info_arr["amt"] = $val;
            }
            $amt .= option($val, number_format(doubleval($val)), $attr);
        }
        $template->reg($prefix . "amt", $amt); 

        //6 종이 정보 생성
        $paper = $dao->selectCatePaperHtml($conn, $sortcode_b, $price_info_arr);
        $template->reg($prefix . "paper", $paper["info"]); 
        $template->reg($prefix . "paper_sort", $paper["sort"]); 

        //7 도수 정보 생성
        $param["cate_sortcode"] = $sortcode_b;
        $param["affil"]         = $price_info_arr["affil"];

        $print_tmpt = $dao->selectCatePrintTmptHtml($conn,
                                                    $param,
                                                    $price_info_arr);
        $print_tmpt = $print_tmpt["단면"] . $print_tmpt["양면"];
        $template->reg($prefix . "print_tmpt", $print_tmpt); 
        unset($param);

        //9 내지 페이지 정보 생성
        $page_arr = PrdtDefaultInfo::PAGE_INFO[$sortcode_b]["내지"];
        $page_arr_count = count($page_arr);
        $page = "";
        $page_default = $default_sel_arr["inner"];
        for ($i = 0; $i < $page_arr_count; $i++) {
            $val = $page_arr[$i];
            $page_val = number_format(doubleval($val)) . 'p';
            $attr = "";
            if ($page_val === $page_default) {
                $attr = "selected=\"selected\"";
                $price_info_arr["page"]["내지"]        = $val;
                $price_info_arr["page_detail"]["내지"] = '';
            }
            $page .= option($val, $page_val, $attr);
        }
        $template->reg($prefix . "page", $page); 

        //10 카테고리 독판여부, 수량단위 검색
        $cate_info_arr = $dao->selectCateInfo($conn, $sortcode_b);
        $mono_dvs = $cate_info_arr["mono_dvs"];
        $amt_unit = $cate_info_arr["amt_unit"];
        $tmpt_dvs = $cate_info_arr["tmpt_dvs"];
        unset($cate_info_arr);
        $template->reg($prefix . "mono_dvs", makeMonoDvsOption($mono_dvs)); 
        $template->reg($prefix . "amt_unit", $amt_unit); 
        $template->reg($prefix . "tmpt_dvs", $tmpt_dvs); 

        //12 지질느낌 검색
        $paper_sense = $dao->selectPaperDscr($conn, $price_info_arr["paper_mpcode"]);
        $template->reg($prefix . "paper_sense", $paper_sense); 

        //13 인쇄방식 정보 생성
        $param["cate_sortcode"] = $sortcode_b;
        $param["mpcode"] = $price_info_arr["print_mpcode"];
        $print_purp = $dao->selectCatePrintPurpHtml($conn, $param);
        $template->reg($prefix . "print_purp", $print_purp); 
        unset($param);

        if ($mix_yn === false) {
            //14-1 옵션 정보 생성
            $opt = $dao->selectCateOptHtml($conn, $sortcode_b);
            $template->reg($prefix . "opt", $opt["html"]); 

            //14-2 옵션 가격 레이어 생성
            $template->reg($prefix . "add_opt", ''); 
            if (empty($opt["info_arr"]["name"]) === false) {
                $add_opt = $opt["info_arr"]["name"];
                $add_opt = $dao->parameterArrayEscape($conn, $add_opt);
                $add_opt = $frontUtil->arr2delimStr($add_opt);

                $param["cate_sortcode"] = $sortcode_b;
                $param["opt_name"]      = $add_opt;
                $param["opt_idx"]       = $opt["info_arr"]["idx"];

                $add_opt = $dao->selectCateAddOptInfoHtml($conn, $param);
                unset($param);
                $template->reg($prefix . "add_opt", $add_opt); 
            }
        }

        //15-1 후공정 체크박스 생성
        $except_arr = array("제본" => true);

        $param["cate_sortcode"] = $sortcode_b;
        $param["dvs"]           = $dvs;

        $after = $dao->selectCateAfterHtml($conn, $param, $except_arr);
        $template->reg($prefix . "after", $after["html"]); 

        //8 기본 후공정 내역에 표시할 정보 생성
        $template->reg($prefix . "basic_after", ''); 
        if (empty($after["info_arr"]["basic"]) === false) {
            $basic_after = $after["info_arr"]["basic"];
            $basic_after = $frontUtil->arr2delimStr($basic_after, '|');
            $template->reg($prefix . "basic_after", $basic_after); 
        }
        unset($param);

        //15-2 추가 후공정 가격 레이어 생성
        $template->reg($prefix . "add_after", ''); 
        if (empty($after["info_arr"]["add"]) === false) {
            $add_after = $after["info_arr"]["add"];
            $add_after = $dao->parameterArrayEscape($conn, $add_after);
            $add_after = $frontUtil->arr2delimStr($add_after);

            $param["cate_sortcode"] = $sortcode_b;
            $param["after_name"]    = $add_after;
            $param["dvs"]           = $dvs;
            $add_after = $dao->selectCateAddAfterInfoHtml($conn, $param);
            unset($param);
            $template->reg($prefix . "add_after", $add_after); 
        }

        //16-1 가격 테이블 검색
        $mono_dvs = ($mono_dvs === '1' || $mono_dvs === '2') ? '0' : '1';
        $price_tb = $dao->selectPriceTableName($conn, $mono_dvs, $sell_site);

        //16-2 가격 검색용 공통 검색파라미터 생성
        $param["table_name"]           = $price_tb;
        $param["cate_sortcode"]        = $sortcode_b;
        $param["paper_mpcode"]         = $price_info_arr["paper_mpcode"];
        $param["bef_print_mpcode"]     = $price_info_arr["print_mpcode"];
        $param["bef_add_print_mpcode"] = '0';
        $param["aft_print_mpcode"]     = '0';
        $param["aft_add_print_mpcode"] = '0';
        $param["stan_mpcode"]          = $price_info_arr["stan_mpcode"];
        $param["amt"]                  = $price_info_arr["amt"];

        //17 내지 가격 검색
        $param["page"]        = $price_info_arr["page"]["내지"];
        $param["page_dvs"]    = "내지";
        $param["page_detail"] = $price_info_arr["page_detail"]["내지"];
        $param["affil"]       = $price_info_arr["affil"];

        $sell_price   = 0;
        $paper_price  = 0;
        $print_price  = 0;
        $output_price = 0;

        $page_dvs = '';

        if ($mono_dvs === '0') {
            $price_rs = $dao->selectPrdtPlyPrice($conn, $param);

            $page_dvs = $price_rs["page_dvs"];
            
            $sell_price  = doubleval($price_rs["new_price"]);
            $sell_price  = $frontUtil->ceilVal($sell_price);
            $print_price = $sell_price;
        } else {
            $param["affil"] = $price_info_arr["affil"];

            $price_rs = $dao->selectPrdtCalcPrice($conn, $param);

            $page_dvs = $price_rs["page_dvs"];

            $paper_price  = $frontUtil->ceilVal($price_rs["paper_price"]);
            $print_price  = $frontUtil->ceilVal($price_rs["print_price"]);
            $output_price = $frontUtil->ceilVal($price_rs["output_price"]);
            $sell_price   = $frontUtil->ceilVal($price_rs["sum_price"]);
        }

        //echo "$prefix / $paper_price / $print_price / $output_price / $sell_price\n";

        $template->reg($prefix . "paper_price" , $paper_price); 
        $template->reg($prefix . "print_price" , $print_price); 
        $template->reg($prefix . "output_price", $output_price); 

        $template->reg($prefix . "page"     , $page); 
        $template->reg($prefix . "page_dvs" , $page_dvs); 

        //18 제본 가격 검색용 맵핑코드 검색
        $param["cate_sortcode"] = $sortcode_b;
        $param["after_name"] = "제본";
        $param["depth1"] = $price_info_arr["binding_depth1"];
        $param["depth2"] = $price_info_arr["binding_depth2"];

        $binding_rs = $dao->selectCateAfterInfo($conn, $param);

        $binding_mpcode = $binding_rs->fields["mpcode"];

        unset($binding_rs);
        unset($param);

        //19-1 제본가격 검색용 종이수량 공통 정보 생성
        $param["amt"]     = $price_info_arr["amt"];
        $param["pos_num"] = $pos_num;
        $param["amt_unit"]  = $amt_unit;
        $param["crtr_unit"] =
            $dao->selectPrdtPaperInfo($conn,
                                      $price_info_arr["paper_mpcode"],
                                      "crtr_unit")["crtr_unit"];

        //19-2 제본가격 검색용 내지1 종이수량 계산
        $param["page_num"]  = $price_info_arr["page"]["내지"];

        $paper_amt_inner = $commonUtil->getPaperRealPrintAmt($param);

        unset($param);

        //20 내지 제본 가격 계산
        $param["sell_site"] = $sell_site;
        $param["mpcode"]    = $binding_mpcode;
        $param["amt"]       = $paper_amt_inner;

        $binding_price = $dao->selectBindingPrice($conn, $param);
        $binding_price = intval($binding_price);
        $template->reg($prefix . "binding_price", $binding_price); 

        //21 기본 옵션 가격 검색
        $param["cate_sortcode"] = $sortcode_b;
        $param["basic_yn"]      = 'Y';
        $param["sell_site"]     = $sell_site;

        $opt_price = intval($dao->selectCateOptSinglePrice($conn, $param));
        $template->reg($prefix . "opt_default_price", $opt_price);

        $sell_price += $binding_price + $opt_price;

        unset($param);

        $template->reg($prefix . "sell_price", number_format($sell_price));

        // 회원등급 할인 정보 생성
        if (empty($_SESSION["grade"]) === true) {
            $price_info_arr["grade_sale_rate"] = '0';
            $param["dscr"] = NO_LOGIN;
            $grade_sale = makeGradeSaleDl($param, $price_info_arr);
        } else {
            $param["cate_sortcode"] = $sortcode_b;
            $param["grade"]         = $_SESSION["grade"];
            $param["sell_price"]    = $sell_price;
            $grade_sale = $dao->selectGradeSalePriceHtml($conn,
                                                         $param,
                                                         $price_info_arr);
        }

        unset($param);
        $template->reg($prefix . "grade_sale", $grade_sale); 

        // 이벤트 할인 정보 생성
        $param["dscr"]  = NO_EVENT;
        $template->reg($prefix . "event_sale", makeEventSaleDl($param)); 
        unset($param);

        // 기본 할인가격 계산
        $sale_price = doubleval($sell_price - $price_info_arr["grade_sale"]);
        $template->reg($prefix . "sale_price", number_format($sale_price)); 

        $this->sell_price       = $sell_price;
        $this->grade_sale_rate  = $price_info_arr["grade_sale_rate"];
        $this->grade_sale_price = $price_info_arr["grade_sale"];
        $this->sale_price       = $sale_price;
        $this->paper_price      = $paper_price;
        $this->print_price      = $print_price;
        $this->output_price     = $output_price;
        $this->opt_price        = $opt_price;

        // 견적서 html 생성
        if ($mix_yn === false) {
            $param["esti_paper"]  = $paper_price;
            $param["esti_output"] = $output_price;
            $param["esti_print"]  = $print_price;
            $param["esti_after"]  = $binding_price;
            $param["esti_opt"]    = $opt_price;
            //$param["esti_tax"]    = $tax;
            $param["esti_sell_price"] = $sell_price;
            $param["esti_sale_price"] = $sale_price;
            $template->reg("quick_esti", getQuickEstimateHtml($param)); 
        }
    }
}
?>
