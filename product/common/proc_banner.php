<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new ProductCommonDAO();
$fb = new FormBean();

$cate_sortcode = $fb->form("cs");

// 상품 배너 정보 생성
$rs = $dao->selectCateBanner($conn, $cate_sortcode, false);

$file_path = NIMDA_PATH . $rs->fields["file_path"];
$file_name = $rs->fields["save_file_name"];

$temp = explode('.', $file_name);
$ext = strtolower($temp[1]);

$full_path = $file_path . $file_name;

if (is_file($full_path) === false) {
    $ext = "jpg";
    $full_path = NO_IMAGE;
}

$mime = "";
if ($ext === "jpg" || $ext === "jpeg") {
    $mime = "image/jpeg";
} else if ($ext === "png") {
    $mime = "image/png";
}

header("Content-Type:" . $type);
header("Content-Length:" . filesize($full_path));
readfile($full_path);
?>
