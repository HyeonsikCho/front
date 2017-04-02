<!DOCTYPE html>
<head>
	<script src="/design_template/js/lib/jquery-1.11.2.min.js"></script>
	<script src="./Common/js/product.js"></script>
</head>
<body>

<?
require_once('./Common/DPrintingFactory.php');

$factory = new DPrintingFactory();
$product = $factory->create('001001001');

//$amt, $count, $sizename, $gram, $paper
$amt = 4000;
$count = 1;
$sizename = "A5";
$gram = "180g";
$paper = "아트지";
$weight = 3399;

$dlvrCost = $product->getParcelCost($amt, $count, $sizename, $gram, $paper); // 매수, 건수, 사이즈명, 평량, 종이명

echo "매수 : " . $amt
		. " <br> 건수 : " . $count
		. " <br> 사이즈명 : " . $sizename
		. " <br> 평량 : " . $gram
		. " <br> 종이명 : " . $paper . "<br><br>";


if($dlvrCost == 0 ) { // 배송료를 확실히 결정 할 수 없다면 택배사의 가격정책에 맞춘다. 무게, 사이즈 필요
	$dlvrCost = $product->getCommonParcelCost($weight);
}

echo "예상운임료 : " . $dlvrCost;

//해당 상품의 html문을 출력
//echo $product->makeHtml();


/*구현해야할 기능
1. sortcode를 받으면 선택가능한 종이, 후공정, 옵션의 내용을 보여줄것. (완료)


2. 상품의 옵션(종이, 옵션, 후공정)들을 받아서 DB의 내용을 불러와 가격을 출력(완료)
->1) 종이, 옵션, 후공정의 가격정보를 가지고 오기위한 필수 정보들
--> 1. 종이 : sortcode, amt, stan_mpcode, paper_mpcode, print_name, print_purp
--> 2. 옵션 : sortcode, opt_name, depth1, depth2, depth3
--> 3. 후공정 : sortcode, after_name, depth1, depth2, depth3


3. 사용자가 주문하고자하는 상품의 정보들(종이, 옵션, 후공정 등)을 받아서 테이블에 저장
-> 1) 주문에 필요한 데이터들은 무엇인지


4. Product.php에서 상품의 유형을 결정 할 수있는지?



5. 종이가 여러 장 오더라도 주문을 받을 수 있도록 (완료)
-> 1) 여러장을 보여줄 수 있는 기능(html)을 만들고
-> 2) 옵션, 후공정과 같이 데이터를 보낼 수 있도록해야한다

*/

?>
</body>
