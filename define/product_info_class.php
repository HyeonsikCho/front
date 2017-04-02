<?
/*
 * Copyright (c) 2015-2017 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *=============================================================================
 * 2016/09/02 엄준현 수정(CATE_INFO에 스타일명함 추가)
 * 2016/09/20 엄준현 수정(AFTER_ARR에 가공, 복권실크 추가)
 * 2016/11/23 엄준현 수정(NCR 조 수 추가)
 * 2016/12/11 엄준현 수정(후공정 계열/절수 적용여부 배열 추가 -> 삭제)
 *=============================================================================
 *
 */
class ProductInfoClass {
    // 후공정 종류 배열
    const AFTER_ARR = array(
        "코팅"     => "coating",
        "귀도리"   => "rounding",
        "오시"     => "impression",
        "미싱"     => "dotline",
        "타공"     => "punching",
        "접지"     => "foldline",
        "엠보싱"   => "embossing",
        "박"       => "foil",
        "형압"     => "press",
        "도무송"   => "thomson",
        "넘버링"   => "numbering",
        "재단"     => "cutting",
        "제본"     => "binding",
        "접착"     => "bonding",
        "라미넥스" => "laminex",
        "가공"     => "manufacture",
        "복권실크" => "lotterysilk"
    );

    // 상품별 리다이렉트 페이지
    const PAGE_ARR = array(
        "001" => array(
            "001003"    => "nc_card.html",
            "001004"    => "nc_style.html",
            "001005"    => "nc_special.html",
            "001001002" => "nc_stan.html",
            "001001005" => "nc_stan.html",
            "001005002" => "nc_lottery.html",
            "ELSE"      => "nc.html"
         ),
        "002" => array(
            "002001"    => "st_cut.html",
            "002002"    => "st_thomson.html",
            "002002010" => "st_free_thomson.html"
        ),
        "003" => array(
            "003001" => "bl.html",
            "003003" => "bl_mono.html"
        ),
        "004" => array(
            "004001"    => "ad_catabro.html",
            "004002"    => "ad_book.html",
            "004003001" => "ad_poster.html",
            "004003003" => "ad_stan.html",
            "004003006" => "ad_holder.html",
            "004003007" => "ad_multiple.html",
            "004003008" => "ad_door.html",
            "004003009" => "ad_memo.html",
            "004003011" => "ad_multiple.html",
            "ELSE"      => "ad.html"
         ),
        "005" => array(
            "005003" => "ev_master.html",
            "ELSE"   => "ev.html"
         ),
        "006" => array(
            "006001" => "mt_ncr.html",
            "006002" => "mt_form.html"
         ),
        "007" => "bl.html",
        "008" => array(
            "008001"    => "gb.html",
            "008002001" => "esti_sheet.html",
            "008002002" => "esti_booklet.html"
         ),
        "009" => "dt.html"
    );

    // 카테고리별 재단, 작업사이즈 차이
    const SIZE_GAP = array(
        "002002010" => 6
    );

    // 주문페이지 상단 상품명, 설명
    const CATE_INFO = array(
        "001001" => array(
            "cate_dvs"  => "nc",
            "cate_dscr" => "일반명함입니다."
        ),
        "001002" => array(
            "cate_dvs"  => "nc",
            "cate_dscr" => "고급명함입니다."
        ),
        "001003" => array(
            "cate_dvs"  => "nc",
            "cate_dscr" => "카드명함입니다."
        ),
        "001004" => array(
            "cate_dvs"  => "nc",
            "cate_dscr" => "스타일명함입니다."
        ),
        "001005" => array(
            "cate_dvs"  => "nc",
            "cate_dscr" => "스페셜상품입니다."
        ),
        "002001" => array(
            "cate_dvs"  => "st",
            "cate_dscr" => "재단형스티커입니다."
        ),
        "002002" => array(
            "cate_dvs"  => "st",
            "cate_dscr" => "도무송형스티커입니다."
        ),
        "003001" => array(
            "cate_dvs"  => "bl",
            "cate_dscr" => "합판전단입니다."
        ),
        "003002" => array(
            "cate_dvs"  => "bl",
            "cate_dscr" => "특가전단입니다."
        ),
        "003003" => array(
            "cate_dvs"  => "bl",
            "cate_dscr" => "독판전단입니다."
        ),
        "004001" => array(
            "cate_dvs"  => "ad",
            "cate_dscr" => "카탈로그/브로셔입니다."
        ),
        "004002" => array(
            "cate_dvs"  => "ad",
            "cate_dscr" => "책자입니다."
        ),
        "004003" => array(
            "cate_dvs"  => "ad",
            "cate_dscr" => "기획인쇄물입니다."
        ),
        "005001" => array(
            "cate_dvs"  => "ev",
            "cate_dscr" => "합판봉투입니다."
        ),
        "005002" => array(
            "cate_dvs"  => "ev",
            "cate_dscr" => "독판봉투입니다."
        ),
        "005003" => array(
            "cate_dvs"  => "ev",
            "cate_dscr" => "마스터봉투입니다."
        ),
        "006001" => array(
            "cate_dvs"  => "mt",
            "cate_dscr" => "마스터 ncr입니다."
        ),
        "006002" => array(
            "cate_dvs"  => "mt",
            "cate_dscr" => "마스터 모조지입니다."
        ),
        "007001" => array(
            "cate_dvs"  => "bl",
            "cate_dscr" => "초소량인쇄입니다."
        ),
        "008001" => array(
            "cate_dvs"  => "gb",
            "cate_dscr" => "그린백입니다."
        ),
        "008002" => array(
            "cate_dvs"  => "etc",
            "cate_dscr" => "별도견적입니다."
        ),
        "009001" => array(
            "cate_dvs"  => "dt",
            "cate_dscr" => "디지털명함입니다."
        )
    );
}
?>
