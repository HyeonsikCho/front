<?
/***********************************************************************************
 *** 프로 젝트 : 3.0
 *** 개발 영역 : 택배운임료 측정
 *** 개  발  자 : 조현식
 *** 개발 날짜 : 2016.06.29
 ***********************************************************************************/


include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/SheetDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/doc/order/SheetPopup.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/test/Common/DPrintingFactory.php");


/***********************************************************************************
**** 기초 데이터 사용을 위한 요소들 정의
 ***********************************************************************************/

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();
$fb = new FormBean();
$session = $fb->getSession();
$fb = $fb->getForm();
$dao = new SheetDAO();
$state_arr = $session["state_arr"];

//그루핑이 된 목록들은 sortcode가 같고 합쳐서 20kg가 안넘어야한다
$seqAll = $fb["seqno"];
$dlvr_way = $fb["dlvr_way"];
$zipcode = $fb["zipcode"];

if (empty($seqAll) === false) {
    $seqAll = explode('|', $seqAll);
    $seqAll = $dao->arr2paramStr($conn, $seqAll);
}

/***********************************************************************************
******* 데이터 불러오기
 ***********************************************************************************/

$param = array();
$param["member_seqno"] = $session["org_member_seqno"];
$param["order_state"]  = $state_arr["주문대기"];
$param["order_common_seqno"] = $seqAll;
$param['zipcode'] = $zipcode;
$sheet_list = $dao->selectCartOrderList($conn, $param);


$i = 0;
$dlvr_param = array();
while($sheet_list && !$sheet_list->EOF) {
	$dlvr_param[$i]['order_detail'] = $sheet_list->fields['order_detail'];
	$dlvr_param[$i]['order_common_seqno'] = $sheet_list->fields['order_common_seqno'];
	$dlvr_param[$i]['amt'] = $sheet_list->fields['amt'];
	$dlvr_param[$i]['count'] = $sheet_list->fields['count'];
	$dlvr_param[$i]['order_detail'] = $sheet_list->fields['order_detail'];
	$dlvr_param[$i]['cate_sortcode'] = $sheet_list->fields['cate_sortcode'];
	$dlvr_param[$i]['expec_weight'] = $sheet_list->fields['expec_weight'];

	$i++;
	$sheet_list->moveNext();
}


/***********************************************************************************
**** 가져온 데이터들을 통해 택배 운임요금 계산
 ***********************************************************************************/

$factory = new DPrintingFactory();
$dlvr_cost_nc = 0;
$dlvr_cost_bl = 0;
$weight_leaflet = 0;
$weight_namecard = 0;
$seq_leaflet = "";
$seq_namecard = "";
$boxCount = 0;
$island_cost = 0;

if($dlvr_way == "01") {
	$rs = $dao->selectIslandParcelCost($conn, $param);
	while ($rs && !$rs->EOF) {
		$island_cost = $rs->fields["price"];
		$rs->MoveNext();
	}
}

for($i=0; $i < count($dlvr_param) ; $i++) {
	$cate_sortcode = $dlvr_param[$i]['cate_sortcode'];
	$product = $factory->create($cate_sortcode);

	$sort = $product->getSort();


	// 명함은 주문건의 모든 상품을 합쳐서 배송비를 받아야함
	if ($sort == "namecard") {
		$weight_namecard += $dlvr_param[$i]['expec_weight'];
		$seq_namecard .= $dlvr_param[$i]['order_common_seqno'] . "|";
	}
	// 전단은 건당으로 배송비를 받아야함
	else if ($sort == "leaflet") {
		//$weight_leaflet += $dlvr_param[$i]['expec_weight'];
		$param['expec_weight'] = $dlvr_param[$i]['expec_weight'];
		$dlvr_cost_bl += $product->getDlvrCost($param, $dlvr_way);
		$seq_leaflet .= $dlvr_param[$i]['order_common_seqno'] . "|";
		$blBoxCount = getLeafletBoxcount($dlvr_param[$i]['expec_weight']);
		$boxCount += $blBoxCount;
		$dlvr_cost_bl += $blBoxCount * $island_cost;
		$weight_leaflet += $dlvr_param[$i]['expec_weight'];
	} else { // 모든 상품들이 전단 / 명함으로 구분지어지면 삭제해야한다.
		$weight_leaflet += $dlvr_param[$i]['expec_weight'];
		$seq_leaflet .= $dlvr_param[$i]['order_common_seqno'] . "|";
	}
}

if($weight_namecard != 0) {
	$ncBoxCount = (int)($weight_namecard / 12) + 1;
	$boxCount += $ncBoxCount;
	$dlvr_cost_nc += $ncBoxCount * $island_cost;
}

if($seq_leaflet != "") {
	$seq_leaflet = substr($seq_leaflet , 0, -1);
}

if($seq_namecard != "") {
	$seq_namecard = substr($seq_namecard , 0, -1);
}

if($weight_namecard != 0) {
	$product = $factory->create("001001001");
	$param = array();
	$param['zipcode'] = $zipcode;
	$param['expec_weight'] = $weight_namecard;
	$dlvr_cost_nc += $product->getDlvrCost($param, $dlvr_way);
}

if($weight_leaflet != 0) {
	$product = $factory->create("002001001");
	$param = array();
	$param['zipcode'] = $zipcode;
	$param['expec_weight'] = $weight_leaflet;
	//$dlvr_cost_bl += $product->getDlvrCost($param, $dlvr_way);
}

$ret = "{\"cover\" : {
\"price_nc\" : \"%s\"
, \"price_bl\" : \"%s\"
, \"bl\" : \"%s\"
, \"nc\" : \"%s\"
, \"island_cost\" : \"%s\"
, \"boxcount_nc\" : \"%s\"
, \"boxcount_bl\" : \"%s\"
, \"expec_weight_nc\" : \"%s\"
, \"expec_weight_bl\" : \"%s\"}}";

echo sprintf($ret,
		$dlvr_cost_nc,
		$dlvr_cost_bl,
		$seq_leaflet,
		$seq_namecard,
		$island_cost,
		$ncBoxCount,
		$blBoxCount,
		$weight_namecard,
		$weight_leaflet
);

function getLeafletBoxcount($expec_weight) {
	$count = 1;

	if($expec_weight > 32) {
		$count = (int)($expec_weight / 25) + 1;
	}

	return $count;
}
?>
