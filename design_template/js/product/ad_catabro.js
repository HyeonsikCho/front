var tmptDvs   = null;
var monoYn    = null;
var affil     = null;
var sortcode  = null;
var amtUnit   = null;
var cateName  = null;
var commonDvs = null;
// 표지내지 on/off 확인
var dvsOnOff = {
    "cover"  : true,
    "inner1" : true,
    "inner2" : false,
    "inner3" : false,
    "exec" : function(dvs) {
        if (checkBlank(dvs)) {
            if (!dvsOnOff["inner2"]) {
                dvs = "inner2";
            } else if  (!dvsOnOff["inner3"]) {
                dvs = "inner3";
            }

            $("#wrap_" + dvs).show();
        } else {
            $("#wrap_" + dvs).hide();
        }

        dvsOnOff[dvs] = !dvsOnOff[dvs];
        reCalcAfterPrice(commonDvs, null);
        reCalcAfterPrice(dvs, null);
        changeData(dvs);
    }
};
var dvsIdx = {
    "cover"  : 0,
    "inner1" : 1,
    "inner2" : 2,
    "inner3" : 3
};
var dvsKo = {
    "cover"  : "표지",
    "inner1" : "내지1",
    "inner2" : "내지2",
    "inner3" : "내지3"
};
var dvsArr = [
    "cover",
    "inner1",
    "inner2",
    "inner3"
];

$(document).ready(function() {
    monoYn    = $("#cover_mono_yn").val();
    tmptDvs   = $("#ad_tmpt_dvs").val();
    affil     = $("#ad_size").find("option:selected").attr("affil");
    sortcode  = $("#ad_cate_sortcode").val();
    cateName  = $("#ad_cate_sortcode").find("option:selected").text();
    amtUnit   = $("#ad_amt").attr("amt_unit");
    commonDvs = $("#common_prdt_dvs").val();

    var option = "";
    for (var i = 8; i <= 320; i += 4) {
        option += "<option value=\"" + i + "\">" + i + "p</option>";
    }
    $("#inner1_page").append(option);
    $("#inner2_page").append(option);
    $("#inner3_page").append(option);

    $("#cover_cate_sortcode").val(sortcode);
    $("#inner1_cate_sortcode").val(sortcode);
    $("#inner2_cate_sortcode").val(sortcode);
    $("#inner3_cate_sortcode").val(sortcode);

    $("#ad_print_purp").val($("#cover_print_purp").val());

    // 제본 셀렉트박스 초기화
    chkBookletBinding(calcBindingPrice.getPage());
    showUvDescriptor("cover");
    showUvDescriptor("inner1");
});

/**
 * @param 종이변경시 후공정 제약사항 체크
 *
 * @param dvs = 제품구분값
 * @param val = 종이 맵핑코드
 */
var changePaper = function(dvs) {
    loadPaperPreview(dvs);
    reCalcAfterPrice(dvs, null);
    changeData(dvs);
};

/**
 * @brief 비규격 사이즈 선택할 경우 재단사이즈 값 초기화
 *
 * @param val  = 구분값
 */
var changeSizeDvs = function(val) {
    var prefix = getPrefix(commonDvs);
    $(prefix + "similar_size").attr("divide", '1');

    // 비규격 사이즈 선택시 기본 사이즈로 데이터 변경
    if (val === "manu") {
        var str = $(prefix + "size > option:selected").text() + " 1/1 등분";

        $(prefix + "similar_size").show();
        $(prefix + "similar_size").html(str);
    } else {
        $(prefix + "similar_size").hide();
        calcSheetCount(commonDvs);
    }

    changeData("all");
};

/**
 * @brief 계산형일 때 사이즈 선택할 경우 사이즈 계열에 맞는 도수값 검색
 *
 * @param val = 구분값
 */
var changeSize = {
    "exec" : function() {
        var prefix = getPrefix(commonDvs);

        if (monoYn === '1') {
            var selectAffil =
                    $(prefix + "size").find("option:selected").attr("affil");
            var bindingDepth1 =
                $(prefix + "binding_depth1 > option:selected").text();
    
            if (affil === selectAffil) {
                loadBindingDepth2(bindingDepth1, commonDvs);
                changeData("all");
                return false;
            } else {
                affil = selectAffil;
            }
    
            var callback = function(result) {
                var arrLength = dvsArr.length;

                for (var i = 0; i < arrLength; i++) {
                    var pfx = getPrefix(dvsArr[i]);

                    $(pfx + "bef_tmpt").html(result.bef_tmpt);
                    $(pfx + "aft_tmpt").html(result.aft_tmpt);
                }

                loadBindingDepth2(bindingDepth1, commonDvs);
                changeData("all");
            };
        
            loadPrintTmptCommon.exec(commonDvs, callback);
        } else {
            loadBindingDepth2(bindingDepth1, commonDvs);
            changeData("all");
        }
    }
};

/**
 * @brief 수량변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeAmt = function() {
    var arrLength = dvsArr.length;
    reCalcAfterPrice(commonDvs, null);

    for (var i = 0; i < arrLength; i++) {
        var tmp = dvsArr[i];

        if (!dvsOnOff[tmp]) {
            continue;
        }

        reCalcAfterPrice(dvsArr[i], null);
    }
    changeData("all");
};

/**
 * @brief 페이지 변경시 제본가격 등 재계산
 *
 * @param dvs = 제품 구분값
 * @param val = 페이지
 */
var changePage = function(dvs, val) {
    if (chkBookletBinding(calcBindingPrice.getPage())) {
        reCalcAfterPrice(commonDvs, null);
    }
    reCalcAfterPrice(dvs, null);
    changeData(dvs);
};

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 *
 * @param dvs = 범위 구분
 */
var changeData = function(dvs) {
    monoYn = $("#cover_mono_yn").val();

    var data = {
        "dvs"           : dvs,
        "cate_sortcode" : sortcode,
        "amt"           : $("#ad_amt").val(),
        "amt_unit"      : amtUnit,
        "stan_mpcode"   : $("#ad_size").val(),
        "pos_num"       : $("#ad_size > option:selected").attr("pos_num"),
        "affil"         : affil
    };

    var arrLength = dvsArr.length;

    if (dvs === "all") {
        for (var i = 0; i < arrLength; i++) {
            var tmp = dvsArr[i];
            var pfx = getPrefix(tmp);
            var page = $(pfx + "page").val();

            if (!dvsOnOff[tmp] || page === '0') {
                continue;
            }

            tmp += '_';

            data[tmp + "paper_mpcode"]         = $(pfx + "paper").val();
            data[tmp + "bef_print_mpcode"]     = $(pfx + "bef_tmpt").val();
            data[tmp + "bef_add_print_mpcode"] = '';
            data[tmp + "aft_print_mpcode"]     = $(pfx + "aft_tmpt").val();
            data[tmp + "aft_add_print_mpcode"] = '';
            data[tmp + "bef_print_name"]       =
                $(pfx + "bef_tmpt > option:selected").text();
            data[tmp + "bef_add_print_name"]   = '';
            data[tmp + "aft_print_name"]       =
                $(pfx + "aft_tmpt > option:selected").text();
            data[tmp + "aft_add_print_name"]   = '';
            data[tmp + "print_purp"]           = $(pfx + "print_purp").val();
            data[tmp + "page_info"]            = page;
        }
    } else {
        var tmp = dvs + '_';
        var pfx = getPrefix(dvs);
        var page = $(pfx + "page").val();

        if (!dvsOnOff[dvs] || page === '0') {
            $(pfx + "sell_price").val(0);
            $(pfx + "paper_price").val(0);
            $(pfx + "output_price").val(0);
            $(pfx + "print_price").val(0);
	    calcPrice();

            return false;
        }

        data[tmp + "paper_mpcode"]         = $(pfx + "paper").val();
        data[tmp + "bef_print_mpcode"]     = $(pfx + "bef_tmpt").val();
        data[tmp + "bef_add_print_mpcode"] = '';
        data[tmp + "aft_print_mpcode"]     = $(pfx + "aft_tmpt").val();
        data[tmp + "aft_add_print_mpcode"] = '';
        data[tmp + "bef_print_name"]       =
            $(pfx + "bef_tmpt > option:selected").text();
        data[tmp + "bef_add_print_name"]   = '';
        data[tmp + "aft_print_name"]       =
            $(pfx + "aft_tmpt > option:selected").text();
        data[tmp + "aft_add_print_name"]   = '';
        data[tmp + "print_purp"]           = $(pfx + "print_purp").val();
        data[tmp + "page_info"]            = page;
    }

    loadPrdtPrice.data = data;
    loadPrdtPrice.exec(dvs);
};

/**
 * @brief 상품 가격정보 검색
 */
var loadPrdtPrice = {
    "data"  : {},
    "price" : {
        "cover"  : null,
        "inner1" : null,
        "inner2" : null,
        "inner3" : null,
    },
    "exec"  : function(dvs) {
        var url = null;
        if (monoYn === '0') {
            url = "/ajax/product/load_ply_price.php";
        } else {
            url = "/ajax/product/load_book_calc_price.php";
        }
        var callback = function(result) {
            if (dvs === "all") {
                if (parseInt(result[commonDvs]["cover"].sell_price) === 0) {
                    return alertReturnFalse("해당하는 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
                }

                loadPrdtPrice.price = result[commonDvs];

                var arrLength = dvsArr.length;
            
                for (var i = 0; i < arrLength; i++) {
                    var tmp = dvsArr[i];
                    var pfx = getPrefix(tmp);
                    var priceArr = loadPrdtPrice.price[tmp];

                    $(pfx + "paper_price").val(priceArr.paper);
                    $(pfx + "output_price").val(priceArr.output);
                    $(pfx + "print_price").val(priceArr.print);
                    $(pfx + "sell_price").val(priceArr.sell_price);
                }
            } else {
                if (parseInt(result[commonDvs][dvs].sell_price) === 0) {
                    return alertReturnFalse("해당하는 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
                }

                loadPrdtPrice.price[dvs] = result[commonDvs][dvs];

                var pfx = getPrefix(dvs);
                var priceArr = loadPrdtPrice.price[dvs];
                $(pfx + "paper_price").val(priceArr.paper);
                $(pfx + "output_price").val(priceArr.output);
                $(pfx + "print_price").val(priceArr.print);
                $(pfx + "sell_price").val(priceArr.sell_price);
            }

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
    var i2Flag = dvsOnOff[dvsArr[2]];
    var i3Flag = dvsOnOff[dvsArr[3]];

    // 정상판매가
    var coverSellPrice  = parseInt($("#cover_sell_price").val());
    var inner1SellPrice = parseInt($("#inner1_sell_price").val());
    var inner2SellPrice = 0;
    if (i2Flag) {
        inner2SellPrice = parseInt($("#inner2_sell_price").val());
    }
    var inner3SellPrice = 0;
    if (i3Flag) {
        inner3SellPrice = parseInt($("#inner3_sell_price").val());
    }
    var sellPrice = coverSellPrice + inner1SellPrice +
                    inner2SellPrice + inner3SellPrice;
    sellPrice  = ceilVal(sellPrice);

    // 등급 할인율
    var gradeSale = parseFloat($("#ad_grade_sale_rate").val());
    gradeSale /= 100.0;
    // 회원 할인율
    var memberSale = parseFloat($("#ad_member_sale_rate").val());
    memberSale /= 100.0;
    // 옵션비 총합
    var sumOptPrice = getSumOptPrice();
    sumOptPrice = ceilVal(sumOptPrice);
    // 후공정비 총합
    var bindingPrice    = getSumAfterPrice(commonDvs);
    var coverAfterPrice = getSumAfterPrice(dvsArr[0]);
    var inner1AfterPrice = getSumAfterPrice(dvsArr[1]);
    var inner2AfterPrice = 0;
    if (i2Flag) {
        getSumAfterPrice(dvsArr[2]);
    }
    var inner3AfterPrice = 0;
    if (i3Flag) {
        getSumAfterPrice(dvsArr[3]);
    }
    var sumAfterPrice = bindingPrice + coverAfterPrice + inner1AfterPrice +
                        inner2AfterPrice + inner3AfterPrice;
    sumAfterPrice = ceilVal(sumAfterPrice);

    // 견적서 종이비 계산
    var paper  = 0;
    if (monoYn === '1') {
        var coverPaper  = parseInt($("#cover_paper_price").val());
        var inner1Paper = parseInt($("#inner1_paper_price").val());
        var inner2Paper = 0;
        if (i2Flag) {
            inner2Paper = parseInt($("#inner2_paper_price").val());
        }
        var inner3Paper = 0;
        if (i3Flag) {
            inner3Paper = parseInt($("#inner3_paper_price").val());
        }
        paper = ceilVal(coverPaper + inner1Paper +
                        inner2Paper + inner3Paper);
    }

    // 견적서 출력비 계산
    var output = 0;
    if (monoYn === '1') {
        var coverOutput  = parseInt($("#cover_output_price").val());
        var inner1Output = parseInt($("#inner1_output_price").val());
        var inner2Output = 0;
        if (i2Flag) {
            inner2Output = parseInt($("#inner2_output_price").val());
        }
        var inner3Output = 0;
        if (i3Flag) {
            inner3Output = parseInt($("#inner3_output_price").val());
        }
        output = ceilVal(coverOutput + inner1Output +
                         inner2Output + inner3Output);
    }

    // 견적서 인쇄비 계산
    var print  = sellPrice;
    if (monoYn === '1') {
        var coverPrint  = parseInt($("#cover_print_price").val());
        var inner1Print = parseInt($("#inner1_print_price").val());
        var inner2Print = 0;
        if (i2Flag) {
            inner2Print = parseInt($("#inner2_print_price").val());
        }
        var inner3Print = 0;
        if (i3Flag) {
            inner3Print = parseInt($("#inner3_print_price").val());
        }
        print = ceilVal(coverPrint + inner1Print +
                        inner2Print + inner3Print);
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
        "count"         : 1,
        "gradeSaleRate" : gradeSale,
        "sellPrice"     : sellPrice
    };

    changeQuickEsti(param);

    if (flag === false) {
        return false;
    }

    reCalcOptPrice(commonDvs, null);
};

/**
 * @brief 장바구니로 이동
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

    if (!chkSubmitValidation()) {
        return false;
    }

    setAddOptInfo();

    var optPrice = getSumOptPrice();

    var amt       = $("#ad_amt").val();
    var sizeName  = $("#ad_size > option:selected").text();
    var posNum    = $("#ad_size > option:selected").attr("pos_num");
    var sellPrice = $("#sell_price").attr("val");
    var salePrice = $("#sale_price").attr("val");
    var cutWid    = $("#ad_cut_wid_size").val();
    var cutVert   = $("#ad_cut_vert_size").val();
    var workWid   = $("#ad_work_wid_size").val();
    var workVert  = $("#ad_work_vert_size").val();

    $frm = $("#frm");

    var arrLength = dvsArr.length;

    for (var i = 0; i < arrLength; i++) {
        var dvs = dvsArr[i];
        var prefix = getPrefix(dvs);

        if (!dvsOnOff[dvs]) {
            continue;
        }

        if ($(prefix + "page").val() === '0') {
            continue;
        }

        var afterPrice = getSumAfterPrice(dvs);
        var ret = makeAfterInfo.all(dvs);
        if (ret === false) {
            return false;
        }

        var paperPrice  = $(prefix + "paper_price").val();
        var outputPrice = $(prefix + "output_price").val();
        var printPrice  = $(prefix + "print_price").val();

        var paperName = $(prefix + "paper_name").val();
        paperName += ' ' + $(prefix + "paper > option:selected").text();
        var befTmptName = $(prefix + "bef_tmpt > option:selected").text();
        var aftTmptName = $(prefix + "aft_tmpt > option:selected").text();

        $frm.find("input[name='" + dvs + "_cate_sortcode']").val(sortcode);
        $frm.find("input[name='" + dvs + "_cate_name']").val(cateName);
        $frm.find("input[name='" + dvs + "_amt']").val(amt);
        $frm.find("input[name='" + dvs + "_amt_unit']").val(amtUnit);
        $frm.find("input[name='" + dvs + "_cut_wid_size']").val(cutWid);
        $frm.find("input[name='" + dvs + "_cut_vert_size']").val(cutVert);
        $frm.find("input[name='" + dvs + "_work_wid_size']").val(workWid);
        $frm.find("input[name='" + dvs + "_work_vert_size']").val(workVert);
        $frm.find("input[name='" + dvs + "_paper_name']").val(paperName);
        $frm.find("input[name='" + dvs + "_size_name']").val(sizeName);
        $frm.find("input[name='" + dvs + "_pos_num']").val(posNum);
        $frm.find("input[name='" + dvs + "_bef_tmpt_name']").val(befTmptName);
        $frm.find("input[name='" + dvs + "_aft_tmpt_name']").val(aftTmptName);
        $frm.find("input[name='" + dvs + "_after_price']").val(afterPrice);
    }

    // 공통
    $("#prdt_dvs").val(getPrdtDvs());

    $("#ad_order_detail").val(makeOrderDetail());
    $frm.find("input[name='opt_price']").val(optPrice);
    $frm.find("input[name='ad_amt_unit']").val(amtUnit);
    $frm.find("input[name='ad_sell_price']").val(sellPrice);
    $frm.find("input[name='ad_sale_price']").val(salePrice);
    $frm.find("input[name='ad_sheet_count']").val(getPaperRealPrintAmt(commonDvs));

    return true;
};

/**
 * @brief submit 전에 validation 체크
 */
var chkSubmitValidation = function() {
    // 표지 평량이 내지 평량보다 커야됨
    var arrLength = dvsArr.length;
    var coverBasisweight = 0;
    for (var i = 0; i < arrLength; i++) {
        var dvs = dvsArr[i];

	if (!dvsOnOff[dvs]) {
            continue;
        }

        var prefix = getPrefix(dvs);

        var paper = $(prefix + "paper > option:selected").text().split(' ');
        var basisweight = parseInt(paper[paper.length - 1]);

        if (dvs === "cover") {
            coverBasisweight = basisweight;
        }

        if (basisweight > coverBasisweight) {
            return alertReturnFalse("표지의 평량이 내지보다 낮습니다.");
        }
    }

    return true;
};

/**
 * @brief 제품구분값 생성
 */
var getPrdtDvs = function() {
    var arrLength = dvsArr.length;
    var prdtDvs = '';

    for (var i = 0; i < arrLength; i++) {
        var dvs = dvsArr[i];
        var prefix = getPrefix(dvs);

        if (!dvsOnOff[dvs]) {
            continue;
        }

        if ($(prefix + "page").val() === '0') {
            continue;
        }

        prdtDvs += dvs + '|';
    }

    return prdtDvs.substr(0, prdtDvs.length - 1);
};

/**
 * @brief 주문내역 생성
 *
 * @detail 일반지 카다로그 / A4 / 50부 / 표지 : 모조지 백색 70g, 전면 - 4도 / 후면 - 없음, 2p / 내지1 : ~이하동일~
 *
 * @return 주문내역
 */
var makeOrderDetail = function() {
    var ret = '';

    var arrLength = dvsArr.length;
    var prdtDvs = '';

    // 공통카테고리
    ret += cateName;
    ret += " / ";
    ret += $("#ad_size > option:selected").text();
    ret += " / ";
    ret += $("#ad_amt > option:selected").text();

    for (var i = 0; i < arrLength; i++) {
        var dvs    = dvsArr[i];
        var prefix = getPrefix(dvs);
        var ko     = dvsKo[dvs];

        if (!dvsOnOff[dvs]) {
            continue;
        }

        if ($(prefix + "page").val() === '0') {
            continue;
        }

        ret += " / " + ko + " : ";
        ret += $(prefix + "paper_name").val() + ' ';
        ret += $(prefix + "paper > option:selected").text();
        ret += ", 전면 - ";
        ret += $(prefix + "bef_tmpt > option:selected").text();
        ret += ", 후면 - ";
        ret += $(prefix + "aft_tmpt > option:selected").text();
        ret += ", ";
        ret += $(prefix + "page > option:selected").text();
    }

    return ret;
};

/**
 * @brief 제본 예시팝업 출력
 */
var showBindingPop = function() {
    var url = "/ajax/product/load_binding_pop.php";
    layerPopup("l_binding", url);
};

/**
 * @brief 견적서 팝업 본문정보 생성
 */
var makeEstiPopInfo = {
    "data" : null,
    "exec" : function() {
        var prefix = getPrefix(commonDvs);

        var size   = $.trim($(prefix + "size > option:selected").text());
        var amt    = $.trim($(prefix + "amt").val());
        var count  = $.trim($("#esti_count").text());

        var data = {
            "cate_name" : [
                cateName
            ],
            "paper" : [
            ],
            "size" : [
                size
            ],
            "tmpt" : [
            ],
            "amt" : [
                amt
            ],
            "amt_unit" : [
                amtUnit
            ],
            "page" : [
            ],
            "count" : [
                count
            ],
            "after" : [
                // 제본 추가하기
            ],
            "booklet" : 'Y'
        };

        data = getEstiPopData(data);

        var prdtDvsArr = getPrdtDvs().split('|');
        var prdtDvsArrLen = prdtDvsArr.length;

        for (var i = 0; i < prdtDvsArrLen; i++) {
            var dvs    = prdtDvsArr[i];
            prefix = getPrefix(dvs);

            var paper   = $.trim($(prefix + "paper > option:selected").text());
            var page    = $.trim($(prefix + "page").val());
            var befTmpt = $.trim($(prefix + "bef_tmpt > option:selected").text());
            var aftTmpt = $.trim($(prefix + "aft_tmpt > option:selected").text());
            var after   = '';

            if ($(prefix + "paper_name").length > 0) {
                paper = $(prefix + "paper_name").val() + ' ' + paper;
            }

            var tmpt = "전면 : " + befTmpt + " / 후면 : " + aftTmpt;

            $("." + dvs + "_after .overview ul li").each(function() {
                after += $(this).text();
                after += ', ';
            });

            after = after.substr(0, after.length - 2);

            data.paper.push(paper);
            data.page.push(page);
            data.tmpt.push(tmpt);
            data.after.push(after);
        }

        this.data = data;

        getEstiPopHtml(data);
    }
};
