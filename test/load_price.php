<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
require_once('./Common/DPrintingFactory.php');
require_once('./Common/PrintoutInterface.php');
require_once('./BasicMaterials/Paper.php');
require_once('./BasicMaterials/Option.php');
require_once('./BasicMaterials/Afterprocess.php');

$fb = new FormBean();

$sortcode = $fb->form("sortcode");
$stan_mpcode  = explode("|", $fb->form("stan_mpcode"));
$paper_mpcode    = explode("|", $fb->form("paper_mpcode"));
$print_name    = explode("|", $fb->form("print_name"));
$print_purp   = explode("|", $fb->form("print_purp"));
$amt           = explode("|", $fb->form("amt"));
$opt_name   = explode("|", $fb->form("opt_name_list"));
$opt_mpcode   = explode("|", $fb->form("opt_mp_list"));
$after_name   = explode("|", $fb->form("after_name_list"));
$after_mpcode   = explode("|", $fb->form("after_mp_list"));

$factory = new DPrintingFactory();
$product = $factory->create($sortcode);

$basic_count = count($stan_mpcode);
$total_amt = 0;

for($i = 0; $i < $basic_count ; $i++) {
	if($stan_mpcode[$i] == '') {
		break;
	}
	$product = new Paper($product);
	$product->setPaper($sortcode, $amt[$i], $stan_mpcode[$i], $paper_mpcode[$i], $print_name[$i], $print_purp[$i]);
	$total_amt += $amt[$i];
}

$opt_count = count($opt_name);
for($i = 0; $i < $opt_count ; $i++) {
	if($opt_name[$i] == '') {
		break;
	}
	$product = new Option($product);
	$product->setOption($sortcode, $opt_name[$i] , $opt_mpcode[$i], $total_amt); // $sortcode ,$opt_name, $mpcode, $amt = ''
}

$after_count = count($after_name);
for($i = 0; $i < $after_count ; $i++) {
	if($after_name[$i] == '') {
		break;
	}
	$product = new Afterprocess($product);
	$product->setAfterprocess($sortcode, $after_name[$i] , $after_mpcode[$i], $total_amt); // $sortcode ,$after_name, $mpcode, $amt = ''
}

$price = $product->cost();
$detail = base64_encode($product->getDescription());
$ret = "{\"cover\" : {\"price\" : \"%s\" , \"detail\" : \"%s\"}}";

echo sprintf($ret, $price, $detail);

?>