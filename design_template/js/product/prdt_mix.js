$(document).ready(function() {
    // 건수 초기화
    var option = "";
    for (var i = 1; i <= 99; i++) {
        option += "<option value=\"" + i + "\">" + i + "</option>";
    }
    $("#cut_count").html(option);

    // 빠른견적서 초기화
    $(".esti_paper_info").show();
    $(".esti_output_info").show();
    $(".esti_print_info").show();
    $(".esti_count_info").hide();

    var paper  = parseInt($("#inner_paper_price").val());
    var print  = parseInt($("#inner_print_price").val()) +
                 parseInt($("#cut_prdt_price").val());
    var output = parseInt($("#inner_output_price").val());
    var after  = parseInt($("#inner_binding_val").attr("price"));
    var sell   = paper + print + output + after;
    var sale   = parseInt($("#grade_sale").attr("price"));

    $("#esti_paper").html(paper.format());
    $("#esti_print").html(print.format());
    $("#esti_output").html(output.format());
    $("#esti_after").html(after.format());
    $("#esti_sum").html(sell.format());
    $("#esti_sell_price").html(sell.format());
    $("#esti_sale_price").html((sell - sale).format());
});

/**
 * @brief 표지, 내지1/2/3 실제 종이인쇄수량 계산
 *
 * @param dvs = 바꾼 페이지 위치값
 */
var getRealPaperAmt = {
    "amt"  : {
        "inner" : 0,
    },
    "exec" : function(dvs) {
        var prefix = getPrefix(dvs);
        var $amtObj = $(prefix + "amt");
        var amt     = parseFloat($amtObj.val());
        var amtUnit = $amtObj.attr("amt_unit");
        var posNum  = $(prefix + "size").attr("pos_num");

        var info = {
           "amt"      : amt,
           "posNum"   : posNum,
           "amtUnit"  : amtUnit
        };

        var pageNum = $(prefix + "page").val();
        info.pageNum = pageNum;

        this.amt[dvs] = calcRealPaperAmt(info);
    }
};

/**
 * @brief 수량과 페이지수에 따른 실제 종이수량 계산
 *
 * @param info = 계산용 정보데이터
 */
var calcRealPaperAmt = function(info) {
    var amt      = info["amt"];
    var posNum   = info["posNum"];
    var pageNum  = info["pageNum"].split('!')[0];
    var amtUnit  = info["amtUnit"];

    // 0page일 경우 인쇄 수량 0 반환
    if (pageNum == 0) {
        return 0;
    }

    var ret = Math.round((amt / posNum) / (2 / pageNum));

    return ret;
};

/**
 * @brief 비규격 사이즈 선택할 경우 재단사이즈 값 초기화
 *
 * @param val = 구분값
 */
var changeSizeDvs = function(dvs, val) {
    var prefix = getPrefix(dvs);
    // 비규격 사이즈 선택시 기본 사이즈로 데이터 변경
    if (val === "manu") {
        $(prefix + "cut_wid_size").val($(prefix + "size").attr("def_cut_wid"));
        $(prefix + "cut_vert_size").val($(prefix + "size").attr("def_cut_vert"));
    }

    changeData(dvs);
};

/**
 * @brief 수량변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeAmt = function(dvs) {
    reCalcAfterPrice(dvs, null);
    changeData(dvs);
}

/**
 * @brief 페이지 변경시 종이수량 재계산을 위한 중간함수
 *
 * @param dvs = 바꾼 페이지 위치값
 */
var changePage = function(dvs) {
    getRealPaperAmt.exec(dvs);
    reCalcAfterPrice(dvs, null);
    getBindingCalcBookletPrice("binding", dvs);
    changeData(dvs);
};

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 *
 * @param dvs = 제품구분값
 */
var changeData = function(dvs) {
    var prefix = getPrefix(dvs);
    var monoYn = $(prefix + "mono_yn").val();
    var data = {
        "dvs"            : dvs,
        "typ"            : $(prefix + "typ").val(),
        "mono_yn"        : monoYn,
        "tmpt_dvs"       : $(prefix + "tmpt_dvs").val(),
        "affil"          : $(prefix + "size").find("option:selected").attr("affil"),
        "cate_sortcode"  : $(prefix + "cate_sortcode").val(),
        "stan_mpcode"    : $(prefix + "size").val(),
        "amt"            : $(prefix + "amt").val(),
        "paper_mpcode"   : $(prefix + "paper").val(),
        "page_info"      : $(prefix + "page").val(),
        "bef_print_name" : $(prefix + "bef_tmpt_name").val(),
        "print_purp"     : $(prefix + "print_purp").val(),
    };

    var sizeDvs = $(prefix + "size_dvs").val();

    if (monoYn === '1' &&  sizeDvs === "manu") {
        data.stan_mpcode   =
            $(prefix + "size").attr("def_val");
        data.def_stan_name =
            $(prefix + "size > option[value='" + data.stan_mpcode + "']").html();
        data.manu_pos_num  =
            $(prefix + "manu_pos_num").val();
        data.amt_unit      =
            $(prefix + "amt").attr("amt_unit");
    }

    loadPrdtPrice.data   = data;
    loadPrdtPrice.dvs    = dvs;
    loadPrdtPrice.monoYn = monoYn;
    loadPrdtPrice.exec();
};

/**
 * @brief 상품 가격정보 json으로 반환
 */
var loadPrdtPrice = {
    "data"   : null,
    "dvs"    : null,
    "monoYn" : null,
    "price"  : {
        "cut"   : {},
        "inner" : {}
    },
    "exec"  : function() {
        var url = null;
        if (this.data.mono_yn === '0') {
            url = "/ajax/product/load_ply_price.php";
        } else {
            url = "/ajax/product/load_calc_price.php";

            if ($("#size_dvs").val() === "manu") {
                url = "/ajax/product/load_calc_price_manu.php";
            }
        }

        var callback = function(result) {
        var dvs    = loadPrdtPrice.dvs;
        var monoYn = loadPrdtPrice.monoYn;
        var prefix = getPrefix(dvs);

        var price  = result[dvs].price;

        var gradeSaleRate = $(prefix + "grade_sale_rate").val();
        gradeSaleRate = parseFloat(gradeSaleRate) / 100;

        if (monoYn === '0') {
            loadPrdtPrice.price[dvs].price = price;

    		salePrice = ceilVal(price * gradeSaleRate);
    		salePrice = price - salePrice;

            } else {
                var paperPrice  = result[dvs].paper;
                var printPrice  = result[dvs].print;
                var outputPrice = result[dvs].output;

                loadPrdtPrice.price[dvs].paper  = paperPrice;
                loadPrdtPrice.price[dvs].print  = printPrice;
                loadPrdtPrice.price[dvs].output = outputPrice;
                loadPrdtPrice.price[dvs].price  = price;

		salePrice = ceilVal(price * gradeSaleRate);
		salePrice = price - salePrice;

                $(prefix + "paper_price").val(paperPrice);
                $(prefix + "print_price").val(printPrice);
                $(prefix + "output_price").val(outputPrice);
            }

            $(prefix + "prdt_price").val(price);
            $(prefix + "basic_price").val(salePrice);

            calcPrice(dvs);
        };

        ajaxCall(url, "json", loadPrdtPrice.data, callback);
    }
};

/**
 * @brief 화면에 출력되는 가격 및 빠른견적서 내용 수정
 */
var calcPrice = function(dvs) {
    // 부가세율
    //var taxRate = 0.1;

    // 자리수
    var cutPosNum = 1;
    if ($("#cut_size_dvs").val() === "manu") {
        cutPosNum = parseFloat($("#cut_manu_pos_num").val());
    }
    // 건수
    var cutCount = parseInt($("#cut_count").val());

    // 가격 정보
    var cutPrice = ceilVal(parseInt($("#cut_prdt_price").val()));
    cutPrice *= cutCount * cutPosNum;

    var innerPaperPrice  = ceilVal(parseInt($("#inner_paper_price").val()));
    var innerPrintPrice  = ceilVal(parseInt($("#inner_print_price").val()));
    var innerOutputPrice = ceilVal(parseInt($("#inner_output_price").val()));
    var innerSumPrice    = ceilVal(parseInt($("#inner_prdt_price").val()));

    // 상품 합산 가격
    var sellPrice = innerSumPrice + cutPrice;

    // 회원등급 할인
    var cutGradeSale   = parseFloat($("#cut_grade_sale_rate").val());
    var innerGradeSale = parseFloat($("#inner_grade_sale_rate").val());

    cutGradeSale   /= 100;
    innerGradeSale /= 100;

    var sumGradeSale = (cutPrice * cutGradeSale) +
                       (innerSumPrice * innerGradeSale);
    sumGradeSale = ceilVal(sumGradeSale);

    // 제본 가격
    var bindingPrice = $("#inner_binding_val").attr("price");

    if (checkBlank(bindingPrice) === true) {
        var data = {
            "cate_sortcode" : $("#inner_cate_sortcode").val(),
            "after_name"    : "제본",
        };

        getAfterPrice.load("binding", data, dvs);
        return false;
    }

    bindingPrice = parseInt(bindingPrice);

    // 옵션비 총합
    var optDefaultPrice = parseInt($("#opt_default_price").attr("price"));
    var sumOptPrice = getSumOptPrice();
    sumOptPrice += optDefaultPrice;
    sumOptPrice  = ceilVal(sumOptPrice);

    // 후공정비 총합
    var cutSumAfterPrice = getSumAfterPrice("cut");
    var innerSumAfterPrice = getSumAfterPrice("inner");
    var sumAfterPrice = cutSumAfterPrice + innerSumAfterPrice + bindingPrice;
    sumAfterPrice  = ceilVal(sumAfterPrice);

    // 견적서 종이비 계산
    var paper = innerPaperPrice;

    // 견적서 인쇄비 계산
    var print = innerPrintPrice + cutPrice;

    // 견적서 출력비 계산
    var output = innerOutputPrice;

    // 정상 판매가 계산
    sellPrice += sumAfterPrice + sumOptPrice;


    // 부가세 포함가격 계산
    //var tax = sellPrice * taxRate;
    var calcSellPrice = sellPrice;

    // 회원등급 할인가 계산
    var salePrice = calcSellPrice - sumGradeSale;

    // 정상 판매가 변경
    $("#sell_price").attr("val", sellPrice);
    $("#sell_price").html(calcSellPrice.format() + "원");
    // 회원등급 할인가 변경
    $("#grade_sale").html(sumGradeSale.format() + "원");
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
    //$("#esti_tax").html(tax.format());
    // 견적서 판매가 변경
    $("#esti_sell_price").html(calcSellPrice.format());
    // 견적서 기본할인가 변경
    $("#esti_sale_price").html(salePrice.format());
};

/**
 * @brief 장바구니로 이동
 */
var goCart = function() {
    if ($("#il").val() === "0") {
        $("#cart_flag").val('Y');
        alert("로그인 후 확인 가능합니다.");
        return false;
    }

    if (checkBlank($("#title").val().trim()) === true) {
        $("#cart_flag").val('Y');
        alert("인쇄물제목을  입력해주세요.");
        $("#title").focus();
        return false;
    }

    setAddOptInfo();

    var optPrice = getSumOptPrice();

    $frm = $("#frm");

    // 일반명함
    var prefix = getPrefix("cut");
    var cateName  = $(prefix + "cate_sortcode").find("option:selected").text();
    var amtUnit   = $(prefix + "amt").attr("amt_unit");
    var paperName = $(prefix + "paper").find("option:selected").text();
    var tmptName  = $(prefix + "bef_tmpt_name").find("option:selected").text();
    var sizeName  = $(prefix + "size").find("option:selected").text();

    var afterPrice    = getSumAfterPrice("cut");

    var ret = makeAfterInfo.all("cut");

    if (ret === false) {
        return false;
    }

    $frm.find("input[name='cut_cate_name']").val(cateName);
    $frm.find("input[name='cut_amt_unit']").val(amtUnit);
    $frm.find("input[name='cut_paper_name']").val(paperName);
    $frm.find("input[name='cut_size_name']").val(sizeName);

    $frm.find("input[name='cut_after_price']").val(afterPrice);

    // 내지
    prefix    = "#inner_";
    cateName  = $(prefix + "cate_sortcode").find("option:selected").text();
    amtUnit   = $(prefix + "amt").attr("amt_unit");
    paperName = $(prefix + "paper").find("option:selected").text();
    sizeName  = $(prefix + "size").find("option:selected").text();

    afterPrice    = getSumAfterPrice("inner");

    ret = makeAfterInfo.all("inner");

    if (ret === false) {
        return false;
    }

    $frm.find("input[name='inner_cate_name']").val(cateName);
    $frm.find("input[name='inner_amt_unit']").val(amtUnit);
    $frm.find("input[name='inner_paper_name']").val(paperName);
    $frm.find("input[name='inner_paper_amt']").val(getRealPaperAmt.amt["inner"]);
    $frm.find("input[name='inner_size_name']").val(sizeName);

    $frm.find("input[name='inner_after_price']").val(afterPrice);

    // 공통
    $frm.find("input[name='cate_sortcode']").val($("#cate_bot").val());
    $frm.find("input[name='opt_price']").val(optPrice);
    $frm.find("input[name='order_detail']").val(makeOrderDetail());

    $frm.submit();
};

/**
 * @brief 혼합형 주문내역 생성
 *
 * @return 주문내역
 */
var makeOrderDetail = function() {
    var ret = '';

    $frm = $("#frm");

    // 공통카테고리
    ret += $("#cate_bot").find("option:selected").text();
    ret += " / ";
    ret += $frm.find("input[name='cut_cate_name']").val();
    ret += ", ";
    ret += $("#cut_bef_tmpt_name").find("option:selected").text();
    ret += ", ";
    ret += $("#cut_amt").find("option:selected").text();
    ret += " / ";
    ret += $frm.find("input[name='inner_cate_name']").val();
    ret += ", ";
    ret += $("#inner_bef_tmpt_name").find("option:selected").text();
    ret += ", ";
    ret += $("#inner_amt").find("option:selected").text();
    ret += " ";
    ret += $("#inner_amt").attr("amt_unit");
    
    return ret;
};

/******************************************************************************
 * 후공정 계산 관련 함수
 ******************************************************************************/

/**
 * @brief 가격 배열에서 후공정 가격 계산해서 반환
 *
 * @param priceArr = 가격 배열
 * @param dvs      = 제품 구분값
 *
 * @return 계산된 가격
 */
var getAfterPriceCalc = function(priceArr, dvs) {
    var prefix = getPrefix(dvs);
    var flattypYn = $(prefix + "flattyp_yn").val();

    var amt      = null;
    var crtrUnit = priceArr.crtr_unit;
    var amtUnit  = $(prefix + "amt").attr("amt_unit");

    if (flattypYn === "true") {
        amt = parseInt($(prefix + "amt").val());
    } else {
        amt = getRealPaperAmt.amt[dvs];
    }

    amt = amtCalc(amt, amtUnit, crtrUnit);

    return calcAfterPrice(priceArr, amt);
};

/******************************************************************************
 * 내지 후공정 관련 함수
 ******************************************************************************/

/**
 * @brief 제본의 가격을 검색한다
 * 가격 배열이 null일 경우 새로 검색한다
 *
 * @param aft = 후공정 구분값
 * @param dvs = 제품 구분값
 */
var getBindingCalcBookletPrice = function(aft, dvs) {
    var prefix = getPrefix(dvs);
    var mpcode = $(prefix + aft + "_val").val();
    var priceArr  = getAfterPrice.price[aft];

    // 가격정보가 없을경우
    if (checkBlank(priceArr)) {
        var data = {
            "cate_sortcode" : $(prefix + "cate_sortcode").val(),
            "after_name"    : "제본",
        };

        getAfterPrice.load(aft, data, dvs);

        return false;
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var sumPrice = getAfterPriceCalc(priceArr, dvs);

    $(prefix + aft +"_val").attr("price", sumPrice);

    calcPrice(dvs);
};

/******************************************************************************
 * 명함 후공정 관련 함수
 ******************************************************************************/

/**
 * @brief 명함 후공정 가격 공통계산 함수
 *
 * @param aft      = 후공정 구분값
 * @param name     = 후공정 이름
 * @param selector = mpcode 객체 selector
 * @param dvs      = 제품 구분값
 */
var getNcAfterPriceCommon = function(aft, name, selector, dvs) {
    var priceArr  = getAfterPrice.price[aft];
    var prefix = getPrefix(dvs);

    if (checkBlank(priceArr) === true) {
        var data = {
            "cate_sortcode" : $(prefix + "cate_sortcode").val(),
            "after_name"    : name
        };

        getAfterPrice.load(aft, data, dvs);
        return false;
    }

    var mpcode = null;
    if (selector === null) {
        mpcode = $(prefix + aft + "_val").val();
    } else {
        mpcode = $(selector).val();
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var afterPrice = getAfterPriceCalc(priceArr, dvs);

    setAfterPrice(dvs, aft, afterPrice);

    calcPrice(dvs);
};

/**
 * @brief 명함 미싱 가격 공통계산 함수
 *
 * @param aft = 후공정 구분값
 */
var getNcDotlinePrice = function(aft, name, dvs) {
    var selector = "input[name='" +
                   aft + '_' +
                   $("#" + aft).val() +
                   "_val']:checked";

    var mpcode = $(selector).val();

    if (checkBlank(mpcode) === true) {
        return false;
    }

    getNcAfterPriceCommon(aft, name, selector, dvs);
};

/**
 * @brief 명함 오시 가격 계산 함수, 미싱 가격하고 동일
 */
var getNcImpressionPrice = getNcDotlinePrice;

/**
 * @brief 코팅 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getCoatingPlySheetPrice = function(aft, dvs) {
    getNcAfterPriceCommon(aft, "코팅", null, dvs);
};

/**
 * @brief 귀도리 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getRoundingPlySheetPrice = function(aft, dvs) {
    getNcAfterPriceCommon(aft, "귀도리", null, dvs);
};

/**
 * @brief 엠보싱 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getEmbossingPlySheetPrice = function(aft, dvs) {
    getNcAfterPriceCommon(aft, "엠보싱", null, dvs);
};

/**
 * @brief 엠보싱 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getEmbossingSheetPrice = function(aft, dvs) {
    getNcAfterPriceCommon(aft, "엠보싱", null, dvs);
};

/**
 * @brief 미싱 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getDotlinePlySheetPrice = function(aft, dvs) {
    getNcDotlinePrice(aft, "미싱", dvs);
};

/**
 * @brief 타공 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getPunchingPlySheetPrice = function(aft, dvs) {
    getNcAfterPriceCommon(aft, "타공", null, dvs);
};

/**
 * @brief 오시 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getImpressionPlySheetPrice = function(aft, dvs) {
    getNcImpressionPrice(aft, "오시");
};
