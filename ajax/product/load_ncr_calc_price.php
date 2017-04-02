<?
/*
 * Copyright (c) 2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 2016-11-22 엄준현 추가(ncr 로직만 따로 분리)
 *============================================================================
 *
 */
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/prdt_default_info.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/CalcPriceUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$util = new FrontCommonUtil();
$dao = new ProductCommonDAO();
$fb = new FormBean();

$sell_site = $fb->session("sell_site");

$fb = $fb->getForm();

$cate_sortcode = $fb["cate_sortcode"];
$dvs           = $fb["dvs"];
$flattyp_yn    = $fb["flattyp_yn"];
$amt_unit      = $fb["amt_unit"];
$diff_yn       = ($fb["diff_yn"] === "true") ? true : false;

$paper_mpcode_arr = $fb["paper_mpcode_arr"];
$stan_mpcode      = $fb["stan_mpcode"];
$print_bef_mpcode = $fb["bef_print_mpcode"];
$print_aft_mpcode = $fb["aft_print_mpcode"];
$print_bef_add_mpcode = $fb["bef_add_print_mpcode"];
$print_aft_add_mpcode = $fb["aft_add_print_mpcode"];

$print_bef_name = $fb["bef_print_name"];
$print_aft_name = $fb["aft_print_name"];
$print_bef_add_name = $fb["bef_add_print_name"];
$print_aft_add_name = $fb["aft_add_print_name"];

$amt     = PrdtDefaultInfo::MST_GROUP * intval($fb["amt"]);
$affil   = $fb["affil"];
$pos_num = $fb["pos_num"];

$page_info    = explode('!', $fb["page_info"]);
$page         = $page_info[0];
$page_detail  = $page_info[1];

$temp = array();
$temp["sell_site"]     = $sell_site;
$temp["cate_sortcode"] = $cate_sortcode;
$temp["amt_unit"]      = $amt_unit;
$temp["flattyp_yn"]    = $flattyp_yn;

$temp["amt"]     = $amt;
$temp["page"]    = $page;
$temp["pos_num"] = $pos_num;
$temp["affil"]   = $affil;

$temp["cate_output_mpcode"] = $stan_mpcode;

$temp["bef_print_mpcode"]     = $print_bef_mpcode;
$temp["aft_print_mpcode"]     = $print_aft_mpcode;
$temp["bef_add_print_mpcode"] = $print_bef_add_mpcode;
$temp["aft_add_print_mpcode"] = $print_aft_add_mpcode;

$calc_util = new CalcPriceUtil($temp);

unset($temp);
$temp["bef_print_name"] = $print_bef_name;
$temp["aft_print_name"] = $print_aft_name;
$temp["bef_add_print_name"] = $print_bef_add_name;
$temp["aft_add_print_name"] = $print_aft_add_name;

$sum_paper_price  = 0;
$sum_print_price  = 0;
$sum_output_price = 0;

$paper_mpcode_arr_count = count($paper_mpcode_arr);

$is_fst = true;
for ($i = 0; $i < $paper_mpcode_arr_count ; $i++) {
    $paper_mpcode = $paper_mpcode_arr[$i];

    $calc_util->setCatePaperMpcode($paper_mpcode);

    $paper_price = $util->ceilVal($calc_util->calcPaperPrice($temp));

    $sum_paper_price  += $paper_price;

    if ($diff_yn) {
        $print_price  = $util->ceilVal($calc_util->calcSheetPrintPrice());
        $output_price = $util->ceilVal($calc_util->calcSheetOutputPrice());

        $sum_print_price  += $print_price;
        $sum_output_price += $output_price;
    } else {
        if ($is_fst) {
            $calc_util->setAmt($amt * $paper_mpcode_arr_count);
            $calc_util->calcRealPaperAmt();

            $print_price  = $util->ceilVal($calc_util->calcSheetPrintPrice());
            $output_price = $util->ceilVal($calc_util->calcSheetOutputPrice());

            $sum_print_price  += $print_price;
            $sum_output_price += $output_price;
            $is_fst = false;

            $calc_util->setAmt($amt);
        }
    }
}

$sell_price = $sum_paper_price + $sum_print_price + $sum_output_price;

$price_json  = '{';
$price_json .= " \"paper\"  : \"%s\",";
$price_json .= " \"print\"  : \"%s\",";
$price_json .= " \"output\" : \"%s\",";
$price_json .= " \"sell_price\" : \"%s\"";
$price_json .= '}';

$outer  = '{';
$outer .= " \"%s\"  : %s";

$outer  = sprintf($outer, $dvs, $price_json);
$outer  = sprintf($outer, $util->ceilVal($sum_paper_price)
                        , $util->ceilVal($sum_print_price)
                        , $util->ceilVal($sum_output_price)
                        , $util->ceilVal($sell_price));
$outer .= '}';

echo $outer;

$conn->Close();
?>
