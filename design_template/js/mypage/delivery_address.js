$(document).ready(function(){
    getMainPage(1, $("#init_btn"));
    searchBasicDlvr();
});

//전체 선택
var allCheck = function() {
    //만약 전체선택 체크박스가 체크 된 상태일 경우
    if ($("._general").prop("checked")) {
        $("._individual:not(:disabled)").prop("checked", true);
    } else {
        $("._individual:not(:disabled)").prop("checked", false);
    }
}

//보여줄 페이지 수
var listCnt = "10";

/**
 * @brief 기본배송지 조회
 */
var searchBasicDlvr = function() {
    
    var url = "/ajax/mypage/delivery_address/load_basic_dlvr.php";
    var blank = "기본배송지가 없습니다.";
    var data = {};
    var callback = function(result) {
        if (!result) {
            $("#basicDlvr").html(blank);
            $("#basicDvs").html(blank);
            return false;
        }
        var rs = result.split("♪");
        $("#basicDlvr").html(rs[0] + "<br>" + rs[1]);
        $("#basicDvs").html(rs[2]);
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

/**
 * @brief 선택조건으로 검색 클릭시
 */
var searchDlvrList = function(listSize, page, pop) {
    
    if (listSize != null) {
        listCnt = listSize; 
    }
    console.log(listCnt);
    var url = "/ajax/mypage/delivery_address/load_dlvr_list.php";
    var blank = "<tr><td colspan='7'>검색내용이 없습니다.</td></tr>";
            
    var data = {
    	"from"       : $("#from").val(),
    	"to"         : $("#to").val(),
    	"category"   : $("#category").val(),
    	"searchkey"  : $("#searchkey").val()
    };
    var callback = function(result) {
   
        var rs = result.split("♪");
        if (rs[0].trim() == "") {
            $("#list").html(blank);
            $("#paging").html("<li><button class='on'>1</button><li>");
            $("#resultNum").html("총 <em>0</em>건의 배송지가 있습니다.");
        } else {
            $("#list").html(rs[0]);
            $("#paging").html(rs[1]);
            $("#resultNum").html(rs[2]);
        }

        if (pop)
            closePopup($(".l_address"));
    };

    data.list_num      = listCnt;
    data.page          = page;

    showMask();
    ajaxCall(url, "html", data, callback);
}

/**
* @brief 보여줄 페이지 수 설정
*/
var changeListNum = function(val) {
    listCnt = val;
    searchDlvrList(listCnt, 1);
}

/**
* @brief 페이지 이동
*/
var movePage = function(val) {
    searchDlvrList(listCnt, val);
}

/**
* @brief 조건 검색
*/
var searchList = function() {
    searchDlvrList(listCnt, 1);
}

/**
 * @brief 다음 API 주소검색 함수
 *
 */
var getPostcode = function() {
    new daum.Postcode({
        oncomplete: function(data) {

           var fullAddr = ''; // 최종 주소 변수
           var extraAddr = ''; // 조합형 주소 변수

           // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
           if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
               fullAddr = data.roadAddress;

           } else { // 사용자가 지번 주소를 선택했을 경우(J)
               fullAddr = data.jibunAddress;
           }

           // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
           if (data.userSelectedType === 'R') {
               //법정동명이 있을 경우 추가한다.
               if(data.bname !== ''){
                   extraAddr += data.bname;
               }
               // 건물명이 있을 경우 추가한다.
               if(data.buildingName !== '') {
                   extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
               }
               // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
               fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
           }

           // 우편번호와 주소 정보를 해당 필드에 넣는다.
           document.getElementById('zipcode').value = data.zonecode; //5자리 새우편번호 사용
           document.getElementById('addr').value = fullAddr;

           // 커서를 상세주소 필드로 이동한다.
           document.getElementById('addr_detail').focus();
        }
    }).open({
        pupName: 'postcodePopup'
    });
};

var validation = function() {

    if ($("#dlvr_name").val() == "") {
        alert("배송지별칭을 입력해주세요.");
        $("#dlvr_name").focus();
        return false;
    }

    if ($("#recei").val() == "") {
        alert("성명/상호를 입력해주세요.");
        $("#recei").focus();
        return false;
    }

    var regExp = /^\d{3,4}-\d{4}$/;
    var chk = 0;
    var telnum = $("#tel_num2").val() +"-"+ $("#tel_num3").val();
    var cellnum = $("#cell_num2").val() +"-"+ $("#cell_num3").val();

    if (($("#tel_num2").val() != "" 
            && $("#tel_num3").val() != "")
            && regExp.test(telnum)) {
            chk++;
    }

    if (($("#cell_num2").val() != "" 
            && $("#cell_num3").val() != "")
            && regExp.test(cellnum)) {
            chk++;
    }

    if (chk < 1) {
        alert("전화번호나 휴대전화 둘중에 하나는 정확하게 입력해주세요.");
        $("#tel_num2").focus();
        return false;
    }

    if ($("#zipcode").val() == "" || $("#addr").val() == "" || $("#addr_detail").val() == "") {
        alert("주소를 입력해주세요.");
        $("#addr_detail").focus();
        return false;
    }

    return true;
}


var regi = function() {

    if (!validation())
        return false;

    var url = "/proc/mypage/delivery_address/regi_dlvr.php";
    var data = {
                 "dlvr_name" : $("#dlvr_name").val()
                ,"recei" : $("#recei").val()
                ,"tel_num" : $("#tel_num").val()+"-"+$("#tel_num2").val()+"-"+$("#tel_num3").val()
                ,"cell_num" : $("#cell_num").val()+"-"+$("#cell_num2").val()+"-"+$("#cell_num3").val()
                ,"zipcode" : $("#zipcode").val()
                ,"addr" : $("#addr").val()
                ,"addr_detail" : $("#addr_detail").val()
    };
    var callback = function(result) {
        alert(result);
        searchDlvrList(listCnt, 1, "POP");
        return false;
    };

    showMask();
    ajaxCall(url, "html", data, callback);

}

var edit = function(seq) {

    if (!validation())
        return false;

    var url = "/proc/mypage/delivery_address/modi_dlvr.php";
    var data = {
                 "seq" : seq
                ,"dlvr_name" : $("#dlvr_name").val()
                ,"recei" : $("#recei").val()
                ,"tel_num" : $("#tel_num").val()+"-"+$("#tel_num2").val()+"-"+$("#tel_num3").val()
                ,"cell_num" : $("#cell_num").val()+"-"+$("#cell_num2").val()+"-"+$("#cell_num3").val()
                ,"zipcode" : $("#zipcode").val()
                ,"addr" : $("#addr").val()
                ,"addr_detail" : $("#addr_detail").val()
    };
    var callback = function(result) {
        alert(result);
        searchDlvrList(listCnt, 1, "POP");
        return false;
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

var del = function(seq) {

    if (checkBlank(seq)) {
        alert("삭제 할 대상을 선택해주세요.");
        return false;
    }

    var url = "/proc/mypage/delivery_address/del_dlvr.php";
    var data = {
        "seq" : "seq="+seq
    };
    var callback = function(result) {
        alert(result);
        searchDlvrList(listCnt, 1, "POP");
        return false;
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

var multiDel = function() {

    if (checkBlank($("#frm").serialize())) {
        alert("삭제 할 대상을 선택해주세요.");
        return false;
    }

    var url = "/proc/mypage/delivery_address/del_dlvr.php";
    var data = {
        "seq" : $("#frm").serialize()
    };
    var callback = function(result) {
        alert(result);
        $("._general").prop("checked", false);
        searchDlvrList(listCnt, 1, "POP");
        return false;
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

var updateBasicDlvr = function() {

    if ($("input[name='chk[]']:checked").length != 1) {
        alert("기본배송지 설정은 한개만 가능합니다.\n하나만 선택해주세요.");
        return false;
    }

    var url = "/proc/mypage/delivery_address/modi_basic_dlvr.php";
    var data = {
                 "seq" : $("input[name='chk[]']:checked").val()
    };
    var callback = function(result) {
        alert(result);
        searchDlvrList(listCnt, 1, "POP");
        searchBasicDlvr();
        return false;
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

var getMainPage = function(dvs, el) {

    var url = "/ajax/mypage/delivery_address/load_address_main.php";
    var data = {
        "dvs" : dvs
    };
    var callback = function(result) {
        $("#main_page").html(result);
        $(".main").removeClass("on");
        $(el).addClass("on");

	if (dvs == 1) {
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
	}
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

var regiMainReq = function() {

    var url = "/proc/mypage/delivery_address/modi_main_req.php";
    var data = {
	"seqno" : main_seq,
	"type"  : "2"
    }
    var callback = function(result) {
        alert(result);
        hideRegiPopup();
        searchDlvrFriend("All");
    };
    showMask();
    ajaxCall(url, "html", data, callback);
}

//배송친구 메인리스트
var getDlvrFriendList = function() {

    var url = "/ajax/mypage/delivery_address/load_dlvrfriend_list.php";
    var data = {
	"search_txt" : $("#search_txt").val()
    }
    var callback = function(result) {
        $("#dlvrfriend_list").html(result);
    };
    showMask();
    ajaxCall(url, "html", data, callback);
}

//배송친구 메인리스트 엔터 검색
var enterCheck = function() {
    if (event.keyCode != 13) {
	return false;
    }
    getDlvrFriendList();

}

//배송친구 메인 신청
var regiDlvrFriendMain = function() {

    var url = "/proc/mypage/delivery_address/modi_main_req.php";
    var data = {
    }
    var callback = function(result) {
	    console.log("리저트", result);
        if (result.trim() == 1) {
            $(".close").click();
	    alert("배송친구 메인을 신청하였습니다.");
        } else if (result.trim() == 0){
	    alert("배송친구 메인신청에 실패하였습니다.");
        } else {
            $(".close").click();
	    alert(result.trim());
	}
    };
    showMask();
    ajaxCall(url, "html", data, callback);
}

//배송친구 서브 신청
var regiDlvrFriendSub = function() {
    if (!($('input[name=friendCompany]:checked').val())) {
	alert("배송친구 메인 업체를 선택해주세요");
	return false;
    }

    var url = "/proc/mypage/delivery_address/modi_sub_req.php";
    var data = {
        "main_seqno" : $('input[name=friendCompany]:checked').val(),
    }
    var callback = function(result) {
        if (result.trim() == 1) {
            $(".close").click();
	    alert("배송친구를 신청하였습니다.");
        } else if (result.trim() == 0){
	    alert("배송친구 신청에 실패하였습니다.");
        } else {
            $(".close").click();
	    alert(result.trim());
	}
    };
    showMask();
    ajaxCall(url, "html", data, callback);
}

