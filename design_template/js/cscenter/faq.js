$(document).ready(function(){
    cndSearch(1);
});

//보여줄 페이지 수
var listSize = "10";

/**
 * @brief 선택조건으로 검색 클릭시
 */
var cndSearch = function(page) {
    
    showMask();
        
    var url = "/ajax/cscenter/faq/load_faq_list.php";
    var blank = "<li style='text-align:center'><dl><dt>검색내용이 없습니다.</dt></dl></li>";
    var data = {
         "cont" : $("#cont").val()
        ,"type" : $("#type").val()
    };
    var callback = function(result) {
        
        var rs = result.split("♪");
        if (rs[0].trim() == "") {
            $("#list").html(blank);
            return false;
        }
        $("#list").html(rs[0]);
        $("#paging").html(rs[1]);
    };

    data.list_num      = listSize;
    data.page          = page;

    ajaxCall(url, "html", data, callback);
}

/**
* @brief 페이지 이동
*/
var movePage = function(val) {
    cndSearch(val);
}

/**
* @brief 조건 검색
*/
var searchKey = function(event) {
    if(event.keyCode != 13) {
        return false;
    }
    cndSearch(1);
}

/**
* @brief 조건 검색
*/
var searchTxt = function() {
    cndSearch(1);
}

/**
 *@brief FAQ뷰 액션
 */
var viewFAQ = function(seqno, li) {

    var url = "/proc/cscenter/faq/modi_faq_list.php";
    var data = {
        "seqno" : seqno
    };
    var callback = function(result) {
	if (result != 1) {
            alert("게시물 조회에 문제가 생겼습니다.\n잠시후 다시 시도해주세요.");
	    return false;
	}
        if ($(li).attr("class") == "") {
            $("#list li").removeClass("on");
            $("#list dd").hide();
            $(li).addClass("on");
            $(li).find("dd").show();
        } else {
            $(li).removeClass("on");
            $(li).find("dd").hide();
        }
    };

    ajaxCall(url, "html", data, callback);
}
