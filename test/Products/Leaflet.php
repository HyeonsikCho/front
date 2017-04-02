<?php
/***********************************************************************************
 *** 프로 젝트 : 3.0
 *** 개발 영역 : 전단클래스
 *** 개  발  자 : 조현식
 *** 개발 날짜 : 2016.06.30
 ***********************************************************************************/
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Common/PrintoutInterface.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Products/Product.php');

class Leaflet extends Product implements PrintoutInterface
{
	/**
	 * @var string
	 */
	var $amt;
	var $size_w, $size_h;
	var $papers;
	var $options;
	var $afterprocesses;

	function makeHtml() {
		$html = '<h2>전단주문</h2></br></br>';
		$html .= $this->makeBasicOption(0);
		$html .= $this->makeOptOption();
		$html .= $this->makeAfterOption();
		$html .= $this->makeCommonInfo();
		return $html;
	}

	public function cost() {
		return 0;
	}


/***********************************************************************************
*** 출고실의 경험에 의해 배송비가 거의 확정적인 경우에 해당 가격을 불러오고 없을 경우는
*** getParcelCost를 호출한다
***********************************************************************************/

	function getParcelCost($param) {
		$sizename = $param['sizename'];
		$amt = $param['amt'];
		$count = $param['count'];

		$cost = 0;

		$count = 1;
		$modulo = $param['expec_weight'] % 32;
		$price = 0;

		if($param['expec_weight'] < 16) {
			$price = 2750;
		} else if($param['expec_weight'] < 25 && $param['expec_weight'] >= 16) {
			$price = 3300;
		} else if($param['expec_weight'] < 32 && $param['expec_weight'] >= 25) {
			$price = 3850;
		} else {
			$count = ceil($param['expec_weight'] / 25);
			$price = $count * 3300;
		}
		return $price;
	}

/***********************************************************************************
*** 사이즈명, 매수, 건수를 통해 연단위로 환산
***********************************************************************************/

	function tranlateYeon($sizename, $amt, $count) { //사이즈명, 매수, 건수
		$yeon = 0;
		switch($sizename) {
			case 'A1' :
				$yeon = $amt / 500 * $count;
				break;
			case 'A2' :
				$yeon = $amt / 1000 * $count;
				break;
			case 'A3' :
				$yeon = $amt / 2000 * $count;
				break;
			case 'A4' :
				$yeon = $amt / 4000 * $count;
				break;
			case 'A5' :
				$yeon = $amt / 8000 * $count;
				break;
			case 'A6' :
				$yeon = $amt / 16000 * $count;
				break;
			case '2절' :
				$yeon = $amt / 500 * $count;
				break;
			case '4절' :
				$yeon = $amt / 1000 * $count;
				break;
			case '8절' :
				$yeon = $amt / 2000 * $count;
				break;
			case '16절' :
				$yeon = $amt / 4000 * $count;
				break;
			case '32절' :
				$yeon = $amt / 8000 * $count;
				break;
			case '64절' :
				$yeon = $amt / 16000 * $count;
				break;
		}
		return ceil($yeon);
	}


/***********************************************************************************
*** 출고실의 경험에 의해 배송비가 거의 확정적인 경우에 해당 가격을 불러오고 없을 경우는
*** getCargoCost를 호출한다
***********************************************************************************/

	function getCargoCost($param) {
		$price = 0;
		$count = $param['count'];

		if($param['expec_weight'] < 16) {
			$price = 2750;
		} else if($param['expec_weight'] < 25 && $param['expec_weight'] >= 16) {
			$price = 3300;
		} else if($param['expec_weight'] < 32 && $param['expec_weight'] >= 25) {
			$price = 3850;
		} else {
			$count = ceil($param['expec_weight'] / 25);
			$price = $count * 3300;
		}
		// 배송비가 정확히 정해지지 않았기때문에 무게와 사이즈를 얻어서 택배회사의 운임정책에 맞춘다
		if ($price == 0) {
			$cost = parent::getCargoCost($param);
		}

		return $price;
	}


/***********************************************************************************
*** 출고실의 경험에 의해 배송비가 거의 확정적인 경우에 해당 가격을 불러오고 없을 경우는
*** getSubwayCost를 호출한다
***********************************************************************************/

	function getSubwayCost($param) {
		$cost = 0;

		$weight = $param['expec_weight'];

		// 배송비가 정확히 정해지지 않았기때문에 무게와 사이즈를 얻어서 택배회사의 운임정책에 맞춘다
		if ($cost == 0) {
			$cost = parent::getSubwayCost($param);
		}

		return $cost;
	}


/***********************************************************************************
*** 출고실의 경험에 의해 배송비가 거의 확정적인 경우에 해당 가격을 불러오고 없을 경우는
*** getAutobikeCost를 호출한다
***********************************************************************************/
/*
	function getAutobikeCost($param) {
		$weight = $param['expec_weight'];
		$zipcode = $param['zipcode'];
		include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/SheetDAO.php");
		$dao = new SheetDAO();
		$this->conn->debug = 1;
		$rs = $dao->selectAutobikeCostPrice($this->conn, $param);

		if(!$rs || $rs->fields['autobike'] == 0) {
			return 0; // 퀵 이용 불가
		} else {
			if ($weight < 21) {
				return $rs->fields['autobike']; // 1연 이하 (오토바이)
			} else if ($weight < 42) {
				return $rs->fields['autobike'] + 2000; // 2연 이하 (오토바이)
			} else if ($weight < 63) {
				return $rs->fields['autobike'] + 5000; // 3연 이하 (오토바이)
			} else if ($weight < 315) {
				return $rs->fields['damas']; // 15연 이하 (다마스)
			} else if ($weight < 525) {
				return $rs->fields['rabo']; // 25연 이하 (라보)
			} else if ($weight < 900){
				return $rs->fields['1ton'];
			} else {
				return -1;
			}
		}
	}
*/
	function getSort() {
		return "leaflet";
	}
}

?>