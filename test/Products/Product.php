<?php
/***********************************************************************************
 *** 프로 젝트 : 3.0
 *** 개발 영역 : 상품클래스
 *** 개  발  자 : 조현식
 *** 개발 날짜 : 2016.06.30
 ***********************************************************************************/
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductNcDAO.php");

class Product {
	var $name;
	var $sortcode;
	var $dao;
	var $connectionPool;
	var $util;
	var $conn;

	function __construct($sortcode) {
		$this->sortcode = $sortcode;
		$this->connectionPool = new ConnectionPool();
		$this->conn = $this->connectionPool->getPooledConnection();
		$this->dao = new ProductNcDAO();
		$this->util = new FrontCommonUtil();
		$this->name = "";
	}


/***********************************************************************************
*** 모든 상품의 공통적인 부분, 종이명, 인쇄명, 사이즈, 매수의 정보를 불러옴
*** 인자 number는 페이지물과 같은 여러 종이명, 인쇄명 등이 오는 경우를 대비해서 넣음
***********************************************************************************/

	function makeBasicOption($number) {
		$html = $this->makePaperOption($number);
		$html .= $this-> makePrintOption($number);
		$html .= $this->makeSizeOption($number);
		$html .= $this->makeAmtOption($number);

		return $html;
	}

	function makePaperOption($number) { // 혼합형의 경우 넘버링(number)을 통해 다수의 종이정보를 만들어내는것이 가능하다.
		$price_info_arr = array();
		$param = array();
		$paper = $this->dao->selectCatePaperHtml($this->conn, $this->sortcode, $price_info_arr);

		$html = '종이 : <select id="paper_' . $number . '" name="paper" onchange="changeData();">' . $paper['info'] . '</select></br></br>';

		return $html;
	}

	function makePrintOption($number) {
		$price_info_arr = array();
		$price_info_arr['cate_sortcode'] = $this->sortcode;
		$param = array();
		$param['cate_sortcode'] = $this->sortcode;
		$print_tmpt = $this->dao->selectCatePrintTmptHtml($this->conn, $param, $price_info_arr);
		$print_tmpt = $print_tmpt["단면"] . $print_tmpt["양면"];

		$print_purp = $this->dao->selectCatePrintPurpHtml($this->conn, $this->sortcode);

		$html = '인쇄도수 : <select id="print_' . $number . '" name="print" onchange="changeData();">' . $print_tmpt . '</select></br></br>';
		$html .= '<select id="print_purp_' . $number . '" name="print_purp" style="display:none">' . $print_purp .'</select>';

		return $html;
	}

	function makeSizeOption($number) {
		$price_info_arr = array();
		$price_info_arr['cate_sortcode'] = $this->sortcode;
		$param = array();
		$param['cate_sortcode'] = $this->sortcode;
		$size = $this->dao->selectCateSizeHtml($this->conn, $param, $price_info_arr);

		$html = '사이즈 : <select id="size_' . $number . '" name="size" def_cut_wid='.$price_info_arr['def_cut_wid'].' def_cut_vert=' . $price_info_arr['def_cut_vert'] . ' stan_mpcode=' . $price_info_arr['stan_mpcode'] . '>' . $size . '</select></br></br>';

		return $html;
	}

	function makeAmtOption($number) {
		$price_info_arr = array();
		$price_info_arr['cate_sortcode'] = $this->sortcode;
		$param = array();
		$param['cate_sortcode'] = $this->sortcode;
		$param["table_name"]    = 'ply_price_gp';
		$param["amt_unit"]      = '장';
		$amt = $this->dao->selectCateAmtHtml($this->conn, $param, $price_info_arr);

		$html =  '수량 : <select id="amt_' . $number . '" name="amt" onchange="changeData();">' . $amt . '</select></br></br>';
		return $html;
	}

	function makeOptOption() {
		$opt = $this->dao->selectCateOptHtml($this->conn, $this->sortcode);
		$add_opt = $opt["info_arr"]["name"];
		$add_opt = $this->dao->parameterArrayEscape($this->conn, $add_opt);
		$add_opt = $this->util->arr2delimStr($add_opt);
		$param = array();
		$param["cate_sortcode"] = $this->sortcode;
		$param["opt_name"]      = $add_opt;
		$param["opt_idx"]       = $opt["info_arr"]["idx"];
		$add_opt = $this->dao->selectCateAddOptInfoHtml($this->conn, $param);

		$html = '---------------------옵션-------------------------';
		$html .= '<dd class="_folder list">' . $opt['html'] .$add_opt . '</dd>';
		$html .= '-------------------------------------------------</br></br>';

		return $html;
	}

	function makeAfterOption() {
		$param = array();
		$param["cate_sortcode"] = $this->sortcode;

		$after = $this->dao->selectCateAfterHtml($this->conn, $param);
		$basic_after = $after["info_arr"]["add"];
		$basic_after = $this->dao->parameterArrayEscape($this->conn, $basic_after);
		$basic_after  = $this->util->arr2delimStr($basic_after, ',');

		$param["after_name"]      = $basic_after;

		$add_after = $this->dao->selectCateAddAfterInfoHtml($this->conn, $param);

		$html = '---------------------후공정-------------------------';
		$html .= '<dd class="_folder list">' . $after['html'] .$add_after . '</dd>';
		$html .= '-----------------------------------------------------';

		return $html;
	}

	function makeCommonInfo() {
		$html = '<input type="hidden" id="paper" value="">';
		$html .= '<input type="hidden" id="print" value="">';
		$html .= '<input type="hidden" id="print_purp" value="">';
		$html .= '<input type="hidden" id="size" value="">';
		$html .= '<input type="hidden" id="amt" value="">';
		$html .= '<input type="hidden" id="sortcode" name="sortcode" value="' . $this->sortcode .'"></br>';
		$html .= '<input type="hidden" id="opt_name_list" name="opt_name_list" value="">';
		$html .= '<input type="hidden" id="opt_mp_list" name="opt_mp_list" value="">';
		$html .= '<input type="hidden" id="after_name_list" name="after_name_list" value="">';
		$html .= '<input type="hidden" id="after_mp_list" name="after_mp_list" value="">';
		$html .= '<span id="detail"></span></br></br>';
		$html .= '<span id="total_price">0원</span>';

		return $html;
	}

	function getDescription() {
		return $this->name;
	}

	function cost() {}


/***********************************************************************************
*** 인자 $dlvr_way로 배송료를 선택하는 메서드
*** 외부에서 사용 할 수 있도록 반드시 public권한을 주어야함
***********************************************************************************/

	function getDlvrCost($param, $dlvr_way) {
		switch($dlvr_way) {
			case "01" :
				return $this->getParcelCost($param);
				break;
			case "03" :
				return $this->getCargoCost($param);
				break;
			case "04" :
				return $this->getAutobikeCost($param);
				break;
			case "05" :
				return $this->getSubwayCost($param);
				break;
			default :
				return 0;
				break;
		}
	}


/***********************************************************************************
*** 택배사에 맞춰 운임료를 예측하는 함수
*** 주로 이 클래스에서 파생되는 getParcelCost(회사 자체적으로 정해진 운임료 가져옴) 실행 이후 가격을 불러오지 못할경우 실행됨
*** 현재는 무게만 고려하고 있지만 추후 거리나 부피에 따라서도 변동되도록 해야한다
***********************************************************************************/

	function getParcelCost($param) {
		include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/SheetDAO.php");
		$dao = new SheetDAO();
		$rs = $dao->selectParcelCostPrice($this->conn, $param);

		$x_cost_per = 2750;

		if($rs->fields['price'] != NULL) {
			$x_cost_per = $rs->fields['price'];
		}

		$weight = $param['expec_weight'];

		$x_degree = 15; // 택배회사에서 덩이를 나누는 기준KG

		$parcel_count = ceil($weight / $x_degree);

		return $parcel_count * $x_cost_per;
	}


/***********************************************************************************
*** 화물사에 맞춰 운임료를 예측하는 함수
*** 주로 이 클래스에서 파생되는 getCargoCost(회사 자체적으로 정해진 운임료 가져옴) 실행 이후 가격을 불러오지 못할경우 실행됨
*** 현재는 무게만 고려하고 있지만 추후 거리나 부피에 따라서도 변동되도록 해야한다
***********************************************************************************/

	function getCargoCost($param) {
		$weight = $param['expec_weight'];
		$x_degree = 20; //화물사에서 덩이를 나누는 기준KG
		$x_cost_per = 2500; // 화물사에서 받는 덩이당 가격

		$cargo_count = ceil($weight / $x_degree);

		return $cargo_count * $x_cost_per;
	}


/***********************************************************************************
*** 지하철퀵사에 맞춰 운임료를 예측하는 함수
*** 주로 이 클래스에서 파생되는 getSubwayCost(회사 자체적으로 정해진 운임료 가져옴) 실행 이후 가격을 불러오지 못할경우 실행됨
*** 현재는 무게만 고려하고 있지만 추후 거리나 부피에 따라서도 변동되도록 해야한다
***********************************************************************************/

	function getSubwayCost($param) {
		$weight = $param['expec_weight'];
		$x_degree = 20; //지하철퀵사에서 덩이를 나누는 기준KG
		$x_cost_per = 2500; // 지하철퀵사에서 받는 덩이당 가격

		$subway_count = ceil($weight / $x_degree);

		return $subway_count * $x_cost_per;
	}


/*********************************************************************************************************
*** 짐콜퀵사에 맞춰 운임료를 예측하는 함수
*** 주로 이 클래스에서 파생되는 getSubwayCost(회사 자체적으로 정해진 운임료 가져옴) 실행 이후 가격을 불러오지 못할경우 실행됨
*** 현재는 무게만 고려하고 있지만 추후 거리나 부피에 따라서도 변동되도록 해야한다
****************************************************************************************************/

	function getAutobikeCost($param) { // 오토바이퀵사에 맞춰 운임료, 측정 무게에 따라 오토바이, 다마스, 라보로 구분됨
		$weight = $param['expec_weight'];
		$zipcode = $param['zipcode'];
		include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/SheetDAO.php");
		$dao = new SheetDAO();
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
}