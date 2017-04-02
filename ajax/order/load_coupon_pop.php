<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/SheetDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/doc/order/SheetPopup.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_lib/CommonUtil.php");

$frontUtil = new FrontCommonUtil();

if ($is_login === false) {
    $frontUtil->errorGoBack("로그인 상태가 아닙니다.");
    exit;
}

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$commonUtil = new CommonUtil();

$fb = new FormBean();
$dao = new SheetDAO();

$sell_site    = $fb->session("sell_site");
$member_seqno = $fb->session("org_member_seqno");

$fb = $fb->getForm();

$seqno_arr = explode('|', $fb["seq"]);

$order_rs = $dao->selectOrderCateSortcode($conn, $seqno_arr);

$tbody_base  = "<tr>";
$tbody_base .= "    <td class=\"btn\"><input type=\"checkbox\" name=\"cp_seqno[]\" value=\"%s\" class=\"_individual\"></td>";
$tbody_base .= "    <th scope=\"row\" class=\"subject\">%s</th>";
$tbody_base .= "    <td>%s</td>";
$tbody_base .= "    <td>%s</td>";
$tbody_base .= "    <input type=\"hidden\" id=\"cp_val_%s\" value=\"%s\">";
$tbody_base .= "    <input type=\"hidden\" id=\"cp_unit_%s\" value=\"%s\">";
$tbody_base .= "    <input type=\"hidden\" id=\"cp_min_price_%s\" value=\"%s\">";
$tbody_base .= "    <input type=\"hidden\" id=\"cp_max_price_%s\" value=\"%s\">";
$tbody_base .= "</tr>";

$param = array();
$tbody = '';

while ($order_rs && !$order_rs->EOF) {
    $cate_sortcode = $order_rs->fields["cate_sortcode"];

    $param["cate_sortcode"] = substr($cate_sortcode, 0, 6);
    $param["member_seqno"] = $member_seqno;

    $cp_rs = $dao->selectValidCpSeqno($conn, $param);

    unset($param);
    $param["sell_site"] = $sell_site;
    while ($cp_rs && !$cp_rs->EOF) {
        $param["cp_seqno"] = $cp_rs->fields["cp_seqno"];
        $rs = $dao->selectValidCpInfo($conn, $param);

        while($rs && !$rs->EOF) {
            $fields = $rs->fields;

            $cp_seqno = $fields["cp_seqno"];

            $val  = $fields["val"];
            $unit = $fields["unit"];

            $sale_val = null;

            if ($unit === '%') {
                $sale_val = $val . "%";
            } else {
                $sale_val = "&#8361; " . number_format($val);;
            }

            $start_date = $fields["public_period_start_date"];
            $end_date   = $fields["cp_extinct_date"];

            $period = "%s ~ %s";

            if ($end_date === "0000-00-00 00:00:00") {
                $period = sprintf($period, $start_date, "무기한");
            } else {
                $period = sprintf($period, $start_date, $end_date);
            }

            $tbody .= sprintf($tbody_base, $cp_seqno
                                         , $fields["cp_name"]
                                         , $sale_val
                                         , $period
                                         , $cp_seqno
                                         , $val
                                         , $cp_seqno
                                         , $unit
                                         , $cp_seqno
                                         , $fields["min_order_price"]
                                         , $cp_seqno
                                         , $fields["max_sale_price"]);

            $rs->MoveNext();
        }

        $cp_rs->MoveNext();
    }

    $order_rs->MoveNext();
}

echo couponPopup($tbody);

$conn->Close();
exit;
?>
