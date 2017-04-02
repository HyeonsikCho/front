<?php
/***********************************************************************************
 *** 프로 젝트 : 3.0
 *** 개발 영역 : 명함클래스
 *** 개  발  자 : 조현식
 *** 개발 날짜 : 2016.06.30
 *** 세부 사항 : 같은 폴더내 product클래스에서 파생된 클래스
 ***********************************************************************************/
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Common/PrintoutInterface.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Products/Product.php');

class Namecard extends Product implements PrintoutInterface
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
		$html = '<h2>명함</h2></br></br>';
		//표지
		$html .= '-----------표지-----------</br>';
		$html .= $this->makeBasicOption(0);
		$html .= $this->makeOptOption();
		$html .= $this->makeAfterOption();
		$html .= $this->makeCommonInfo();
		return $html;
	}


/***********************************************************************************
*** 출고실의 경험에 의해 배송비가 거의 확정적인 경우에 해당 가격을 불러오고 없을 경우는
*** getParcelCost를 호출한다
***********************************************************************************/

	function getParcelCost($param) {
		$cost = 0;

		$weight = $param['expec_weight'];

		$count = ceil($weight / 12);
		$cost = $count * 2750;

		// 배송비가 정확히 정해지지 않았기때문에 무게와 사이즈를 얻어서 택배회사의 운임정책에 맞춘다
		if ($cost == 0) {
			$cost = parent::getParcelCost($param);
		}

		return $cost;
	}


/***********************************************************************************
*** 출고실의 경험에 의해 배송비가 거의 확정적인 경우에 해당 가격을 불러오고 없을 경우는
*** getCargoCost 호출한다
***********************************************************************************/

	function getCargoCost($param) {
		$cost = 0;

		$weight = $param['expec_weight'];

		$count = ceil($weight / 12);
		$cost = $count * 2750;
		// 배송비가 정확히 정해지지 않았기때문에 무게와 사이즈를 얻어서 택배회사의 운임정책에 맞춘다
		if ($cost == 0) {
			$cost = parent::getCargoCost($param);
		}

		return $cost;
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

	function getAutobikeCost($param) {
		$weight = $param['expec_weight'];
		$area = $param['area'];
		$cost = 0;

		// 배송비가 정확히 정해지지 않았기때문에 무게와 사이즈를 얻어서 짐콜사의 운임정책에 맞춘다
		if ($cost == 0) {
			$cost = parent::getAutobikeCost($param);
		}

		return $cost;
	}

	function cost() {
		return 0;
	}

	function getSort() {
		return "namecard";
	}
}

?>