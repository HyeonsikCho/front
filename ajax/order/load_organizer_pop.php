<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/doc/order/SheetPopup.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/job/common/CommonDAO.php');

$frontUtil = new FrontCommonUtil();

if ($is_login === false) {
    $frontUtil->errorGoBack("로그인 상태가 아닙니다.");
    exit;
}

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new CommonDAO();
$fb = new FormBean();

$session = $fb->getSession();
unset($fb);

$member_seqno = $session["member_seqno"];

$param = array();
$param["table"] = "admin_licenseeregi";
$param["col"] = "admin_licenseeregi_seqno, corp_name";
$param["where"]["member_seqno"] = $member_seqno;

$rs = $dao->selectData($conn, $param);

$html = "<li><label><input type=\"radio\" name=\"organizer_chk\" value=\"%s\" %s>%s</label></li>";
$list = "";
$i = 1;

while ($rs && !$rs->EOF) {

    $check = "";
    if ($i == 1) {
        $check = "checked=\"checked\"";
    }

    $list .= sprintf($html, $rs->fields["admin_licenseeregi_seqno"]
                          , $check
                          , $rs->fields["corp_name"]);
    
    $i++;
    $rs->moveNext();
}

unset($param);
echo organizerPopup($list);

exit;
?>
