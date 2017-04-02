<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductNcDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new ProductNcDAO();
$fb = new FormBean();

$cate_sortcode = $fb->form("cate_sortcode");
$paper_mpcode  = $fb->form("paper_mpcode");
$stan_mpcode   = $fb->form("stan_mpcode");
$mono_dvs      = $fb->form("mono_yn");
$amt_unit      = $fb->form("amt_unit");
$sell_site     = $fb->session("sell_site");

$price_tb = $dao->selectPriceTableName($conn, $mono_dvs, $sell_site);

$temp = array();

$param = array();
$param["table_name"]    = $price_tb;
$param["cate_sortcode"] = $cate_sortcode;
$param["paper_mpcode"]  = $paper_mpcode;
$param["stan_mpcode"]   = $stan_mpcode;
$param["amt_unit"]      = $amt_unit;

echo $dao->selectCateAmtHtml($conn, $param, $temp);

$conn->Close();
?>
