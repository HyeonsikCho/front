<?php
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Common/FactoryMethod.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Products/CommonProduct.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Products/Leaflet.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Products/Namecard.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Products/Sticker.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Products/Page.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Common/PrintoutInterface.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/BasicMaterials/Paper.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/BasicMaterials/Option.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/BasicMaterials/Afterprocess.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Common/PrintoutInterface.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/test/Products/Product.php');

class DPrintingFactory extends FactoryMethod
{
	/**
	 * {@inheritdoc}
	 */
	function createProduct($type)
	{
		$tmp_type = substr($type,0, 3);
		switch ($tmp_type) {
			case '001' :
				return new Namecard($type);
				break;
			case '002' :
				return new Namecard($type);
				break;
			default:
				return new Leaflet($type);
				break;
		}
		/*
		switch ($type) {
			case '001001001':
				return new Namecard($type);
				break;
			case '001001002':
				return new Namecard($type);
				break;
			case '001001004':
				return new Namecard($type);
				break;
			case '001001005':
				return new Namecard($type);
				break;
			case '001002001': // 빌리지명함
				return new Namecard($type);
				break;
			case '001002002': // 베이직명함
				return new Namecard($type);
				break;
			case '001002003': // 반누브명함
				return new Namecard($type);
				break;
			case '001002004': // 그레이스명함
				return new Namecard($type);
				break;
			case '001002005': // 그레이스명함
				return new Namecard($type);
				break;
			case '001002006': // 키칼라메탈릭 골드명함
				return new Namecard($type);
				break;
			case '001002007': // 키칼라메탈릭 아이스골드명함
				return new Namecard($type);
				break;
			case '001002008': // 마쉬맬로우명함
				return new Namecard($type);
				break;
			case '001002009': // 팝셋명함
				return new Namecard($type);
				break;
			case '001002010': // 샤인스페셜명함
				return new Namecard($type);
				break;
			case '001002011': // 스타드림명함
				return new Namecard($type);
				break;
			case '001002012': // 스타(드림) 골드명함
				return new Namecard($type);
				break;
			case '001002013': // 스타(드림) 실버명함
				return new Namecard($type);
				break;
			case '001002014': // 스코틀랜드명함
				return new Namecard($type);
				break;
			case '001002015': // 탄트지명함
				return new Namecard($type);
				break;
			case '001002016': // 유포지명함
				return new Namecard($type);
				break;
			case '001002017': // 휘라레명함
				return new Namecard($type);
				break;
			case '001002018': // 랑데뷰명함
				return new Namecard($type);
				break;
			case '001003001': // 빌리지명함
				return new Namecard($type);
				break;
			case '001003002': // 베이직명함
				return new Namecard($type);
				break;
			case '001003003': // 반누브명함
				return new Namecard($type);
				break;
			case '001003004': // 그레이스명함
				return new Namecard($type);
				break;
			case '001003005': // 그레이스명함
				return new Namecard($type);
				break;
			case '001003006': // 키칼라메탈릭 골드명함
				return new Namecard($type);
				break;
			case '001003007': // 키칼라메탈릭 아이스골드명함
				return new Namecard($type);
				break;
			case '001003008': // 마쉬맬로우명함
				return new Namecard($type);
				break;
			case '001003009': // 팝셋명함
				return new Namecard($type);
				break;
			case '001003010': // 샤인스페셜명함
				return new Namecard($type);
				break;
			case '001003011': // 스타드림명함
				return new Namecard($type);
				break;
			case '001003012': // 스타(드림) 골드명함
				return new Namecard($type);
				break;
			case '001003013': // 스타(드림) 실버명함
				return new Namecard($type);
				break;
			case '001003014': // 스코틀랜드명함
				return new Namecard($type);
				break;
			case '001003015': // 탄트지명함
				return new Namecard($type);
				break;
			case '001003016': // 유포지명함
				return new Namecard($type);
				break;
			case '001003017': // 휘라레명함
				return new Namecard($type);
				break;
			case '001003018': // 랑데뷰명함
				return new Namecard($type);
				break;
			case '001003019': // 유포지명함
				return new Namecard($type);
				break;
			case '001003020': // 휘라레명함
				return new Namecard($type);
				break;
			case '001003021': // 랑데뷰명함
				return new Namecard($type);
				break;
			case '001004001': // 빌리지명함
				return new Namecard($type);
				break;
			case '001004002': // 베이직명함
				return new Namecard($type);
				break;
			case '001004003': // 반누브명함
				return new Namecard($type);
				break;
			case '001004004': // 그레이스명함
				return new Namecard($type);
				break;
			case '001004005': // 그레이스명함
				return new Namecard($type);
				break;
			case '001004006': // 키칼라메탈릭 골드명함
				return new Namecard($type);
				break;
			case '001005001': // 빌리지명함
				return new Namecard($type);
				break;
			case '001005002': // 베이직명함
				return new Namecard($type);
				break;
			case '001005003': // 반누브명함
				return new Namecard($type);
				break;
			case '001005004': // 그레이스명함
				return new Namecard($type);
				break;
			case '001005005': // 그레이스명함
				return new Namecard($type);
				break;
			case '001005006': // 키칼라메탈릭 골드명함
				return new Namecard($type);
				break;
			case '002001001':
				return new Sticker($type);
				break;
			case '002001002':
				return new Sticker($type);
				break;
			case '002001003':
				return new Sticker($type);
				break;
			case '002001004':
				return new Sticker($type);
				break;
			case '002001005':
				return new Sticker($type);
				break;
			case '002001006':
				return new Sticker($type);
				break;
			case '002001010':
				return new Sticker($type);
				break;
			case '002002001':
				return new Sticker($type);
				break;
			case '002002002':
				return new Sticker($type);
				break;
			case '002002003':
				return new Sticker($type);
				break;
			case '002002004':
				return new Sticker($type);
				break;
			case '002002005':
				return new Sticker($type);
				break;
			case '002002006':
				return new Sticker($type);
				break;
			case '002002007':
				return new Sticker($type);
				break;
			case '002002008':
				return new Sticker($type);
				break;
			case '002002009':
				return new Sticker($type);
				break;
			case '002002010':
				return new Sticker($type);
				break;
			case '002003002':
				return new Sticker($type);
				break;
			case '002004009':
				return new Sticker($type);
				break;
			case '003001001':
				return new Leaflet($type);
				break;
			case '003001003':
				return new Leaflet($type);
				break;
			case '003003001':
				return new Leaflet($type);
				break;
			case '003003002':
				return new Leaflet($type);
				break;
			case '003003003':
				return new Leaflet($type);
				break;
			case '003003004':
				return new Leaflet($type);
				break;
			case '005001001':
				return new Page($type);
				break;
			default:
				return new Leaflet($type);
				break;
		}*/
	}
}
?>