var prdtDvs   = null;
var sortcode  = null;
var cateName  = null;

$(document).ready(function() {
    // 건수 초기화
    initCount(99, "count");

    prdtDvs   = $("#prdt_dvs").val();
    sortcode  = $("#esti_cate_sortcode").val();
    cateName  = $("#esti_cate_sortcode > option:selected").text();
    amtUnit   = $("#esti_amt").attr("amt_unit");

    if (amtUnit === 'R') {
        $("#sheet_count_div").show();
        calcSheetCount(prdtDvs);
    }

    calcLaminexMaxCount();
    $("input[name='esti_chk_after[]']").removeAttr("onclick");

    var today = new Date();
    var year  = today.getFullYear();
    var month = today.getMonth() + 1;
    var date  = today.getDate();
    $("#title").val(year + '-' + month + '-' + date + " 별도견적");
});

/**
 * @brief 종이명 변경시 인쇄방식 체크하고 종이정보 검색
 */
var changePaperSort = function(dvs, val) {
    loadPaperName(dvs, val);
};

/**
 * @brief 종이명 변경시 인쇄방식 체크하고 종이정보 검색
 */
var changePaperName = function(dvs, val) {
    var prefix = getPrefix(dvs);
    loadPaperInfo(dvs, val, $(prefix + "paper_sort").val());
};

/**
 * @brief 종이 변경 시 라미넥스 최대수량 조정
 */
var calcLaminexMaxCount = function() {
    // 종이 변경 시 라미넥스 최대수량 조정
    var prefix = getPrefix(prdtDvs);
    var paper = $(prefix + "paper > option:selected").text();
    paper = paper.split(' ');
    var basisweight = parseInt(paper[paper.length - 1]);

    aftRestrict.laminex.max = parseInt($(prefix + "sheet_count").val());
    $("#bl_laminex_max").text($("#sheet_count_span").text());
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
};

/**
 * @brief 수량변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeAmt = function() {
    if (amtUnit === 'R') {
        calcSheetCount(prdtDvs);
    }
    calcLaminexMaxCount();
};

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
        }
    }
};

/**
 * @brief 파일 업로드 팝업 출력
 */
var showUploadPop = function() {
    $("#" + commonObj.listId).html('');

    if (!checkBlank(commonObj.uploader.files[0])) {
        commonObj.uploader.removeFile(commonObj.uploader.files[0]);
    }

    var $modalMask =  $(".modalMask.l_orderInformation");
    var $contentsWrap = $modalMask.find('.layerPopupWrap');

    if ($modalMask.outerHeight() > $contentsWrap.height() &&
            $modalMask.outerWidth() > $contentsWrap.width()) {
        //drag
        $contentsWrap.draggable({
            addClasses  : false,
            cursor      : false,
            containment : $modalMask,
            handle      : "header"
        });
    } else {
        $("body").css("overflow", "hidden");
    }

    $modalMask.fadeIn(300, function () {
        $contentsWrap.css({
            'top' : $(window).height() > $contentsWrap.height() ?
	                ($(window).height() - $contentsWrap.height()) / 2 + 'px' : 0,
            'left' : $modalMask.width() > $contentsWrap.width() ?
	                ($modalMask.width() - $contentsWrap.width()) / 2 + 'px' : 0
        });

        orderTable($modalMask);

        var hideFunc = function() {
            $modalMask.fadeOut(300, function() {
                $("body").css("overflow", "auto");
            });
        };

        $modalMask.addClass("_on")
                  .find("button.close")
                  .on("click", hideFunc);
    });
};

/**
 * @brief 견적 정보 파라미터 생성
 */
var setSubmitParam = function() {
    var prefix = getPrefix(prdtDvs);
    var amtUnit = $(prefix + "amt").attr("amt_unit");

    var paperInfo = $(prefix + "paper_sort").val() + ' ' +
                    $(prefix + "paper_name").val() + ' ' +
                    $(prefix + "paper > option:selected").text();
    if (!checkBlank($.trim($(prefix + "ext_paper").val()))) {
        paperInfo += " [" + $(prefix + "ext_paper").val() + ']';
    }
    var befPrintInfo = $(prefix + "bef_tmpt > option:selected").text();
    if (!checkBlank($.trim($(prefix + "ext_bef_tmpt").val()))) {
        befPrintInfo += " [" + $(prefix + "ext_bef_tmpt").val() + ']';
    }
    var aftPrintInfo = $(prefix + "aft_tmpt > option:selected").text();
    if (!checkBlank($.trim($(prefix + "ext_aft_tmpt").val()))) {
        aftPrintInfo += " [" + $(prefix + "ext_aft_tmpt").val() + ']';
    }
    var printPurpInfo = $(prefix + "print_purp").val();
    if (!checkBlank($.trim($(prefix + "ext_print_purp").val()))) {
        printPurpInfo += " [" + $(prefix + "ext_print_purp").val() + ']';
    }
    var amtInfo = $(prefix + "amt").val() + amtUnit;
    if (!checkBlank($.trim($(prefix + "ext_amt").val()))) {
        amtInfo += " [" + $(prefix + "ext_amt").val() + ']';
    }
    var orderDetail = cateName + " / " +
                      paperInfo + " / 전면 : " + 
                      befPrintInfo + " / 후면 : " + 
                      aftPrintInfo + " / " + 
                      printPurpInfo;
    var sizeName = $(prefix + "size > option:selected").text();

    if ($(prefix + "size_dvs").val() === "manu") {
        sizeName = "비규격";
    }

    $(prefix + "amt_unit").val(amtUnit);
    $(prefix + "paper_info").val(paperInfo);
    $(prefix + "bef_print_info").val(befPrintInfo);
    $(prefix + "aft_print_info").val(aftPrintInfo);
    $(prefix + "print_purp_info").val(printPurpInfo);
    $(prefix + "amt_info").val(amtInfo);
    $(prefix + "size_name").val(sizeName);
    $(prefix + "order_detail").val(orderDetail);
};

/**
 * @brief 견적값 입력
 *
 * @param flag = 파일업로드 여부
 */
var insertEsti = function(flag) {
    if ($("#il").val() === "0") {
        alert("로그인 후 확인 가능합니다.");
        return false;
    }
    if (checkBlank($.trim($("#title").val()))) {
        return alertReturnFalse("제목을 입력해주세요.");
    }
    if (!checkBlank(commonObj.estiSeqno) && flag) {
        showUploadPop();
        return false;
    }

    var ret = makeAfterInfo.all(prdtDvs);

    if (ret === false) {
        return false;
    }

    setSubmitParam();

    var url  = "/proc/product/insert_esti.php";
    var data = $("#frm").serialize();
    var callback = function(result) {
        if (!result.success) {
            alert(result.err_msg);
            return false;
        } 

        if (flag) {
            commonObj.estiSeqno = result.seqno;
            commonObj.orderNum  = result.order_num;
            showUploadPop();
            return false;
        }

        alert("견적요청이 완료되었습니다.");
        location.reload();
    };

    ajaxCall(url, "json", data, callback);
};

var changeData = function() {};
var calcPrice = function() {};
