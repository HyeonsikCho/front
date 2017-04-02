<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/Template.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/CommonDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$template = new Template();
$frontUtil = new FrontCommonUtil();
$dao = new CommonDAO();

$res_cd     = $fb->form("res_cd");              // 응답코드          (CA, CAO, CC, CCO, CPC)
$account_no = $fb->form("account_no");          // 계좌번호
$res_msg    = $fb->form("res_msg");             // 응답메시지        (CA, CAO, CC, CCO, CPC)
$member_seqno = $fb->session("member_seqno");
$cpn_admin_seqno = $fb->session("sell_site");
$bank_nm = $fb->form("bank_nm");
$return_url = $fb->form("return_url");

if ($res_cd == 0000) {

    $param = array();
    $param["ba_num"] = preg_replace("/[^0-9]*/s", "", $account_no);
    $param["bank_name"] = $bank_nm;
    $param["cpn_admin_seqno"] = $cpn_admin_seqno;
    $param["member_seqno"] = $member_seqno;

    $dao->deleteVirtBaAdmin($conn, $param);
    $rs = $dao->insertVirtBaAdmin($conn, $param);

    $fb->addSession("bank_name"         , $bank_nm);
    $fb->addSession("ba_num"            , preg_replace("/[^0-9]*/s", "", $account_no));

    if ($rs) {
        header("Location: " . $return_url);
        exit;
    }

} else {
    echo "<script>alert(\"" . $res_msg . "\");</script>";
}

$conn->Close();
?>
