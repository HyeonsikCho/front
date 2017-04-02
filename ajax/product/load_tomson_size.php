<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductSheetTomsonDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new ProductSheetTomsonDAO();
$fb = new FormBean();

$sell_site = $fb->session("sell_site");

$cate_sortcode = $fb->form("cate_sortcode");
$typ           = $fb->form("typ");

$param = array();
$param["cate_sortcode"] = $cate_sortcode;
$param["typ"]           = $typ;

$temp = array();

echo $dao->selectCateSizeHtml($conn,
                              $param,
                              $temp);

$conn->Close();
?>
