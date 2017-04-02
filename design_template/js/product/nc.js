var monoYn   = null;
var prdtDvs  = null;
var sortcode = null;
var cateName = null;
var amtUnit  = null;

$(document).ready(function() {
    // 건수 초기화
    initCount(99, "count");

    monoYn   = $("#nc_mono_yn").val();
    prdtDvs  = $("#prdt_dvs").val();
    sortcode = $("#nc_cate_sortcode").val();
    cateName = $("#nc_cate_sortcode").find("option:selected").text();
    amtUnit  = $("#nc_amt").attr("amt_unit");

    calcManuPosNum.defWid  = parseFloat($("#nc_size").attr("def_cut_wid"));
    calcManuPosNum.defVert = parseFloat($("#nc_size").attr("def_cut_vert"));

    setSizeWarning();
    chkSizeDotImpWarning();
    showUvDescriptor(prdtDvs);

    if (sortcode.substr(0, 6) === "001003") {
        preview.content.add(preview.content.children('.after'))
                       .css('border-radius', "16px");
    }
});

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 */
var changeData = function() {
    monoYn = $("#nc_mono_yn").val();

    var data ={
        "dvs"           : prdtDvs,
        "mono_yn"       : monoYn,
        "cate_sortcode" : sortcode,
        "amt"           : $("#nc_amt").val(),
        "stan_mpcode"   : $("#nc_size").val()
    };

    data.paper_mpcode       = $("#nc_paper").val();
    data.bef_print_mpcode     = $("#nc_print_tmpt").val();
    data.bef_add_print_mpcode = '';
    data.aft_print_mpcode     = '';
    data.aft_add_print_mpcode = '';
    data.print_purp         = $("#nc_print_purp").val();
    data.page_info          = "2";

    if ($("#size_dvs").val() === "manu") {
        data.stan_mpcode   =
            $("#nc_size").attr("def_val");
        data.def_stan_name =
            $("#nc_size > option[value='" + data.stan_mpcode + "']").html();
        data.manu_pos_num  =
            $("#manu_pos_num").val();
        data.amt_unit      =
            $("#nc_amt").attr("amt_unit");
    }

    loadPaperPreview(prdtDvs);
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
            if (checkBlank(result[prdtDvs].sell_price) === true) {
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
 *
 * @param flag = 옵션가격 재검색 후 무한루프방지
 */
var calcPrice = function(flag) {
    // 자리수
    var posNum = 1;
    if ($("#size_dvs").val() === "manu") {
        posNum = parseFloat($("#nc_manu_pos_num").val());
    }
    // 건수
    var count = parseInt($("#count").val());
    // 정상판매가
    var sellPrice  = loadPrdtPrice.price.sell_price;
    if (checkBlank(sellPrice)) {
        sellPrice = parseInt($("#sell_price").attr("val").replace(',', ''));
        loadPrdtPrice.price.sell_price = sellPrice;
    }
    sellPrice  = ceilVal(sellPrice);
    sellPrice *= posNum;
    sellPrice *= count;
    // 등급 할인율
    var gradeSale = parseFloat($("#nc_grade_sale_rate").val());
    gradeSale /= 100.0;
    // 회원 할인율
    var memberSale = parseFloat($("#nc_member_sale_rate").val());
    memberSale /= 100.0;
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
    }

    // 견적서 출력비 계산
    var output = 0;
    if (monoYn === '1') {
        output  = parseInt(loadPrdtPrice.price.output);
        output  = ceilVal(output);
        output *= count;
    }

    // 견적서 인쇄비 계산
    var print  = sellPrice;
    if (monoYn === '1') {
        print  = parseInt(loadPrdtPrice.price.print);
        print  = ceilVal(print);
        print *= count;
    }

    // 견적서 후공정비 계산
    var after = sumAfterPrice;
    // 견적서 옵션비 계산
    var opt = sumOptPrice;

    // 회원등급 할인가 계산
    var calcGradeSale = sellPrice * gradeSale;
    calcGradeSale = ceilVal(calcGradeSale);
    // 회원 할인가 계산
    var calcMemberSale = (sellPrice + calcGradeSale) * memberSale;
    calcMemberSale = ceilVal(calcMemberSale);
    // 기본할인가 계산
    var calcSalePrice = sellPrice + calcGradeSale + calcMemberSale;
    // 결제금액 계산
    var calcPayPrice = calcSalePrice + after + opt;
    // 부가세
    var tax = Math.round(calcPayPrice / 11);
    // 공급가
    var supplyPrice = calcPayPrice - tax;

    // 정상판매가 변경(후공정, 옵션은 할인하지 않는다)
    $("#sell_price").attr("val", (sellPrice + after + opt));
    $("#sell_price").html((sellPrice + after + opt).format() + ' 원');
    // 회원등급 할인가 변경
    $("#grade_sale").html((calcGradeSale + calcMemberSale).format() + ' 원');
    // 결제금액 변경
    $("#sale_price").attr("val", calcPayPrice);
    $("#sale_price").html(calcPayPrice.format());
    // 공급가 변경
    $("#supply_price").html(supplyPrice.format());
    // 부가세 변경
    $("#tax").html(tax.format());

    var param = {
        "paper"         : paper,
        "print"         : print,
        "output"        : output,
        "after"         : after,
        "opt"           : opt,
        "count"         : count,
        "gradeSaleRate" : gradeSale,
        "sellPrice"     : sellPrice
    };

    changeQuickEsti(param);

    if (flag === false) {
        return false;
    }

    reCalcOptPrice(prdtDvs, null);
};

/**
 * @param 인쇄방식에 해당하는 인쇄도수 검색
 *
 * @param val = 인쇄방식
 */
var loadPrintTmpt = function(dvs) {
    var callback = function(result) {
        var prefix = getPrefix(dvs);
        $(prefix + "print_tmpt").html(result.bef_tmpt);

        if (monoYn === '1') {
            changeData();
        }
    };

    loadPrintTmptCommon.exec(dvs, callback);
};

/**
 * @param 고급명함에서 종이변경시 수량 가져오는 함수
 *
 * @param dvs = 제품구분값
 * @param val = 종이 맵핑코드
 */
var loadPrdtAmt = function(dvs, val) {
    var prefix = getPrefix(dvs);

    var url = "/ajax/product/load_amt.php";
    var data = {
        "cate_sortcode" : sortcode,
        "paper_mpcode"  : val,
        "mono_yn"       : monoYn,
        "amt_unit"      : amtUnit
    };
    var callback = function(result) {
        $("#nc_amt").html(result);
        optionPosition = null;
        rangeBarBySelect();
        changePaper(dvs, val);
    };

    ajaxCall(url, "html", data, callback);
};

/**
 * @param 종이변경시 후공정 제약사항 체크
 *
 * @param dvs = 제품구분값
 * @param val = 종이 맵핑코드
 */
var changePaper = function(dvs, val) {
    aftRestrict.press.common(dvs);
    loadPaperDscr.exec(dvs, val);
};

/**
 * @brief 비규격 사이즈 선택할 경우 재단사이즈 값 초기화
 *
 * @param val = 구분값
 */
var changeSizeDvs = function(val) {
    // 비규격 사이즈 선택시 기본 사이즈로 데이터 변경
    if (val === "manu") {
        $("#nc_manu_pos_num").val('1');
        $("#cut_wid_size").val($("#nc_size").attr("def_cut_wid"));
        $("#cut_vert_size").val($("#nc_size").attr("def_cut_vert"));
    }

    changeData();
};

/**
 * @brief 수량변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeAmt = function() {
    reCalcAfterPrice(prdtDvs, null);
    changeData();
    setSizeWarning();
};

/**
 * @brief 건수변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeCount = function() {
    reCalcAfterPrice(prdtDvs, null);
    calcPrice();
};

/**
 * @brief 관심상품 등록이나 장바구니 전에 데이터 세팅
 */
var setSubmitParam = function() {
    if ($("#il").val() === "0") {
        $("#cart_flag").val('Y');
        return alertReturnFalse("로그인 후 확인 가능합니다.");
    }

    if (checkBlank($("#title").val().trim()) === true) {
        $("#cart_flag").val('Y');
        $("#title").focus();
        return alertReturnFalse("인쇄물 제목을 입력해주세요.");
    }

    if (chkTotalAmt() === false) {
        return alertReturnFalse("수량과 상세수량이 맞지 않습니다.");
    }

    if (!aftRestrict.all(prdtDvs)) {
        return false;
    }

    if (!optRestrict.all(prdtDvs)) {
        return false;
    }

    if ($("#back_wid_size").length > 0) {
        if ($("input[name='back_pos_dvs']").length > 0) {
            if ($("input[name='back_pos_dvs']:checked").length === 0) {
                return alertReturnFalse("백판위치를 선택해주세요.");
            }
        }
    }

    var paperName = $("#nc_paper").find("option:selected").text();
    var tmptName  = $("#nc_print_tmpt").find("option:selected").text();
    var sizeName  = $("#nc_size").find("option:selected").text();

    var sellPrice     = $("#sell_price").attr("val");
    var salePrice     = $("#sale_price").attr("val");
    var afterPrice    = getSumAfterPrice(prdtDvs);
    var optPrice      = getSumOptPrice();

    var ret = makeAfterInfo.all(prdtDvs);

    if ($("#back_wid_size").length > 0) {
        var orderDetail = cateName + " / " +
                          paperName + " / " +
                          sizeName + " / " +
                          tmptName + " / " +
                          "백판사이즈 : " +
                          $("#back_wid_size").val() + '*' +
                          $("#back_vert_size").val() + " / " +
                          "백판위치 : " +
                          $("input[name='back_pos_dvs']:checked").val();

        $("#order_detail").val(orderDetail);
    }

    if ($("#detail_amt_dd").length > 0) {
        var sizeDvs = $("#size_dvs").val();
        if (sizeDvs === "manu") {
            sizeName = "비규격";
        }

        var orderDetail = cateName + " / " +
                          paperName + " / " +
                          sizeName + " / " +
                          tmptName + " / 상세수량 : " +
                          makeDetailAmt(); 

        $("#order_detail").val(orderDetail);
    }

    if (ret === false) {
        return false;
    }

    setAddOptInfo();

    $frm = $("#frm");

    $frm.find("input[name='nc_cate_name']").val(cateName);
    $frm.find("input[name='nc_amt_unit']").val(amtUnit);
    $frm.find("input[name='nc_paper_name']").val(paperName);
    $frm.find("input[name='nc_bef_tmpt_name']").val(tmptName);
    $frm.find("input[name='nc_size_name']").val(sizeName);

    $frm.find("input[name='nc_sell_price']").val(sellPrice);
    $frm.find("input[name='nc_sale_price']").val(salePrice);
    $frm.find("input[name='nc_after_price']").val(afterPrice);
    $frm.find("input[name='opt_price']").val(optPrice);
};

/**
 * @brief 수량과 상세수량이 맞는지 확인
 */
var chkTotalAmt = function() {
    if ($("#detail_amt_dd").length === 0) {
        return true;
    }

    var $detailAmtArr = $("#detail_amt_dd").find("select[name='detail_amt']");
    var len = $detailAmtArr.length;
    var amt = parseInt($("#nc_amt").val());

    var detailAmt = 0;
    for (var i = 0; i < len; i++) {
        detailAmt += parseInt($($detailAmtArr[i]).val());
    }

    if (amt !== detailAmt) {
        return false;
    }

    return true;
};

/**
 * @brief 주문상세에 추가될 상세수량 내용 생성
 *
 * @return 상세수량 내용
 */
var makeDetailAmt = function() {
    var $detailAmtArr = $("#detail_amt_dd").find("select[name='detail_amt']");
    var $detailConArr = $("#detail_amt_dd").find("input[name='detail_con']");
    var len = $detailAmtArr.length;

    var ret = '';

    for (var i = 0; i < len; i++) {
        var amt = $($detailAmtArr[i]).val();
        var con = $($detailConArr[i]).val();

        ret += amt.format() + "매 " + con + ", ";
    }

    return ret.substr(0, ret.length - 2);
};

/**
 * @brief 상세수량 추가
 */
var addDetailAmt = function() {
    var $copyObj = $("#detail_amt_div").clone();
    $copyObj.find("input[name='detail_con']").val('');
    $copyObj.addClass("detail_amt_div");
    $copyObj.removeAttr("id");

    $("#detail_amt_dd").append($copyObj);
};

/**
 * @brief 상세수량 삭제
 */
var removeDetailAmt = function() {
    var $objArr = $(".detail_amt_div");
    var len = $objArr.length;

    $($objArr[len - 1]).remove();
};

/**
 * @brief 도수 변경시 단면일 경우 양면라디오버튼 disabled
 */
var chkTmptBack = function(dvs, val) {
    if (val.indexOf("단면") > -1) {
        $("#back_pos_both").prop("disabled", true);
        $("#back_pos_both").prop("checked", false);
        $("#back_pos_bef").prop("checked", true);
    } else {
        $("#back_pos_both").prop("disabled", false);
        $("#back_pos_both").prop("checked", true);
        $("#back_pos_bef").prop("checked", false);
    }

    changeTmpt(dvs, val);
}

/**
 * @brief 견적서 팝업 본문정보 생성
 */
var makeEstiPopInfo = {
    "data" : null,
    "exec" : function(dvs) {
        var prefix = getPrefix(prdtDvs);

        var paper = $.trim($(prefix + "paper > option:selected").text());
        var size  = $.trim($(prefix + "size > option:selected").text());
        var tmpt  = $.trim($(prefix + "print_tmpt > option:selected").text());
        var amt   = $.trim($(prefix + "amt").val());
        var count = $.trim($("#esti_count").text());

        var after = '';
        $('.after .overview ul li').each(function() {
            after += $(this).text();
            after += ', ';
        });
        after = after.substr(0, after.length - 2);
    
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
            ]
        };

        data = getEstiPopData(data);

        this.data = data;

        if (dvs === "pop") {
            getEstiPopHtml(data);
        } else {
            downEstiExcel(data);
        }
    }
};

/**
 * @brief 복권에서 주의사항 출력
 */
var setSizeWarning = function() {
    var prefix = getPrefix(prdtDvs);
    var amt = $(prefix + "amt > option:selected").val();
    if(parseInt(amt) < 3000) {
        $("#nc_warning").text("* 총수량 3천장 미만 주문시 납기가 오래걸릴 수 있습니다.");
    } else {
        $("#nc_warning").text("");
    }
};

/**
 * @brief 86*52일 때 미싱/오시 
 */
var chkSizeDotImpWarning = function() {
    var prefix = getPrefix(prdtDvs);
    var size = $(prefix + "size > option:selected").text();

    if (size === "86*52") {
        var html = "<dd class=\"br note\" style=\"float:left; width:83%; display:block;\">";
        html += "사이즈가 작아서 선 끝 2~3mm 후공정이 들어가지 않을 수 있습니다.";
        html += "</dd>";
        $("._dotline > dl").append(html);
        $("._impression > dl").append(html);
    }
};
