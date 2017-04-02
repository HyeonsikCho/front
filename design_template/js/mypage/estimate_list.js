/*
 *
 * Copyright (c) 2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 2016/03/07 박상용 생성
 *============================================================================
 *
 */

$(document).ready(function() {

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

    dateSet('0');
    cndSearch.exec(10,1);
});

var initSearchParam = function() {

    if (!$("#searchParam").val())
        return false;
    var params = $("#searchParam").val().split("&");
    $.each(params, function(i,v){
        var tmp = v.split("=");
        $("#"+tmp[0]).val(tmp[1]);
    });
};


/**
 *  * @brief 선택조건으로 검색 클릭시
 *   */
var cndSearch = {
    "exec"       : function(listSize, page) {

        var url = "/ajax/mypage/esti_list/load_esti_list.php";

        var blank = "<tr><td colspan=\"7\">검색 된 내용이 없습니다.</td></tr>";

        var data = {
            "from"       : $("#from").val(),
            "to"         : $("#to").val(),
            "state"      : $("#state").val(),
            "title"      : $("#title").val(),
            "listSize"   : listSize,
            "page"       : page
        };

        var callback = function(result) {

            var rs = result.split("♪");
            if (rs[0].trim() == "") {
                $("#list").html(blank);
                $("#page").html("<li><button class='on'>1</button><li>");
                $("#resultNum").html("<em>0</em>건의 검색결과가 있습니다.");
                return false;
            } else {
                $("#list").html(rs[0]);
                $("#page").html(rs[1]);
                $("#resultNum").html("<em>"+rs[2]+"</em>건의 검색결과가 있습니다.");
            }
            
            orderTable($('body'));
        };

        data.listSize      = listSize;
        data.page          = page;

        showMask();
        ajaxCall(url, "html", data, callback);
       
    }
};

var movePage = function(val) {

    cndSearch.exec($("#listSize").val(), val);
}

var search = function() {
    cndSearch.exec($("#listSize").val(),1);
};

var changeListSize = function() {
    cndSearch.exec($("#listSize").val(),1);
};

var searchEnt = function(e) {
    if (e.keyCode == 13)
        cndSearch.exec($("#listSize").val(),1);
};

var estimateView = function(seq) {
    var url = "/mypage/estimate_view.html";

    $("#seq").val(seq);
    $("#searchParam").val($("#searchFrm").serialize());

    $("#frm").attr("action", url);
    $("#frm").attr("method", "post");
    $("#frm").submit();
};

var goList = function() {
    var url = "/mypage/estimate_list.html";
    location.href = url;
};

var goWrite = function() {
    var url = "/mypage/estimate_write.html";
    location.href = url;
};
