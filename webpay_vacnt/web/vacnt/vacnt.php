<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>KICC EASYPAY8.0 SAMPLE</title>
<meta name="robots" content="noindex, nofollow">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link href="../css/style.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/default.js" type="text/javascript"></script>
<script type="text/javascript">

    /* 입력 자동 Setting */
    function f_init(){
        var frm_pay = document.frm_pay;

        var today = new Date();
        var year  = today.getFullYear();
        var month = today.getMonth() + 1;
        var date  = today.getDate();
        var time  = today.getTime();

        if(parseInt(month) < 10) {
            month = "0" + month;
        }

        if(parseInt(date) < 10) {
            date = "0" + date;
        }

        frm_pay.EP_mall_id.value = "T5102001"                                //가맹점ID TEST
//        frm_pay.EP_mall_id.value = "05528819"                                //가맹점ID REAL
        frm_pay.EP_order_no.value = "ORDER_" + year + month + date + time;   //가맹점주문번호
        frm_pay.EP_user_id.value = "goodpt";                           //고객ID
        frm_pay.EP_user_nm.value = "김시향";
        frm_pay.EP_user_mail.value = "master@dprinting.biz";
        frm_pay.EP_user_phone1.value = "";
        frm_pay.EP_user_phone2.value = "";
        frm_pay.EP_user_addr.value = "서울 중구 필동2가 84-12번지";
        frm_pay.EP_product_nm.value = "임의채번";
        frm_pay.EP_product_amt.value = "10";

        frm_pay.EP_expire_date.value = "" + year + month + date; // 무통장입금 입금만료일(YYYYMMDD)
        frm_pay.EP_expire_time.value = "235959";                 // 무통장입금 입금만료시간(HHMMSS)

    }

    function f_submit() {
        var frm_pay = document.frm_pay;

        var bRetVal = false;

        /*  주문정보 확인 */
        if( !frm_pay.EP_order_no.value ) 
        {
            alert("가맹점주문번호를 입력하세요!!");
            frm_pay.EP_order_no.focus();
            return;
        }

        if( !frm_pay.EP_product_amt.value ) 
        {
            alert("상품금액을 입력하세요!!");
            frm_pay.EP_product_amt.focus();
            return;
        }

        /* 결제금액 설정 */
        frm_pay.EP_tot_amt.value = frm_pay.EP_vacct_amt.value = frm_pay.EP_product_amt.value;

        /* 결제 정보 확인 */
        /* 입금만료일 확인 */
        if( !frm_pay.EP_expire_date.value ) 
        {
            alert("입금만료일을 입력하세요!!");
            frm_pay.EP_expire_date.focus();
            return;
        }
        /* 입금만료시간 확인 */
        if( !frm_pay.EP_expire_time.value ) 
        {
            alert("입금만료일을 입력하세요!!");
            frm_pay.EP_expire_time.focus();
            return;
        }

        /* 현금영수증 발급 구분에 따라 처리 */
            if( frm_pay.EP_cash_yn.value == "1" ) 
            {

                /* 현금영수증 발행용도 */
                if( frm_pay.EP_cash_issue_type.value == "01" ) 
                {
                    /* 개인 */
                    if( frm_pay.EP_cash_auth_type.value == "2" ) 
                    {

                        if( frm_pay.EP_cash_auth_value.value.length != 13 ) 
                        {
                            alert("주민등록번호를 입력하세요!!");
                            frm_pay.EP_cash_auth_value.focus();
                            return;
                        }
                    }
                    else if( frm_pay.EP_cash_auth_type.value == "3" ) 
                    {

                        if( frm_pay.EP_cash_auth_value.value.length < 10 ) 
                        {
                            alert("휴대폰번호를 입력하세요!!");
                            frm_pay.EP_cash_auth_value.focus();
                            return;
                        }
                    }
                    else 
                    {
                        alert("소득공제용은 인증구분을 주민등록번호, 휴대폰번호를 선택하세요.!!");
                        frm_pay.EP_cash_auth_type.focus();
                        return;
                    }
                }
                else 
                {
                    /* 법인 */
                    if( frm_pay.EP_cash_auth_value.value.length != 10 ) 
                    {
                        alert("사업자번호를 입력하세요!!");
                        frm_pay.EP_cash_auth_value.focus();
                        return;
                    }
                }
            }

            bRetVal = true;
        if ( bRetVal ) frm_pay.submit();
    }
</script>
</head>
<body onload="f_init();">
<form name="frm_pay" method="post" action="../easypay_request.php">

<!--------------------------->
<!-- ::: 공통 인증 요청 값 -->
<!--------------------------->

<input type="hidden" id="EP_mall_nm"           name="EP_mall_nm"           value="주식회사 굿프린팅">         <!-- 가맹점명-->
<input type="hidden" id="EP_currency"          name="EP_currency"          value="00">       <!-- 통화코드 // 00 : 원화-->
<!--<input type="hidden" id="EP_return_url"        name="EP_return_url"        value="<?=$_SERVER['HTTP_HOST']?>/mypage/comp_pay.html">-->         <!-- 가맹점 CALLBACK URL // -->
<input type="hidden" id="EP_return_url"        name="EP_return_url"        value="<?=$_SERVER['HTTP_HOST']?>/mypage/comp_pay.html">         <!-- 가맹점 CALLBACK URL // -->
<input type="hidden" id="EP_ci_url"            name="EP_ci_url"            value="">         <!-- CI LOGO URL // -->
<input type="hidden" id="EP_lang_flag"         name="EP_lang_flag"         value="">         <!-- 언어 // -->
<input type="hidden" id="EP_charset"           name="EP_charset"           value="utf-8">    <!-- 가맹점 CharSet // -->
<input type="hidden" id="EP_user_type"         name="EP_user_type"         value="">         <!-- 사용자구분 // -->
<input type="hidden" id="EP_user_id"           name="EP_user_id"           value="goodpt">         <!-- 가맹점 고객ID // -->
<input type="hidden" id="EP_memb_user_no"      name="EP_memb_user_no"      value="05528819">         <!-- 가맹점 고객일련번호 // -->
<input type="hidden" id="EP_user_nm"           name="EP_user_nm"           value="김시향">         <!-- 가맹점 고객명 // -->
<input type="hidden" id="EP_user_mail"         name="EP_user_mail"         value="master@dprinting.biz">         <!-- 가맹점 고객 E-mail // -->
<input type="hidden" id="EP_user_phone1"       name="EP_user_phone1"       value="">         <!-- 가맹점 고객 연락처1 // -->
<input type="hidden" id="EP_user_phone2"       name="EP_user_phone2"       value="">         <!-- 가맹점 고객 연락처2 // -->
<input type="hidden" id="EP_user_addr"         name="EP_user_addr"         value="서울 중구 필동2가 84-12번지">         <!-- 가맹점 고객 주소 // -->
<input type="hidden" id="EP_user_define1"      name="EP_user_define1"      value="">         <!-- 가맹점 필드1 // -->
<input type="hidden" id="EP_user_define2"      name="EP_user_define2"      value="">         <!-- 가맹점 필드2 // -->
<input type="hidden" id="EP_user_define3"      name="EP_user_define3"      value="">         <!-- 가맹점 필드3 // -->
<input type="hidden" id="EP_user_define4"      name="EP_user_define4"      value="">         <!-- 가맹점 필드4 // -->
<input type="hidden" id="EP_user_define5"      name="EP_user_define5"      value="">         <!-- 가맹점 필드5 // -->
<input type="hidden" id="EP_user_define6"      name="EP_user_define6"      value="">         <!-- 가맹점 필드6 // -->
<input type="hidden" id="EP_product_type"      name="EP_product_type"      value="">         <!-- 상품정보구분 // -->
<input type="hidden" id="EP_product_expr"      name="EP_product_expr"      value="">         <!-- 서비스 기간 // (YYYYMMDD) -->

<input type="hidden" id="EP_tr_cd"             name="EP_tr_cd"             value="00101000">   <!-- 거래구분(수정불가) -->
<input type="hidden" id="EP_tot_amt"           name="EP_tot_amt"           value="">           <!-- 결제총금액 -->
<input type="hidden" id="EP_currency"          name="EP_currency"          value="00">         <!-- 통화코드 : 00(원), 01(달러)-->
<input type="hidden" id="EP_escrow_yn"         name="EP_escrow_yn"         value="N">          <!-- 에스크로여부(수정불가) -->
<input type="hidden" id="EP_complex_yn"        name="EP_complex_yn"        value="N">          <!-- 복합결제여부(수정불가) -->
<input type="hidden" id="EP_vacct_txtype"      name="EP_vacct_txtype"      value="10">         <!-- 무통장입금 처리종류(수정불가) -->
<input type="hidden" id="EP_vacct_amt"         name="EP_vacct_amt"         value="">           <!-- 무통장입금 결제금액 -->
<input type="hidden" id="EP_expire_date"       name="EP_expire_date"       value="">           <!-- 무통장입금 입금만료일(YYYYMMDD) -->
<input type="hidden" id="EP_expire_time"       name="EP_expire_time"       value="">           <!-- 무통장입금 입금만료시간(HHMMSS) -->
<input type="hidden" id="EP_vacct_txtype"      name="EP_vacct_txtype"      value="10">         <!-- 10 : 일반형 // 20 : 고정형 -->
<input type="hidden" id="EP_cash_yn"           name="EP_cash_yn"           value="2">          <!-- 현금영수증발행여부 -->
<input type="hidden" id="EP_cash_issue_type"   name="EP_cash_issue_type"   value="01">           <!-- 현금영수증발행용도 -->
<input type="hidden" id="EP_cash_auth_type"    name="EP_cash_auth_type"    value="3">           <!-- 인증구분 -->
<input type="hidden" id="EP_cash_auth_value"   name="EP_cash_auth_value"   value="01047601529">           <!-- 인증번호 -->

<table border="0" width="910" cellpadding="10" cellspacing="0">
<tr>
    <td>
    <!-- title start -->
	<table border="0" width="900" cellpadding="0" cellspacing="0">
	<tr>
		<td height="30" bgcolor="#FFFFFF" align="left">&nbsp;<img src="../img/arow3.gif" border="0" align="absmiddle">&nbsp;가상계좌 > <b>채번</td>
	</tr>
	<tr>
		<td height="2" bgcolor="#2D4677"></td>
	</tr>
	</table>
	<!-- title end -->

    <!-- mallinfo start -->
    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" bgcolor="#FFFFFF">&nbsp;<img src="../img/arow2.gif" border="0" align="absmiddle">&nbsp;<b>가맹점정보</b>(*필수)</td>
    </tr>
    </table>

    <table border="0" width="900" cellpadding="0" cellspacing="1" bgcolor="#DCDCDC">
    <tr height="25">
        <td bgcolor="#EDEDED" width="150">&nbsp;*가맹점ID</td>
        <td bgcolor="#FFFFFF" width="750" colspan="3">&nbsp;<input type="text" id="EP_mall_id" name="EP_mall_id" size="15" class="input_F" value=""></td>
    </tr>
    </table>
    <!-- mallinfo end -->

    <!-- trade start -->
    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" bgcolor="#FFFFFF">&nbsp;<img src="../img/arow2.gif" border="0" align="absmiddle">&nbsp;<b>결제정보</b>(*필수)</td>
    </tr>
    </table>

    <table border="0" width="900" cellpadding="0" cellspacing="1" bgcolor="#DCDCDC">
    <tr height="25">
        <td bgcolor="#EDEDED" width="150">&nbsp;*은행종류</td>
        <td bgcolor="#FFFFFF" width="750" colspan="3">&nbsp;<select id="EP_bank_cd" name="EP_bank_cd" class="input_F">
        	<option value="003" selected>기업은행</option>
        	<option value="004" >국민은행</option>
        	<option value="011" >농협중앙회</option>
        	<option value="020" >우리은행</option>
        	<option value="023" >SC제일은행</option>
        	<option value="032" >부산은행</option>
        	<option value="071" >우체국</option>
        	<option value="081" >하나은행</option>
        	</select>
        </td>
    </tr>
    </table>
    <!-- trade end -->

    <!-- order start -->
    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" bgcolor="#FFFFFF">&nbsp;<img src="../img/arow2.gif" border="0" align="absmiddle">&nbsp;<b>주문정보</b>(*필수)</td>
    </tr>
    </table>
    <table border="0" width="900" cellpadding="0" cellspacing="1" bgcolor="#DCDCDC">
    <tr height="25">
        <td bgcolor="#EDEDED" width="150">&nbsp;*주문번호</td>
        <td bgcolor="#FFFFFF" width="750" colspan="3">&nbsp;<input type="text" id="EP_order_no" name="EP_order_no" size="50" class="input_F"></td>
    </tr>
    <tr height="25">
        <td bgcolor="#EDEDED" width="150">&nbsp;상품명</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" id="EP_product_nm" name="EP_product_nm" size="50" class="input_A"></td>
        <td bgcolor="#EDEDED" width="150">&nbsp;상품금액</td>
        <td bgcolor="#FFFFFF" width="300">&nbsp;<input type="text" id="EP_product_amt" name="EP_product_amt" size="50" class="input_A"></td>
    </tr>
    </table>
    <!-- order Data END -->

    <table border="0" width="900" cellpadding="0" cellspacing="0">
    <tr>
        <td height="30" align="center" bgcolor="#FFFFFF"><input type="button" value="결 제" class="input_D" style="cursor:hand;" onclick="javascript:f_submit();"></td>
    </tr>
    </table>
    </td>
</tr>
</table>
</form>
</body>
</html>
