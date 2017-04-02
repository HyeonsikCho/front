<?
/*
 * Copyright (c) 2015-2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * 옵션 계산 관련 모든 함수를 포함하고 있는 파일 
 * 
 * REVISION HISTORY (reverse chronological order)
 *=============================================================================
 * 2016/09/17 엄준현 추가(옵션계산용)
 * 2016/09/21 엄준현 수정(포장방법용 로직 수정)
 *=============================================================================
 *
 */

/**
 * @brief 수량에서 덩어리 수를 계산하는 함수
 *
 * @param $conn = db connection
 * @param $dao  = dao 객체
 * @param $util = front util 객체
 * @param $fb   = 폼빈 객체
 *
 * @return 계산된 덩어리 수
 */
function getAmtChunk($conn, $dao, $util, $fb) {
    $amt           = $fb["amt"];
    $cate_sortcode = $fb["cate_sortcode"];

    $amt = intval($amt);

    $sortcode_arr = $util->getTMBCateSortcode($conn, $dao, $cate_sortcode);

    $sortcode_t = $sortcode_arr["sortcode_t"];
    $sortcode_m = $sortcode_arr["sortcode_m"];
    $sortcode_b = $sortcode_arr["sortcode_b"];

    if ($sortcode_m === "001001") {
        // 일반명함
        $amt = ceil($amt / 500);
    } else if ($sortcode_m === "001002") {
        // 고급명함
        $amt = ceil($amt / 200);
    } else if ($sortcode_m === "001003") {
        // 카드명함
        $amt = ceil($amt / 200);
    } else if ($sortcode_b === "001004001") {
        // 스타일명함 - 휘라레
        $amt = ceil($amt / 200);
    } else if ($sortcode_b === "001004002") {
        // 스타일명함 - 반누브
        $amt = ceil($amt / 200);
    } else if ($sortcode_b === "001004003") {
        // 스타일명함 - 스타드림
        $amt = ceil($amt / 200);
    } else if ($sortcode_b === "001004004") {
        // 스타일명함 - 코팅
        $amt = ceil($amt / 500);
    } else if ($sortcode_b === "001004006") {
        // 스타일명함 - 무코팅
        $amt = ceil($amt / 500);
    } else if ($sortcode_b === "001005002") {
        // 스페셜상품 - 복권
        $amt = ceil($amt / 500);
    } else if ($sortcode_b === "001005006") {
        // 스페셜상품 - 종이자석
        $amt = ceil($amt / 500);
    } else if ($sortcode_t === "003") {
        // 전단
        $amt  = intval($fb["sheet_count"]) / 4000;

        if ($amt < 1) {
            $amt = 1;
        }
    } else if ($sortcode_t === "004") {
        // 광고홍보물
    }

    return $amt;
}

/**
 * @brief 당일판 기본가격 반환
 *
 * @param $depth1 = 당일판 depth1
 *
 * @return 가격
 */
function getDayBoardPrice($depth1) {
    return 0;
}

/**
 * @brief 시안요청 기본가격 반환
 *
 * @param $depth1 = 시안요청 depth1
 *
 * @return 가격
 */
function getDraftRequestPrice($depth1) {
    return 0;
}

/**
 * @brief 빠른생산요청 가격 반환
 *
 * @param $price  = 판매금액
 *
 * @return 가격
 */
function getQuickProductionPrice($price) {
    $ret = 0;
    $price = intval($price);

    if (0 < $price && $price < 100001) {
        $ret = $price * 0.1;
    } else if (100000 < $price && $price < 200001) {
        $ret = $price * 0.08;
    } else if (200000 < $price && $price < 300001) {
        $ret = $price * 0.06;
    } else if (300000 < $price && $price < 500001) {
        $ret = $price * 0.05;
    } else if (500000 < $price) {
        $ret = $price * 0.03;
    }

    return $ret;
}

/**
 * @brief 정매생산요청 가격 반환
 
 * @param $conn  = db connection
 * @param $dao   = dao 객체
 * @param $param = 검색용 파라미터
 *
 * @return 가격
 */
function getCorrectCountProductionPrice($conn, $dao, $param) {
    // 기준수량 - R
    $PAPER_AMT = array(
        "아트지|46" => array(
            "90g" => array(
                // 카테고리 수량(R) => 여분지 수량(장)
                25  => 90,
                50  => 135,
                75  => 180,
                100 => 224
            ),
            "120g" => array(
                25  => 90,
                50  => 135,
                75  => 180,
                100 => 225
            ),
            "150g" => array(
                25  => 90,
                50  => 135,
                75  => 180,
                100 => 225
            ),
            "180g" => array(
                25  => 90,
                50  => 135,
                75  => 180,
                100 => 225
            ),
            "300g" => array(
                25  => 90,
                50  => 135,
                75  => 180,
                100 => 225
            )
        ),
        "아트지|국" => array(
            "90g" => array(
                // 카테고리 수량(R) => 여분지 수량(장)
                25  => 179,
                50  => 224,
                75  => 269,
                100 => 314 
            ),
            "120g" => array(
                25  => 179,
                50  => 225,
                75  => 270,
                100 => 315
            ),
            "150g" => array(
                25  => 180,
                50  => 225,
                75  => 270,
                100 => 315
            ),
            "180g" => array(
                25  => 180,
                50  => 225,
                75  => 270,
                100 => 315
            )
        ),
        "모조지|46" => array(
            "80g" => array(
                25  => 90,
                50  => 135,
                75  => 180,
                100 => 225
            ),
        ),
        "모조지|국" => array(
            "80g" => array(
                25  => 180,
                50  => 225,
                75  => 270,
                100 => 315
            ),
        )
    );

    $sell_site         = $param["sell_site"];
    $paper_info_arr    = explode(' ', $param["paper_info"]);
    $paper_name        = $paper_info_arr[0];
    $basisweight       = array_pop($paper_info_arr);
    $cate_paper_mpcode = $param["cate_paper_mpcode"];
    $affil             = $param["affil"];
    $amt               = intval($param["amt"]);

    $key = $paper_name . '|' . $affil;

    $extra_amt = 0.0;
    $extra_amt_arr = $PAPER_AMT[$key][$basisweight];
    foreach ($extra_amt_arr as $cate_amt => $extra_amt) {
        if ($amt <= $cate_amt) {
            $extra_amt = doubleval($extra_amt);
            break;
        }
    }

    //$conn->debug = 1;
    unset($param);
    $param["mpcode"] = $cate_paper_mpcode;
    $param["affil"]  = $affil;
    $param["col"]    = "mpcode, crtr_unit";
    $prdt_paper_info = $dao->selectPrdtPaperInfo($conn, $param);
    $prdt_paper_mpcode = $prdt_paper_info["mpcode"];
    $paper_crtr_unit   = $prdt_paper_info["crtr_unit"];

    if ($paper_crtr_unit === 'R') {
        $extra_amt /= 500.0;
    }


    unset($param);
    $param["sell_site"] = $sell_site;
    $param["mpcode"]    = $prdt_paper_mpcode;

    $paper_price = $dao->selectPaperPrice($conn, $param);
    //$conn->debug = 0;
    $calc_price  = $paper_price * $extra_amt;

    //echo $extra_amt . '/' . $paper_price;

    return $calc_price;
}

/**
 * @brief 별도 포장 기본가격 반환
 *
 * @param $depth1 = 포장방법 depth2
 * @param $chunk  = 덩어리 수
 *
 * @return 가격
 */
function getPackPrice($depth2, $chunk) {
    $ret = 0;

    switch ($depth2) {
    case "종이박스" :
        $ret = 0;
        break;
    case "명함케이스" :
        $ret = 500;
        break;
    case "종이(완포지)" :
        $ret = 0;
        break;
    case "박스" :
        $ret = 1000;
        break;
    case "지관통" :
        $ret = 2000;
        break;
    case "별도케이스" :
        $ret = 5000;
        break;
    case "종이(완포지)+박스" :
        $ret = 2000;
        break;
    }

    return $ret * $chunk;
}

/**
 * @brief 동판/목형보관 기본가격 반환
 *
 * @param $depth1 = 동판/목형보관 depth1
 *
 * @return 가격
 */
function getCopperWoodPrice($depth1) {
    return 0;
}

/**
 * @brief 색견본 기본가격 반환
 *
 * @param $depth1 = 색견본 depth1
 *
 * @return 가격
 */
function getColorSamplePrice($depth1) {
    return 0;
}

/**
 * @brief 교정출력 기본가격 반환
 *
 * @param $depth1 = 교정출력 depth1
 * @param $depth2 = 교정출력 depth2
 *
 * @return 가격
 */
function getCorrectionPrintPrice($depth1, $depth2) {
    $ret = 0;

    switch ($depth1 . $depth2) {
    case "A4칼라" :
        $ret = 1000;
        break;
    case "A4흑백" :
        $ret = 500;
        break;
    case "A3칼라" :
        $ret = 2000;
        break;
    case "A3흑백" :
        $ret = 1000;
        break;
    }

    return $ret;
}

/**
 * @brief 감리요청 기본가격 반환
 *
 * @param $depth1 = 감리요청 depth1
 *
 * @return 가격
 */
function getInspectionRequestPrice($depth1) {
    return 50000;
}

/**
 * @brief 판비추가 기본가격 반환
 *
 * @param $depth1 = 판비추가 depth1
 *
 * @return 가격
 */
function getAddPlatePrice($depth1) {
    return 0;
}

/**
 * @brief 베다인쇄 기본가격 반환
 *
 * @param $depth1 = 베다인쇄 depth1
 *
 * @return 가격
 */
function getBackgroundPrice($depth1) {
    return 0;
}
?>
