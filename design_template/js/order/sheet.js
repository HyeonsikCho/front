/***********************************************************************************
 *** 프로 젝트 : 3.0
 *** 개발 영역 : 주문서 작성페이지
 *** 개  발  자 : 엄준현 -> 조현식
 *** 개발 날짜 : 2016.06.29
 ***********************************************************************************/

// 팝업 객체
var popupMask = null;
// 작업 파일 업로더 객체 배열
var uploaderArr = {};
// 작업 파일 업로더 확인여부 배열
var fileFlagArr = {};

window.onbeforeunload = unloadFunc;
var unloadFunc = function () {
    return "입력된 정보가 사라지게 됩니다.\n계속하시겠습니까?";
};
	
$(document).ready(function() {
    // 변하지 않는 정보는 최초 페이지 로드할 때 인코딩
    $("#EP_mall_nm").val(encodeURIComponent($("#EP_mall_nm").val()));
    $("#EP_user_nm").val(encodeURIComponent($("#EP_user_nm").val()));
    modiProductNm();

    // 버튼별로 업로더 바인딩
    // 파일첨부 버튼 개수만큼 바인딩 한다

    // 공통사용 변수 캐싱
    var runtimes = "html5,flash,silverlight,html4";
    var mimeTypes = [
        {extensions : "zip,egg,rar,jpg,jpeg,sit,zip,ai,png,alz,cdr,cdt,eps,cmx,7z,pdf"}
    ];

    $("button[name='work_file']").each(function(i) {
        var idx = i + 1;
        var btnId    = "work_file_" + idx;
        var listId   = "work_file_list_" + idx;
        var uploadId = "work_file_upload_" + idx;
        var delId    = "work_file_del_" + idx;
        var url      = "/proc/order/upload_file.php";

        var uploader = new plupload.Uploader({
            url                 : url,
            runtimes            : runtimes,
            browse_button       : btnId, // you can pass in id...
            flash_swf_url       : "/design_template/js/uploader/Moxie.swf",
            silverlight_xap_url : "/design_template/js/uploader/Moxie.xap",
            multi_selection     : false,

            filters : {
                max_file_size : "500mb",
                mime_types    : mimeTypes
            },
            init : {
                PostInit : function() {
                    //document.getElementById(listId).innerHTML = "<blink class=\"_blink\">파일을 업로드 해주세요.</blink>";
                },
                FilesAdded : function(up, files) {
                    // 기존에 업로드 된 파일이 있는 경우
                    if ($("#uploaded_work_file_list_" + idx + " div").length > 0) {
                        if (confirm("기존 파일은 삭제합니다." +
                                "\n계속 하시겠습니까?") === false) {
                            return false;
                        }

                        var id = "#uploaded_work_file_del_" + idx;
                        var fileSeqno = $(id).attr("file_seqno");
                        removeFile(idx, fileSeqno, -1);

                        $("#uploaded_work_file_list_" + idx).html('');
                    }

                    // 파일을 새로 추가할 경우
                    if (up.files.length > 1) {
                        var fileSeqno = $("#" + delId).attr("file_seqno");

                        // 파일이 업로드 된 상태(fileSeqno !== empty)에서
                        // 다른 파일을 새로 업로드 할 경우
                        if (checkBlank(fileSeqno) === false &&
                            confirm("기존 파일은 삭제합니다." +
                                "\n계속 하시겠습니까?") === false) {
                            return false;
                        }

                        up.removeFile(up.files[0]);

                        if (checkBlank(fileSeqno) === false) {
                            removeFile(idx, fileSeqno, false);
                        }
                    }

                    plupload.each(files, function(file) {
                        if (file.size > 524288000) {
                            up.removeFile(up.files[0]);
                            showWorkFileTr(false, idx);
                            return alertReturnFalse("500MB를 넘는 파일은 웹하드를 이용해주세요.");
                        }

                        document.getElementById(listId).innerHTML =
                            "<div id=\"" + file.id + "\">" +
                            file.name + " (" +
                            plupload.formatSize(file.size) +
                            ")<b></b>" +
                            "&nbsp;" +
                            "<img src=\"/design_template/images/common/btn_circle_x_red.png\"" +
                            "     id=\"work_file_del_" + idx + "\"" +
                            "     file_seqno=\"\"" +
                            "     alt=\"X\"" +
                            "     onclick=\"removeFile('" + idx + "', '', 2);\"" +
                            "     style=\"cursor:pointer; top:-3px; position:relative;\" /></div>";
                    });

                    var $obj = $("#table_" + idx);
                    var seq =  $obj.find("input[type='hidden'][name='seq[]']").val();
                    uploadFile(idx, seq);
                },
                FilesRemoved : function(up, files) {
                    document.getElementById(listId).innerHTML = "<blink class=\"_blink\">파일을 업로드 해주세요.</blink>";
                    fileFlagArr[idx] = false;
                    $("#work_file_seqno_" + idx).val('');
                },
                UploadProgress : function(up, file) {
                    document.getElementById(file.id)
                        .getElementsByTagName("b")[0]
                        .innerHTML = "<span style=\"font-weight:bold;\">" + file.percent + "%</span>";
                },
                FileUploaded : function(up, file, response) {
                    var jsonObj   = JSON.parse(response.response);
                    var fileSeqno = jsonObj.file_seqno;
                    var operSys   = jsonObj.oper_sys;

                    if (!jsonObj.success) {
                        return alertReturnFalse(jsonObj.message);
                    }

                    if (checkBlank(fileSeqno) || checkBlank(operSys)) {
                        return alertReturnFalse("주문파일 업로드에 실패했습니다.\n주문파일을 다시 올려주세요.");
                    }

                    $("#" + delId).attr(
                        {"onclick"    : "removeFile('" + idx + "', '" + fileSeqno + "', 2);",
                            "file_seqno" : fileSeqno}
                    );

                    fileFlagArr[idx] = true;

                    $("#work_file_seqno_" + idx).val(fileSeqno);
                    $("#oper_sys_" + idx).val(operSys);
                },
                Error : function(up, err) {
                    if (checkBlank(err.code) === false &&
                        parseInt(err.code) === -600) {
                        showWorkFileTr(false, idx);
                        return alertReturnFalse("500MB를 넘는 파일은 웹하드를 이용해주세요.");
                    }

                    document.getElementById(listId).innerHTML +=
                        "\nError #" + err.code + ": " + err.message;
                }
            }
        });

        uploader.init();
        uploaderArr[idx] = uploader;
    });
    setTimeout(function () {
        $('.reciept > dt input[type=radio]:checked').click();
        getMemberInfo();
    }, 1);

    $('.reciept > dt input[type=radio]').on('click', function () {
        $(this).closest('dl').children('dd.on').removeClass('on')
            .find('select, input').attr('disabled', true);
        $(this).closest('dt').next().addClass('on');
        $(this).closest('dt').next().find('select, input').attr('disabled', false);
    });

    $('.reciept > dd.tax input[type=radio]').on('click', function () {
        $(this).closest('dd')
            .find('input[type=text]')
            .attr('readonly', !$(this).hasClass('_edit'));
        if ($(this).hasClass('_new')) {
            $(this).closest('dd').find('input[type=text]').val('');
        }
    });

    setUnSelectedValue();

    // 직배일 경우 직배쪽에 전부 세팅
    $("#to_1").hide();
    if($("#direct_items").length > 0) {
        var unselected = $("#unselected_product").val();
        var seqArr = unselected.split("|");
        var arrLen = seqArr.length - 1;

        for (var i = 0; i < arrLen; i++) {
            var seq = seqArr[i];
            var name = $("#title_td_" + seq).text();

            $("#direct_items").append("<li class='_selected' name='selected_"+seq+"' seq='"+ seq +"'><span>" + name + "</span><button type=\"button\" class=\"del\" onclick=\"removeSelected('direct', '" + seq + "');\">삭제</button></li>");
        }

    }
    setUnSelectedValue();

    $("td .uploaded_work_file").parent().next().find("._blink").html('');

    // 파일을 업로드 해주세요. 깜빡거리는 함수.
    startBlink();
    setDlvrReq('1', $("#to_1_dlvr_req_sel"));
    changeFrom("memb");
    $("input[type='radio'][name='fromPreset']:eq(0)").prop("checked", true);
});

/**
 * @brief 깜빡거리는거 시작하는 함수
 */
var startBlink = function() {
    setInterval(doBlink, 700);
};

/**
 * @brief 존재하는 blink 태그에 깜빡거리는 부분
 */
var doBlink = function() {
    var length = $("._blink").length;

    for (var i = 0; i < length; i++) {
        var $obj = $($("._blink")[i]);

        if ($obj.css("display") === "none") {
            $obj.show();
        } else {
            $obj.hide();
        }
    }
};

/**
 * @brief 결제정보로 넘어가는 상품명 수정
 */
var modiProductNm = function() {
    var name = '';
    $("._name").each(function() {
        name += $(this).text() + ", ";
    });
    name = name.substr(0, name.length - 2);
    name = encodeURIComponent(name);

    $("#EP_product_nm").val(name);
};

/**
 * @brief 작업파일 부분 삭제
 *
 * @param idx   = 업로드 위치
 * @param seqno = 주문 파일 일련번호
 * @param flag  = uploader.removeFile 여부
 */
var removeFile = function(idx, seqno, flag) {
    if (checkBlank(seqno) === true) {
        var uploader = uploaderArr[idx];
        var files = uploader.files;
        uploader.removeFile(files[0]);

        return false;
    }

    if (flag > 0) {
        if (confirm("작업파일을 삭제하시겠습니까?" +
                "\n삭제된 파일은 복구되지 않습니다.") === false) {
            return false;
        }
    }

    var orderSeqno = '';
    $("#table_" + idx).find("input[type='hidden'][name='seq[]']").each(function() {
        orderSeqno += $(this).val() + '|';
    });
    orderSeqno = orderSeqno.substr(0, orderSeqno.length - 1);

    var url = "/proc/order/delete_order_file.php";
    var data = {
        "order_seqno" : orderSeqno,
        "file_seqno"  : seqno
    };
    var callback = function(result) {
        if (checkBlank(result) === false) {
            alert(result);
            return false;
        }

        $("#uploaded_work_file_list_" + idx).html('');

        if (flag === 2) {
            var uploader = uploaderArr[idx];
            var files = uploader.files;
            uploader.removeFile(files[0]);
        } else if (flag === 1) {
            $("#work_file_list_" + idx).html("<blink class=\"_blink\">파일을 업로드 해주세요.</blink>");
        }
    };

    showMask();
    ajaxCall(url, "text", data, callback);
};

/**
 * @brief 작업파일 업로드
 *
 * @param idx   = 업로드 위치
 * @param seqno = 주문 공통 일련번호
 */
var uploadFile = function(idx, seqno) {
    var uploader = uploaderArr[idx];

    var selector = "input[type='hidden'][name='order_num_" + seqno + "']";
    var orderNum = $(selector).val();

    if ($("#onefile_yn").val() === 'O') {
        orderNum = $($(".order_num")[0]).val();
        seqno = getOrderSeqStrAll();
    }

    var url = "/proc/order/upload_file.php?" +
        "order_num=" + orderNum + '&' +
        "seqno=" + seqno;

    uploader.settings.url = url;
    uploader.start();
};

/**
 * @brief 다음 API 주소검색 함수
 *
 * @param dvs = 입력 구분값
 */
var getPostcode = function(dvs) {
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
            document.getElementById(dvs + '_zipcode').value = data.zonecode; //5자리 새우편번호 사용
            document.getElementById(dvs + '_addr').value = fullAddr;

            // 커서를 상세주소 필드로 이동한다.
            document.getElementById(dvs + '_addr_detail').focus();

            getDlvrCost.exec(dvs);
        }
    }).open();
};

/**
 * @brief 회원 주소정보, 디프린팅 주소정보 저장
 *
 * @param pos = 위치값
 * @param dvs = 라디오 버튼 구분값
 */
var loadAddrInfo = {
    "memb" : null,
    "cpn"  : null,
    "dvs"  : null,
    "pos"  : null,
    "exec" : function(pos, dvs) {
        this.dvs = dvs;
        this.pos = pos;

        var url = "/ajax/order/load_dlvr_addr_info.php";
        var data = {
            "dvs" : dvs
        };
        var callback = function(result) {
            var dvs = loadAddrInfo.dvs;
            loadAddrInfo.dvs = null;
            var pos = loadAddrInfo.pos;
            loadAddrInfo.pos = null;

            setAddrInfo(pos, result);

            loadAddrInfo[dvs] = result;

            if($("#"+pos+"_zipcode").val() != "") {
                getDlvrCost.exec(pos);
            }
        };

        ajaxCall(url, "json", data, callback);
    }
};

/**
 * @brief 보내시는 분 라디오 버튼 클릭시 해당하는 동작 실행
 *
 * @param dvs = 라디오 버튼 구분값
 */
var changeFrom = function(dvs) {
    if (dvs === "new") {
        $(".input.from").find("input[type='text']").val('');
        return false;
    }

    var pos = "from";

    if (checkBlank(loadAddrInfo[dvs]) === false) {
        setAddrInfo(pos, loadAddrInfo[dvs]);
        return false;
    }

    loadAddrInfo.exec(pos, dvs);
};

/**
 * @brief 받으시는 분 라디오 버튼 클릭시 해당하는 동작 실행
 *
 * @param pos = 위치값
 * @param dvs = 라디오 버튼 구분값
 */
var changeTo = function(pos, dvs) {
    pos = "to_" + pos;
    if (dvs === "new") {
        $("#" + pos).find("input[type='text']").val('');
        return false;
    }

    if (checkBlank(loadAddrInfo.memb) === true) {
        loadAddrInfo.exec(pos, dvs);

        return false;
    }

    setAddrInfo(pos, loadAddrInfo.memb);

    if($("#"+pos+"_zipcode").val() != "") {
        getDlvrCost.exec(pos);
    }
};

/**
 * @brief 나의배송지 선택에서 선택버튼 클릭시
 * 주소정보 세팅
 *
 * @param obj = 선택버튼 객체
 */
var setMemberAddrInfo = {
    "pos"  : null,
    "exec" : function(obj) {
        var arr = {};

        $(obj).nextAll("input[type='hidden']").each(function() {
            var name = $(this).attr("name");
            var val = $(this).val();

            if (name === "tel_num") {
                if (checkBlank(val) === true) {
                    val = '--';
                }

                var telNumArr = val.split('-');
                arr.tel_num1 = telNumArr[0];
                arr.tel_num2 = telNumArr[1];
                arr.tel_num3 = telNumArr[2];
            } else if (name === "cell_num") {
                if (checkBlank(val) === true) {
                    val = '--';
                }

                var cellNumArr = val.split('-');
                arr.cell_num1 = cellNumArr[0];
                arr.cell_num2 = cellNumArr[1];
                arr.cell_num3 = cellNumArr[2];
            } else {
                if (checkBlank(val) === true) {
                    val = '';
                }

                arr[name] = val;
            }
        });

        setAddrInfo(this.pos, arr);

        if($("#"+this.pos+"_zipcode").val() != "") {
            getDlvrCost.exec(this.pos);
        }

        this.pos = null;
        closePopup(popupMask);
        popupMask = null;


    }
};

/**
 * @brief 보내는분 정보 세팅
 *
 * @param pos = 정보 세팅할 위치
 * @param arr = 정보 배열
 */
var setAddrInfo = function(pos, arr) {
    $("#" + pos + "_name").val(arr.name);

    $("#" + pos + "_tel_num1").val(arr.tel_num1);
    $("#" + pos + "_tel_num2").val(arr.tel_num2);
    $("#" + pos + "_tel_num3").val(arr.tel_num3);

    $("#" + pos + "_cell_num1").val(arr.cell_num1);
    $("#" + pos + "_cell_num2").val(arr.cell_num2);
    $("#" + pos + "_cell_num3").val(arr.cell_num3);

    $("#" + pos + "_zipcode").val(arr.zipcode);
    $("#" + pos + "_addr").val(arr.addr);
    $("#" + pos + "_addr_detail").val(arr.addr_detail);
};

/**
 * @brief 묶음 그룹 변경시 무게 합산해서 20kg 넘지 안도록 처리
 *
 * @param group = 그룹명
 */
var changeGroup = {
    "A" : 0,
    "B" : 0,
    "C" : 0,
    "D" : 0,
    "E" : 0,
    "exec" : function(seq, group) {
        calcGroupWeight();

        /***********************************************************
         ****** 다른 종류의 상품은 묶음배송이 안되도록
         ************************************************************/

        $("input[name='seq[]']").each(function() {
            var tmp_seq = $(this).val();
            var tmp_kind = $("#group_" + tmp_seq +" option:selected").val();

            if(group == tmp_kind && seq != tmp_seq && group != "NONE") {
                var sortcode = $("#cate_sortcode_" + seq).val();
                var tmp_sortcode = $("#cate_sortcode_" + tmp_seq).val();

                sortcode = sortcode.substring(0, 3);
                tmp_sortcode = tmp_sortcode.substring(0, 3);

                if(sortcode != tmp_sortcode) {
                    $("#group_" + seq).find("option[value='NONE']")
                        .prop("selected", true);

                    return alertReturnFalse("같은 종류의 상품만 묶음배송이 가능합니다.");
                }
            }
        });


        /********************************************************************
         ****** 묶음 시 20kg가 넘어가는 상품은 묶음배송이 불가능하도록
         *********************************************************************/

        if (this[group] > 20.0) {
            $("#group_" + seq).find("option[value='NONE']")
                .prop("selected", true);

            return alertReturnFalse("20kg이 넘는 상품은 묶을 수 없습니다.");
        }

        /********************************************************************
         ****** 묶음여부 변경 시 배송비 재측정
         *********************************************************************/

        var all_seq = "";
        var none_seq = "";
        var a_seq = "";
        var b_seq = "";
        var c_seq = "";
        var d_seq = "";
        var e_seq = "";

        $("input[name='seq[]']").each(function() {
            var seq = $(this).val();
            var kind = $("#group_" + seq +" option:selected").val();

            all_seq += seq + "|";

            if(kind == 'NONE') {
                none_seq += seq + "|";
            } else if(kind == 'A') {
                a_seq += seq + "|"
            } else if(kind == 'B') {
                b_seq += seq + "|"
            } else if(kind == 'C') {
                c_seq += seq + "|"
            } else if(kind == 'D') {
                d_seq += seq + "|"
            } else if(kind == 'E') {
                e_seq += seq + "|"
            }
        });

        if(all_seq != "") {
            all_seq = all_seq.slice(0,-1);
        }

        if(none_seq != "") {
            none_seq = none_seq.slice(0,-1);
        }

        if(a_seq != "") {
            a_seq = a_seq.slice(0,-1);
        }

        if(b_seq != "") {
            b_seq = b_seq.slice(0,-1);
        }

        if(c_seq != "") {
            c_seq = c_seq.slice(0,-1);
        }

        if(d_seq != "") {
            d_seq = d_seq.slice(0,-1);
        }

        if(e_seq != "") {
            e_seq = e_seq.slice(0,-1);
        }


        /*********************************************************************
         ****** 그룹별 배송방법 설정
         *********************************************************************/

        var dlvr_way_none = "";
        var dlvr_way_a = "";
        var dlvr_way_b = "";
        var dlvr_way_c = "";
        var dlvr_way_d = "";
        var dlvr_way_e = "";

        var zipcode_none = "";
        var zipcode_a = "";
        var zipcode_b = "";
        var zipcode_c = "";
        var zipcode_d = "";
        var zipcode_e = "";

        var tableLength = getToLength();

        for (var i = 1; i <= tableLength; i++) {
            if($("#to_" + i + " #orderGroupNONE:radio:checked").val()) {
                dlvr_way_none = $("#to_" + i + "_dlvr_way option:selected").val();
                zipcode_none = $("#to_" + i + "_zipcode").val();
            }

            if($("#to_" + i + " #orderGroupA:radio:checked").val()) {
                dlvr_way_a = $("#to_" + i + "_dlvr_way option:selected").val();
                zipcode_a = $("#to_" + i + "_zipcode").val();
            }

            if($("#to_" + i + " #orderGroupB:radio:checked").val()) {
                dlvr_way_b = $("#to_" + i + "_dlvr_way option:selected").val();
                zipcode_b = $("#to_" + i + "_zipcode").val();
            }

            if($("#to_" + i + " #orderGroupC:radio:checked").val()) {
                dlvr_way_c = $("#to_" + i + "_dlvr_way option:selected").val();
                zipcode_c = $("#to_" + i + "_zipcode").val();
            }

            if($("#to_" + i + " #orderGroupD:radio:checked").val()) {
                dlvr_way_d = $("#to_" + i + "_dlvr_way option:selected").val();
                zipcode_d = $("#to_" + i + "_zipcode").val();
            }

            if($("#to_" + i + " #orderGroupE:radio:checked").val()) {
                dlvr_way_e = $("#to_" + i + "_dlvr_way option:selected").val();
                zipcode_e = $("#to_" + i + "_zipcode").val();
            }
        }

        var url = "/ajax/order/load_dlvr_cost_info.php";
        var data = {
            "seq_all" : all_seq,
            "seq_none" : none_seq,
            "dlvr_way_none" : dlvr_way_none,
            "zipcode_none" : zipcode_none,
            "seq_a" : a_seq,
            "dlvr_way_a" : dlvr_way_a,
            "zipcode_a" : zipcode_a,
            "seq_b" : b_seq,
            "dlvr_way_b" : dlvr_way_b,
            "zipcode_b" : zipcode_b,
            "seq_c" : c_seq,
            "dlvr_way_c" : dlvr_way_c,
            "zipcode_c" : zipcode_c,
            "seq_d" : d_seq,
            "dlvr_way_d" : dlvr_way_d,
            "zipcode_d" : zipcode_d,
            "seq_e" : e_seq,
            "dlvr_way_e" : dlvr_way_e,
            "zipcode_e" : zipcode_e
        };

        var callback = function(result) {
            var price = result.cover.all;
            if(price == "0") {
                alert("퀵을 이용할 수 없는 지역입니다.");

            } else {
                $("#orderGroupNONE").attr("price", result.cover.none);
                $("#orderGroupA").attr("price", result.cover.a);
                $("#orderGroupB").attr("price", result.cover.b);
                $("#orderGroupC").attr("price", result.cover.c);
                $("#orderGroupD").attr("price", result.cover.d);
                $("#orderGroupE").attr("price", result.cover.e);

                $("#to_1_dlvr_price").attr("value", result.cover.all);

                calcPrice();
            }
        };

        ajaxCall(url, "json", data, callback);
    }
};

/**
 * @brief 각 그룹별 무게 계산
 */
var calcGroupWeight = function() {
    changeGroup.A = 0;
    changeGroup.B = 0;
    changeGroup.C = 0;
    changeGroup.D = 0;
    changeGroup.E = 0;

    $("._deliveryGroup").each(function () {
        var seq = $(this).attr("id").split('_')[1];

        if ($(this).prop("disabled") === false) {
            var group = $(this).find("option:selected").val();

            if (group === "NONE") {
                return true;
            }

            var weight = parseFloat($("#expec_weight_" + seq).val());
            changeGroup[group] += weight;
        }
    });
};

/**
 * @brief 원파일 업체에서 삭제버튼 클릭시 주문서 작성에서 삭제하고
 * 가격 재계산, 순번 재설정 하는 함수
 *
 * @param idx = 삭제되는 tr 인덱스
 */
var removeOrder = function(dvs, idx) {
    // 해당 tr / table 삭제
    var $obj = null;
    var length = 0;
    if (dvs === 'O') {
        $obj = $("#tr_" + idx).parent("tbody");
        length = $("tr._orderDetails").length;
    } else {
        $obj = $("#table_" + idx);
        length = $("table.fileUploads").length;
    }

    removeObj($obj, length);

    // 업로더 객체 삭제
    uploaderArr[idx] = null;
    fileFlagArr[idx] = '-';
    // idx 재설정
    $(".idx").each(function(i) {
        $(this).html(i + 1);
    });

    //상품을 삭제할경우 서버에서 각 상품에 대한 배송비를 다시 가져와야한다
    var seq =  $obj.find("input[type='hidden'][name='seq[]']").val(); //////////////////
    var to = $("#selected_"+seq).parents('table').attr('id');
    $("li[name='selected_" + seq + "']").remove();
    getDlvrCost.exec(to);

    setUnSelectedValue();
    setToUnSelectedValue();

    // 가격 재계산
    calcPrice();

    modiProductNm();
}

/**
 * @brief 가격관련 정보 재계산
 */
var calcPrice = function() {
    var dlvrPrice = 0;
    $(".input.to.addr").each(function(idx) {
        idx++;
        dlvrPrice += Number($("#to_"+idx+"_dlvr_price").attr('value'));
    });
    $("#dlvr_price").html(dlvrPrice.format());

    // 회원 등급할인 재계산
    var gradeSalePrice = 0;
    /*
    $(".grade_sale_price").each(function() {
        gradeSalePrice += parseFloat($(this).val());
    });
    $("#grade_sale_price").html(gradeSalePrice.format());
    */

    // 주문금액 재계산
    var sellPrice = 0;
    $(".sell_price").each(function() {
        sellPrice += parseFloat($(this).val());
    });
    //sellPrice += gradeSalePrice;
    $("#sell_price").html(sellPrice.format());

    // 이벤트 금액 재계산
    var eventPrice = 0;
    $(".event_price").each(function() {
        eventPrice += parseFloat($(this).val());
    });
    $("#event_sale_price").html(eventPrice.format());

    // 쿠폰 금액 재계산
    var couponPrice = 0;
    /*
     var couponPrice = $("input[type='hidden'][name='coupon_price']").val();
     couponPrice = ceilVal(couponPrice);
     */

    // 포인트 금액 재계산
    var pointPrice = $("input[type='hidden'][name='point']").val();
    pointPrice = ceilVal(pointPrice);
    var maxPointPrice = sellPrice + dlvrPrice + gradeSalePrice + couponPrice;

    if (maxPointPrice < pointPrice) {
        $("input[type='hidden'][name='point']").val(maxPointPrice);
        $("#point").html(maxPointPrice.format());
        pointPrice = maxPointPrice;
    }

    // 주문금액합계 재계산
    var sumPrice = sellPrice + dlvrPrice;
    //$("input[type='hidden'][name='sum_order_price']").val(sumPrice);
    //$("#sum_price").html(sumPrice.format());

    // 할인금액합계 재계산
    var sumDiscount = gradeSalePrice -
        eventPrice -
        pointPrice -
        couponPrice;
    //$("input[type='hidden'][name='sum_discount_price']").val(sumDiscount);
    //$("#sum_discount").html(sumDiscount.format());

    // 최종 결제금액 재계산
    var payPrice = sumPrice + sumDiscount;
    $("input[type='hidden'][name='sum_pay_price']").val(payPrice);
    $("#pay_price").html(payPrice.format());
    $("#pay_price_summary").html(payPrice.format());

    // 주문 부족금액 재계산
    var prepayPrice = $("input[type='hidden'][name='prepay_price']").val();
    prepayPrice = parseFloat(prepayPrice.replace(/,/g, ''));
    var orderLackPrice = prepayPrice - (sumDiscount + sumPrice);

    $("input[type='hidden'][name='order_lack_price']").val(orderLackPrice);

    if (orderLackPrice >= 0) {
        $("#order_lack_price").html(0);
    } else {
        $("#order_lack_price").html(orderLackPrice.format());
    }
};

/**
 * @brief 주문 목록을 실제로 삭제하는 함수
 *
 * @param $obj = 삭제대상 객체
 * @param length = 삭제할 객체 개수
 */
var removeObj = function($obj, length) {
    if (length === 1) {
        alert("남은 주문이 한 개일 경우 삭제할 수 없습니다.");
        return false;
    }

    var str  = "주문에서 삭제 하시겠습니까?"
    str += "\n(장바구니에 담긴 상태는 유지됩니다.)";

    if (confirm(str) === false) {
        return false;
    }

    $obj.remove();

    return true;
}

/**
 * @brief 배송방법 변경시 배송비 재계산
 *
 * @param idx = 위치 인덱스
 */
var loadDlvrPrice = function() {
    var first_table = $(".list").attr('id');
    var seq =  $("#"+first_table).find("input[type='hidden'][name='seq[]']").val();
    var group = $("#group_" + seq + ">option:selected").val();
    changeGroup.exec(seq, group);
};

/**
 * @brief 나의배송지로 등록 팝업에서 등록버튼 클릭시
 */
var regiDlvrAddr = {
    "pos"  : null,
    "exec" : function() {
        var pos = this.pos;

        var dlvrName = $("#dlvr_name").val();
        var recei = $("#" + pos + "_name").val();

        var telNum1 = $("#" + pos + "_tel_num1").val();
        var telNum2 = $("#" + pos + "_tel_num2").val();
        var telNum3 = $("#" + pos + "_tel_num3").val();

        var telNum = '';
        if (checkBlank(telNum1) === false) {
            if (checkBlank(telNum2) === false) {
                if (checkBlank(telNum3) === false) {
                    telNum += telNum1;
                    telNum += '-' + telNum2;
                    telNum += '-' + telNum3;
                }
            }
        }

        var cellNum1 = $("#" + pos + "_cell_num1").val();
        var cellNum2 = $("#" + pos + "_cell_num2").val();
        var cellNum3 = $("#" + pos + "_cell_num3").val();

        var cellNum = '';
        if (checkBlank(cellNum1) === false) {
            if (checkBlank(cellNum2) === false) {
                if (checkBlank(cellNum3) === false) {
                    cellNum += cellNum1;
                    cellNum += '-' + cellNum2;
                    cellNum += '-' + cellNum3;
                }
            }
        }

        if (checkBlank(fromTelNum) && checkBlank(fromCellNum)) {
            return alertReturnFalse("연락처나 휴대전화를 입력해주세요.");
            closePopup(popupMask);
        }

        var zipcode    = $("#" + pos + "_zipcode").val();
        var addr       = $("#" + pos + "_addr").val();
        var addrDetail = $("#" + pos + "_addr_detail").val();

        var url = "/proc/order/regi_dlvr_addr.php";
        var data = {
            "dlvr_name"   : escapeHtml(dlvrName),
            "recei"       : escapeHtml(recei),
            "tel_num"     : escapeHtml(telNum),
            "cell_num"    : escapeHtml(cellNum),
            "zipcode"     : escapeHtml(zipcode),
            "addr"        : escapeHtml(addr),
            "addr_detail" : escapeHtml(addrDetail)
        };
        var callback = function(result) {
            if (result === 'F') {
                return alertReturnFalse("나의배송지 등록에 실패했습니다.");
            }

            alert("나의배송지가 등록되었습니다.");

            closePopup(popupMask);
            popupMask = null;
        };

        ajaxCall(url, "text", data, callback);

        this.pos = null;
    }
};

/**
 * @brief 나의배송지 선택 버튼 클릭시 레이어 팝업 출력
 *
 * @param pos = 테이블 위치
 */
var showDlvrAddrListPop = function(pos) {
    setMemberAddrInfo.pos = pos;

    var url = "/ajax/order/load_dlvr_addr_list_pop.php";
    popupMask = layerPopup("l_addressList", url);
};

/**
 * @brief 나의배송지로 등록 버튼 클릭시 레이어 팝업 출력
 */
var showDlvrAddrRegiPop = function(pos) {
    var zipcode = $("#" + pos + "_zipcode").val();
    var addr       = $("#" + pos + "_addr").val();
    var addrDetail = $("#" + pos + "_addr_detail").val();

    if (checkBlank(zipcode) === true ||
        checkBlank(addr) === true ||
        checkBlank(addrDetail) === true) {
        return alertReturnFalse("주소 정보를 입력해주세요.");
    }

    regiDlvrAddr.pos = pos;

    var url = "/ajax/order/load_dlvr_addr_regi_pop.html";
    popupMask = layerPopup("l_addressRegister", url);
};

/**
 * @brief 포인트 사용 버튼 클릭시 레이어 팝업 출력
 */
var showPointPop = function() {
    var pay_price = $("#sum_pay_price").val();
    var url = "/ajax/order/load_point_pop.php?pay_price=" + pay_price;
    popupMask = layerPopup("l_point", url);
};

/**
 * @brief 쿠폰 사용 버튼 클릭시 레이어 팝업 출력
 */
var showCouponPop = function() {
    var seq = getOrderSeqStrAll();

    var url = "/ajax/order/load_coupon_pop.php?seq=" + seq;
    popupMask = layerPopup("l_coupon", url);
};

/**
 * @brief 포인트 사용금액 적용
 */
var setPointPrice = function() {
    var ownPoint = parseInt($("#own_point").val().replace(/,/g, ''));
    var usePoint = parseInt($("#use_point").val());

    if (ownPoint < usePoint) {
        return alertReturnFalse("보유 포인트보다 사용 포인트가 큽니다.");
    }

    usePoint = ceilVal(usePoint);

    $("input[type='hidden'][name='point']").val(usePoint);
    $("#point").html(usePoint.format());

    closePopup(popupMask);
    popupMask = null;

    calcPrice();
};

/**
 * @brief 회원정보 정보 가져옴
 */
var getMemberInfo = function() {

    var url = "/json/order/load_member_info.php";
    var data = {};
    var callback = function(result) {
        $("#receipt_member_name").val(result.member_name);
        $("#supply_corp").val(result.supply_corp);
        $("#crn").val(result.crn);
        $("#repre_name").val(result.repre_name);
        $("#zipcode").val(result.zipcode);
        $("#addr").val(result.addr);
        $("#bc").val(result.bc);
        $("#tob").val(result.tob);
    };

    ajaxCall(url, "json", data, callback);
}

/**
 * @brief 관리사업자 radio 선택시 레이어 팝업 출력
 */
var showOrganizerPop = function() {
    var url = "/ajax/order/load_organizer_pop.php";
    popupMask = layerPopup("l_organizer", url);
};

/**
 * @brief 관리사업자 정보 가져옴
 */
var getOrganizerInfo = function() {

    var url = "/json/order/load_organizer_info.php";
    var data = {
        "seqno" : $(':radio[name="organizer_chk"]:checked').val()
    };
    var callback = function(result) {
        $("#receipt_member_name").val(result.member_name);
        $("#supply_corp").val(result.supply_corp);
        $("#crn").val(result.crn);
        $("#repre_name").val(result.repre_name);
        $("#addr").val(result.addr);
        $("#bc").val(result.bc);
        $("#tob").val(result.tob);
        closePopup(popupMask);
        popupMask = null;
    };

    ajaxCall(url, "json", data, callback);
}

/**
 * @brief 관리사업자 정보 가져옴
 */
var initPublicInput = function() {

    $("#receipt_member_name").val("");
    $("#supply_corp").val("");
    $("#crn").val("");
    $("#repre_name").val("");
    $("#zipcode").val("");
    $("#addr").val("");
    $("#bc").val("");
    $("#tob").val("");
}

/**
 * @brief 결제확인 클릭시 확인 팝업 출력
 */
var showConfirmPop = function() {
    if (validateConfirm() === false) {
        return false;
    }

    // 보내는 사람 그룹정보 생성
    var tableLength = getToLength();
    var groupInfo = "";
    for (var i = 1; i <= tableLength; i++) {
        var $obj = $("#to_" + i);

        groupInfo += "to_" + i + '=';

        $obj.find('.items').find('li').each(function() {
            groupInfo += $(this).attr("seq") + '!';
        });

        groupInfo = groupInfo.substr(0, groupInfo.length - 1);
        groupInfo += '|';
    }
    groupInfo = groupInfo.substr(0, groupInfo.length - 1);

    // 배송정보 추가
    var directNum   = $("#direct_items > ._selected").length;
    var visitInNum  = $("#visit_in_items > ._selected").length;
    var visitPilNum = $("#visit_pil_items > ._selected").length;

    if (directNum > 0) {
        if (!checkBlank(groupInfo)) {
            groupInfo += '|';
        }

        groupInfo += "direct=";
        $("#direct_items > ._selected").each(function() {
            groupInfo += $(this).attr("seq") + '!';
        });
        groupInfo = groupInfo.substr(0, groupInfo.length - 1);
    }
    if (visitInNum > 0) {
        if (!checkBlank(groupInfo)) {
            groupInfo += '|';
        }

        groupInfo += "visit_in=";
        $("#visit_in_items > ._selected").each(function() {
            groupInfo += $(this).attr("seq") + '!';
        });
        groupInfo = groupInfo.substr(0, groupInfo.length - 1);
    }
    if (visitPilNum > 0) {
        if (!checkBlank(groupInfo)) {
            groupInfo += '|';
        }

        groupInfo += "visit_pil=";
        $("#visit_pil_items > ._selected").each(function() {
            groupInfo += $(this).attr("seq") + '!';
        });
        groupInfo = groupInfo.substr(0, groupInfo.length - 1);
    }

    // to_1=A!B|to_2=NONE
    $("#to_group").val(groupInfo);

    // 팝업관련 설정 처리
    var $modalMask =  $(".modalMask.l_confirm");
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

        if (makeConfirmPopInfo() === false) {
            hideConfirmPop();
            return false;
        }

        orderTable($('.l_confirm'));

        $modalMask.addClass("_on")
            .find("button.close")
            .on("click", hideConfirmPop);
    });
};

/**
 * @brief 결제확인 팝업에 존재하는 내용을 생성한다.
 *
 * @return ret = 생성완료시 true, 일부정보 누락시 false
 */
var makeConfirmPopInfo = function() {
    var onefileYn = $("#onefile_yn").val();
    var ret = true;

    // 주문내역 생성
    var tbody = "";

    var $obj = null;
    if (onefileYn === 'O') {
        $obj = $(".list._details.order.fileUploads .info");
    } else {
        $obj = $(".list._details.order.fileUploads");
    }

    $obj.each(function(idx) {
        tbody += "\n<tbody>";

        // 주문요약정보
        var $tdArr = null;
        if (onefileYn === 'O') {
            $tdArr = $(this).find("tr>td");
        } else {
            $tdArr = $(this).find(".info>tr>td");
        }

        var seq = $($tdArr[0]).find("input[name='seq[]']").val();

        var title  = $($tdArr[2]).text().trim();
        var info   = $($tdArr[3]).find(".information").text().trim();
        var amt    = $($tdArr[4]).text().trim();
        var price  = $($tdArr[5]).text().trim();
        //var group  = findToFromSeq(seq);

        var infoTr = "";
        infoTr += "\n    <tr>";
        infoTr += "\n        <td>" + (idx + 1) + "</td>";
        infoTr += "\n        <td>" + title + "</td>";
        infoTr += "\n        <td>";
        infoTr += "\n            <ul class=\"information\"><li>" + info + "</li></ul>";
        infoTr += "\n        </td>";
        infoTr += "\n        <td>" + amt + "</td>";
        infoTr += "\n        <td>" + price + "</td>";
        //infoTr += "\n        <td>" + group + "</td>";
        infoTr += "\n        <td>";
        infoTr += "\n            <button type=\"button\" class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\" onclick=\"openOrderDetail('" + idx + "', '" + seq + "', '6', 'pop_detail');\"><img src=\"/design_template/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>";
        infoTr += "\n            <button type=\"button\" class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\" onclick=\"closeOrderDetail('" + idx + "');\"><img src=\"/design_template/images/common/btn_table_circle_top_green.png\" alt=\"▲\"></button>";
        infoTr += "\n        </td>";
        infoTr += "\n    </tr>";

        var detailTr = "";
        detailTr += "\n    <tr class=\"_orderDetails\" id=\"pop_detail" + idx + "\">";
        detailTr += "\n    </tr>";

        tbody += infoTr;
        tbody += detailTr;
        tbody += "\n</tbody>";
    });

    $("#confirm_table").find("tbody").remove();
    $("#confirm_table").append(tbody);

    // 주문자 정보
    var fromName = $("#from_name").val();

    var fromTelNum1 = $("#from_tel_num1").val();
    var fromTelNum2 = $("#from_tel_num2").val();
    var fromTelNum3 = $("#from_tel_num3").val();

    var fromTelNum = '';
    if (checkBlank(fromTelNum1) === false) {
        if (checkBlank(fromTelNum2) === false) {
            if (checkBlank(fromTelNum3) === false) {
                fromTelNum += fromTelNum1;
                fromTelNum += '-' + fromTelNum2;
                fromTelNum += '-' + fromTelNum3;
            }
        }
    }

    var fromCellNum1 = $("#from_cell_num1").val();
    var fromCellNum2 = $("#from_cell_num2").val();
    var fromCellNum3 = $("#from_cell_num3").val();

    var fromCellNum = '';
    if (checkBlank(fromCellNum1) === false) {
        if (checkBlank(fromCellNum2) === false) {
            if (checkBlank(fromCellNum3) === false) {
                fromCellNum += fromCellNum1;
                fromCellNum += '-' + fromCellNum2;
                fromCellNum += '-' + fromCellNum3;
            }
        }
    }

    if (checkBlank(fromTelNum) && checkBlank(fromCellNum)) {
        return alertReturnFalse("보내시는분 연락처나 휴대전화를 입력해주세요.");
    }

    var fromZipcode = $("#from_zipcode").val();
    var fromAddr = $("#from_addr").val() + ' ' + $("#from_addr_detail").val();

    $("#confirm_from_name").html(fromName);
    $("#confirm_from_tel_num").html(fromTelNum);
    $("#confirm_from_cell_num").html(fromCellNum);
    $("#confirm_from_zipcode").html(fromZipcode);
    $("#confirm_from_addr").html(fromAddr);

    // 받는 사람 정보
    var to = "";
    var toLength = getToLength();

    for (var i = 1; i <= toLength; i++) {
        var toIdx = "to_" + i.toString();

        var toDlvrWay    = $("#" + toIdx + "_dlvr_way>option:selected").text();
        var toDlvrSumWay = $("input[name='" + toIdx + "_dlvr_sum_way']:checked").val();
        var toName = $("#" + toIdx + "_name").val();
        if (toDlvrSumWay === "01") {
            toDlvrWay += "(선불)";
        } else {
            toDlvrWay += "(착불)";
        }

        var toTelNum1 = $("#" + toIdx + "_tel_num1").val();
        var toTelNum2 = $("#" + toIdx + "_tel_num2").val();
        var toTelNum3 = $("#" + toIdx + "_tel_num3").val();

        var toTelNum = '';
        if (checkBlank(toTelNum1) === false) {
            if (checkBlank(toTelNum2) === false) {
                if (checkBlank(toTelNum3) === false) {
                    toTelNum += toTelNum1;
                    toTelNum += '-' + toTelNum2;
                    toTelNum += '-' + toTelNum3;
                }
            }
        }

        var toCellNum1 = $("#" + toIdx + "_cell_num1").val();
        var toCellNum2 = $("#" + toIdx + "_cell_num2").val();
        var toCellNum3 = $("#" + toIdx + "_cell_num3").val();

        var toCellNum = '';
        if (checkBlank(toCellNum1) === false) {
            if (checkBlank(toCellNum2) === false) {
                if (checkBlank(toCellNum3) === false) {
                    toCellNum += toCellNum1;
                    toCellNum += '-' + toCellNum2;
                    toCellNum += '-' + toCellNum3;
                }
            }
        }

        /*
         if (checkBlank(toTelNum) && checkBlank(toCellNum)) {
         return alertReturnFalse("받으시는분 연락처나 휴대전화를 입력해주세요.");
         }
         */

        var toZipcode = $("#" + toIdx + "_zipcode").val();
        var toAddr = $("#" + toIdx + "_addr").val() + ' ' +
            $("#" + toIdx + "_addr_detail").val();
        var toGroup = '';
        $("#" + toIdx).find("._selected").each(function() {
            toGroup += $(this).find("span").text() + ", ";
        });
        toGroup = toGroup.substr(0, toGroup.length - 2);

        var toInfo = "받는 사람 " + i + " - " + toGroup;

        to += "\n<h3 class=\"to\">" + toInfo + "</h3>";
        to += "\n<table class=\"list order\">";
        to += "\n    <colgroup>";
        to += "\n        <col width=\"80\">";
        to += "\n        <col width=\"180\">";
        to += "\n        <col width=\"120\">";
        to += "\n        <col width=\"120\">";
        to += "\n        <col>";
        to += "\n    </colgroup>";
        to += "\n    <thead>";
        to += "\n        <tr>";
        to += "\n            <th>배송방법</th>";
        to += "\n            <th>성명/상호</th>";
        to += "\n            <th>전화번호</th>";
        to += "\n            <th>휴대전화번호</th>";
        to += "\n            <th>주소</th>";
        to += "\n        </tr>";
        to += "\n    </thead>";
        to += "\n    <tbody>";
        to += "\n        <tr>";
        to += "\n            <td>" + toDlvrWay + "</td>";
        to += "\n            <td>" + toName + "</td>";
        to += "\n            <td>" + toTelNum + "</td>";
        to += "\n            <td>" + toCellNum + "</td>";
        to += "\n            <td>[" + toZipcode + "] " + toAddr + "</td>";
        to += "\n        </tr>";
        to += "\n    </tbody>";
        to += "\n</table>";
    }

    $("#confirm_to").html(to);

    // 결제 정보 및 방법
    var payPrice       = parseInt($("#pay_price").text().replace(',', ''));
    var sellPrice      = parseInt($("#sell_price").text().replace(',', ''));
    var dlvrPrice      = parseInt($("#dlvr_price").text().replace(',', ''));
    var cpPrice        = parseInt($("#cp_price").val());
    var pointPrice     = parseInt($("#point").val());

    var sumPrice       = sellPrice + dlvrPrice;

    var gradeSalePrice = sumPrice - payPrice;

    var sumDiscount    = cpPrice + pointPrice + gradeSalePrice;

    var payWay         = $("input[type='radio'][name='card_pay_yn']").val();
    payWay = (payWay === 'Y') ? "카드" : "선입금";
    var prepayPrice    = $("input[type='hidden'][name='prepay_price']").val() + " 원";
    var orderLackPrice = $("#order_lack_price").text() + " 원";

    $("#confirm_sum_price").html(sumPrice.format() + " 원");
    $("#confirm_sum_discount").html(sumDiscount.format() + " 원");
    $("#confirm_pay_price").html(payPrice.format() + " 원");
    $("#confirm_pay_way").html(payWay);
    $("#confirm_prepay_price").html(prepayPrice);
    $("#confirm_order_lack_price").html(orderLackPrice);
};

/**
 * @brief 주문확인 값 검증
 */
var validateConfirm = function() {
    var ret1 = true;
    var ret2 = true;
    var onefileYn = $("#onefile_yn").val();

    // 보내시는 분 - 성명/상호 부분 체크
    if (checkBlank($("#from_name").val()) === true) {
        return alertReturnFalse("보내시는 분 성명/상호를 입력해주세요.");
    }
    // 보내시는 분 - 연락처 or 휴대전화 부분 체크
    ret1 = true;
    if (checkBlank($("#from_tel_num2").val()) === true ||
        checkBlank($("#from_tel_num3").val()) === true) {
        ret1 = false;
    }
    ret2 = true;
    if (checkBlank($("#from_cell_num2").val()) === true ||
        checkBlank($("#from_cell_num3").val()) === true) {
        ret2 = false;
    }
    if (ret1 === false && ret2 === false) {
        return alertReturnFalse("보내시는 분 연락처나 휴대전화를 입력해주세요.");
    }
    // 보내시는 분 - 주소 부분 체크
    if (checkBlank($("#from_zipcode").val()) === true ||
        checkBlank($("#from_addr").val()) === true) {
        return alertReturnFalse("보내시는 분 주소정보를 입력해주세요.");
    }
    if (checkBlank($("#from_addr_detail").val()) === true) {
        if (confirm("보내시는 분 상세주소가 입력되지 않았습니다.\n계속 진행 하시겠습니까?") === false) {
            return false;
        }
    }

    if (checkEmptyDlvrInfo()) {
        return alertReturnFalse("받으시는 분에서 주문선택-상품선택을 해주시기 바랍니다.");
    }

    var tableLength = getToLength();

    for (var i = 1; i <= tableLength; i++) {
        ret1 = true;
        ret2 = true;

        var idx = "to_" + i;

        var is_visit = $("#"+idx+"_dlvr_way").val();
        if(is_visit != "06" && is_visit != "07") {
            // 받으시는 분 - 성명/상호 부분 체크
            if (checkBlank($("#" + idx + "_name").val()) === true) {
                return alertReturnFalse("받으시는 분 성명/상호를 입력해주세요.");
            }
            // 받으시는 분 - 연락처 or 휴대전화 부분 체크
            ret1 = true;
            if (checkBlank($("#" + idx + "_tel_num2").val()) === true ||
                checkBlank($("#" + idx + "_tel_num3").val()) === true) {
                ret1 = false;
            }
            ret2 = true;
            if (checkBlank($("#" + idx + "_cell_num2").val()) === true ||
                checkBlank($("#" + idx + "_cell_num3").val()) === true) {
                ret2 = false;
            }
            if (ret1 === false && ret2 === false) {
                return alertReturnFalse("받으시는 분 연락처나 휴대전화를 입력해주세요.");
            }
            // 받으시는 분 - 주소 부분 체크
            if (checkBlank($("#" + idx + "_zipcode").val()) === true ||
                checkBlank($("#" + idx + "_addr").val()) === true) {

                return alertReturnFalse("받으시는 분 주소정보를 입력해주세요.");
            }
            if (checkBlank($("#" + idx + "_addr_detail").val()) === true) {
                if (confirm("받으시는 분 상세주소가 입력되지 않았습니다.\n계속 진행 하시겠습니까?") === false) {
                    return false;
                }
            }
        }
        if($("#unselected_product").val() != "") {
            return alertReturnFalse("주문선택 되지 않은 상품이 있습니다.");
        }
    }

    if ($("#onefile_yn").val() === 'O') {
        // 웹하드 업로드면 넘어감
        if ($("input[type='radio'][name='file_upload_dvs_1']").val() === 'N') {
            return true;
        }

        //fileFlagArr에 값이 존재할 경우 false 값이 존재하지 않고
        //fileFlagArr이 빈 배열이 아닐경우 통과
        if (fileFlagArr.length === 0) {
            // 파일 업로드 관련이 아무것도 진행되지 않은 경우
            return alertReturnFalse("작업파일을 올리지 않은 주문이 있습니다.");
        }
        for (var j in fileFlagArr) {
            var flag = fileFlagArr[j];

            if (checkBlank(flag) === true || flag === false) {
                return alertReturnFalse("작업파일을 올리지 않은 주문이 있습니다.");
            }
        }
    } else {
        // uploaded_work_file 클래스가 존재할 경우 pass
        // 없을 경우 fileFlagArr 확인해서 false 아니면 pass
        var ret = false;

        $(".order.fileUploads").each(function() {
            var $idxObj = $(this).find(".idx");
            var idx = $idxObj.attr("idx");
            var seqno = $idxObj.next().val();

            if ($("input[type='radio'][name='file_upload_dvs_" + seqno + "']:checked").val() === 'N') {
                ret = true;
                return ret;
            }

            // 사전에 올려둔 파일이 있는지 확인
            if ($("#uploaded_work_file_list_" + idx).children().length > 0) {
                var fileSeqno =
                    $("#uploaded_work_file_del_" + idx).attr("file_seqno");
                $("#work_file_seqno_" + idx).val(fileSeqno);

                ret = true;
                return ret;
            }

            if (fileFlagArr[idx] === true) {
                ret = true;
                return ret;
            }

            ret = false;
            return ret;
        });

        if (ret === false) {
            return alertReturnFalse("작업파일을 올리지 않은 주문이 있습니다.");
        }
    }

    // 주문파일 누락되는 경우 체크
    var ret = true;
    var str = '';
    $(".oper_sys").each(function() {
        if (checkBlank($(this).val())) {
	    var id = $(this).attr("id").split('_');
            id = id[id.length - 1];
            str = id + "번 주문의 작업파일을 다시 올려주세요."

            ret = false;
            return ret;
        }
    });

    if (ret === false) {
        return alertReturnFalse(str);
    }

    return true;
};

/**
 * @brief 결제확인팝업 hide
 */
var hideConfirmPop = function() {
    var $modalMask =  $(".modalMask.l_confirm");

    $modalMask.fadeOut(300, function() {
        $("body").css("overflow", "auto");
    });
};

/**
 * @brief 결제과정 진입 전 값 검증
 */
var chkValue = function() {
    var url = "/ajax/order/chk_value.php";
    var callback = function(result) {
        if (result === "NO_LOGIN") {
            alert("로그인 후 확인 가능합니다.");
            location.replace("/member/login.html");
            return false;
        }

        /*
         */
        if (result === "ERR") {
            alert("정상적인 주문이 아닙니다.\n다시 로그인 해주세요.");
            location.replace("/common/logout.php");
            return false;
        }

        if (result === "NO_FILE") {
            return alertReturnFalse("업로드된 작업파일이 없습니다.");
        }

        doPay();
    };

    ajaxCall(url, "text", $("#frm").serialize(), callback);
};

/**
 * @brief 결제 버튼 클릭시 선택한 결제방식 팝업 출력
 */
var doPay = function() {
    hideConfirmPop();

    // 카드결제여부
    var payWay = $("input[type='radio'][name='card_pay_yn']:checked").val();

    var orderLackPrice =
        $("input[type='hidden'][name='order_lack_price']").val();
    orderLackPrice = parseInt(orderLackPrice);

    // 결제금액 0원인지 확인
    if (payWay === 'Y') {
        creditCardParamSet();

        // 카드결제
        var frmPay = document.frm_pay;

        easypay_webpay(frmPay,
            "/webpay_card/web/normal/iframe_req.php",
            "hiddenifr",
            "0",
            "0",
            "iframe",
            30);
    } else {
        // 선입금일 때
        window.onbeforeunload = null;
        $("#frm").submit();
    }
};

/**
 * @brief 신용카드 결제시 파라미터 세팅
 * UTF-8을 사용하기 때문에 한글 부분은 전부 인코딩 시켜준다
 */
var creditCardParamSet = function() {
    var ts = new Date();
    ts = ts.getTime();

    // 가맹점 주문번호(EP_order_no)
    $("#EP_order_no").val(ts);
    $("#card_order_num").val(ts);

    orderLackPrice =
        $("input[type='hidden'][name='sum_pay_price']").val();

    $("#EP_product_amt").val(orderLackPrice);
};

/**
 * @brief 결제 승인요청 submit
 */
var reqSubmit = function() {
    showBgMask();

    var frm_pay = document.frm_pay;
    frm_pay.target = "iframe_pay";
    frm_pay.action = "/webpay_card/web/easypay_request.php";
    frm_pay.submit();
}

/**
 * @brief 결제완료 페이지로 이동
 */
var goComplete = function() {
    hideBgMask();

    var $obj = $("#iframe_pay").contents().find("body");
    var resCd  = $obj.find("#res_cd").val();
    var amount = $obj.find("#amount").val();

    if (resCd !== "0000") {
        return alertReturnFalse($obj.find("#res_msg").val());
    }

    var orderLackPrice =
        $("input[type='hidden'][name='order_lack_price']").val();
    orderLackPrice = orderLackPrice.replace(/,/g, '').replace(/-/g, '');

    var payWay = $("input[type='radio'][name='card_pay_yn']:checked").val();
    if (payWay === 'Y') {
        orderLackPrice = amount;
    }

    if (amount !== orderLackPrice) {
        return alertReturnFalse("결제 승인금액이 실제와 상이합니다.\n관리자에게 문의하세요.");
    }

    window.onbeforeunload = null;

    $("#frm").submit();
};

/**
 * @brief 작업파일 업로드 구분에 따라 작업파일 부분 출력
 *
 * @param flag = 출력여부
 * @param idx  = 작업파일 부분 위치인덱스
 */
var showWorkFileTr = function(flag, idx) {
    if (flag === false) {
        $("#work_file_tr_" + idx).hide();
        $("#webhard_tr_" + idx).show();
    } else {
        $("#work_file_tr_" + idx).show();
        $("#webhard_tr_" + idx).hide();
    }
};

/**
 * @brief 웹하드페이지로 이동
 *
 * @return 웹하드 팝업 오픈
 */
var goWebhardPage = function() {

    //window.open("/ajax/order/load_webhard_page.php", "POP");
    //window.open("http://www.webhard.co.kr/webII/page/member/?load=2", "POP");
    window.open("http://www.webhard.co.kr", "POP");
}

/**
 * @brief 일련번호 문자열 생성, 조건없음
 *
 * @return 문자열
 */
var getOrderSeqStrAll = function() {
    var seqno = ''

    $("input[type='hidden'][name='seq[]']").each(function() {
        seqno += $(this).val() + '|';
    });

    seqno = seqno.substr(0, seqno.length - 1);

    return seqno;
};

/**
 * @brief 일련번호 문자열 생성
 *
 * @return 문자열
 */
var getOrderSeqStr = function(to) {
    var selector = "#unselected_product";

    if (to.indexOf("to_") > -1) {
        // 받으시는 분에서 선택했을 경우
        // 배송방법에 따라 가져오는 selector 틀려짐
        var dlvrWay = $('#' + to + "_dlvr_way").val();

        if (dlvrWay === "01") {
            selector = "#parcel_unselected_product";
        } else {
            selector = "#quick_unselected_product";
        }
    }

    var seqno = $(selector).val();
    seqno = seqno.substr(0, seqno.length - 1);

    return seqno;
};

var getSelecedSeqStr = function(to) {
    var selcted_seq = '';

    $("#"+to).find(".items").find("li").each(function() {
        selcted_seq += $(this).attr('seq') + "|";
    });

    selcted_seq = selcted_seq.substr(0, selcted_seq.length - 1);

    return selcted_seq;
};

/**
 * @brief 쿠폰 팝업에서 적용버튼 클릭시
 */
var aplyCoupon = function() {
};


/***********************************************************************************
 *** 택배가격 계산, calcPrice() 호출시 첫부분에서 택배가를 계산하는데 쓰인다
 ***********************************************************************************/

var setDlvrCost = function() {
    // 각 테이블마다 선택되어있는 라디오버튼을 확인해서
    // 가격을 to_idx_dlvrcost에 변경

    var tableLength = getToLength();
    var to = new Array(0,0,0,0,0);
    var all = 0;

    for (var i = 1; i <= tableLength; i++) {
        checked = $("#to_" + i + " #orderGroupNONE:radio:checked").val();
        dlvr_way = $(":radio[name=to_" + i + "_dlvr_sum_way]:checked").val();
        if(checked && dlvr_way == "01") {
            var a = $("#orderGroupNONE").attr('price');
            to[i-1] += Number(a);
            all += Number(a);
        }

        checked = $("#to_" + i + " #orderGroupA:radio:checked").val();
        if(checked && dlvr_way == "01") {
            var a = $("#orderGroupA").attr('price');
            to[i-1] += Number(a);
            all += Number(a);
        }

        checked = $("#to_" + i + " #orderGroupB:radio:checked").val();
        if(checked && dlvr_way == "01") {
            var a = $("#orderGroupB").attr('price');
            to[i-1] += Number(a);
            all += Number(a);
        }

        checked = $("#to_" + i + " #orderGroupC:radio:checked").val();
        if(checked && dlvr_way == "01") {
            var a = $("#orderGroupC").attr('price');
            to[i-1] += Number(a);
            all += Number(a);
        }

        checked = $("#to_" + i + " #orderGroupD:radio:checked").val();
        if(checked && dlvr_way == "01") {
            var a = $("#orderGroupD").attr('price');
            to[i-1] += Number(a);
            all += Number(a);
        }

        checked = $("#to_" + i + " #orderGroupE:radio:checked").val();
        if(checked && dlvr_way == "01") {
            var a = $("#orderGroupE").attr('price');
            to[i-1] += Number(a);
            all += Number(a);
        }
    }

    for (var i = 1; i <= tableLength; i++) {
        $("#to_" + i + "_dlvrcost").html("배송비 : "+to[i-1].format() + "원");
        $("#to_" + i + "_dlvr_price").val(to[i-1]);
    }

    return all;
    //$("#dlvr_price").html(all.format());
    //calcPrice();
};

//주문선택 팝업
var showSelectProductPopup = function(to) {
    // 전체 주문 seq
    var seq = getOrderSeqStr(to);

    var url = "/ajax/order/load_product_pop.php?";
    url += "seq=" + seq;
    url += "&to=" + to;
    popupMask = layerPopup("l_productList", url);
};

var changestate = function() {
    if($("#cb_chooseorder").is(':checked')) {
        $("input:checkbox[name='product_ck']").prop("checked", true);
    } else {
        $("input:checkbox[name='product_ck']").prop("checked", false);
    }
};

/**
 * @brief 배송방법이나 받으시는 분 상품선택에 항목 추가
 */
var setProducts = function(to) {
    //$("#"+to).find(".items").find("li").remove();

    $(".popupProducts").each(function() {
        if ($(this).is(':checked')) {
            var seq = $(this).val();
            var name = $("#title_td_" + seq).text();
            $('#'+to).find('.items').append("<li class='_selected' name='selected_"+seq+"' seq='"+ seq +"'><span>" + name + "</span><button type=\"button\" class=\"del\" onclick=\"removeSelected('" + to + "', '" + seq +"');\">삭제</button></li>");
        }
    });

    setUnSelectedValue();
    setToUnSelectedValue();

    closePopup(popupMask);
    popupMask = null;

    var parcelNum = $("#parcel_items > ._selected").length;
    var quickNum  = $("#quick_items > ._selected").length;
    
    if (to.indexOf("to_") === -1) {
        if (parcelNum > 0 || quickNum > 0) {
            setToDlvrWay();
            $(".input.to.addr").show();
        }
    }

    if($("#"+to+"_zipcode").length > 0) {
        getDlvrCost.exec(to);
    }
};

/**
 * @brief 주문선택 안된상품 체크
 */
var setUnSelectedValue = function() {
    var unselected = "";

    $("input[type='hidden'][name='seq[]']").each(function() {
        unselected += $(this).val() + "|";
    });

    $("._selected").each(function() {
        var selected = $(this).attr('seq')+"|";
        unselected = unselected.replace(selected, "");
    });

    $("#unselected_product").val(unselected);
};

/**
 * @brief 받으시는분 주문선택 안된상품 체크
 */
var setToUnSelectedValue = function() {
    var unselected = "";
    var selected = "";

    $("#parcel_items > ._selected").each(function() {
        var seq = $(this).attr('seq');
        unselected += seq + '|';
    });
    $(".input.to.addr ._selected").each(function() {
        var seq = $(this).attr('seq');
        var selected = seq + '|';
        unselected = unselected.replace(selected, "");
    });
    //unselected = unselected.substr(0, unselected.length);
    $("#parcel_unselected_product").val(unselected);

    unselected = '';
    $("#quick_items > ._selected").each(function() {
        var seq = $(this).attr('seq');
        unselected += seq + '|';
    });
    $(".input.to.addr ._selected").each(function() {
        var seq = $(this).attr('seq');
        var selected = seq + '|';
        unselected = unselected.replace(selected, "");
    });
    //unselected = unselected.substr(0, unselected.length - 1);
    $("#quick_unselected_product").val(unselected);
};

/**
 * @brief 상품선택 삭제
 *
 * @param dvs = 삭제위치 구분
 * @param seq = 일련번호
 */
var removeSelected = function(dvs, seq) {
    var selector = "li[name='selected_" + seq + "']";

    if (dvs.indexOf("to_") > -1) {
        selector = '#' + dvs + " li[name='selected_" + seq + "']";
    }

    $(selector).remove();
    setUnSelectedValue();
    setToUnSelectedValue();

    var parcelNum = $("#parcel_items > ._selected").length;
    var quickNum  = $("#quick_items > ._selected").length;

    setToDlvrWay();

    if (parcelNum === 0 && quickNum === 0) {
        initTo();
    }
};

/**
 * @brief 배송비 계산
 */
var getDlvrCost = {
    "exec" : function(to) {
        var seqno = "";
        var expec_weight = 0;

        $('#'+to).find('.items').find('li').each(function() {
            seqno += $(this).attr('seq') + "|";
        });

        var dlvr = $("#" + to + "_dlvr_way option:selected").val();
        var zipcode = $("#" + to + "_zipcode").val();

        if((seqno == "" || zipcode == "") && (dlvr != "06" && dlvr != "07"))
            return;

        seqno = seqno.substr(0, seqno.length - 1);

        var url = "/ajax/order/load_dlvr_cost_info.php";
        var data = {
            "seqno" : seqno,
            "dlvr_way" : dlvr,
            "zipcode" : zipcode
        };

        var callback = function(result) {
            var price_nc = result.cover.price_nc;
            var price_bl = result.cover.price_bl;
            var island_cost = result.cover.island_cost;
            var boxcount_nc = result.cover.boxcount_nc;
            var boxcount_bl = result.cover.boxcount_bl;
            var expec_weight_nc = result.cover.expec_weight_nc;
            var expec_weight_bl = result.cover.expec_weight_bl;
            var price = Number(price_nc) + Number(price_bl);
            var bl = result.cover.bl;
            var nc = result.cover.nc;

            //착불인 경우
            dlvr_way = $(':radio[name='+to+'_dlvr_sum_way]:checked').val();

            if(dlvr_way == "02") {
                $("#"+to+"_dlvr_price").attr('value', '0');
            } else {
                if(price == "0" && dlvr == "04") {
                    alert("퀵을 이용할 수 없는 지역입니다.");
                    $("#" + to + "_dlvr_way").find("option:eq(0)").prop("selected", true);

                    if($("#"+to+"_zipcode").val() != "") {
                        getDlvrCost.exec(to);
                    }

                    return;
                } else if(price == "-1") {
                    alert("퀵배송 무게한도를 초과했습니다.");
                    $("#" + to + "_dlvr_way").find("option:eq(0)").prop("selected", true);

                    if($("#"+to+"_zipcode").val() != "") {
                        getDlvrCost.exec(to);
                    }
                } else {
                    $("#" + to + "_dlvrcost").html("배송비 : " + price.format() + "원");
                    $("#" + to + "_dlvr_price").attr('value', price);
                    $("#" + to + "_bl_price").attr('value', price_bl);
                    $("#" + to + "_nc_price").attr('value', price_nc);
                    $("#" + to + "_bl_expec_weight").attr('value', expec_weight_bl);
                    $("#" + to + "_nc_expec_weight").attr('value', expec_weight_nc);
                    $("#" + to + "_bl_boxcount").attr('value', boxcount_bl);
                    $("#" + to + "_nc_boxcount").attr('value', boxcount_nc);

                    if(island_cost != "0") {
                        boxcount = Number(boxcount_bl) + Number(boxcount_nc);
                        alert("배송지가 도서산간 지역이므로 도서비용이 발생 되었습니다.\n" +
                        island_cost.format() + "(도서비용단가) X " + boxcount + "(덩어리갯수) = " +  (Number(island_cost) * Number(boxcount)).format() + "(도서비용합계)" );
                    }
                }
            }
            $("#"+to+"_bl_group").attr('value',bl);
            $("#"+to+"_nc_group").attr('value',nc);
            calcPrice();
        };

        ajaxCall(url, "json", data, callback);
    }
};

function findToFromSeq(seq) {
    var to_group = "";
    $(".input.to.addr").each(function() {
        var id = $(this).attr("id");


        bl_group = $(this).find("#"+id+"_bl_group").attr("value");
        if(bl_group.indexOf(seq) != -1) {
            to_group = id;
        }

        nc_group = $(this).find("#"+id+"_nc_group").attr("value");
        if(nc_group.indexOf(seq) != -1) {
            to_group = id;
        }
    });

    return to_group;
}

function removeTo(to) {
    var currentToNum = getToLength();

    if (currentToNum == 1) {
        resetToTable(1,1);
        calcPrice();
        return;
    }

    $("#to_"+to).find(".items").find("li").each(function() {
        var seq = $(this).attr('seq');
        $("#to_product_"+seq).attr('disabled', false);
        $("#to_product_"+seq).attr('checked', false);
    });
    $("#to_"+to).remove();

    for(; to < currentToNum; to++) {
        resetToTable(to + 1, to);
    }

    var newNum = currentToNum - 1;
    $("#addTo_" + newNum).show();

    setUnSelectedValue();
    setToUnSelectedValue();
    calcPrice();
};

function addToTable(to) {
    var parcelNum = $("#parcel_items > ._selected").length;
    var quickNum  = $("#quick_items > ._selected").length;

    if (parcelNum === 0 && quickNum === 0) {
        alert("추가 가능한 상품이 없습니다.");
        return;
    }
    copyToTable(to+1);
    $("#removeTo_" + to + 1).show();
    $("#addTo_" + to).hide();

    to = "to_" + (to+1);
};

/**
 * @brief 받으시는 분 비어있는지 확인
 */
function checkEmptyDlvrInfo() {
    var isEmpty = false;
    var parcelNum = $("#parcel_items > ._selected").length;
    var quickNum  = $("#quick_items > ._selected").length;

    if (parcelNum === 0 && quickNum === 0) {
        return isEmpty;
    }

    $(".input.to.addr .items").each(function() {
        if($(this).children().length == 0) {
            isEmpty = true;
            return false;
        }
    });
    return isEmpty;
};

/**
 * @brief 받으시는분 개수 반환, hide 된건 제외
 */
var getToLength = function() {
    var ret = 0;

    var parcelNum = $("#parcel_items > ._selected").length;
    var quickNum  = $("#quick_items > ._selected").length;

    if (parcelNum > 0 || quickNum > 0) {
        ret = $('.input.to.addr').length;
    }

    return ret;
};

/**
 * @brief 선택된 항목에 따라 배송방법 셀렉트박스 내용 설정
 */
var setToDlvrWay = function() {
    var html = '';

    var parcelNum = $("#parcel_items > ._selected").length;
    var quickNum  = $("#quick_items > ._selected").length;

    if (parcelNum > 0) {
        html += "<option value=\"01\">택배</option>";
    }

    if (quickNum > 0) {
        html += "<option value=\"04\">퀵(오토바이)</option>";
        html += "<option value=\"05\">퀵(지하철)</option>";
    }

    $(".dlvr_way").html(html);
};

/**
 * @brief 받으시는분 전체 초기화하고 숨김
 */
var initTo = function() {
    var toLength = getToLength();
    resetToTable(1, 1);
    $(".input.to.addr").hide();

    for (var i = toLength; i > 1; i--) {
        $("#to_" + to).remove();
    }
};

/**
 * @brief 배송시 요구사항 처리
 *
 * @param idx = 받으시는 분 위치
 * @param obj = 자기 객체
 */
var setDlvrReq = function(idx, obj) {
    var val = $(obj).val();
    var str = $(obj).find("option:selected").text();
    if (checkBlank(val)) {
        $("#to_" + idx + "_dlvr_req_sel").hide();
        $("#to_" + idx + "_dlvr_req").show();
        $("#to_" + idx + "_hide_dlvr_req_txt").show();
        $("#to_" + idx + "_dlvr_req").val('');

        return false;
    }

    $("#to_" + idx + "_dlvr_req").val(str);
};

/**
 * @brief 배송시 요구사항 직접입력일 때 셀렉트박스 재출력
 *
 * @param idx = 받으시는 분 위치
 * @param val = 셀렉트박스 선택값
 */
var showDlvrReqSel = function(idx) {
    $("#to_" + idx + "_dlvr_req_sel").val('1');
    setDlvrReq(idx, $("#to_" + idx + "_dlvr_req_sel"));

    $("#to_" + idx + "_dlvr_req_sel").show();
    $("#to_" + idx + "_dlvr_req").hide();
    $("#to_" + idx + "_hide_dlvr_req_txt").hide();

};
