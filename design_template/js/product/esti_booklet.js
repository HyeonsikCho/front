var cateName  = null;
var commonDvs = null;
var sortcode  = null;

var dvsOnOff = {
    "cover"  : true,
    "inner1" : true,
    "inner2" : false,
    "inner3" : false,
    "exec" : function(dvs, flag) {
        dvsOnOff[dvs] = flag;
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
    sortcode  = $("#esti_cate_sortcode").val();
    cateName  = $("#esti_cate_sortcode").find("option:selected").text();
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
 * @brief 견적 정보 파라미터 생성
 */
var setSubmitParam = function() {
    var amt      = $("#esti_amt").val();
    var amtUnit  = $("#esti_amt").attr("amt_unit");
    var sizeName = $("#esti_size > option:selected").text();
    var posNum   = $("#esti_size > option:selected").attr("pos_num");
    var cutWid   = $("#esti_cut_wid_size").val();
    var cutVert  = $("#esti_cut_vert_size").val();
    var workWid  = $("#esti_work_wid_size").val();
    var workVert = $("#esti_work_vert_size").val();

    if ($(prefix + "size_dvs").val() === "manu") {
        sizeName = "비규격";
    }

    var prefix = getPrefix(commonDvs);
    var amtInfo = $(prefix + "amt").val() + amtUnit;
    if (!checkBlank($.trim($(prefix + "ext_print_purp").val()))) {
        amtInfo += " [" + $(prefix + "ext_print_purp").val() + ']';
    }

    $frm = $("#frm");

    var arrLength = dvsArr.length;
    for (var i = 0; i < arrLength; i++) {
        var dvs = dvsArr[i];
        prefix = getPrefix(dvs);

        if (!dvsOnOff[dvs]) {
            continue;
        }
        if ($(prefix + "page").val() === '0') {
            continue;
        }
        var ret = makeAfterInfo.all(dvs);
        if (ret === false) {
            return false;
        }

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
        var pageInfo = dvsKo[dvs] + ' ' + $(prefix + "page").val() + 'p';
        if (!checkBlank($.trim($(prefix + "ext_page").val()))) {
            pageInfo += " [" + $(prefix + "ext_page").val() + ']';
        }

        $frm.find("input[name='" + dvs + "_amt_unit']").val(amtUnit);
        $frm.find("input[name='" + dvs + "_paper_info']").val(paperInfo);
        $frm.find("input[name='" + dvs + "_bef_print_info']").val(befPrintInfo);
        $frm.find("input[name='" + dvs + "_aft_print_info']").val(aftPrintInfo);
        $frm.find("input[name='" + dvs + "_print_purp_info']").val(printPurpInfo);
        $frm.find("input[name='" + dvs + "_amt_info']").val(amtInfo);
        $frm.find("input[name='" + dvs + "_size_name']").val(sizeName);
        $frm.find("input[name='" + dvs + "_cut_wid_size']").val(cutWid);
        $frm.find("input[name='" + dvs + "_cut_vert_size']").val(cutVert);
        $frm.find("input[name='" + dvs + "_work_wid_size']").val(workWid);
        $frm.find("input[name='" + dvs + "_work_vert_size']").val(workVert);
        $frm.find("input[name='" + dvs + "_page_info']").val(pageInfo);
    }

    // 공통
    $("#prdt_dvs").val(getPrdtDvs());
    $("#esti_order_detail").val(makeOrderDetail());

    $("#esti_amt_unit").val(amtUnit);
    $("#esti_sheet_count").val(getPaperRealPrintAmt(commonDvs));

    return true;
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
    ret += $("#esti_size > option:selected").text();
    ret += " / ";
    ret += $("#esti_amt > option:selected").text();

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

    if (!setSubmitParam()) {
        return alertReturnFalse("페이지를 새로고침해주세요.");
    }

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
        //location.reload();
    };

    //ajaxCall(url, "json", data, callback);
};

var changeData = function() {};
var calcPrice = function() {};
