var tmptDvs   = null;
var monoYn    = null;
var flattypYn = true;
var prdtDvs   = null;
var sortcode  = null;
var cateName  = null;

$(document).ready(function() {
    // 건수 초기화
    var option = "";
    for (var i = 1; i <= 99; i++) {
        option += "<option value=\"" + i + "\">" + i + "</option>";
    }
    $("#count").html(option);

    monoYn   = $("#etc_mono_yn").val();
    tmptDvs  = $("#etc_tmpt_dvs").val();
    prdtDvs  = $("#prdt_dvs").val();
    sortcode = $("#etc_cate_sortcode").val();
    cateName = $("#etc_cate_sortcode").find("option:selected").text();

    if ($("#amt").attr("amt_unit") === 'R') {
        $("#sheet_count_div").show();
        calcSheetCount(prdtDvs);
    }
});

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 */
var changeData = function() {
    monoYn = $("#etc_mono_yn").val();

    var data ={
        "dvs"           : prdtDvs,
        "mono_yn"       : monoYn,
        "cate_sortcode" : sortcode,
        "amt"           : $("#amt").val(),
        "stan_mpcode"   : $("#etc_size").val(),
        "tmpt_dvs"      : tmptDvs
    };

    data.flag = $("#frm").find("input[name='flag']").val();
    data.paper_mpcode       = $("#paper").val();
    data.bef_print_name     = $("#print_tmpt").val();
    data.bef_add_print_name = '0';
    data.aft_print_name     = '0';
    data.aft_add_print_name = '0';
    data.print_purp         = $("#etc_print_purp").val();
    data.page_info          = "2";

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
        if (monoYn === '0') {
            url = "/ajax/product/load_ply_price.php";
        } else {
            url = "/ajax/product/load_calc_price.php";
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
    // 건수
    var count = parseInt($("#count").val());
    // 정상판매가
    var sellPrice = loadPrdtPrice.price.price;
    if (checkBlank(sellPrice)) {
        changeData();
        return false;
    }
    sellPrice = ceilVal(sellPrice);
    // 등급 할인율
    var gradeSale = parseFloat($("#grade_sale").attr("rate"));
    gradeSale /= 100.0;
    // 옵션비 총합
    var sumOptPrice = getSumOptPrice();
    sumOptPrice = ceilVal(sumOptPrice);
    // 후공정비 총합
    var sumAfterPrice = getSumAfterPrice(prdtDvs);
    sumAfterPrice = ceilVal(sumAfterPrice);

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
var loadPrintTmpt = function(dvs) {
    var callback = function(result) {
        $("#print_tmpt").html(result.bef_tmpt);

        if (monoYn === '1') {
            changeData();
        }
    };

    loadPrintTmptCommon.exec(dvs, callback);
};

/**
 * @brief 수량변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeAmt = function() {
    calcSheetCount(prdtDvs);
    reCalcAfterPrice(prdtDvs, null);
    changeData();
};

/**
 * @brief 사이즈 변경시 종이수량 재계산
 */
var changeSize = function() {
    calcSheetCount(prdtDvs);
    changeData();
};

/******************************************************************************
 * 후공정 관련 함수
 ******************************************************************************/

/**
 * @brief  후공정 가격 공통계산 함수
 *
 * @param aft      = 후공정 구분값
 * @param name     = 후공정 이름
 * @param selector = mpcode 객체 selector
 */
var getEvAfterPriceCommon = function(aft, name, selector) {
    var priceArr  = getAfterPrice.price[aft];

    if (checkBlank(priceArr) === true) {
        var data = {
            "cate_sortcode" : sortcode,
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
 * @brief  미싱 가격 공통계산 함수
 *
 * @param aft = 후공정 구분값
 */
var getEvDotlinePrice = function(aft, name) {
    var selector = "input[name='etc_" +
                   aft + '_' +
                   $("#etc_" + aft + "_cnt").val() +
                   "_val']:checked";

    var mpcode = $(selector).val();

    if (checkBlank(mpcode) === true) {
        return false;
    }

    getEvAfterPriceCommon(aft, name, selector);
};

/**
 * @brief  오시 가격 계산 함수, 미싱 가격하고 동일
 */
var getEvImpressionPrice = getEvDotlinePrice;

/**
 * @brief 코팅 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getCoatingPlySheetPrice = function(aft) {
    getEvAfterPriceCommon(aft, "코팅", null);
};

/**
 * @brief 코팅 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getCoatingCalcSheetPrice = function(aft) {
    getEvAfterPriceCommon(aft, "코팅", null);
};

/**
 * @brief 귀도리 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getRoundingPlySheetPrice = function(aft) {
    getEvAfterPriceCommon(aft, "귀도리", null);
};

/**
 * @brief 귀도리 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getRoundingCalcSheetPrice = function(aft) {
    getEvAfterPriceCommon(aft, "귀도리", null);
};

/**
 * @brief 엠보싱 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getEmbossingPlySheetPrice = function(aft) {
    getEvAfterPriceCommon(aft, "엠보싱", null);
};

/**
 * @brief 엠보싱 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getEmbossingSheetPrice = function(aft) {
    getEvAfterPriceCommon(aft, "엠보싱", null);
};

/**
 * @brief 미싱 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getDotlinePlySheetPrice = function(aft) {
    getEvDotlinePrice(aft, "미싱");
};

/**
 * @brief 미싱 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getDotlineCalcSheetPrice = function(aft) {
    getEvDotlinePrice(aft, "미싱");
};

/**
 * @brief 타공 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getPunchingPlySheetPrice = function(aft) {
    getEvAfterPriceCommon(aft, "타공", null);
};

/**
 * @brief 타공 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getPunchingCalcSheetPrice = function(aft) {
    getEvAfterPriceCommon(aft, "타공", null);
};

/**
 * @brief 오시 확정형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getImpressionPlySheetPrice = function(aft) {
    getEvImpressionPrice(aft, "오시");
};

/**
 * @brief 오시 계산형 가격 검색
 *
 * @param aft = 후공정 구분값
 */
var getImpressionCalcSheetPrice = function(aft) {
    getEvImpressionPrice(aft, "오시");
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
 * @brief 관심상품 등록이나 장바구니 전에 데이터 세팅
 */
var setSubmitParam = function() {
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

    var amtUnit   = $("#amt").attr("amt_unit");
    var paperName = $("#paper").find("option:selected").text();
    var tmptName  = $("#print_tmpt").find("option:selected").text();
    var sizeName  = $("#etc_size").find("option:selected").text();

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

    $frm.find("input[name='etc_cate_name']").val(cateName);
    $frm.find("input[name='etc_amt_unit']").val(amtUnit);
    $frm.find("input[name='etc_paper_name']").val(paperName);
    $frm.find("input[name='etc_bef_tmpt_name']").val(tmptName);
    $frm.find("input[name='etc_size_name']").val(sizeName);

    $frm.find("input[name='etc_prdt_price']").val(prdtPrice);
    $frm.find("input[name='etc_basic_price']").val(basicPrice);
    $frm.find("input[name='etc_grade_sale_rate']").val(gradeSaleRate);
    $frm.find("input[name='etc_after_price']").val(afterPrice);
    $frm.find("input[name='etc_opt_price']").val(optPrice);
};

/**
 * @brief 견적서 팝업 본문정보 생성
 */
var makeEstiPopInfo = {
    "data" : null,
    "exec" : function(dvs) {
        var prefix = getPrefix(prdtDvs);
    
        var sumPrice    = $("#esti_sale_price").text();
        var paper   = $("#paper > option:selected").text();
        var size    = $(prefix + "size > option:selected").text();
        var tmpt    = $("#print_tmpt").val();
        var amt     = $("#amt").val();
        var amtUnit = $("#amt").attr("amt_unit");
        var count   = $("#count").val();
    
        var after = '';
        $('.after .overview ul li').each(function() {
            after += $(this).text();
            after += ', ';
        });
        after = after.substr(0, after.length - 2);
    
        var printPrice = $("#esti_print").text();
        var afterPrice = $("#esti_after").text();
    
        var data = {
            "cate_name" : [
                cateName
            ],
            "paper" : [
                paper
            ],
            "size" : [
                size
            ],
            "tmpt" : [
                tmpt
            ],
            "amt" : [
                amt
            ],
            "amt_unit" : [
                amtUnit
            ],
            "count" : [
                count
            ],
            "after" : [
                after
            ],
            "sum_price"    : sumPrice,
            "paper_price"  : '-',
            "print_price"  : printPrice,
            "output_price" : '-',
            "after_price"  : afterPrice
        };

        this.data = data;
    
        if (dvs === "pop") {
            getEstiPopHtml(data);
        } else {
            downEstiExcel(data);
        }
    }
};