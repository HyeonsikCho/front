<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/BindingPriceUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/prdt_default_info.php");

$fb = new FormBean();
$fb = $fb->getForm();

$cate_sortcode = $fb["cate_sortcode"];
$stan_name     = $fb["stan_name"];

$param = array();
$param["cate_sortcode"] = $cate_sortcode;
$param["amt"]           = $fb["amt"];
$param["page"]          = $fb["page"];
$param["price"]         = $fb["price"];
$param["coating_yn"]    = ($fb["coating_yn"] === "true") ? true : false;
$param["depth1"]        = $fb["depth1"];
$param["stan_name"]     = $stan_name;
$param["pos_num"] = PrdtDefaultInfo::POSITION_NUMBER[$cate_sortcode][$stan_name];

$binding_price_util = new BindingPriceUtil($param);
echo $binding_price_util->calcBindingPrice();
?>
