// calcPrice() 건너뛸 때 사용, 후공정 가격 재계산시 로직 반복타는거 방지
var passFlag = false;

$(document).ready(function() {
    // product.js에 존재함
    monoYn = '1';
    flattypYn = false;

    getRealPaperAmt.exec("all");
});

/**
 * @brief 내지 추가여부 저장 배열
 */
var innerPaper = {
   "inner2" : false, 
   "inner3" : false
};

/**
 * @brief 내지 추가할 경우 추가여부와 가격 재계산
 * 
 * @param dvs = 추가하는 내지 구분값
 */
var addInner = function(dvs) {
    innerPaper[dvs] = true;

    // 내지 추가될 경우 후공정 가격 재계산
    reCalcAfterPrice();

    if (loadPrdtPrice.price[dvs] === true) {
        calcPrice();
    } else {
        changeData(dvs);
    }
}

/**
 * @brief 내지 제거할 경우 추가여부와 가격 재계산
 *
 * @param dvs = 제거하는 내지 구분값
 */
var delInner = function(dvs) {
    innerPaper[dvs] = false;

    // 내지 삭제될 경우 후공정 가격 재계산
    reCalcAfterPrice();

    calcPrice();
}

/**
 * @param 인쇄방식에 해당하는 인쇄도수 검색
 *
 * @param dvs = 인쇄방식을 선택한 위치구분값
 * @param val = 인쇄방식
 */
var loadPrintTmpt = function(dvs, val) {
    var callback = function(result) {
        var dvs = loadPrintTmptCommon.dvs; 

        $("#bef_tmpt_" + dvs).html(result.bef_tmpt);
        $("#bef_add_tmpt_" + dvs).html(result.bef_add_tmpt);
        $("#aft_tmpt_" + dvs).html(result.aft_tmpt);
        $("#aft_add_tmpt_" + dvs).html(result.aft_add_tmpt);

        changeData(dvs);
    };

    loadPrintTmptCommon(dvs, val, callback);
};

/**
 * @param 전면(추가) 인쇄도수 변경시 후면(추가) 인쇄도수 변경
 *
 * @param dvs = 선택한 위치구분값
 * @param add = 추가도수 구분값
 * @param val = 인쇄도수
 */
var changeTmpt = function(dvs, add, val) {
    var $aftObj = $("#aft" + add + "_tmpt_" + dvs);

    $aftObj.children("option").each(function() {
        if ($(this).val() === val) {
            $(this).prop("selected", true);
            changeData(dvs);
        } else {
            $(this).prop("selected", false);
        }
    });
};

/**
 * @brief 수량과 페이지수에 따른 실제 종이수량 계산
 *
 * @param info = 계산용 정보데이터
 */
var calcRealPaperAmt = function(info) {
    amt      = info["amt"];
    posNum   = info["posNum"];
    pageNum  = info["pageNum"].split('!')[0];
    amtUnit  = info["amtUnit"];

    // 0page일 경우 인쇄 수량 0 반환
    if (pageNum == 0) {
        return 0;
    }

    var ret = Math.round((amt / posNum) / (2 / pageNum));

    return ret;
};

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 *
 * @param dvs = 데이터 변경 영역 구분값
 */
var changeData = function(dvs) {
    var data = {
        "cate_sortcode" : $("#cate_bot").val(),
        "stan_mpcode"   : $("#size").val(),
        "amt"           : $("#amt").val(),
        "dvs"           : dvs
    };

    if (dvs === "all") {
        // 표지
        data.cover_paper_mpcode       = $("#paper_cover").val();
        data.cover_bef_print_name     = $("#bef_tmpt_cover").val();
        data.cover_bef_add_print_name = $("#bef_add_tmpt_cover").val();
        data.cover_aft_print_name     = $("#aft_tmpt_cover").val();
        data.cover_aft_add_print_name = $("#aft_add_tmpt_cover").val();
        data.cover_print_purp         = $("#print_purp_cover").val();
        data.cover_page_info          = $("#page_cover").val();
        // 내지1
        data.inner1_paper_mpcode       = $("#paper_inner1").val();
        data.inner1_bef_print_name     = $("#bef_tmpt_inner1").val();
        data.inner1_bef_add_print_name = $("#bef_add_tmpt_inner1").val();
        data.inner1_aft_print_name     = $("#aft_tmpt_inner1").val();
        data.inner1_aft_add_print_name = $("#aft_add_tmpt_inner1").val();
        data.inner1_print_purp         = $("#print_purp_inner1").val();
        data.inner1_page_info          = $("#page_inner1").val();
        // 내지2
        data.inner2_paper_mpcode       = $("#paper_inner2").val();
        data.inner2_bef_print_name     = $("#bef_tmpt_inner2").val();
        data.inner2_bef_add_print_name = $("#bef_add_tmpt_inner2").val();
        data.inner2_aft_print_name     = $("#aft_tmpt_inner2").val();
        data.inner2_aft_add_print_name = $("#aft_add_tmpt_inner2").val();
        data.inner2_print_purp         = $("#print_purp_inner2").val();
        data.inner2_page_info          = $("#page_inner2").val();
        // 내지3
        data.inner3_paper_mpcode       = $("#paper_inner3").val();
        data.inner3_bef_print_name     = $("#bef_tmpt_inner3").val();
        data.inner3_bef_add_print_name = $("#bef_add_tmpt_inner3").val();
        data.inner3_aft_print_name     = $("#aft_tmpt_inner3").val();
        data.inner3_aft_add_print_name = $("#aft_add_tmpt_inner3").val();
        data.inner3_print_purp         = $("#print_purp_inner3").val();
        data.inner3_page_info          = $("#page_inner3").val();
    } else if (dvs === "cover") {
        // 표지
        data.cover_paper_mpcode       = $("#paper_cover").val();
        data.cover_bef_print_name     = $("#bef_tmpt_cover").val();
        data.cover_bef_add_print_name = $("#bef_add_tmpt_cover").val();
        data.cover_aft_print_name     = $("#aft_tmpt_cover").val();
        data.cover_aft_add_print_name = $("#aft_add_tmpt_cover").val();
        data.cover_print_purp         = $("#print_purp_cover").val();
        data.cover_page_info          = $("#page_cover").val();
    } else if (dvs === "inner1") {
        // 내지1
        data.inner1_paper_mpcode       = $("#paper_inner1").val();
        data.inner1_bef_print_name     = $("#bef_tmpt_inner1").val();
        data.inner1_bef_add_print_name = $("#bef_add_tmpt_inner1").val();
        data.inner1_aft_print_name     = $("#aft_tmpt_inner1").val();
        data.inner1_aft_add_print_name = $("#aft_add_tmpt_inner1").val();
        data.inner1_print_purp         = $("#print_purp_inner1").val();
        data.inner1_page_info          = $("#page_inner1").val();
    } else if (dvs === "inner2") {
        // 내지2
        data.inner2_paper_mpcode       = $("#paper_inner2").val();
        data.inner2_bef_print_name     = $("#bef_tmpt_inner2").val();
        data.inner2_bef_add_print_name = $("#bef_add_tmpt_inner2").val();
        data.inner2_aft_print_name     = $("#aft_tmpt_inner2").val();
        data.inner2_aft_add_print_name = $("#aft_add_tmpt_inner2").val();
        data.inner2_print_purp         = $("#print_purp_inner2").val();
        data.inner2_page_info          = $("#page_inner2").val();
    } else if (dvs === "inner3") {
        // 내지3
        data.inner3_paper_mpcode       = $("#paper_inner3").val();
        data.inner3_bef_print_name     = $("#bef_tmpt_inner3").val();
        data.inner3_bef_add_print_name = $("#bef_add_tmpt_inner3").val();
        data.inner3_aft_print_name     = $("#aft_tmpt_inner3").val();
        data.inner3_aft_add_print_name = $("#aft_add_tmpt_inner3").val();
        data.inner3_print_purp         = $("#print_purp_inner3").val();
        data.inner3_page_info          = $("#page_inner3").val();
    }

    loadPrdtPrice.data = data;
    loadPrdtPrice.dvs  = dvs;
    loadPrdtPrice.exec();
};

/**
 * @brief 상품 가격정보 json으로 반환
 */
var loadPrdtPrice = {
    "data"  : null,
    "dvs"   : null,
    "price" : {
        // 가격이 있는지 확인
        "cover"  : false,
        "inner1" : false,
        "inner2" : false,
        "inner3" : false
    },
    "exec"  : function() {
        var url = "/ajax/product/load_calc_price.php";
        var callback = function(result) {

            var dvs = loadPrdtPrice.dvs;

            if (dvs === "all") {
                loadPrdtPrice.price.cover  = true;
                loadPrdtPrice.price.inner1 = true;
                loadPrdtPrice.price.inner2 = true;
                loadPrdtPrice.price.inner3 = true;

                var coverPaperPrice  = result.cover.paper;
                var coverPrintPrice  = result.cover.print;
                var coverOutputPrice = result.cover.output;
                var coverPrice = result.cover.price;

                var inner1PaperPrice  = result.inner1.paper;
                var inner1PrintPrice  = result.inner1.print;
                var inner1OutputPrice = result.inner1.output;
                var inner1Price = result.inner1.price;

                var inner2PaperPrice  = result.inner2.paper;
                var inner2PrintPrice  = result.inner2.print;
                var inner2OutputPrice = result.inner2.output;
                var inner2Price = result.inner2.price;

                var inner3PaperPrice  = result.inner3.paper;
                var inner3PrintPrice  = result.inner3.print;
                var inner3OutputPrice = result.inner3.output;
                var inner3Price = result.inner3.price;

                var $coverObj  = $("#cover_price");
                var $inner1Obj = $("#inner1_price");
                var $inner2Obj = $("#inner2_price");
                var $inner3Obj = $("#inner3_price");
                
                $coverObj.attr("paper" , coverPaperPrice);
                $coverObj.attr("print" , coverPrintPrice);
                $coverObj.attr("output", coverOutputPrice);
                $coverObj.attr("price" , coverPrice);
                
                $inner1Obj.attr("paper" , inner1PaperPrice);
                $inner1Obj.attr("print" , inner1PrintPrice);
                $inner1Obj.attr("output", inner1OutputPrice);
                $inner1Obj.attr("price" , inner1Price);
                
                $inner2Obj.attr("paper" , inner2PaperPrice);
                $inner2Obj.attr("print" , inner2PrintPrice);
                $inner2Obj.attr("output", inner2OutputPrice);
                $inner2Obj.attr("price" , inner2Price);
                
                $inner3Obj.attr("paper" , inner3PaperPrice);
                $inner3Obj.attr("print" , inner3PrintPrice);
                $inner3Obj.attr("output", inner3OutputPrice);
                $inner3Obj.attr("price" , inner3Price);
            } else {
                loadPrdtPrice.price[dvs] = true;

                var paperPrice  = result[dvs].paper;
                var printPrice  = result[dvs].print;
                var outputPrice = result[dvs].output;
                var price = result[dvs].price;

                var $obj  = $("#" + dvs + "_price");
                
                $obj.attr("paper" , paperPrice);
                $obj.attr("print" , printPrice);
                $obj.attr("output", outputPrice);
                $obj.attr("price" , price);
            }

            calcPrice();
        };

        ajaxCall(url, "json", loadPrdtPrice.data, callback);
    }
};

/**
 * @brief 화면에 출력되는 가격 및 빠른견적서 내용 수정
 */
var calcPrice = function() {
    // 부가세율
    var taxRate = 0.1;

    // 가격 정보 객체
    $coverObj  = $("#cover_price");
    $inner1Obj = $("#inner1_price");
    $inner2Obj = $("#inner2_price");
    $inner3Obj = $("#inner3_price");

    // 표지, 내지1/2/3 가격
    var coverPrice  = parseInt($coverObj.attr("price"));
    var inner1Price = parseInt($inner1Obj.attr("price"));

    var sellPrice = coverPrice + inner1Price;

    if (innerPaper.inner2 === true) {
        sellPrice += parseInt($inner2Obj.attr("price"));
    }
    if (innerPaper.inner3 === true) {
        sellPrice += parseInt($inner3Obj.attr("price"));
    }
    sellPrice = ceilVal(sellPrice);

    // 회원등급 할인
    var gradeSale = parseFloat($("#grade_sale").attr("rate"));
    gradeSale /= 100.0;

    // 제본 가격
    var bindingPrice = $("#binding_val").attr("price");

    if (checkBlank(bindingPrice) === true) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : "제본",
        };

        getAfterPrice.load("binding", data);
        return false;
    }

    bindingPrice = parseInt(bindingPrice);

    // 옵션비 총합
    var optDefaultPrice = parseInt($("#opt_default_price").attr("price"));
    var sumOptPrice = getSumOptPrice();
    sumOptPrice += optDefaultPrice;
    sumOptPrice  = ceilVal(sumOptPrice);

    // 후공정비 총합
    var afterDefaultPrice = parseInt($("#after_default_price").attr("price"));
    var sumAfterPrice = getSumAfterPrice();
    sumAfterPrice += bindingPrice;
    sumAfterPrice += afterDefaultPrice;
    sumAfterPrice  = ceilVal(sumAfterPrice);

    // 견적서 종이비 계산
    var coverPaperPrice  = parseInt($coverObj.attr("paper"));
    var inner1PaperPrice = parseInt($inner1Obj.attr("paper"));
    var inner2PaperPrice = 0;
    var inner3PaperPrice = 0;

    if (innerPaper.inner2 === true) {
        inner2PaperPrice = parseInt($inner2Obj.attr("paper"));
    }
    if (innerPaper.inner3 === true) {
        inner3PaperPrice = parseInt($inner3Obj.attr("paper"));
    }

    coverPaperPrice  = ceilVal(coverPaperPrice);
    inner1PaperPrice = ceilVal(inner1PaperPrice);
    inner2PaperPrice = ceilVal(inner2PaperPrice);
    inner3PaperPrice = ceilVal(inner3PaperPrice);

    var paper = coverPaperPrice +
                inner1PaperPrice +
                inner2PaperPrice +
                inner3PaperPrice;

    // 견적서 인쇄비 계산
    var coverPrintPrice  = parseInt($coverObj.attr("print"));
    var inner1PrintPrice = parseInt($inner1Obj.attr("print"));
    var inner2PrintPrice = 0;
    var inner3PrintPrice = 0;

    if (innerPaper.inner2 === true) {
        inner2PrintPrice = parseInt($inner2Obj.attr("print"));
    }
    if (innerPaper.inner3 === true) {
        inner3PrintPrice = parseInt($inner3Obj.attr("print"));
    }

    coverPrintPrice  = ceilVal(coverPrintPrice);
    inner1PrintPrice = ceilVal(inner1PrintPrice);
    inner2PrintPrice = ceilVal(inner2PrintPrice);
    inner3PrintPrice = ceilVal(inner3PrintPrice);

    var print = coverPrintPrice +
                inner1PrintPrice +
                inner2PrintPrice +
                inner3PrintPrice;

    // 견적서 출력비 계산
    var coverOutputPrice  = parseInt($coverObj.attr("output"));
    var inner1OutputPrice = parseInt($inner1Obj.attr("output"));
    var inner2OutputPrice = 0;
    var inner3OutputPrice = 0;

    if (innerPaper.inner2 === true) {
        inner2OutputPrice = parseInt($inner2Obj.attr("output"));
    }
    if (innerPaper.inner3 === true) {
        inner3OutputPrice = parseInt($inner3Obj.attr("output"));
    }

    coverOutputPrice  = ceilVal(coverOutputPrice);
    inner1OutputPrice = ceilVal(inner1OutputPrice);
    inner2OutputPrice = ceilVal(inner2OutputPrice);
    inner3OutputPrice = ceilVal(inner3OutputPrice);

    var output = coverOutputPrice +
                 inner1OutputPrice +
                 inner2OutputPrice +
                 inner3OutputPrice;


    // 정상 판매가 계산
    sellPrice += sumAfterPrice + sumOptPrice;

    // 부가세 포함가격 계산
    var tax = sellPrice * taxRate;
    tax = ceilVal(tax);
    var calcSellPrice = sellPrice + tax;

    // 회원등급 할인가 계산
    var calcGradeSale = calcSellPrice * gradeSale;
    calcGradeSale = ceilVal(calcGradeSale);
    var salePrice = calcSellPrice - calcGradeSale;

    // 정상 판매가 변경
    $("#sell_price").attr("val", sellPrice);
    $("#sell_price").html(calcSellPrice.format() + "원");
    // 회원등급 할인가 변경
    $("#grade_sale").html(calcGradeSale.format() + "원");
    // 기본할인가 변경
    $("#sale_price").attr("val", salePrice);
    $("#sale_price").html(salePrice.format() + "원");

    // 견적서 종이비 변경
    $("#esti_paper").html(paper.format());
    // 견적서 출력비 변경
    $("#esti_output").html(output.format());
    // 견적서 인쇄비 변경
    $("#esti_print").html(print.format());
    // 견적서 후공정비 변경
    $("#esti_after").html(sumAfterPrice.format());
    // 견적서 옵션비 변경
    $("#esti_opt").html(sumOptPrice.format());
    // 견적서 합계 변경
    $("#esti_sum").html(sellPrice.format());
    // 견적서 부가세 변경
    $("#esti_tax").html(tax.format());
    // 견적서 판매가 변경
    $("#esti_sell_price").html(calcSellPrice.format());
    // 견적서 기본할인가 변경
    $("#esti_sale_price").html(salePrice.format());
};

/**
 * @brief 페이지 변경시 종이수량 재계산을 위한 중간함수
 *
 * @param dvs = 바꾼 페이지 위치값
 */
var changePage = function(dvs) {
    getRealPaperAmt.exec(dvs);
    changeData(dvs);
};

/**
 * @brief 표지, 내지1/2/3 실제 종이인쇄수량 계산
 *
 * @param dvs = 바꾼 페이지 위치값
 */
var getRealPaperAmt = {
    "amt"  : {
        "cover"  : 0,
        "inner1" : 0,
        "inner2" : 0,
        "inner3" : 0
    },
    "exec" : function(dvs) {
        var $amtObj = $("#amt");
        var amt     = parseFloat($amtObj.val());
        var amtUnit = $amtObj.attr("amt_unit");
        var posNum  = $("#size").attr("pos_num");

        var info = {
           "amt"      : amt,
           "posNum"   : posNum,
           "amtUnit"  : amtUnit
        };

        var pageNum = 0;

        // 표지 종이수량 계산
        if (dvs === "all" || dvs === "cover") {
            pageNum = $("#page_cover").val();
            info.pageNum = pageNum;

            this.amt.cover = calcRealPaperAmt(info);
        }
             
        // 내지1 종이수량 계산
        if (dvs === "all" || dvs === "inner1") {
            pageNum = $("#page_inner1").val();
            info.pageNum = pageNum;

            this.amt.inner1 = calcRealPaperAmt(info);
        }

        // 내지2 종이수량 계산
        if (dvs === "all" || dvs === "inner2") {
            pageNum = $("#page_inner2").val();
            info.pageNum = pageNum;
            
            this.amt.inner2 = calcRealPaperAmt(info);
        }

        // 내지3 종이수량 계산
        if (dvs === "all" || dvs === "inner3") {
            pageNum = $("#page_inner3").val();
            info.pageNum = pageNum;

            this.amt.inner3 = calcRealPaperAmt(info);
        }
    }
};

/******************************************************************************
 * 후공정 관련 함수
 ******************************************************************************/

/**
 * @brief 제본의 가격을 검색한다
 * 가격 배열이 null일 경우 새로 검색한다
 *
 * @param dvs = 후공정 구분값
 */
var getBindingCalcBookletPrice = function(dvs) {
    var mpcode = $("#" + dvs + "_val").val();
    var priceArr  = getAfterPrice.price[dvs];

    // 가격정보가 없을경우
    if (checkBlank(priceArr)) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : "제본",
        };

        getAfterPrice.load(dvs, data);

        return false;
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var sumPrice = getAfterSumPrice(priceArr);

    $("#" + dvs +"_val").attr("price", sumPrice);

    execCalcPrice();
};

/**
 * @brief 코팅의 가격을 검색한다
 *
 * @param dvs = 후공정 구분값
 */
var getCoatingCalcBookletPrice = function(dvs) {
    var mpcode = $("#" + dvs + "_val").val();
    var priceArr  = getAfterPrice.price[dvs];

    if (checkBlank(priceArr)) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : "코팅",
        };

        getAfterPrice.load(dvs, data);
        return false;
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var sumPrice = getAfterSumPrice(priceArr);

    $("#" + dvs + "_price").html(sumPrice.format() + "원");
    $("#chk_" + dvs).attr("price", sumPrice);

    execCalcPrice();
};

/**
 * @brief 귀도리의 가격을 검색한다
 * 가격 배열이 null일 경우 새로 검색한다
 *
 * @param dvs = 후공정 구분값
 */
var getRoundingCalcBookletPrice = function(dvs) {
    var mpcode = $("#" + dvs + "_val").val();
    var priceArr  = getAfterPrice.price[dvs];

    // 가격정보가 없을경우
    if (checkBlank(priceArr)) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : "귀도리",
        };

        getAfterPrice.load(dvs, data);

        return false;
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var sumPrice = getAfterSumPrice(priceArr);

    $("#" + dvs + "_price").html(sumPrice.format() + "원");
    $("#chk_" + dvs).attr("price", sumPrice);

    execCalcPrice();
};

/**
 * @brief 종이 구성 변경 등으로 후공정 가격 재계산시
 * calcPrice() 함수 중복호출 되지 않도록 처리
 */
var execCalcPrice = function() {
    if (passFlag === true) {
        passFlag = false;
        return;
    }

    passFlag = false;
    calcPrice();

    return;
}

/**
 * @brief 가격 배열에서 각 종이구분별로
 * 후공정 가격 계산해서 합산 후 반환
 *
 * @param priceArr = 가격 배열
 *
 * @return 합산된 가격
 */
var getAfterSumPrice = function(priceArr) {
    var crtrUnit = priceArr.crtr_unit;
    var amtUnit  = $("#amt").attr("amt_unit");

    // 표지 종이수량
    var paperAmtCover = getRealPaperAmt.amt.cover;
    paperAmtCover = amtCalc(paperAmtCover, amtUnit, crtrUnit);
        
    // 내지1 종이수량
    var paperAmtInner1 = getRealPaperAmt.amt.inner1;
    paperAmtInner1 = amtCalc(paperAmtInner1, amtUnit, crtrUnit);

    // 표지 제본 가격 계산
    var priceCover = calcAfterPrice(priceArr, paperAmtCover);
    // 내지1 제본 가격 계산
    var priceInner1 = calcAfterPrice(priceArr, paperAmtInner1);
    var priceInner2 = 0;
    var priceInner3 = 0;

    if (innerPaper.inner2 === true) {
        // 내지2 종이수량
        var paperAmtInner2 = getRealPaperAmt.amt.inner2;
        paperAmtInner2 = amtCalc(paperAmtInner2, amtUnit, crtrUnit);

        // 내지2 제본 가격 계산
        priceInner2 = calcAfterPrice(priceArr, paperAmtInner2);
    }
    if (innerPaper.inner3 === true) {
        // 내지3 종이수량
        var paperAmtInner3 = getRealPaperAmt.amt.inner3;
        paperAmtInner3 = amtCalc(paperAmtInner3, amtUnit, crtrUnit);

        // 내지3 제본 가격 계산
        priceInner3 = calcAfterPrice(priceArr, paperAmtInner3);
    }

    var sumPrice = priceCover + priceInner1 + priceInner2 + priceInner3;

    return sumPrice;
}

/**
 * @brief 내지 추가/삭제시 후공정 가격 재계산 함수
 */
var reCalcAfterPrice = function() {
    passFlag = true;
    getAfterPrice.common("binding");

    $("input[name='chk_after']").each(function() {
        $obj = $(this);

        if ($obj.prop("checked") === false) {
            return true;
        }

        // calcPrice() 함수 회피용
        passFlag = true;
        loadAfterPrice.exec($obj.prop("checked"), $obj.val())
    });
};
