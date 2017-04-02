<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_lib/CommonUtil.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$util = new CommonUtil();
$dao = new ProductCommonDAO();
$fb = new FormBean();

$sell_site = $fb->session("sell_site");

$cate_sortcode  = $fb->form("cate_sortcode");
$after_info_arr = $fb->form("after_info_arr");
$size           = $fb->form("size");

$param = array();
$param["cate_sortcode"] = $cate_sortcode;

$after_info_arr_count = count($after_info_arr);
$html_arr = array();
for ($i = 0; $i < $after_info_arr_count; $i++) {
    $html = '';
    $after_name = $after_info_arr[$i]["name"];

    $param["after_name"] = $after_name;
    $param["depth1"]     = $after_info_arr[$i]["depth1"];
    $param["depth2"]     = $after_info_arr[$i]["depth2"];
    $param["size"]       = $size;

    $rs = $dao->selectCateAfterInfo($conn, $param);

    if ($after_name === "미싱" || $after_name === "오시") {
        while ($rs && !$rs->EOF) {
            $fields = $rs->fields;
            $depth2 = $fields["depth2"];
            $mpcode = $fields["mpcode"];

            if ($depth2 === "중앙" ||
                    strpos($depth2, "비례") !== false) {
                $html_arr[$after_name]["manual"] = $mpcode;
            } else {
                $html_arr[$after_name]["custom"] = $mpcode;
            }

            $rs->MoveNext();
        }
    } else if ($after_name === "코팅") {
        while ($rs && !$rs->EOF) {
            $fields = $rs->fields;
            $depth1 = $fields["depth1"];
            $depth2 = $fields["depth2"];
            $mpcode = $fields["mpcode"];

            $html .= option($mpcode, $depth1 . ' ' . $depth2);

            $rs->MoveNext();
        }
    } else if ($after_name === "접지") {
        while ($rs && !$rs->EOF) {
            $fields = $rs->fields;
            $depth2 = $fields["depth2"];
            $mpcode = $fields["mpcode"];
            
            $attr = '';
            if ($depth2 === "비중앙") {
                $attr = "class=\"_custom\"";
            }

            $html .= option($mpcode, $depth2, $attr);

            $rs->MoveNext();
        }
    } else {
        while ($rs && !$rs->EOF) {
            $fields = $rs->fields;
            $depth2 = $fields["depth2"];
            $mpcode = $fields["mpcode"];

            $html .= option($mpcode, $depth2);

            $rs->MoveNext();
        }
    }

    $html_arr[$after_name]["html"] = $util->convJsonStr($html);

    unset($param["affil"]);
    unset($param["subpaper"]);
}
unset($rs);

$ret = '[';

$json = '';
$json_base  = '{';
$json_base .= "\"name\" : \"%s\", \"html\" : \"%s\",";
$json_base .= "\"m\" : \"%s\", \"c\" : \"%s\"";
$json_base .= '}';

foreach ($html_arr as $after_name => $info_arr) {
    $json .= sprintf($json_base,
                     $after_name,
                     $info_arr["html"],
                     $info_arr["manual"],
                     $info_arr["custom"]);
    $json .= ',';
}

$ret .= substr($json, 0, -1);
$ret .= ']';

echo $ret;

$conn->Close();
?>
