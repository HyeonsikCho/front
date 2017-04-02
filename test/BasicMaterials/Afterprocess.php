<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ErpCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Common/DAO/ProductDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Common/CondimentDecorator.php');

class Afterprocess extends CondimentDecorator {
	var $product;
	var $name;
	var $price;

	var $dao;
	var $connectionPool;
	var $util;
	var $conn;


	function __construct($product) {
		$this->product = $product;
		$this->dao = new ProductDAO();
		$connectionPool = new ConnectionPool();
		$this->conn = $connectionPool->getPooledConnection();
	}

	function getDescription() {
		return $this->product->getDescription() . "후공정 : " . $this->name . "(" .$this->price . ")</br>";
	}

	function setAfterprocess($sortcode ,$after_name, $mpcode, $amt = '') {
		$param = array();
		$param['cate_sortcode'] = $sortcode;
		$param['amt'] = $amt;
		$param['after_mpcode'] = $mpcode;
		if($param['after_mpcode']) {
			$this->conn->debug = 1;
			$rs_price = $this->dao->selectCateAftPriceList($this->conn, $param);
			$this->price = $this->getPrice($rs_price);
			$this->name = $after_name;
		} else {
			$this->price = 0;
			$this->name = '상품정보 없음';
		}
	}

	function getAfterMpcode($param) {
		$rs = $this->dao->selectCateAftInfo($this->conn, 'SEQ' ,$param);
		return $rs->fields['mpcode'];
	}

	function getPrice($rs_price) {
		return $rs_price->fields['sell_price'];
	}

	function cost() {
		return $this->price + $this->product->cost();
	}
}


?>
