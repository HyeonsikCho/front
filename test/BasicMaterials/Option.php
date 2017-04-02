<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ErpCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Common/DAO/ProductDAO.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Common/CondimentDecorator.php');

class Option extends CondimentDecorator {
	var $product;
	var $name;
	var $price;
	var $depth1;
	var $depth2;
	var $depth3;


	var $dao;
	var $connectionPool;
	var $util;
	var $conn;

	function __construct($product)
	{
		$this->product = $product;
		$this->dao = new ProductDAO();
		$connectionPool = new ConnectionPool();
		$this->conn = $connectionPool->getPooledConnection();
	}

	function getDescription() {
		return $this->product->getDescription() . "옵션 : " . $this->name . "(" .$this->price . ")</br>";
	}

	function setOption($sortcode ,$opt_name, $mpcode, $amt = '')
	{
		$param = array();
		$param['cate_sortcode'] = $sortcode;
		$param['amt'] = $amt;
		$param['opt_mpcode'] = $mpcode;
		$rs_price = $this->dao->selectCateOptPriceList($this->conn, $param);

		$this->name = $opt_name;
		$this->setPrice($rs_price);
	}

	function setPrice($rs_price) {
		$this->price = $rs_price->fields['sell_price'];
	}

	function cost() {
		return $this->price + $this->product->cost();
	}
}

?>