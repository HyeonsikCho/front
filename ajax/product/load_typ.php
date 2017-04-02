<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductSheetCutDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$util = new CommonUtil();
$dao = new ProductSheetCutDAO();
$fb = new FormBean();

$cate_sortcode = $fb->form("cate_sortcode");
$stan_name     = $fb->form("stan_name");
$pos_yn        = ($fb->form("pos_yn") === 'Y') ? true : false;

$param = array();
$param["cate_sortcode"] = $cate_sortcode;
$param["stan_name"]     = $stan_name;

$html = $dao->selectCateStanTypHtml($conn,
                                    $param,
                                    $pos_yn,
                                    $temp);

echo $html;

$conn->Close();
?>
