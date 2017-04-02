/*
 *
 * Copyright (c) 2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 2016/03/29 박상용
 *============================================================================
 *
 */

$(document).ready(function() {
    dateSet('0');

    //전화번호에 숫자만 입력 가능
    numKeyCheck('tel_num2');
    numKeyCheck('tel_num3');
    numKeyCheck('cell_num2');
    numKeyCheck('cell_num3');

    //일자별 검색 datepicker 기본 셋팅
    $("#from").datepicker({
        dateFormat: 'yy-mm-dd',
        prevText: '이전 달',
        nextText: '다음 달',
        monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        dayNames: ['일','월','화','수','목','금','토'],
        dayNamesShort: ['일','월','화','수','목','금','토'],
        dayNamesMin: ['일','월','화','수','목','금','토'],
        showMonthAfterYear: true,
        yearSuffix: '년'
    });

    $("#to").datepicker({
        dateFormat: 'yy-mm-dd',
        prevText: '이전 달',
        nextText: '다음 달',
        monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        dayNames: ['일','월','화','수','목','금','토'],
        dayNamesShort: ['일','월','화','수','목','금','토'],
        dayNamesMin: ['일','월','화','수','목','금','토'],
        showMonthAfterYear: true,
        yearSuffix: '년'
    });

    initSearchParam();
    ftfSearch(10,1);
});

/**
 * @brief 선택조건으로 검색 클릭시
 */
var ftfSearch = function(listSize, page) {

    var url = "/ajax/mypage/ftf_list/load_ftf_list.php";
    var blank ="<tr><td colspan=\"6\">검색 된 내용이 없습니다.</td></tr>";
    var data = {
    	"from"       : $("#from").val(),
    	"to"         : $("#to").val(),
    	"answ_yn"    : $("#answ_yn").val(),
    	"inq_typ"    : $("#inq_typ").val(),
    	"title"      : $("#title").val(),
	};
    var callback = function(result) {
        var rs = result.split("♪");
        if (rs[0].trim() == "") {

            $("#list").html(blank);
            $("#paging").html("<li><button class='on'>1</button><li>");
            $("#resultNum").html("<em>0</em>건의 검색결과가 있습니다.");
            return false;

        } else {

            $("#list").html(rs[0]);
            $("#paging").html(rs[1]);
            $("#resultNum").html(rs[2]);
            return false;

        }

    };

    data.list_num      = listSize;
    data.page          = page;

    showMask();
    ajaxCall(url, "html", data, callback);
}

var listCnt = "";

/**
* @brief 보여줄 페이지 수 설정
*/
var changeListNum = function(val) {
    listCnt = val;
    ftfSearch(listCnt, 1);
}

/**
* @brief 페이지 이동
*/
var movePage = function(val) {

    ftfSearch(listCnt, val);
}

/**
* @brief 페이지 이동
*/
var ftfSelectMove = function() {

    var url = "/mypage/ftf_write.html";
    $(location).attr('href', url);
    return false; 
}

var initSearchParam = function() {
    if (checkBlank($("#searchParam").val())) {
        return false;
    }

    var params = $("#searchParam").val().split("&");
    $.each(params, function(i, v){
        var tmp = v.split("=");
        $("#"+tmp[0]).val(tmp[1]);
    });
};

var goList = function() {
    var url = "/mypage/ftf_list.html";
    $("#frm").attr("action", url);
    $("#frm").attr("method", "post");
    $("#frm").submit();
};


/**
* @brief 페이지 이동
*/
var ftfWrite = function() {

    var url = "/mypage/ftf_write.html";
    $("#frm").attr("action", url);
    $("#frm").attr("method", "post");
    $("#frm").submit();

}


/**
* @brief 페이지 이동
*/
var ftfView = function(seq) {

    var url = "/mypage/ftf_view.html";
    $("#seq").val(seq);
    $("#searchParam").val($("#searchFrm").serialize());

    $("#frm").attr("action", url);
    $("#frm").attr("method", "post");
    $("#frm").submit();

}

/**
* @brief 조건 검색
*/
var searchKey = function(event) {
    if(event.keyCode != 13) {
        return false;
    }
    ftfSearch(listCnt, 1);
}

/**
* @brief 조건 검색
*/
var searchTxt = function() {
    ftfSearch(listCnt, 1);
}

var validation = function() {

    if ($("#title").val() == "") {
        alert("제목을 입력해주세요.");
        $("#title").focus();
        return false;
    }

    
    if ( ($("#cell_num2").val() == "" || $("#cell_num3").val() == "")
        && ($("#tel_num2").val() == "" || $("#tel_num3").val() == "")
        && ($("#mail").val() == "" || $("#mail2").val() == "") ) {
        alert("연락처를 최소 한개는 남겨주셔야 합니다.");
        return false;
    }


    if ($("#cont").val() == "") {
        alert("내용을 입력해주세요.");
        $("#cont").focus();
        return false;
    }

    return true;
}

var regiReq = function() {

    if (!validation())
        return false;

    showMask();
    var formData = new FormData();

    formData.append("title", $("#title").val());
    formData.append("inq_typ", $("#inq_typ").val());
    if ($("#tel_num2").val() != "" && $("#tel_num3").val() != "")
        formData.append("tel_num", $("#tel_num").val() + "-" + $("#tel_num2").val() + "-" + $("#tel_num3").val());
    if ($("#cell_num2").val() != "" && $("#cell_num3").val() != "")
        formData.append("cell_num", $("#cell_num").val() + "-" + $("#cell_num2").val() + "-" + $("#cell_num3").val());
    if ($("#mail").val()) {
        formData.append("mail", $("#mail").val() + "@" + $("#mail2").val());
        formData.append("answ_mail_yn", "Y");
    } else {
        formData.append("answ_mail_yn", "N");
    }
    formData.append("cont", $("#cont").val());
    formData.append("file", $("#file")[0].files[0]);
    if ($("#file").val())
        formData.append("upload_yn", "Y");
    else
        formData.append("upload_yn", "N");


    $.ajax({
        type: "POST",
        data: formData,
        url: "/proc/mypage/ftf_write/regi_ftf_list.php",
        dataType : "html",
        processData : false,
        contentType : false,
        success: function(result) {
            alert($.trim(result));
            hideMask();
            location.href = "/mypage/ftf_list.html";
        },
        error    : getAjaxError
    });


}
