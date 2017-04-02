/*
 * Copyright (c) 2015 Nexmotion, Inc
 * All rights reserved.
 *
 * REVISION HISTORY (reverse chronological order)
 * =============================================================================
 * 2015/09/17 김관우 수정(utf-8)
 * =============================================================================
 */

$(document).ready(function(){
});

//upload control 생성
function initUploadCtl(btnId) {
    //파일을 저장할 경로
    var targetdir = "attach/test";
    var uniqueno = $("#uniqueno").val();
    var host = "yesprinting.co.kr";
    var activeXHtml = "";
    var html5Html = "";
    var activeXUploadUrl = "/com/nexmotion/job/upload/uploadproc.php";
    var html5UploadUrl = "/com/nexmotion/job/upload/html5uploadproc.php";

	activeXHtml += " <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
    activeXHtml += " <tr>";
    activeXHtml += "     <td class=\"uploadTd01\" rowspan=\"2\">";
    activeXHtml += "         <div>";
    activeXHtml += "             <OBJECT ID=\"uploadCtl\" CLASSID=\"CLSID:81006BB3-C86B-4DEF-965D-D01EFD4694A5\" HEIGHT=\"102\" WIDTH=\"610\" codebase=\"/activex/nexupload.cab#version=1,0,0,7\" >";
	activeXHtml += "                 <param name=\"url\" value=\"" + host + "\">";
	activeXHtml += "                 <param name=\"value\" value=\"" + activeXUploadUrl + "?uniqueno=" + uniqueno + "&amp;ymd=y&amp;targetdir=" + targetdir  + ">";
	activeXHtml += "             </OBJECT>";
    activeXHtml += "         </div>";
    activeXHtml += "     </td>";
    activeXHtml += "     <td class=\"uploadTd02\">";
    activeXHtml += "         <img src=\"/design_template/[TPH_Vimages]/btn_file.gif\" onClick=\"uploadCtl.FindFiles()\" style=\"cursor:pointer\"/>";
    activeXHtml += "     </td>";
    activeXHtml += " </tr>";
    activeXHtml += " <tr>";
    activeXHtml += "     <td class=\"uploadTd03\">";
    activeXHtml += "         <img src=\"/design_template/[TPH_Vimages]/btn_sdel2.gif\" onClick=\"uploadCtl.DeleteItem()\" style=\"cursor:pointer\"/>";
    activeXHtml += "     </td>";
    activeXHtml += " </tr>";
    activeXHtml += " </table>";

	html5Html += " <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
    html5Html += " <tr>";
    html5Html += "     <td>";
    html5Html += "         <div id=\"html5_uploader\" style=\"width:720px;\">Your browser doesn't support native upload.</div>";
    html5Html += "     </td>";
    html5Html += " </tr>";
    html5Html += " </table>";

    if (ieVerChk() == true) {
        $("#uploadTable").html(html5Html);
        // Setup html5 version
        $("#html5_uploader").pluploadQueue({
            // General settings
            runtimes : 'html5',
            file_data_name : 'upload',
            url : html5UploadUrl,
            multipart : true,

            multipart_params : {
                targetdir : targetdir,
                ymd : 'y',
                uniqueno : uniqueno
            },
    
            filters : {
                //한개 파일의 최대 크기
                max_file_size : '500mb',
                //업로드 가능 확장자 목록
    		    mime_types: [
					{title : "Office files", extensions : "csv,dcx,doc,docx,hwp,pdf,ppt,pptx,txt,xls,xlsx"},
					{title : "Image files", extensions : "bmp,gif,jpeg,jpg,png,tif,tiff"},
					{title : "Zip files", extensions : "7z,alz,egg,rar,zip"}
	    	    ]
            }
        });

        var uploader = $("#html5_uploader").pluploadQueue();
        //파일 업로드 완료 시 동작하는 함수
        uploader.bind('UploadComplete', function() {
            /*
            var f = document.wform;
            f.target = '_self';
            f.action = "write_proc.php";
            f.submit();
            */
        });

        //btlID 클릭시 파일 업로드 시작
        $("#" + btnId).bind("click", function() {
            startUpload();
        });

        //업로드 시작버튼 숨김
        $('a.plupload_start').hide();
    } else {
        $("#uploadTable").html(activeXHtml);
    }
}

//plupload 사용 가능 여부 체크(ie ver >= 10)
function ieVerChk() {
    //IE 10 or Over
    if (window.atob) {    
        return true;
    } else {
        return false;
    }
}

//Html5 파일 업로드
function startUpload() {
    var uploader = $("#html5_uploader").pluploadQueue();
    uploader.start();
}
