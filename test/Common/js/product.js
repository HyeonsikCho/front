

var setAfterList = {
	"코팅" : "coating",
	"귀도리" : "rounding",
	"오시" : "impression",
	"미싱" : "dotline",
	"타공" : "punching",
	"접지" : "foldline",
	"엠보싱" : "embossing",
	"박" : "foil",
	"형압" : "press",
	"도무송" : "thomson",
	"넘버링" : "numbering",
	"재단" : "cutting",
	"제본" : "binding",
	"접착" : "bonding",
	"라미넥스" : "laminex"
}


$(document).ready(function () {
	changeData();
});

var changeData = function() {
	setAddBasicInfo();
	setAddOptInfo();
	setAddAfterInfo();

    var data ={
        "cate_sortcode" : $("#cate_bot").val(),
        "amt"           : $("#amt").val(),
        "stan_mpcode"   : $("#size").val(),
		"paper_mpcode"   : $("#paper").val(),
		"print_name"	: $("#print").val(),
		"print_purp"    : $("#print_purp").val(),
		"sortcode"    : $("#sortcode").val(),
		"opt_name_list"	:	$("#opt_name_list").val(),
		"opt_mp_list"	:	$("#opt_mp_list").val(),
		"after_name_list"	:	$("#after_name_list").val(),
		"after_mp_list"	:	$("#after_mp_list").val()
    };

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
		var url = "/test/load_price.php";
        var callback = function(result) {
			var price = result.cover.price + "원";
			var detail = Base64.decode(result.cover.detail);
            $('#total_price').html(price);
			$('#detail').html(detail);
        };
        ajaxCall(url, "json", loadPrdtPrice.data, callback);
    }
};

/**
 * @brief 수량에 해당하는 옵션가격 검색
 *
 * @param idx = 옵션 위치구분값
 * @param mpcode = 개별선택시 넘어오는 맵핑코드값
 */
var getOptPrice = function(idx, mpcode) {
    changeData();
};

var loadOptPrice = {
    "exec" : function(obj, idx, val) {
        changeData();
    }
};

var getAfterPrice = function(idx, mpcode) {
    changeData();
};

var loadAfterPrice = {
    "exec" : function(obj, idx, val) {
        changeData();
    }
};

var ajaxCall  = function(url, dataType, data, sucCallback) {
    $.ajax({
        type     : "POST",
        url      : url,
        dataType : dataType,
        data     : data,
        success  : function(result) {
            return sucCallback(result);
        }
    });
};

var setAddBasicInfo = function() {
    var id = null;
    var mpcode = "";
    var name = "";

    $("select[name='paper']").each(function() {
        id = $(this).attr("id");
        name += $("#" + id).val();
        name += '|';
    });
    name = name.substr(0, (name.length - 1));
    $("#paper").val(name);

	name = "";
	$("select[name='print']").each(function() {
        id = $(this).attr("id");
        name += $("#" + id).val();
        name += '|';
    });
    name = name.substr(0, (name.length - 1));
    $("#print").val(name);

	name = "";
	$("select[name='print_purp']").each(function() {
        id = $(this).attr("id");
        name += $("#" + id).val();
        name += '|';
    });
    name = name.substr(0, (name.length - 1));
    $("#print_purp").val(name);

	name = "";
	$("select[name='size']").each(function() {
        id = $(this).attr("id");
        name += $("#" + id).val();
        name += '|';
    });
    name = name.substr(0, (name.length - 1));
    $("#size").val(name);

	name = "";
	$("select[name='amt']").each(function() {
        id = $(this).attr("id");
        name += $("#" + id).val();
        name += '|';
    });
    name = name.substr(0, (name.length - 1));
    $("#amt").val(name);
};

/**
 * @brief 추가 옵션 정보 생성
 */
var setAddOptInfo = function() {
    var id = null;
    var mpcode = "";
    var name = "";

    $("input[name='chk_opt']").each(function() {
        if ($(this).prop("checked") === false ||
                $(this).prop("disabled") === true) {
            return true;
        }

        id = $(this).attr("id");
        mpcode += $("#" + id + "_sel").val();
        mpcode += '|';

        name += $(this).val();
        name += '|';
    });

    mpcode = mpcode.substr(0, (mpcode.length - 1));
    name = name.substr(0, (name.length - 1));

    $("#opt_mp_list").val(mpcode);
    $("#opt_name_list").val(name);
};

/**
 * @brief 추가 후공정 정보 생성
 */
var setAddAfterInfo = function() {
    var aft_name = "";
    var mpcode = "";
    var name = "";

    $("input[name='_chk_after[]']").each(function() {
        if ($(this).prop("checked") === false ||
                $(this).prop("disabled") === true) {
            return true;
        }

        aft_name = $(this).attr("value");

        mpcode += $("#_" + setAfterList[aft_name]).val();
        mpcode += '|';

        name += $(this).attr("value");
        name += '|';
    });

    mpcode = mpcode.substr(0, (mpcode.length - 1));
    name = name.substr(0, (name.length - 1));

	$("#after_name_list").val(name);
    $("#after_mp_list").val(mpcode);
};

var Base64 = {

            // private property
_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

          // public method for encoding
          encode : function (input) {
              var output = "";
              var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
              var i = 0;

              input = Base64._utf8_encode(input);

              while (i < input.length) {

                  chr1 = input.charCodeAt(i++);
                  chr2 = input.charCodeAt(i++);
                  chr3 = input.charCodeAt(i++);

                  enc1 = chr1 >> 2;
                  enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                  enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                  enc4 = chr3 & 63;

                  if (isNaN(chr2)) {
                      enc3 = enc4 = 64;
                  } else if (isNaN(chr3)) {
                      enc4 = 64;
                  }

                  output = output +
                      this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                      this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

              }

              return output;
          },

          // public method for decoding
decode : function (input) {
             var output = "";
             var chr1, chr2, chr3;
             var enc1, enc2, enc3, enc4;
             var i = 0;

             input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

             while (i < input.length) {

                 enc1 = this._keyStr.indexOf(input.charAt(i++));
                 enc2 = this._keyStr.indexOf(input.charAt(i++));
                 enc3 = this._keyStr.indexOf(input.charAt(i++));
                 enc4 = this._keyStr.indexOf(input.charAt(i++));

                 chr1 = (enc1 << 2) | (enc2 >> 4);
                 chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                 chr3 = ((enc3 & 3) << 6) | enc4;

                 output = output + String.fromCharCode(chr1);

                 if (enc3 != 64) {
                     output = output + String.fromCharCode(chr2);
                 }
                 if (enc4 != 64) {
                     output = output + String.fromCharCode(chr3);
                 }

             }

             output = Base64._utf8_decode(output);

             return output;

         },

         // private method for UTF-8 encoding
_utf8_encode : function (string) {
                   string = string.replace(/\r\n/g,"\n");
                   var utftext = "";

                   for (var n = 0; n < string.length; n++) {

                       var c = string.charCodeAt(n);

                       if (c < 128) {
                           utftext += String.fromCharCode(c);
                       }
                       else if((c > 127) && (c < 2048)) {
                           utftext += String.fromCharCode((c >> 6) | 192);
                           utftext += String.fromCharCode((c & 63) | 128);
                       }
                       else {
                           utftext += String.fromCharCode((c >> 12) | 224);
                           utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                           utftext += String.fromCharCode((c & 63) | 128);
                       }

                   }

                   return utftext;
               },

               // private method for UTF-8 decoding
_utf8_decode : function (utftext) {
                   var string = "";
                   var i = 0;
                   var c = c1 = c2 = 0;

                   while ( i < utftext.length ) {

                       c = utftext.charCodeAt(i);

                       if (c < 128) {
                           string += String.fromCharCode(c);
                           i++;
                       }
                       else if((c > 191) && (c < 224)) {
                           c2 = utftext.charCodeAt(i+1);
                           string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                           i += 2;
                       }
                       else {
                           c2 = utftext.charCodeAt(i+1);
                           c3 = utftext.charCodeAt(i+2);
                           string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                           i += 3;
                       }

                   }

                   return string;
               },

URLEncode : function (string) {
                return escape(this._utf8_encode(string));
            },

            // public method for url decoding
URLDecode : function (string) {
                return this._utf8_decode(unescape(string));
            }
        }
