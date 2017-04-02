<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/message.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/CommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");

if ($is_login === false) {
    echo "{\"err\" : \"" . NO_LOGIN . "\"}";
    exit;
}

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$util = new FrontCommonUtil();
$dao = new CommonDAO();

$session = $fb->getSession();

$dvs = $fb->form("dvs");

$date = date("Y-m-d");

$param = array();
$param["seqno"] = $session["member_seqno"];

if ($dvs === "week") {
    $start_date = date("Y-m-d", strtotime($date . "-7day")) . " 00:00:00";
    $end_date   = $date . " 23:59:59";

    $param["start_date"] = $start_date;
    $param["end_date"]   = $end_date;
} else {
    $start_date = date("Y-m") . "-01 00:00:00";
    $end_date   = $date . " 23:59:59";

    $param["start_date"] = $start_date;
    $param["end_date"]   = $end_date;
}

$summary = $dao->selectOrderSummary($conn, $param);
$summary = $util->makeOrderSummaryArr($summary);

$summary_prdc = $summary["400"] + $summary["600"] +
                $summary["700"] + $summary["800"];

// 입출고 요약
$summary_rels = $summary["900"];

$ret  = '{';
$ret .= " \"wait\" : \"%s\",";
$ret .= " \"rcpt\" : \"%s\",";
$ret .= " \"prdc\" : \"%s\",";
$ret .= " \"rels\" : \"%s\",";
$ret .= " \"dlvr\" : \"%s\",";
$ret .= " \"comp\" : \"%s\"";
$ret .= '}';

echo sprintf($ret, $summary["200"]
                 , $summary["300"]
                 , $summary_prdc
                 , $summary_rels
                 , $summary["010"]
                 , $summary["020"]);
?>
