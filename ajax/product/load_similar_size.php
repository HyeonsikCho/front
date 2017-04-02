<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new ProductCommonDAO();
$fb = new FormBean();

$cate_sortcode = $fb->form("cate_sortcode");
// 독판전단류 최대사이즈 검색여부
$max_flag = false;

if (substr($cate_sortcode, 0, 6) === "003003") {
    $max_flag = true;
}

$cut_wid  = intval($fb->form('w'));
$cut_vert = intval($fb->form('v'));

$param = array();
$param["cate_sortcode"] = $cate_sortcode;
$param["cut_wid"]       = $cut_wid;
$param["cut_vert"]      = $cut_vert;
$param["size_val"]      = $cut_wid + $cut_vert;

$rs = $dao->selectSimilarStanInfo($conn, $param);

if (empty($rs)) {
    $rs = $dao->selectMaxStanInfo($conn, $param);

    $max_wid_size  = $rs["cut_wid_size"];
    $max_vert_size = $rs["cut_vert_size"];

    if ($max_flag) {
        $max_wid_size  = $rs["max_wid_size"];
        $max_vert_size = $rs["max_vert_size"];
    }

    $org_cut_wid  = doubleval($max_wid_size);
    $org_cut_vert = doubleval($max_vert_size);
} else {
    $org_cut_wid  = doubleval($rs["cut_wid_size"]);
    $org_cut_vert = doubleval($rs["cut_vert_size"]);
}

$wid_divide  = floor($org_cut_wid / $cut_wid);
$vert_divide = floor($org_cut_vert / $cut_vert);

$divide = $wid_divide * $vert_divide;

$json  = "{\"name\"     : \"%s\",";
$json .= " \"affil\"    : \"%s\",";
$json .= " \"divide\"   : \"%s\",";
$json .= " \"mpcode\"   : \"%s\",";
$json .= " \"max_wid\"  : \"%s\",";
$json .= " \"max_vert\" : \"%s\"}";

echo sprintf($json, $rs["name"]
                  , $rs["affil"]
                  , ($divide < 1) ? 1 : $divide
                  , $rs["mpcode"]
                  , $max_wid_size
                  , $max_vert_size);
?>
