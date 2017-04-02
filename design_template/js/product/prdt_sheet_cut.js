var tmptDvs   = null;
var monoYn    = '0';
var flattypYn = true;
var prdtDvs   = null;

$(document).ready(function() {
    // 건수 초기화
    var option = "";
    for (var i = 1; i <= 99; i++) {
        option += "<option value=\"" + i + "\">" + i + "</option>";
    }
    $("#count").html(option);

    tmptDvs = $("#cut_tmpt_dvs").val();
    prdtDvs = $("#prdt_dvs").val();
});

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 */
var changeData = function() {
    monoYn = $("#cut_mono_yn").val();

    var data ={
        "dvs"           : prdtDvs,
        "mono_yn"       : monoYn,
        "cate_sortcode" : $("#cate_bot").val(),
        "amt"           : $("#amt").val(),
        "stan_mpcode"   : $("#size").val(),
        "tmpt_dvs"      : tmptDvs
    };

    data.flag = $("#frm").find("input[name='flag']").val();
    data.paper_mpcode       = $("#paper").val();
    data.bef_print_name     = $("#print_tmpt").val();
    data.bef_add_print_name = '0';
    data.aft_print_name     = '0';
    data.aft_add_print_name = '0';
    data.print_purp         = $("#print_purp").val();
    data.page_info          = "2";

    if ($("#size_dvs").val() === "manu") {
        data.stan_mpcode   =
            $("#size").attr("def_val");
        data.def_stan_name =
            $("#size > option[value='" + data.stan_mpcode + "']").html();
        data.manu_pos_num  =
            $("#manu_pos_num").val();
        data.amt_unit      =
            $("#amt").attr("amt_unit");
    }

    loadPrdtPrice.data = data;
    loadPrdtPrice.exec();
};

/**
 * @brief 상품 가격정보 json으로 반환
 */
var loadPrdtPrice = {
    "data"  : {},
    "price" : {},
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
            if (checkBlank(result[prdtDvs].price) === true) {
                return alertReturnFalse("해당하는 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
            }

            loadPrdtPrice.price = result[prdtDvs];

            calcPrice();
        };

        ajaxCall(url, "json", loadPrdtPrice.data, callback);
    }
};

/**
 * @brief 화면에 출력되는 가격 및 빠른견적서 내용 수정
 */
var calcPrice = function() {
    // 자리수
    var posNum = 1;
    if ($("#size_dvs").val() === "manu") {
        posNum = parseFloat($("#manu_pos_num").val());
    }
    // 건수
    var count = parseInt($("#count").val());
    // 정상판매가
    var sellPrice = loadPrdtPrice.price.price;
    if (checkBlank(sellPrice)) {
        changeData();
        return false;
    }
    sellPrice  = ceilVal(sellPrice);
    sellPrice *= posNum;
    // 등급 할인율
    var gradeSale = parseFloat($("#grade_sale").attr("rate"));
    gradeSale /= 100.0;
    // 옵션비 총합
    var sumOptPrice = getSumOptPrice();
    sumOptPrice = ceilVal(sumOptPrice);
    // 후공정비 총합
    var sumAfterPrice = getSumAfterPrice(prdtDvs);
    sumAfterPrice = ceilVal(sumAfterPrice);

    // 부가세율
    //var taxRate = 0.1;

    // 견적서 종이비 계산
    var paper  = 0;
    if (monoYn === '1') {
        paper  = parseInt(loadPrdtPrice.price.paper);
        paper  = ceilVal(paper);
        paper *= count;
        $(".esti_paper_info").css("display", "");
    } else {
        $(".esti_paper_info").css("display", "none");
    }

    // 견적서 출력비 계산
    var output = 0;
    if (monoYn === '1') {
        output  = parseInt(loadPrdtPrice.price.output);
        output  = ceilVal(output);
        output *= count;
        $(".esti_output_info").css("display", "");
    } else {
        $(".esti_output_info").css("display", "none");
    }

    // 견적서 인쇄비 계산
    var print  = sellPrice;
    if (monoYn === '1') {
        print  = parseInt(loadPrdtPrice.price.print);
        print  = ceilVal(print);
    }
    print *= count;

    // 견적서 후공정비 계산
    var after = sumAfterPrice * count;
    // 견적서 옵션비 계산
    var opt = sumOptPrice * count;
    // 견적서 합계 계산
    var sum = paper + output + print + after + opt;
    // 견적서 부가세 계산
    //var tax = sum * taxRate;

    // 정상판매가 계산
    //var calcSellPrice = sum + tax;
    var calcSellPrice = sum;
    // 회원등급 할인가 계산
    var calcGradeSale = calcSellPrice * gradeSale;
    calcGradeSale = ceilVal(calcGradeSale);
    // 기본할인가 계산
    var calcSalePrice = calcSellPrice - calcGradeSale;

    // 정상판매가 변경
    $("#sell_price").attr("val", calcSellPrice);
    $("#sell_price").html(calcSellPrice.format() + '원');
    // 회원등급 할인가 변경
    $("#grade_sale").html(calcGradeSale.format() + '원');
    // 기본할인가 변경
    $("#sale_price").html(calcSalePrice.format() + '원');
    $("#sale_price").attr("val", calcSalePrice.format());

    // 견적서 종이비 변경
    $("#esti_paper").html(paper.format());
    // 견적서 출력비 변경
    $("#esti_output").html(output.format());
    // 견적서 인쇄비 변경
    $("#esti_print").html(print.format());
    // 견적서 후공정비 변경
    $("#esti_after").html(after.format());
    // 견적서 옵션비 변경
    $("#esti_opt").html(opt.format());
    // 견적서 건수 변경
    $("#esti_count").html(count.format());
    // 견적서 합계 변경
    $("#esti_sum").html(sum.format());
    // 견적서 부가세 변경
    //$("#esti_tax").html(tax.format());
    // 견적서 판매가 변경
    $("#esti_sell_price").html(calcSellPrice.format());
    // 견적서 기본할인가 변경
    $("#esti_sale_price").html(calcSalePrice.format());
};

/**
 * @param 인쇄방식에 해당하는 인쇄도수 검색
 *
 * @param val = 인쇄방식
 */
var loadPrintTmpt = function(dvs, val) {
    var callback = function(result) {
        $("#print_tmpt").html(result.bef_tmpt);

        if (monoYn === '1') {
            changeData();
        }
    };

    loadPrintTmptCommon(null, val, callback);
};

/**
 * @brief 인쇄유형 변경시 전역변수 변경처리
 *
 * @param val = 인쇄유형 값
 */
/*
var reCalcRealPaperAmt = function() {
    getRealPaperAmt.exec();

    changeData();
}
*/

/**
 * @brief 수량에 따른 종이 실제수량 계산
 */
/*
var getRealPaperAmt = {
    "amt"  : 0,
    "exec" : function() {
        var url  = "/ajax/product/load_sheet_real_paper_amt.php";
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "size_dvs"      : $("#size_dvs").val(),
            "size_name"     : $("#size > option:selected").html(),
            "manu_pos_num"  : $("#manu_pos_num").val(),
            "amt"           : $("#amt").val(),
            "amt_unit"      : $("#amt").attr("amt_unit"),
        };
        var callback = function(result) {
            getRealPaperAmt.amt = parseFloat(result);
        };

        ajaxCall(url, "text", data, callback);
    }
}
*/

/**
 * @brief 비규격 사이즈 선택할 경우 재단사이즈 값 초기화
 *
 * @param val = 구분값
 */
var changeSizeDvs = function(val) {
    // 비규격 사이즈 선택시 기본 사이즈로 데이터 변경
    if (val === "manu") {
        $("#cut_wid_size").val($("#size").attr("def_cut_wid"));
        $("#cut_vert_size").val($("#size").attr("def_cut_vert"));
    }

    changeData();
};

/**
 * @brief 수량변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeAmt = function() {
    reCalcAfterPrice(prdtDvs, null);
    changeData();
}

/******************************************************************************
 * 후공정 관련 함수
 ******************************************************************************/

/**
 * @brief 명함 후공정 가격 공통계산 함수
 *
 * @param aft      = 후공정 구분값
 * @param name     = 후공정 이름
 * @param selector = mpcode 객체 selector
 */
var getNcAfterPriceCommon = function(aft, name, selector) {
    var priceArr  = getAfterPrice.price[aft];

    if (checkBlank(priceArr) === true) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : name
        };

        getAfterPrice.load(aft, data, prdtDvs);
        return false;
    }

    var mpcode = null;
    if (selector === null) {
        mpcode = $("#" + prdtDvs + '_' + aft + "_val").val();
    } else {
        mpcode = $(selector).val();
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var afterPrice = getAfterCalcPrice(priceArr);

    setAfterPrice(prdtDvs, aft, afterPrice);

    calcPrice();
};

/**
 * @brief 명함 미싱 가격 공통계산 함수
 *
 * @param aft = 후공정 구분값
 */
var getNcDotlinePrice = function(aft, name) {
    var selector = "input[name='" +
                   aft + '_' +
                   $("#" + aft).val() +
                   "_val']:checked";

    var mpcode = $(selector).val();

    if (checkBlank(mpcode) === true) {
        return false;
    }

    getNcAfterPriceCommon(aft, name, selector);
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
var getCoatingPlySheetPrice = function(aft) {
    getNcAfterPriceCommon(aft, "코팅", null);
};

/**
 * @brief 코팅 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getCoatingCalcSheetPrice = function(aft) {
    getNcAfterPriceCommon(aft, "코팅", null);
};

/**
 * @brief 귀도리 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getRoundingPlySheetPrice = function(aft) {
    getNcAfterPriceCommon(aft, "귀도리", null);
};

/**
 * @brief 귀도리 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getRoundingCalcSheetPrice = function(aft) {
    getNcAfterPriceCommon(aft, "귀도리", null);
};

/**
 * @brief 엠보싱 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getEmbossingPlySheetPrice = function(aft) {
    getNcAfterPriceCommon(aft, "엠보싱", null);
};

/**
 * @brief 엠보싱 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getEmbossingSheetPrice = function(aft) {
    getNcAfterPriceCommon(aft, "엠보싱", null);
};

/**
 * @brief 미싱 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getDotlinePlySheetPrice = function(aft) {
    getNcDotlinePrice(aft, "미싱");
};

/**
 * @brief 미싱 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getDotlineCalcSheetPrice = function(aft) {
    getNcDotlinePrice(aft, "미싱");
};

/**
 * @brief 타공 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getPunchingPlySheetPrice = function(aft) {
    getNcAfterPriceCommon(aft, "타공", null);
};

/**
 * @brief 타공 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getPunchingCalcSheetPrice = function(aft) {
    getNcAfterPriceCommon(aft, "타공", null);
};

/**
 * @brief 오시 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getImpressionPlySheetPrice = function(aft) {
    getNcImpressionPrice(aft, "오시");
};

/**
 * @brief 오시 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getImpressionCalcSheetPrice = function(aft) {
    getNcImpressionPrice(aft, "오시");
};

////////////////////////////////////////////////////////////////////////////////

/**
 * @brief 가격 배열에서 후공정 가격 계산해서 반환
 *
 * @param priceArr = 가격 배열
 *
 * @return 계산된 가격
 */
var getAfterCalcPrice = function(priceArr) {
    var crtrUnit = priceArr.crtr_unit;
    var amt      = parseInt($("#amt").val());
    var amtUnit  = $("#amt").attr("amt_unit");

    // 표지 종이수량
    amt = amtCalc(amt, amtUnit, crtrUnit);

    return calcAfterPrice(priceArr, amt);
};

/**
 * @brief 관심상품 등록
 */
var goWishlist = function() {
    if ($("#il").val() === "0") {
        alert("로그인 후 확인 가능합니다.");
        return false;
    }

    if (checkBlank($("#title").val().trim()) === true) {
        alert("인쇄물제목을  입력해주세요.");
        $("#title").focus();
        return false;
    }

    var cateName  = $("#cate_bot").find("option:selected").text();
    var amtUnit   = $("#amt").attr("amt_unit");
    var paperName = $("#paper").find("option:selected").text();
    var tmptName  = $("#print_tmpt").find("option:selected").text();
    var sizeName  = $("#size").find("option:selected").text();

    var basicPrice    = $("#sell_price").attr("val");
    var afterPrice    = getSumAfterPrice(prdtDvs);
    var optPrice      = getSumOptPrice();
    var gradeSaleRate = $("#grade_sale").attr("rate");

    var ret = makeAfterInfo.all(prdtDvs);

    if (ret === false) {
        return false;
    }

    setAddOptInfo();

    $frm = $("#frm");

    $frm.find("input[name='cut_cate_name']").val(cateName);
    $frm.find("input[name='cut_amt_unit']").val(amtUnit);
    $frm.find("input[name='cut_paper_name']").val(paperName);
    $frm.find("input[name='cut_bef_tmpt_name']").val(tmptName);
    $frm.find("input[name='cut_size_name']").val(sizeName);

    $frm.find("input[name='cut_basic_price']").val(basicPrice);
    $frm.find("input[name='cut_grade_sale_rate']").val(gradeSaleRate);
    $frm.find("input[name='cut_after_price']").val(afterPrice);
    $frm.find("input[name='cut_opt_price']").val(optPrice);

    $("#frm").attr("action", "/mypage/add_wishlist.html");
    $("#frm").submit();
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

    var cateName  = $("#cate_bot").find("option:selected").text();
    var amtUnit   = $("#amt").attr("amt_unit");
    var paperName = $("#paper").find("option:selected").text();
    var tmptName  = $("#print_tmpt").find("option:selected").text();
    var sizeName  = $("#size").find("option:selected").text();

    var prdtPrice     = $("#esti_print").text();
    var basicPrice    = $("#sale_price").attr("val");
    var afterPrice    = getSumAfterPrice(prdtDvs);
    var optPrice      = getSumOptPrice();
    var gradeSaleRate = $("#grade_sale").attr("rate");

    var ret = makeAfterInfo.all(prdtDvs);

    if (ret === false) {
        return false;
    }

    setAddOptInfo();

    $frm = $("#frm");

    $frm.find("input[name='cut_cate_name']").val(cateName);
    $frm.find("input[name='cut_amt_unit']").val(amtUnit);
    $frm.find("input[name='cut_paper_name']").val(paperName);
    $frm.find("input[name='cut_bef_tmpt_name']").val(tmptName);
    $frm.find("input[name='cut_size_name']").val(sizeName);

    $frm.find("input[name='cut_prdt_price']").val(prdtPrice);
    $frm.find("input[name='cut_basic_price']").val(basicPrice);
    $frm.find("input[name='cut_grade_sale_rate']").val(gradeSaleRate);
    $frm.find("input[name='cut_after_price']").val(afterPrice);
    $frm.find("input[name='cut_opt_price']").val(optPrice);

    $frm.submit();
};
