<?
/*
 * Copyright (c) 2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 2016-11-17 엄준현 생성
 *============================================================================
 *
 */
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/common_define/prdt_default_info.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/common_lib/CommonUtil.php');

// 1/2, 1/4, 1/8
define("HALF_QUARTER_EIGHTH", 875);
// 1/2, 1/4
define("HALF_QUARTER", 75);
// 1/2, 1/8
define("HALF_EIGHTH", 625);
// 1/2
define("HALF", 5);
// 1/4, 1/8
define("QUARTER_EIGHTH", 375);
// 1/4
define("QUARTER", 25);
// 1/8
define("EIGHTH", 125);

// 디버깅용 define
define("DEBUG", false);
define("DEBUG_STR", 'if (DEBUG) {echo sprintf("<pre>%s</pre>", $d_str);}');

class CalcPriceUtil {
    // 공통사용 객체
    var $conn = null;
    var $dao  = null;
    var $util = null;

    // 공통사용 독립값
    var $sell_site     = null;
    var $cate_sortcode = null;
    var $amt_unit      = null;
    var $flattyp_yn    = null;

    var $amt     = null;
    var $page    = null;
    var $pos_num = null;
    var $affil   = null;

    var $paper_crtr_unit = null;
    var $print_crtr_unit = null;

    var $cate_paper_mpcode         = null;
    var $cate_bef_print_mpcode     = null;
    var $cate_aft_print_mpcode     = null;
    var $cate_bef_add_print_mpcode = null;
    var $cate_aft_add_print_mpcode = null;
    var $cate_output_mpcode        = null;

    // 본 클래스 내에서 계산된 공통사용값
    var $real_paper_amt  = null;
    var $extra_paper_amt = null;
    var $board_amt       = null;
    var $tot_tmpt        = null;
    var $bef_tmpt        = null;
    var $aft_tmpt        = null;
    var $bef_add_tmpt    = null;
    var $aft_add_tmpt    = null;
    var $hong_count      = null;
    var $don_count       = null;

    // 각 항목 기준가격
    var $base_paper_price  = null;
    var $base_output_price = null;
    var $base_print_price  = null;

    // 각 항목 계산가격
    var $calc_paper_price  = null;
    var $calc_output_price = null;
    var $calc_print_price  = null;

    /**
     * @brief 공통으로 사용되는 값들 초기화
     *
     * @detail $param["sell_site"] = 판매채널(cpn_admin_seqno)
     * $param["cate_sortcode"] = 카테고리 분류코드
     * $param["amt_unit"] = 카테고리 수량단위
     * $param["flattyp_yn"] = 카테고리 낱장여부
     * $param["amt"] = 상품 수량
     * $param["page"] = 페이지 수량
     * $param["pos_num"] = 사이즈 자리수
     * $param["cate_paper_mpcode"] = 카테고리 종이 맵핑코드
     * $param["cate_bef_print_mpcode"] = 카테고리 전면 인쇄 맵핑코드
     * $param["cate_aft_print_mpcode"] = 카테고리 후면 인쇄 맵핑코드
     * $param["cate_bef_add_print_mpcode"] = 카테고리 전면추가 인쇄 맵핑코드
     * $param["cate_aft_add_print_mpcode"] = 카테고리 후면추가 인쇄 맵핑코드
     * $param["cate_output_mpcode"] = 카테고리 규격 맵핑코드
     *
     * @param 초기화 할 값들 파라미터
     */
    function __construct($param) {
        $cp = new ConnectionPool();
        $this->conn = $cp->getPooledConnection();
        $this->dao  = new ProductCommonDAO();
        $this->util = new CommonUtil();

        $this->sell_site     = $param["sell_site"];
        $this->cate_sortcode = $param["cate_sortcode"];
        $this->amt_unit      = $param["amt_unit"];
        $this->flattyp_yn    = $param["flattyp_yn"];

        $this->amt     = doubleval($param["amt"]);
        $this->page    = intval($param["page"]);
        $this->pos_num = intval($param["pos_num"]);
        $this->affil   = $param["affil"];

        $this->cate_paper_mpcode         = $param["cate_paper_mpcode"];
        $this->cate_bef_print_mpcode     = $param["bef_print_mpcode"];
        $this->cate_aft_print_mpcode     = $param["aft_print_mpcode"];
        $this->cate_bef_add_print_mpcode = $param["bef_add_print_mpcode"];
        $this->cate_aft_add_print_mpcode = $param["aft_add_print_mpcode"];
        $this->cate_output_mpcode        = $param["cate_output_mpcode"];
    }

    /**
     * @brief 계산형 가격중 종이가격 계산
     *
     * @detail 수량단위는 계산을 위해 종이수량을 기준으로 맞춘다
     * $param["*_print_name"] = 여분지 값을 구하기 위한 인쇄명
     *
     * @param $param = 계산필요값 파라미터
     *
     * @return 계산된 종이가격
     */
    function calcPaperPrice($param) {
        $d_str = "---------------------------- PAPER\n";
        eval(DEBUG_STR);

        $conn = $this->conn;
        $dao  = $this->dao;
        $util = $this->util;

        $sell_site = $this->sell_site;

        $amt      = $this->amt;
        $page     = $this->page;
        $pos_num  = $this->pos_num;
        $amt_unit = $this->amt_unit;

        $amt_paper_sale_flag = $param["amt_paper_sale_flag"];

        if ($page === 0) {
            return 0;
        }

        // 기계 대수 계산
        if ($this->flattyp_yn === 'Y') {
            $machine_count = 1;
        } else {
            $machine_count_info = $this->getMachineCount($page, $pos_num);

            $hong = $machine_count_info["hong"];
            $don  = $machine_count_info["don"];
            
            $machine_count = $hong + $this->getDonMachineCount($don);
        }

        // 상품종이 정보
        $temp = array();
        $temp["mpcode"] = $this->cate_paper_mpcode;
        $temp["col"]    = "mpcode, crtr_unit";
        $temp["affil"]  = $this->affil;
        $prdt_paper_info = $dao->selectPrdtPaperInfo($conn, $temp);
        $prdt_paper_mpcode = $prdt_paper_info["mpcode"];
        $paper_crtr_unit   = $prdt_paper_info["crtr_unit"];

        $d_str = "prdt_paper_mpcode : $prdt_paper_mpcode / paper_crtr_unit : $paper_crtr_unit\n";
        eval(DEBUG_STR);

        // 수량별 할인정보
        unset($temp);
        $temp["cate_sortcode"]     = $this->cate_sortcode;
        $temp["cate_paper_mpcode"] = $this->cate_paper_mpcode;
        $temp["cate_stan_mpcode"]  = $this->cate_output_mpcode;
        $temp["amt"]               = $amt;
        if ($amt_paper_sale_flag === false) {
            $temp["page_amt"]      = $page;
        }

        $sortcode_m = substr($this->cate_sortcode, 0, 6);
        if ($sortcode_m === "006001" || $sortcode_m === "006002") {
            $temp["amt"] /= PrdtDefaultInfo::MST_GROUP;
        }

        $amt_paper_sale_info = $dao->selectAmtPaperSale($conn, $temp);
        $amt_sale_rate       = doubleval($amt_paper_sale_info["rate"]);
        $amt_sale_aplc_price = doubleval($amt_paper_sale_info["aplc_price"]);

        $d_str = "amt_sale_rate : $amt_sale_rate / amt_sale_aplc_price : $amt_sale_aplc_price\n";
        eval(DEBUG_STR);

        // 여분지 수량
        $bef_print_name = $param["bef_print_name"];
        $aft_print_name = $param["aft_print_name"];
        $bef_add_print_name = $param["bef_add_print_name"];
        $aft_add_print_name = $param["aft_add_print_name"];

        $extra_paper_amt_arr = PrdtDefaultInfo::EXTRA_PAPER_AMT;

        $extra_paper_amt = $extra_paper_amt_arr[$bef_print_name];
        if (!empty($aft_print_name)) {
            $extra_paper_amt += $extra_paper_amt_arr[$aft_print_name];
        }
        if (!empty($bef_add_print_name)) {
            $extra_paper_amt += $extra_paper_amt_arr[$bef_add_print_name];
        }
        if (!empty($aft_add_print_name)) {
            $extra_paper_amt += $extra_paper_amt_arr[$aft_add_print_name];
        }
        $extra_paper_amt *= $machine_count;

        $d_str = "machine_count : $machine_count / paper_extra_amt1 : $extra_paper_amt\n";
        eval(DEBUG_STR);

        if ($paper_crtr_unit === 'R') {
            $extra_paper_amt /= 500.0;
        }

        $d_str = "paper_extra_amt2 : $extra_paper_amt\n";
        eval(DEBUG_STR);

        // 사용자가 선택한 수량과 페이지에 따른 실제 종이수량 계산
        if ($this->flattyp_yn === 'N') {
            $real_paper_amt = $util->getPaperRealPrintAmt(
                array(
                    "amt"       => $amt,
                    "pos_num"   => $pos_num,
                    "page_num"  => $page,
                    "amt_unit"  => $amt_unit,
                    "crtr_unit" => $paper_crtr_unit
                )
            );

        } else {
            $real_paper_amt = $util->calcPaperAmtByCrtrUnit($paper_crtr_unit,
                                                            $amt_unit,
                                                            $amt);
        }

        $d_str = "paper_amt : $amt / pos_num : $pos_num / page : $page / extra_paper_amt : $extra_paper_amt / real_paper_amt : $real_paper_amt\n";
        eval(DEBUG_STR);

        unset($temp);
        $temp["sell_site"] = $sell_site;
        $temp["mpcode"]    = $prdt_paper_mpcode;

        $paper_price = $dao->selectPaperPrice($conn, $temp);

        $d_str = "paper_price : $paper_price\n";
        eval(DEBUG_STR);

        // 여분지 종이 가격
        $extra_paper_price = round($extra_paper_amt * $paper_price);
        // 인쇄 종이 가격
        $real_paper_price = $real_paper_amt * $paper_price;
        // 종이 수량별 할인금액 계산
        $real_paper_price = (($amt_sale_rate / 100.0) * $real_paper_price) +
                            $real_paper_price + $amt_sale_aplc_price;
        $real_paper_price = round($real_paper_price);
        // 종이 합계 가격
        $calc_paper_price = $extra_paper_price + $real_paper_price;

        $d_str = "extra_paper_price : $extra_paper_price / real_paper_price : $real_paper_price / calc_paper_price : $calc_paper_price\n";
        eval(DEBUG_STR);

        $this->paper_crtr_unit  = $paper_crtr_unit;
        $this->real_paper_amt   = $real_paper_amt;
        $this->extra_paper_amt  = $extra_paper_amt;
        $this->base_paper_price = $paper_price;
        $this->calc_paper_price = $calc_paper_price;
        $this->hong_count       = $machine_count_info["hong"];
        $this->don_count        = $machine_count_info["don"];

        return $calc_paper_price;
    }

    /**
     * @brief 계산형 가격중 낱장형 인쇄가격 계산
     *
     * @detail 카테고리 수량 A, 인쇄수량1(ex. 1R) B, 인쇄수량2(ex. 2R) C 
     * price = A*B > A*C ? A*B : A*C;
     *
     * 카테고리 수량 A(ex. 0.5R), 인쇄가격 최소수량 B(ex. 1R)
     * amt = A < B ? B : A;
     *
     * @return 계산된 인쇄가격
     */
    function calcSheetPrintPrice() {
        $d_str = "---------------------------- SHEET PRINT\n";
        eval(DEBUG_STR);

        $conn = $this->conn;
        $dao  = $this->dao;
        $util = $this->util;

        $sell_site = $this->sell_site;
        $page      = $this->page;

        if ($page === 0) {
            return 0;
        }

        $real_paper_amt  = $this->real_paper_amt;
        $paper_crtr_unit = $this->paper_crtr_unit;

        $print_bef_cate_mpcode = $this->cate_bef_print_mpcode;
        $print_aft_cate_mpcode = $this->cate_aft_print_mpcode;
        $print_bef_add_cate_mpcode = $this->cate_bef_add_print_mpcode;
        $print_aft_add_cate_mpcode = $this->cate_aft_add_print_mpcode;

        $tot_tmpt  = 0;
        $board_amt = 0;

        $prdt_bef_print_info = $dao->selectPrdtPrintInfo($conn,
                                                        $print_bef_cate_mpcode);
        $tot_tmpt  = $prdt_bef_print_info["tot_tmpt"];
        $board_amt = $prdt_bef_print_info["output_board_amt"];
        if (!empty($print_aft_cate_mpcode)) {
            $prdt_aft_print_info = $dao->selectPrdtPrintInfo($conn,
                                                        $print_aft_cate_mpcode);
            $tot_tmpt  += $prdt_aft_print_info["tot_tmpt"];
            $board_amt += $prdt_aft_print_info["output_board_amt"];
        }
        if (!empty($print_bef_add_cate_mpcode)) {
            $prdt_bef_add_print_info = $dao->selectPrdtPrintInfo($conn,
                                                    $print_bef_add_cate_mpcode);
            $tot_tmpt  += $prdt_bef_add_print_info["tot_tmpt"];
            $board_amt += $prdt_bef_add_print_info["output_board_amt"];
        }
        if (!empty($print_aft_add_cate_mpcode)) {
            $prdt_aft_add_print_info = $dao->selectPrdtPrintInfo($conn,
                                                    $print_aft_add_cate_mpcode);
            $tot_tmpt  += $prdt_aft_add_print_info["tot_tmpt"];
            $board_amt += $prdt_aft_add_print_info["output_board_amt"];
        }

        $prdt_print_mpcode = $prdt_bef_print_info["prdt_mpcode"];

        $d_str = "tot_tmpt : $tot_tmpt / board_amt : $board_amt\n";
        eval(DEBUG_STR);

        $print_crtr_unit = $prdt_bef_print_info["crtr_unit"];

        // 종이 수량단위와 인쇄 수량단위가 틀릴경우
        $real_paper_amt = $util->calcPaperAmtByCrtrUnit($print_crtr_unit,
                                                        $paper_crtr_unit,
                                                        $real_paper_amt);

        $param = array();
        $param["sell_site"] = $sell_site;
        $param["mpcode"]    = $prdt_print_mpcode;
        $param["amt"]       = $real_paper_amt;
        $param["tot_tmpt"]  = $tot_tmpt;

        $min_amt = $dao->selectPrintMinAmt($conn, $param);

        $d_str = "real_paper_amt : $real_paper_amt / min_amt : $min_amt\n";
        eval(DEBUG_STR);

        $print_price = $dao->selectPrintPrice($conn, $param);

        if ($real_paper_amt < $min_amt) {
            $real_paper_amt = $min_amt;
        }

        $calc_price = $print_price * $tot_tmpt * ceil($real_paper_amt);

        $d_str = "print_price : $print_price / tot_tmpt : $tot_tmpt / calc_price : $calc_price\n";
        eval(DEBUG_STR);

        $this->tot_tmpt         = $tot_tmpt;
        $this->board_amt        = $board_amt;
        $this->base_print_price = $print_price;
        $this->calc_print_price = $calc_price;

        return $calc_price;
    }

    /**
     * @brief 책자형 인쇄 가격 계산
     *
     * @detail 책자형 인쇄는 낱장형 인쇄와 다르게 출력과 똑같이
     * 인쇄 대수가 적용된다.
     *
     * 홍각기 / 돈땡에 따라서 페이지가 분할되고 분할된 페이지에 따른
     * 종이 수량을 계산해서 종이 가격을 별도로 계산해서 합친다.
     *
     * @return 인쇄 가격
     */
    function calcBookletPrintPrice() {
        $d_str = "---------------------------- BOOKLET PRINT\n";
        eval(DEBUG_STR);

        if ($this->page === 0) {
            return 0;
        }

        $conn = $this->conn;
        $dao  = $this->dao;
        $util = $this->util;

        $sell_site = $this->sell_site;
        $pos_num   = $this->pos_num;
        $page      = $this->page;
        $amt       = $this->amt;
        $amt_unit  = $this->amt_unit;

        $hong_count = $this->hong_count;
        $don_count  = $this->don_count;

        $paper_crtr_unit = $this->paper_crtr_unit;

        $print_bef_cate_mpcode = $this->cate_bef_print_mpcode;
        $print_aft_cate_mpcode = $this->cate_aft_print_mpcode;
        $print_bef_add_cate_mpcode = $this->cate_bef_add_print_mpcode;
        $print_aft_add_cate_mpcode = $this->cate_aft_add_print_mpcode;

        $tot_tmpt  = 0;
        $board_amt = 0;

        $bef_tmpt = 0;
        $aft_tmpt = 0;
        $bef_add_tmpt = 0;
        $aft_add_tmpt = 0;

        $prdt_bef_print_mpcode = null;
        $prdt_aft_print_mpcode = null;
        $prdt_bef_add_print_mpcode = null;
        $prdt_aft_add_print_mpcode = null;

        $prdt_bef_print_info = $dao->selectPrdtPrintInfo($conn,
                                                        $print_bef_cate_mpcode);
        $bef_tmpt   = intval($prdt_bef_print_info["bef_tmpt"]);
        $tot_tmpt  += intval($prdt_bef_print_info["tot_tmpt"]);
        $board_amt += intval($prdt_bef_print_info["output_board_amt"]);
        $prdt_bef_print_mpcode = $prdt_bef_print_info["prdt_mpcode"];

        $prdt_aft_print_info = $dao->selectPrdtPrintInfo($conn,
                                                        $print_aft_cate_mpcode);
        $aft_tmpt   = intval($prdt_aft_print_info["aft_tmpt"]);
        $tot_tmpt  += intval($prdt_aft_print_info["tot_tmpt"]);
        $board_amt += intval($prdt_aft_print_info["output_board_amt"]);
        $prdt_aft_print_mpcode = $prdt_aft_print_info["prdt_mpcode"];

        if (!empty($print_bef_add_cate_mpcode)) {
            $prdt_bef_add_print_info = $dao->selectPrdtPrintInfo($conn,
                                                    $print_bef_add_cate_mpcode);
            $bef_add_tmpt = intval($prdt_bef_add_print_info["bef_tmpt"]);
            $tot_tmpt  += intval($prdt_bef_add_print_info["tot_tmpt"]);
            $board_amt += intval($prdt_bef_add_print_info["output_board_amt"]);
            $prdt_bef_add_print_mpcode = $prdt_bef_print_info["prdt_mpcode"];
        }

        if (!empty($print_aft_add_cate_mpcode)) {
            $prdt_aft_add_print_info = $dao->selectPrdtPrintInfo($conn,
                                                    $print_aft_add_cate_mpcode);
            $aft_add_tmpt = intval($prdt_aft_add_print_info["aft_tmpt"]);
            $tot_tmpt  += intval($prdt_aft_add_print_info["tot_tmpt"]);
            $board_amt += intval($prdt_aft_add_print_info["output_board_amt"]);
            $prdt_aft_add_print_mpcode = $prdt_aft_print_info["prdt_mpcode"];
        }

        $print_crtr_unit = $prdt_bef_print_info["crtr_unit"];

        $d_str = "bef_tmpt : $bef_tmpt / aft_tmpt : $aft_tmpt / tot_tmpt : $tot_tmpt / board_amt : $board_amt / print_crtr_unit : $print_crtr_unit\n";
        eval(DEBUG_STR);

        // 인쇄 대수로부터 대수별 페이지 계산
        // 인쇄 대수 계산
        $calc_info = array();
        $calc_info["hong"]    = $hong_count;
        $calc_info["don"]     = $don_count;
        $calc_info["pos_num"] = $pos_num;

        // 대수별 페이지 계산
        $calc_info = $this->getPrintBookletPageNum($calc_info);

        $param = array();
        $param["pos_num"]   = $pos_num;
        $param["amt"]       = $amt;
        $param["amt_unit"]  = $amt_unit;
        $param["crtr_unit"] = $paper_crtr_unit;

        // 홍각기 종이수량
        $param["page_num"] = $calc_info["hong_page_num"];
        $hong_paper_amt = $util->getPaperRealPrintAmt($param);
        // 1/2 돈땡 종이수량
        $param["page_num"] = $calc_info["don_h_page_num"];
        $don_h_paper_amt = $util->getPaperRealPrintAmt($param);
        // 1/4 돈땡 종이수량
        $param["page_num"] = $calc_info["don_q_page_num"];
        $don_q_paper_amt = $util->getPaperRealPrintAmt($param);
        // 1/8 돈땡 종이수량
        $param["page_num"] = $calc_info["don_e_page_num"];
        $don_e_paper_amt = $util->getPaperRealPrintAmt($param);

        $d_str = "h_paper_amt : $hong_paper_amt / d_1/2_paper_amt : $don_h_paper_amt / d_1/4_paper_amt : $don_q_paper_amt / d_1/8_paper_amt : $don_e_paper_amt\n";
        eval(DEBUG_STR);

        unset($calc_info);
        unset($param["amt_unit"]);
        unset($param["crtr_unit"]);

        $param["sell_site"] = $sell_site;

        // 전면도수 가격
        $bef_hong_price  = 0;
        $bef_don_h_price = 0;
        $bef_don_q_price = 0;
        $bef_don_e_price = 0;

        $bef_tot_tmpt      = $bef_tmpt + $bef_add_tmpt;
        $param["tot_tmpt"] = $bef_tot_tmpt;
        $param["mpcode"]   = $prdt_bef_print_mpcode;
        // 홍각기 인쇄가격
        if ($hong_paper_amt > 0.0) {
            $param["amt"] = doubleval($hong_paper_amt) / doubleval($hong_count);
            $bef_hong_price  = $this->getPrintSellPrice($conn, $dao, $param);
            $bef_hong_price *= $hong_count;

            $d_str = "hong_amt : $param[amt]\n";
            eval(DEBUG_STR);
        }
        // 1/2 돈땡 인쇄가격
        if ($don_h_paper_amt > 0.0) {
            $param["amt"] = $don_h_paper_amt;
            $bef_don_h_price = $this->getPrintSellPrice($conn, $dao, $param);
        }
        // 1/4 돈땡 인쇄가격
        if ($don_q_paper_amt > 0.0) {
            $param["amt"] = $don_q_paper_amt;
            $bef_don_q_price = $this->getPrintSellPrice($conn, $dao, $param);
        }
        // 1/8 돈땡 인쇄가격
        if ($don_e_paper_amt > 0.0) {
            $param["amt"] = $don_e_paper_amt;
            $bef_don_e_price = $this->getPrintSellPrice($conn, $dao, $param);
        }

        $bef_print_price_sum = $bef_hong_price +
                               $bef_don_h_price +
                               $bef_don_q_price +
                               $bef_don_e_price;

        // 후면도수 가격
        $aft_hong_price  = 0;
        $aft_don_h_price = 0;
        $aft_don_q_price = 0;
        $aft_don_e_price = 0;

        $aft_tot_tmpt      = $aft_tmpt + $aft_add_tmpt;
        $param["tot_tmpt"] = $aft_tot_tmpt;
        $param["mpcode"]   = $prdt_aft_print_mpcode;
        // 홍각기 인쇄가격
        if ($aft_tot_tmpt > 0 && $hong_paper_amt > 0.0) {
            $param["amt"] = doubleval($hong_paper_amt) / doubleval($hong_count);
            $aft_hong_price  = $this->getPrintSellPrice($conn, $dao, $param);
            $aft_hong_price *= $hong_count;

            $d_str = "hong_amt : $param[amt]\n";
            eval(DEBUG_STR);
        }
        // 1/2 돈땡 인쇄가격
        if ($aft_tot_tmpt > 0 && $don_h_paper_amt > 0.0) {
            $param["amt"] = $don_h_paper_amt;
            $aft_don_h_price = $this->getPrintSellPrice($conn, $dao, $param);
        }
        // 1/4 돈땡 인쇄가격
        if ($aft_tot_tmpt > 0 && $don_q_paper_amt > 0.0) {
            $param["amt"] = $don_q_paper_amt;
            $aft_don_q_price = $this->getPrintSellPrice($conn, $dao, $param);
        }
        // 1/8 돈땡 인쇄가격
        if ($aft_tot_tmpt > 0 && $don_e_paper_amt > 0.0) {
            $param["amt"] = $don_e_paper_amt;
            $aft_don_e_price = $this->getPrintSellPrice($conn, $dao, $param);
        }

        $aft_print_price_sum = $aft_hong_price +
                               $aft_don_h_price +
                               $aft_don_q_price +
                               $aft_don_e_price;

        $d_str = "bef_print_price : $bef_print_price_sum / aft_print_price : $aft_print_price_sum\n";
        eval(DEBUG_STR);

        $calc_price = $bef_print_price_sum + $aft_print_price_sum;

        $this->board_amt        = $board_amt;
        $this->tot_tmpt         = $tot_tmpt;
        $this->bef_tmpt         = $bef_tmpt;
        $this->aft_tmpt         = $aft_tmpt;
        $this->bef_add_tmpt     = $bef_add_tmpt;
        $this->aft_add_tmpt     = $aft_add_tmpt;
        $this->calc_print_price = $calc_price;

        return $calc_price;
    }

    /**
     * @brief 인쇄 책자형 홍각기/돈땡별 페이지수 계산
     *
     * @detail $info["hong"] = 홍각기 대수
     * $info["don"] = 돈땡 대수
     * $info["pos_num"] = 자리수
     *
     * @param $info = 정보배열
     *
     * @return 인쇄 가격
     */
    function getPrintBookletPageNum($info) {
        $hong_count = $info["hong"];
        $don_count  = $info["don"];
        $pos_num    = $info["pos_num"];

        // 기본 페이지수
        $def_page_num = $pos_num * 2;

        // 홍각기 페이지수
        $hong_page_num = $hong_count * $def_page_num;
        // 1/2 돈땡 페이지수
        $don_h_page_num = 0;
        // 1/4 돈땡 페이지수
        $don_q_page_num = 0;
        // 1/8 돈땡 페이지수
        $don_e_page_num = 0;

        switch ($don_count) {
            case HALF_QUARTER_EIGHTH :
                // 1/2(8p), 1/4(4p), 1/8(2p) 돈땡
                $don_h_page_num = $def_page_num / 2;
                $don_q_page_num = $def_page_num / 4;
                $don_e_page_num = $def_page_num / 8;

                break;
            case HALF_QUARTER :
                // 1/2, 1/4 돈땡
                $don_h_page_num = $def_page_num / 2;
                $don_q_page_num = $def_page_num / 4;

                break;
            case HALF_EIGHTH :
                // 1/2, 1/8 돈땡
                $don_h_page_num = $def_page_num / 2;
                $don_e_page_num = $def_page_num / 8;

                break;
            case HALF :
                // 1/2 돈땡
                $don_h_page_num = $def_page_num / 2;

                break;
            case QUARTER_EIGHTH :
                // 1/4, 1/8 돈땡
                $don_q_page_num = $def_page_num / 4;
                $don_e_page_num = $def_page_num / 8;

                break;
            case QUARTER :
                // 1/4 돈땡
                $don_q_page_num = $def_page_num / 4;

                break;
            case EIGHTH :
                // 1/8 돈땡
                $don_e_page_num = $def_page_num / 8;

                break;
        }

        $d_str = "hong_count : $hong_count / don_count : $don_count\n";
        eval(DEBUG_STR);
        $d_str = "hong_page : $hong_page_num / 1/2_page :  $don_h_page_num / 1/4_page : $don_q_page_num /  1/8_page : $don_e_page_num\n";
        eval(DEBUG_STR);

        return array(
            "hong_page_num"  => $hong_page_num,
            "don_h_page_num" => $don_h_page_num,
            "don_q_page_num" => $don_q_page_num,
            "don_e_page_num" => $don_e_page_num
        );
    }

    /**
     * @brief 인쇄 가격 계산
     *
     * @detail 실제 계산 공식은 아래와 같다
     *  - 인쇄 기계 대수 공식 =
     *      소수점 올림{$사용자가 입력한 페이지 수$ / ($규격의 자리수$ * 2)}
     *  - 인쇄 가격 공식 =
     *      ($전체 도수$ * $인쇄 대수$ * 소수점 올림{$인쇄 연수$}) * $인쇄 단가$
     *
     * @param $conn  = connection identifier
     * @param $dao   = 가격 검색용 dao
     * @param $param = 가격검색에 필요한 정보배열
     *
     * @return 인쇄 가격
     */
    function getPrintSellPrice($conn, $dao, $param) {
        $page_num = $param["page_num"];
        $tot_tmpt = $param["tot_tmpt"];
        $amt      = $param["amt"];

        if ($amt == 0) {
            return 0;
        }

        $min_amt = $dao->selectPrintMinAmt($conn, $param);

        $d_str = "amt : $amt / min_amt : $min_amt\n";
        eval(DEBUG_STR);

        if ($amt < $min_amt) {
            $amt = $min_amt;
        }

        $d_str = "amt : $amt / min_amt : $min_amt\n";
        eval(DEBUG_STR);

        $sell_price = $dao->selectPrintPrice($conn, $param);

        $price = $tot_tmpt * ceil($amt) * $sell_price;

        $d_str = "print_sell_price : $sell_price / calc_price : $price\n";
        eval(DEBUG_STR);

        return intval($price);
    }

    /**
     * @brief 계산형 가격중 낱장형 출력가격 계산
     *
     * @detail 수량단위는 계산을 위해 종이수량을 기준으로 맞춘다
     * $param["*_print_name"] = 여분지 값을 구하기 위한 인쇄명
     *
     * @param $param = 계산필요값 파라미터
     *
     * @return 계산된 종이가격
     */
    function calcSheetOutputPrice() {
        $d_str = "---------------------------- SHEET OUTPUT\n";
        eval(DEBUG_STR);

        if ($this->page === 0) {
            return 0;
        }

        $conn = $this->conn;
        $dao  = $this->dao;

        $sell_site = $this->sell_site;

        $pos_num   = $this->pos_num;
        $board_amt = $this->board_amt;

        /* 돈땡 기준이라서 이렇게 해놓은듯??
        if ($pos_num > 4) {
            $board_amt /= 2;
        }
        */

        $prdt_output_info = $dao->selectPrdtOutputInfo($conn,
                                                     $this->cate_output_mpcode);
        $prdt_output_mpcode = $prdt_output_info["prdt_mpcode"];

        $param = array();
        $param["sell_site"] = $sell_site;
        $param["mpcode"]    = $prdt_output_mpcode;

        $output_price = $dao->selectOutputPrice($conn, $param);

        $calc_price = $board_amt * $output_price;

        $d_str = "output_price : $output_price / calc_price : $calc_price\n\n";
        eval(DEBUG_STR);

        $this->base_output_price = $output_price;
        $this->calc_output_price = $calc_price;

        return $calc_price;
    }

    /**
     * @brief 책자형 출력 가격 계산
     *
     * $info["sell_site"] = 판매채널
     *
     * @detail 실제 계산 공식은 아래와 같다
     *  - 출력 기계 대수 공식 =
     *      $사용자가 입력한 페이지 수$ / ($규격의 자리수$ * 2)
     *  - 출력판수 산출 공식 =
     *      ($홍각기 대 수$ * $판수$) + ($돈땡 대 수$ + ($판수$ / 2))
     *  - 출력 가격 공식 =
     *      $전체 출력판 수$ * $출력판당 가격$
     *
     * @detail 홍각기/돈땡 구분법은 전/후면 도수로 구분한다
     *  - 홍각기 => 전면도수 != 후면도수
     *  - 돈땡   => 전면도수 == 후면도수
     *
     * @return 출력 가격
     */
    function calcBookletOutputPrice() {
        $d_str = "---------------------------- BOOKLET OUTPUT\n";
        eval(DEBUG_STR);

        if ($this->page === 0) {
            return 0;
        }

        $conn = $this->conn;
        $dao  = $this->dao;
        $util = $this->util;

        $sell_site    = $this->sell_site;
        $board_amt    = intval($this->board_amt);
        $bef_tmpt     = intval($this->bef_tmpt);
        $aft_tmpt     = intval($this->aft_tmpt);
        $bef_add_tmpt = intval($this->bef_add_tmpt);
        $aft_add_tmpt = intval($this->aft_add_tmpt);
        $hong_count   = $this->hong_count;
        $don_count    = $this->getDonMachineCount($this->don_count);

        $d_str = "hong_count : $hong_count / don_count : $don_count / board_amt : $board_amt / board_count : $board_count\n";
        eval(DEBUG_STR);

        // 돈땡 판수 계산용, 각 도수가 1도보다 커야 돈땡 가능
        if ((1 < $bef_tmpt) && (1 < $aft_tmpt) && ($bef_tmpt === $aft_tmpt)) {
            $board_count = $don_count * ($board_amt >> 1);
        } else {
            if (((1 === $bef_tmpt) || (1 === $aft_tmpt)) &&
                    $don_count !== 0) {
                $don_count = 0;
                $hong_count++;
            }

            $board_count = $don_count * $board_amt;
        }

        $board_count += $hong_count * $board_amt;

        $d_str = "hong_count : $hong_count / don_count : $don_count / board_amt : $board_amt / board_count : $board_count\n";
        eval(DEBUG_STR);

        $prdt_output_info = $dao->selectPrdtOutputInfo($conn,
                                                     $this->cate_output_mpcode);
        $prdt_output_mpcode = $prdt_output_info["prdt_mpcode"];

        $param = array();
        $param["sell_site"] = $sell_site;
        $param["mpcode"]    = $prdt_output_mpcode;

        $output_price = $dao->selectOutputPrice($conn, $param);

        $calc_price = $board_count * $output_price;

        $d_str = "output_price : $output_price / calc_price : $calc_price\n";
        eval(DEBUG_STR);

        $this->base_output_price = $output_price;
        $this->calc_output_price = $calc_price;

        return $calc_price;
    }

    /**
     * @brief 인쇄/출력 기계 대수 반환
     *
     * @param $page_num = 페이지 수
     * @param $pos_num  = 자리 수
     *
     * @return $ret["hone"] = 홍각기 상수값
     * $ret["don"] = 돈땡 상수값
     */
    function getMachineCount($page_num, $pos_num) {
        $d_str = "page : $page_num / pos_num : $pos_num\n";
        eval(DEBUG_STR);

        $count = strval($page_num / ($pos_num * 2.0));
        $count = explode('.', $count);
        // 홍각기 대수
        $hong_count = intval($count[0]);
        // 돈땡 대수
        $don_count = intval($count[1]);

        return array(
            "hong" => $hong_count,
            "don"  => $don_count
        );
    }

    /**
     * @brief 종이 실제 인쇄수량 재계산
     */
    function calcRealPaperAmt() {
        if ($this->flattyp_yn === 'N') {
            $real_paper_amt = $this->util->getPaperRealPrintAmt(
                array(
                    "amt"       => $this->amt,
                    "pos_num"   => $this->pos_num,
                    "page_num"  => $this->page,
                    "amt_unit"  => $this->amt_unit,
                    "crtr_unit" => $this->paper_crtr_unit
                )
            );

        } else {
            $real_paper_amt =
                $this->util->calcPaperAmtByCrtrUnit($this->paper_crtr_unit,
                                                    $this->amt_unit,
                                                    $this->amt);
        }

        $this->real_paper_amt = $real_paper_amt;
    }

    /**
     * @brief 돈땡 실 기계대수 계산
     *
     * @param $don_count = 돈땡 구분값
     */
    function getDonMachineCount($don_count) {
        switch ($don_count) {
            case HALF_QUARTER_EIGHTH :
                // 1/2, 1/4, 1/8 돈땡
                $don_count = 3;
                break;
            case HALF_QUARTER :
                // 1/2, 1/4 돈땡
                $don_count = 2;
                break;
            case HALF_EIGHTH :
                // 1/2, 1/8 돈땡
                $don_count = 2;
                break;
            case HALF :
                // 1/2 돈땡
                $don_count = 1;
                break;
            case QUARTER_EIGHTH :
                // 1/4, 1/8 돈땡
                $don_count = 2;
                break;
            case QUARTER :
                // 1/4 돈땡
                $don_count = 1;
                break;
            case EIGHTH :
                // 1/8 돈땡
                $don_count = 1;
                break;
        }

        return $don_count;
    }

    /**
     * @brief 카테고리 종이 맵핑코드 변경
     *
     * @param $mpcode = 카테고리 종이 맵핑코드
     */
    function setCatePaperMpcode($mpcode) {
        $this->cate_paper_mpcode = $mpcode;
    }

    /**
     * @brief 수량 변경
     *
     * @param $amt = 수량
     */
    function setAmt($amt) {
        $this->amt = $amt;
    }
}
?>
