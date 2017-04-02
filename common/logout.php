<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
$fb->removeAllSession();

header("Location: /index.html");
?>
