var monoYn    = null;
var prdtDvs   = null;
var affil     = null;
var sortcode  = null;
var amtUnit   = null;
var cateName  = null;
// 일반옵셋 존재하는지 체크
var printPurpChk = false;
var defaultPurp  = null;

$(document).ready(function() {
    // 건수 초기화
    initCount(99, "count");

    monoYn   = $("#bl_mono_yn").val();
    prdtDvs  = $("#prdt_dvs").val();
    affil    = $("#bl_size").find("option:selected").attr("affil");
    sortcode = $("#bl_cate_sortcode").val();
    cateName = $("#bl_cate_sortcode").find("option:selected").text();
    amtUnit  = $("#bl_amt").attr("amt_unit");

    if (amtUnit === 'R') {
        $("#sheet_count_div").show();
        calcSheetCount(prdtDvs);
    }

    var prefix = getPrefix(prdtDvs);
    if (sortcode === "003003002") {
        $(".option._folding > ._closed").trigger("click");

        $(prefix + "print_purp > option").each(function() {
            if ($(this).prop("selected")) {
                defaultPurp = $(this).val();
            }

            if ($(this).val() === "일반옵셋") {
                printPurpChk = true;
            }
        });

        chkPrintPurp(prdtDvs);
    }
    calcLaminexMaxCount();
    $(prefix + "foldline_info").val(80);
	showUvDescriptor(prdtDvs);
});

/**
 * @brief 계산형일 때 사이즈 선택할 경우 사이즈 계열에 맞는 도수값 검색
 *
 * @param val = 구분값
 */
var changeSize = {
    "exec" : function(dvs) {
        var prefix = getPrefix(dvs);
    
        if (amtUnit === 'R') {
            calcSheetCount(dvs);
        } else {
        }

        //if (monoYn === '1') {
            var aftInfoArr = getAftInfoArr(dvs);
            var selectAffil = $(prefix + "size > option:selected").attr("affil");
            var size = $(prefix + "size > option:selected").text();
            loadAfterMpcode(dvs, aftInfoArr, size);
    
            if (affil === selectAffil) {
                changeData();
                return false;
            } else {
                affil = selectAffil;
            }
    
            var callback = function(result) {
                $(prefix + "bef_tmpt").html(result.bef_tmpt);
                $(prefix + "aft_tmpt").html(result.aft_tmpt);
                changeData();
            };
        
            loadPrintTmptCommon.exec(dvs, callback);
        /*} else {
            changeData();
        }*/
    }
};

/**
 * @brief 인쇄방식 체크
 *
 * @param dvs = 제품구분값
 */
var chkPrintPurp = function(dvs, callback) {
    var prefix = getPrefix(dvs);
    var name = $(prefix + "paper_name").val();

    if (name === "랑데뷰" && printPurpChk) {
        var html = "<option value=\"일반옵셋\""
        if (defaultPurp === "일반옵셋") {
            html += " selected =\"selected\" ";
        }
        html += ">일반옵셋</option>";
        html += "<option value=\"UV특수옵셋\"";
        if (defaultPurp === "UV특수옵셋") {
            html += " selected =\"selected\" ";
        }
        html += ">UV특수옵셋</option>";
    } else if (printPurpChk) {
        var html = "<option value=\"UV특수옵셋\">UV특수옵셋</option>";
    }

    $(prefix + "print_purp").html(html);

    if (checkBlank(callback)) {
        callback = function(result) {
            $("#bl_bef_tmpt").html(result.bef_tmpt);
            $("#bl_aft_tmpt").html(result.aft_tmpt);
        };
    }

    loadPrintTmpt(dvs, callback);
};

/**
 * @brief 종이명 변경시 인쇄방식 체크하고 종이정보 검색
 */
var changePaperName = function(dvs, val) {
    var callback = function(result) {
        $("#bl_bef_tmpt").html(result.bef_tmpt);
        $("#bl_aft_tmpt").html(result.aft_tmpt);

        loadPaperInfo(dvs, val);
    };

    loadPaperPreview(dvs);
    chkPrintPurp(dvs, callback);
};

/**
 * @param 종이변경시 후공정 제약사항 체크
 *
 * @param dvs = 제품구분값
 * @param val = 종이 맵핑코드
 */
var changePaper = function(dvs, val) {
    loadPaperPreview(dvs);
    calcLaminexMaxCount();
    reCalcAfterPrice(dvs, null);
    changeData();
};

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 */
var changeData = function() {
    monoYn = $("#bl_mono_yn").val();

    var data ={
        "dvs"           : prdtDvs,
        "cate_sortcode" : sortcode,
        "amt"           : $("#bl_amt").val(),
        "stan_mpcode"   : $("#bl_size").val(),
        "affil"         : affil
    };

    data.paper_mpcode         = $("#bl_paper").val();
    data.bef_print_mpcode     = $("#bl_bef_tmpt").val();
    data.bef_add_print_mpcode = '';
    data.aft_print_mpcode     = $("#bl_aft_tmpt").val();
    data.aft_add_print_mpcode = '';
    data.bef_print_name       = $("#bl_bef_tmpt > option:selected").text();
    data.bef_add_print_name   = '';
    data.aft_print_name       = $("#bl_aft_tmpt > option:selected").text();
    data.aft_add_print_name   = '';
    data.print_purp           = $("#bl_print_purp").val();
    data.page_info            = "2";
    data.flattyp_yn           = "Y";
    data.amt_unit             = amtUnit;
    data.pos_num              = $("#bl_size > option:selected").attr("pos_num");

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
    // 건수
    var count = parseInt($("#count").val());
    // 정상판매가
    var sellPrice = loadPrdtPrice.price.sell_price;
    if (checkBlank(sellPrice)) {
        changeData();
        return false;
    }
    sellPrice  = ceilVal(sellPrice);
    sellPrice *= count;
    // 등급 할인율
    var gradeSale = parseFloat($("#bl_grade_sale_rate").val());
    gradeSale /= 100.0;
    // 회원 할인율
    var memberSale = parseFloat($("#bl_member_sale_rate").val());
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
    // 견적서 합계 계산
    var sum = paper + output + print + after + opt;

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
 * @param callback = 콜백함수,
 * 셀렉트박스 변경 외적으로 처리되는 부분 때문에 추가
 */
var loadPrintTmpt = function(dvs, callback) {
    if (checkBlank(callback)) {
        callback = function(result) {
            $("#bl_bef_tmpt").html(result.bef_tmpt);
            $("#bl_aft_tmpt").html(result.aft_tmpt);

            changeData();
        };
    }

    loadPrintTmptCommon.exec(dvs, callback);
};

/**
 * @brief 비규격 사이즈 선택할 경우 재단사이즈 값 초기화
 *
 * @param val  = 구분값
 */
var changeSizeDvs = function(val) {
    var prefix = getPrefix(prdtDvs);
    $(prefix + "similar_size").attr("divide", '1');

    // 비규격 사이즈 선택시 기본 사이즈로 데이터 변경
    if (val === "manu") {
        var str = $(prefix + "size > option:selected").text() + " 1/1 등분";

        $(prefix + "similar_size").show();
        $(prefix + "similar_size").html(str);
    } else {
        $(prefix + "similar_size").hide();
        calcSheetCount(prdtDvs);
    }

    changeData();
};

/**
 * @brief 수량변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeAmt = function() {
    if (amtUnit === 'R') {
        calcSheetCount(prdtDvs);
    } else {
    }
    reCalcAfterPrice(prdtDvs, null);
    changeData();
    calcLaminexMaxCount();
};

/**
 * @brief 건수변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeCount = function() {
    reCalcAfterPrice(prdtDvs, null);
    calcPrice();
}

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

    if (!aftRestrict.all(prdtDvs)) {
        return false;
    }

    var amtUnit   = $("#bl_amt").attr("amt_unit");
    var paperName = $("#bl_paper").find("option:selected").text();
    var befTmpt   = $("#bl_bef_tmpt").find("option:selected").text();
    var aftTmpt   = $("#bl_aft_tmpt").find("option:selected").text();
    var sizeName  = $("#bl_size").find("option:selected").text();

    var sellPrice     = $("#sell_price").attr("val");
    var salePrice     = $("#sale_price").attr("val");
    var afterPrice    = getSumAfterPrice(prdtDvs);
    var optPrice      = getSumOptPrice();

    var ret = makeAfterInfo.all(prdtDvs);

    if (ret === false) {
        return false;
    }

    setAddOptInfo();

    $frm = $("#frm");

    $frm.find("input[name='bl_cate_name']").val(cateName);
    $frm.find("input[name='bl_amt_unit']").val(amtUnit);
    $frm.find("input[name='bl_paper_name']").val(paperName);
    $frm.find("input[name='bl_bef_tmpt_name']").val(befTmpt);
    $frm.find("input[name='bl_aft_tmpt_name']").val(aftTmpt);
    $frm.find("input[name='bl_size_name']").val(sizeName);

    $frm.find("input[name='bl_sell_price']").val(sellPrice);
    $frm.find("input[name='bl_sale_price']").val(salePrice);
    $frm.find("input[name='bl_after_price']").val(afterPrice);
    $frm.find("input[name='bl_opt_price']").val(optPrice);
};

/**
 * @brief 견적서 팝업 본문정보 생성
 */
var makeEstiPopInfo = {
    "data" : null,
    "exec" : function(dvs) {
        var prefix = getPrefix(prdtDvs);
    
        var paper   = $.trim($(prefix + "paper > option:selected").text());
        var size    = $.trim($(prefix + "size > option:selected").text());
        var befTmpt = $.trim($(prefix + "bef_tmpt > option:selected").text());
        var aftTmpt = $.trim($(prefix + "aft_tmpt > option:selected").text());
        var amt     = $.trim($(prefix + "amt").val());
        var count   = $.trim($("#esti_count").text());

        if ($(prefix + "paper_name").length > 0) {
            paper = $(prefix + "paper_name").val() + paper;
        }

        var tmpt = "전면 : " + befTmpt + " / 후면 : " + aftTmpt;

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
