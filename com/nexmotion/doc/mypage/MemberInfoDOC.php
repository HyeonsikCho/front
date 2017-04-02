<?
//개인가입정보
function perJoinInfo($param) {

    $html = <<<HTML
    <h4 class="sectionTitle">개인 가입 정보</h4>
    <table class="line input">
        <colgroup>
            <col width="120">
            <col width="225">
            <col width="120">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <th>회원명</th>
                <td><input type="text" value="$param[member_name]" readonly></td>
                <th>생년월일</th>
                <td>
                    <input type="text" style="width:44px;" class="year" id="birth_year" name="birth_year" value="$param[birth_year]" maxlength="4">
                    <select id="birth_month" name="birth_month">
                        $param[month]
                    </select>
                    <select id="birth_day" name="birth_day">
                        $param[day]
                    </select>
                </td>
            </tr>
            <tr>
                <th>이메일</th>
                <td colspan="3" class="email _replyToEmail">
                    <input type="text" class="_id" id="email_addr" name="email_addr" value="$param[email_addr]">
                    <span class="symbol">@</span>
                    <input type="text" class="_domain" id="email_domain" name="email_domain" value="">
                    <input type="hidden" id="pre_domain" value="$param[email_domain]">
                    <select>
                        <option class="_custom">직접입력</option>
                        $param[email]
                    </select>
                </td>
            </tr>
            <tr>
                <th>전화번호</th>
                <td colspan="3" class="telNum">
                    <select id="tel_num1" name="tel_num1">
                        $param[tel]
                    </select>
                    <input type="text" id="tel_num2" name="tel_num2" value="$param[tel_num2]" maxlength="4">
                    <input type="text" id="tel_num3" name="tel_num3" value="$param[tel_num3]" maxlength="4">
                </td>
            </tr>
            <tr>
                <th>휴대전화</th>
                <td colspan="3" class="telNum">
                    <select id="cel_num1" name="cel_num1">
                        $param[cel]
                    </select>
                    <input type="text" id="cel_num2" name="cel_num2" value="$param[cel_num2]" maxlength="4">
                    <input type="text" id="cel_num3" name="cel_num3" value="$param[cel_num3]" maxlength="4">
                </td>
            </tr>
            <tr>
                <th>주소</th>
                <td colspan="3">
                    <div class="rowWrap postNum">
                        <input type="text" id="zipcode" name="zipcode" value="$param[zipcode]" readonly>
                        <button type="button" onclick="getPostcode('');">우편번호 찾기</button>
                    </div>
                    <div class="rowWrap address">
                        <input type="text" class="address" id="addr" name="addr" value="$param[addr]" readonly>
                        <input type="text" class="address" id="addr_detail" name="addr_detail" value="$param[addr_detail]">
                    </div>
                </td>
            </tr>
            <tr>
                <th>이메일 수신여부</th>
                <td colspan="3">
                    <span class="description">뉴스레터, 이벤트 안내 등의 정보를 수신합니다.</span>
                    <label><input type="radio" name="mailing_yn" value="Y"$param[email_ck_y]> 예</label>
                    <label><input type="radio" name="mailing_yn" value="N"$param[email_ck_n]> 아니오</label>
                </td>
            </tr>
            <tr>
                <th>SMS 수신여부</th>
                <td colspan="3">
                    <span class="description">뉴스레터, 이벤트 안내 등의 정보를 수신합니다.</span>
                    <label><input type="radio" name="sms_yn" value="Y"$param[sms_ck_y]> 예</label>
                    <label><input type="radio" name="sms_yn" value="N"$param[sms_ck_n]> 아니오</label>
                </td>
            </tr>
        </tbody>
    </table>

HTML;

    return $html;
}

//사업자 등록정보
function comJoinInfo($param) {

    $html = <<<HTML
    <h4 class="sectionTitle">사업자 등록정보 $param[description]</h4>
    <table class="line input">
        <colgroup>
            <col width="120">
            <col width="225">
            <col width="120">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <th>기업명</th>
                <td><input type="text" id="corp_name" value="$param[corp_name]" $param[readonly]></td>
                <th>사업자등록번호</th>
                <td><input type="text" id="crn" value="$param[crn]" $param[readonly]></td>
            </tr>
            <tr>
                <th>대표자</th>
                <td colspan="3"><input type="text" id="repre_name" value="$param[repre_name]"></td>
                <!--
                <th>생년월일</th>
                <td>
                    <input type="text" style="width:44px;" class="year" id="birth_year" name="birth_year" value="$param[birth_year]" maxlength="4">
                    <select id="birth_month" name="birth_month">
                        $param[month]
                    </select>
                    <select id="birth_day" name="birth_day">
                        $param[day]
                    </select>
                </td>
                -->
            </tr>
            <tr>
                <th>업태</th>
                <td><input type="text" id="bc" value="$param[bc]"></td>
                <th>종목</th>
                <td><input type="text" id="tob" value="$param[tob]"></td>
            </tr>
            <!--
            <tr>
                <th>이메일</th>
                <td colspan="3" class="email _replyToEmail">
                    <input type="text" class="_id" id="email_addr" name="email_addr" value="$param[email_addr]">
                    <span class="symbol">@</span>
                    <input type="text" class="_domain" id="email_domain" name="email_domain">
                    <input type="hidden" id="pre_domain" value="$param[email_domain]">
                    <select>
                        <option class="_custom">직접입력</option>
                        $param[email]
                    </select>
                </td>
            </tr>
            -->
            <!--
            <tr>
                <th>전화번호</th>
                <td colspan="3" class="telNum">
                    <select id="co_tel_num1" name="co_tel_num1">
                        $param[co_tel]
                    </select>
                    <input type="text" id="co_tel_num2" name="co_tel_num2" value="$param[co_tel_num2]" maxlength="4">
                    <input type="text" id="co_tel_num3" name="co_tel_num3" value="$param[co_tel_num3]" maxlength="4">
                </td>
            </tr>
            <tr>
                <th>휴대전화</th>
                <td colspan="3" class="telNum">
                    <select id="cel_num1" name="cel_num1">
                        $param[cel]
                    </select>
                    <input type="text" id="cel_num2" name="cel_num2" value="$param[cel_num2]" maxlength="4">
                    <input type="text" id="cel_num3" name="cel_num3" value="$param[cel_num3]" maxlength="4">
                </td>
            </tr>
            -->
            <tr>
                <th>주소</th>
                <td colspan="3">
                    <div class="rowWrap postNum">
                        <input type="text" id="co_zipcode" name="co_zipcode" value="$param[co_zipcode]" readonly>
                        <button type="button" onclick="getPostcode('co_');">우편번호 찾기</button>
                    </div>
                    <div class="rowWrap address">
                        <input type="text" class="address" id="co_addr" name="co_addr" value="$param[co_addr]" readonly>
                        <input type="text" class="address" id="co_addr_detail" name="co_addr_detail" value="$param[co_addr_detail]">
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

HTML;

    return $html;
}

//주문담당자
function orderMng($param) {

    $html = <<<HTML
    <h4 class="sectionTitle">주문담당자</h4>
    <table class="line input">
        <colgroup>
            <col width="50">
            <col width="90">
            <col width="100">
            <col width="100">
            <col width="100">
            <col>
            <col width="100">
        </colgroup>
        <caption class="function">
            <button class="add blue" onclick="layerPopup('l_officer', 'popup/l_orderofficer_new.html');">+ 추가</buttozn>
        </caption>
        <tbody>
            <tr>
                <th class="center">번호</th>
                <th>성명</th>
                <th>아이디</th>
                <th>전화번호</th>
                <th>휴대전화</th>
                <th>이메일</th>
                <th class="center">관리</th>
            </tr>
        </tbody>
        <tbody id="order_mng_list">
            $param[order_mng_html]
        </tbody>
    </table>
HTML;

    return $html;
}

//회계담당자
function acctingMng($param) {

    $html = <<<HTML
    <h4 class="sectionTitle">회계담당자</h4>
    <table class="line input">
        <colgroup>
            <col width="50">
            <col width="90">
            <col width="150">
            <col width="150">
            <col>
            <col width="100">
        </colgroup>
        <caption class="function">
            <button class="add blue" onclick="layerPopup('l_officer', 'popup/l_accountofficer_new.html');">+ 추가</buttozn>
        </caption>
        <tbody>
            <tr>
                <th class="center">번호</th>
                <th>성명</th>
                <th>전화번호</th>
                <th>휴대전화</th>
                <th>이메일</th>
                <th class="center">관리</th>
            </tr>
        </tbody>
        <tbody id="accting_mng_list">
            $param[accting_mng_html]
        </tbody>
    </table>
HTML;

    return $html;
}

//성향정보
function memberDetail($param) {

    $html = <<<HTML
    <h4 class="sectionTitle">성향정보</h4>
    <table class="line input taste">
        <colgroup>
            <col width="120">
            <col>
            <col width="120">
            <col>
        </colgroup>
        <tbody>
            <tr id="wd_yn_html">
                <th>결혼유무</th>
                <td class="_marriage">
                    <label><input type="radio" name="wd_yn" value="N"$param[wd_n]> 미혼</label>
                    <label><input type="radio" name="wd_yn" value="Y"$param[wd_y]> 기혼</label>
                    <input type="hidden" id="pre_wd_yn" value="$param[wd_yn]">
                </td>
                <th>결혼기념일</th>
                <td class="_marriageDate">
                    <input type="text" id="wd_anniv" value="$param[wd_anniv]">
                </td>
            </tr>
            <tr id="occu_html">
                <th>회원직업</th>
                <td colspan="3">
                    <select id="occu1" name="occu1" onchange="setOccu2()">
                        <option>IT 전산개발</option>
                    </select>
                    <input type="hidden" id="pre_occu1" value="$param[occu1]">
                    <select id="occu2" name="occu2">
                        <option>영업</option>
                    </select>
                    <input type="hidden" id="pre_occu2" value="$param[occu2]">
                    <input type="text" style="width: 308px;" id="occu_detail" name="occu_detail" value="$param[occu_detail]" placeholder="세부적으로 적어주세요.">
                </td>
            </tr>
            <tr>
                <th>관심분야</th>
                <td colspan="3">
                    <select id="interest_field1" name="interest_field1">
                        <option>요식업종</option>
                    </select>
                    <input type="hidden" id="pre_interest_field1" value="$param[interest_field1]">
                    <select id="interest_field2" name="interest_field2">
                        <option>한식</option>
                    </select>
                    <input type="hidden" id="pre_interest_field2" value="$param[interest_field2]">
                    <input type="text" style="width: 308px;" id="interest_field_detail" name="interest_field_detail" value="$param[interest_field_detail]" placeholder="세부적으로 적어주세요.">
                </td>
            </tr>
            <tr>
                <th>관심상품</th>
                <td colspan="3">
                    <label><input type="checkbox" id="inter_prdt1" name="inter_prdt1"$param[inter_prdt1]> 명함</label>
                    <label><input type="checkbox" id="inter_prdt2" name="inter_prdt2"$param[inter_prdt2]> 스티커</label>
                    <label><input type="checkbox" id="inter_prdt3" name="inter_prdt3"$param[inter_prdt3]> 전단</label>
                    <label><input type="checkbox" id="inter_prdt4" name="inter_prdt4"$param[inter_prdt4]> 광고홍보물</label>
                    <label><input type="checkbox" id="inter_prdt5" name="inter_prdt5"$param[inter_prdt5]> 봉투</label>
                    <label><input type="checkbox" id="inter_prdt6" name="inter_prdt6"$param[inter_prdt6]> 마스터(경인쇄)</label>
                    <label><input type="checkbox" id="inter_prdt7" name="inter_prdt7"$param[inter_prdt7]> 초소량인쇄</label>
                    <label><input type="checkbox" id="inter_prdt8" name="inter_prdt8"$param[inter_prdt8]> 디지털인쇄</label>
                    <label><input type="checkbox" id="inter_prdt9" name="inter_prdt9"$param[inter_prdt9]> 청첩장/초대장</label>
                    <label><input type="checkbox" id="inter_prdt10" name="inter_prdt10"$param[inter_prdt10]> 패키지</label>
                    <label><input type="checkbox" id="inter_prdt11" name="inter_prdt11"$param[inter_prdt11]> 실사출력</label>
                    <label><input type="checkbox" id="inter_prdt12" name="inter_prd12"$param[inter_prdt12]> 판촉물</label>
                </td>
            </tr>
            <tr>
                <th>관심디자인</th>
                <td colspan="3">
                    <label><input type="checkbox" id="inter_design1" name="inter_design1"$param[inter_design1]> 항목1</label>
                    <label><input type="checkbox" id="inter_design2" name="inter_design2"$param[inter_design2]> 항목2</label>
                    <label><input type="checkbox" id="inter_design3" name="inter_design3"$param[inter_design3]> 항목3</label>
                    <label><input type="checkbox" id="inter_design4" name="inter_design4"$param[inter_design4]> 항목4</label>
                    <label><input type="checkbox" id="inter_design5" name="inter_design5"$param[inter_design5]> 항목5</label>
                    <label><input type="checkbox" id="inter_design6" name="inter_design6"$param[inter_design6]> 항목6</label>
                </td>
            </tr>
            <tr>
                <th>관심우선순위</th>
                <td colspan="3">
                    <label><input type="radio" name="interest_prior" value="1"$param[inter_prior1]> 가격</label>
                    <label><input type="radio" name="interest_prior" value="2"$param[inter_prior2]> 품질</label>
                    <label><input type="radio" name="interest_prior" value="3"$param[inter_prior3]> 납기</label>
                    <label><input type="radio" name="interest_prior" value="4"$param[inter_prior4]> 서비스</label>
                </td>
            </tr>
            <tr>
                <th>관심이벤트</th>
                <td colspan="3">
                    <label><input type="checkbox" id="inter_event1" name="inter_event1"$param[inter_event1]> 오특이</label>
                    <label><input type="checkbox" id="inter_event2" name="inter_event2"$param[inter_event2]> 골라담자</label>
                    <label><input type="checkbox" id="inter_event3" name="inter_event3"$param[inter_event3]> 요즘바빠요</label>
                    <label><input type="checkbox" id="inter_event4" name="inter_event4"$param[inter_event4]> 포인트</label>
                    <label><input type="checkbox" id="inter_event5" name="inter_event5"$param[inter_event5]> 쿠폰</label>
                </td>
            </tr>
            <tr>
                <th>관심요구사항</th>
                <td colspan="3">
                    <label><input type="checkbox" id="inter_needs1" name="inter_needs1"$param[inter_needs1]> 방문요청</label>
                    <label><input type="checkbox" id="inter_needs2" name="inter_needs2"$param[inter_needs2]> 담당자 지정</label>
                    <label><input type="checkbox" id="inter_needs3" name="inter_needs3"$param[inter_needs3]> 다양한 제품 출시</label>
                    <label><input type="checkbox" id="inter_needs4" name="inter_needs4"$param[inter_needs4]> 지속적인 이벤트</label>
                    <label><input type="checkbox" id="inter_needs5" name="inter_needs5"$param[inter_needs5]> 원활한 A/S</label>
                    <label><input type="checkbox" id="inter_needs6" name="inter_needs6"$param[inter_needs6]> 사이트 제작 조언</label>
                    <label><input type="checkbox" id="inter_needs7" name="inter_needs7"$param[inter_needs7]> 프로그램 공동구매</label>
                    <label><input type="checkbox" id="inter_needs8" name="inter_needs8"$param[inter_needs8]> 커뮤니티 강화</label>
                    <label><input type="checkbox" id="inter_needs9" name="inter_needs9"$param[inter_needs9]> 정기모임</label>
                    <label><input type="checkbox" id="inter_needs10" name="inter_needs10"$param[inter_needs10]> 유/무상 교육</label>
                </td>
            </tr>
            <tr>
                <th>추가 관심사항</th>
                <td colspan="3">
                    <div class="inputWrap"><input type="text" id="add_interest_items" name="add_interest_items" value="$param[add_interest_items]" placeholder="세부적으로 적어주세요."></div>
                </td>
            </tr>
            <tr>
                <th>디자인</th>
                <td colspan="3">
                    <label class="long"><input type="radio" name="design_outsource_yn" value="Y" $param[design_outsource_y]> 디자인 관련 기획 전체를 외주합니다.</label>
                    <label class="long"><input type="radio" name="design_outsource_yn" value="N" $param[design_outsource_n]> 디자인은 자체적으로 처리합니다.</label>
                </td>
            </tr>
            <tr>
                <th>생산</th>
                <td colspan="3">
                    <label class="long"><input type="radio" name="produce_outsource_yn" value="Y" $param[produce_outsource_y]> 인쇄 외 모든 품목을 외주합니다.</label>
                    <label class="long"><input type="radio" name="produce_outsource_yn" value="N" $param[produce_outsource_n]> 자체적으로 인쇄를 생산합니다.</label>
                </td>
            </tr>
            <tr>
                <th>사용 OS</th>
                <td colspan="3">
                    <label><input type="radio" name="use_opersys" onchange="changeOs(this.value, '$param[member_seqno]');" value="IBM"$param[ibm]>IBM</label>
                    <label><input type="radio" name="use_opersys" onchange="changeOs(this.value, '$param[member_seqno]');" value="MAC"$param[mac]>MAC</label>
                </td>
            </tr>
            <tr>
                <th>주요 사용프로그램</th>
                <td colspan="3" id="use_pro_list">
                    $param[pro_html]
                </td>
            </tr>
            <tr>
                <th>복수거래유무</th>
                <td colspan="3" class="_printBusiness">
                    <label><input type="radio" name="plural_deal_yn" value="Y"$param[plural_deal_y]> 복수거래 중</label>
                    <label><input type="radio" name="plural_deal_yn" value="N"$param[plural_deal_n]> 단수거래 중</label>
                </td>
            </tr>
            <tr>
                <th>복수거래업체</th>
                <td colspan="3" class="_printBusinessDetail">
                    <div class="rowWrap">
                        <select id="plural_deal_site_name1" name="plural_deal_site_name1"$param[plural_disabled]>
                            <option value="">- 선택 -</option>
                            <option value="애드코아">애드코아</option>
                            <option value="디티피아">디티피아</option>
                        </select>
                        <input type="hidden" id="pre_plural_deal_site_name1" value="$param[plural_deal_site_name1]">
                        <input type="text" id="plural_deal_site_detail1" name="plural_deal_site_detail1" value="$param[plural_deal_site_detail1]" placeholder="세부적으로 적어주세요."$param[plural_disabled]>
                    </div>
                    <div class="rowWrap">
                        <select id="plural_deal_site_name2" name="plural_deal_site_name2"$param[plural_disabled]>
                            <option value="">- 선택 -</option>
                            <option value="애드코아">애드코아</option>
                            <option value="디티피아">디티피아</option>
                        </select>
                        <input type="hidden" id="pre_plural_deal_site_name2" value="$param[plural_deal_site_name2]">
                        <input type="text" id="plural_deal_site_name1" name="plural_deal_site_name1" value="$param[plural_deal_site_detail2]" placeholder="세부적으로 적어주세요."$param[plural_disabled]>
                    </div>
                </td>
            </tr>
            <tr>
                <th>추천인 성명</th>
                <td colspan="3">
                    <div class="rowWrap">
                        <input type="text" id="recomm_id" name="recomm_id" value="$param[recomm_id]">
                        <input type="text" style="width: 466px;" id="recomm_id_detail" name="recomm_id_detail" value="$param[recomm_id_detail]" placeholder="추천인의 사업자번호를 적어주세요.">
                    </div>
                </td>
            </tr>
            <tr>
                <th>메모</th>
                <td colspan="3"><div class="inputWrap"><textarea rows="5" id="memo" name="memo">$param[memo]</textarea></div></td>
            </tr>
        </tbody>
    </table>
HTML;

    return $html;
}

//선입금 충전 팝업
function prepaymentPop($info) {
    $group_name   = $info["group_name"];
    $member_name  = $info["member_name"];
    $member_mail  = $info["email"];
    $member_seqno = $info["member_seqno"];
    $url          = $info["url"];

    $mail_nm    = urlencode("디프린팅");
    $product_nm = urlencode("선입금충전");
    $user_nm    = urlencode($group_name . ' ' . $member_name);


    $html = <<<HTML
<header>
    <h2>선입금 결제하기</h2>
    <button class="close" title="닫기"><img src="/design_template/images/common/btn_circle_x_white.png" alt="X"></button>
</header>
<article>
    <h3>거래금액 선택(VAT포함)</h3>
    <ul class="amount">
        <li><label><input type="radio" name="charge_price" onclick="document.getElementById('charge_price').value = this.value.format();" checked="checked" value="100000"> 100,000(10만원)</label></li>
        <li><label><input type="radio" name="charge_price" onclick="document.getElementById('charge_price').value = this.value.format();" value="300000"> 300,000(30만원)</label></li>
        <li><label><input type="radio" name="charge_price" onclick="document.getElementById('charge_price').value = this.value.format();" value="500000"> 500,000(50만원)</label></li>
        <li><label><input type="radio" name="charge_price" onclick="document.getElementById('charge_price').value = this.value.format();" value="700000"> 700,000(70만원)</label></li>
        <li><label><input type="radio" name="charge_price" onclick="document.getElementById('charge_price').value = this.value.format();" value="1000000"> 1,000,000(100만원)</label></li>
        <li><label><input type="radio" name="charge_price" onclick="document.getElementById('charge_price').value = this.value.format();" value="3000000"> 3,000,000(300만원)</label></li>
        <li><label><input type="radio" name="charge_price" onclick="document.getElementById('charge_price').value = this.value.format();" value="5000000"> 5,000,000(500만원)</label></li>
        <li><label><input type="radio" name="charge_price" value="" onclick="document.getElementById('charge_price').value = document.getElementById('head_order_lack_price').innerHTML; this.value = document.getElementById('head_order_lack_price').innerHTML;"> 주문부족금액 결제</label></li>
    </ul>
    결제방식
    <select id="pay_type">
        <option value="11">신용카드</option>
        <option value="22">가상계좌</option>
    </select>
    <hr>
    <h3>총 구입가격</h3>
    <p><input id="charge_price" type="text" readonly value="100,000"> 원 (VAT포함)</p>
    <p class="note">농협이나 씨티카드로 결제하시는 경우,<br>결제 후 02-2260-9000(고객센터)로 연락바랍니다.</p>
    <div class="function center">
        <strong><button type="button" onclick="doCharge();">결제하기</button></strong>
        <button type="button" class="close">취소</button>
    </div>

    <!-- PG 결제관련 form -->
    <form name="p_frm_pay" method="post" action="">
        <!--------------------------->
        <!-- ::: 공통 인증 요청 값 -->
        <!--------------------------->

        <!-- 가맹점 이름 // -->
        <input type="hidden" id="P_EP_mall_nm"      name="EP_mall_nm"      value="{$mail_nm}">
        <!-- 가맹점 주문번호 // -->
        <input type="hidden" id="P_EP_order_no"     name="EP_order_no"     value="">
        <!-- 통화코드 // 00 : 원화-->
        <input type="hidden" id="P_EP_currency"     name="EP_currency"     value="00">
        <!-- 가맹점 CALLBACK URL // -->
        <input type="hidden" id="P_EP_return_url"   name="EP_return_url"   value="$url/webpay_card_prepay/web/normal/order_res.php">
        <!-- CI LOGO URL // -->
        <input type="hidden" id="P_EP_ci_url"       name="EP_ci_url"       value="">
        <!-- 언어 // -->
        <input type="hidden" id="P_EP_lang_flag"    name="EP_lang_flag"    value="KOR">
        <!-- 가맹점 CharSet // -->
        <input type="hidden" id="P_EP_charset"      name="EP_charset"      value="UTF-8">
        <!-- 사용자구분 // -->
        <input type="hidden" id="P_EP_user_type"    name="EP_user_type"    value="2">
        <!-- 가맹점 고객ID // -->
        <input type="hidden" id="P_EP_user_id"      name="EP_user_id"      value="">
        <!-- 가맹점 고객일련번호 // -->
        <input type="hidden" id="P_EP_memb_user_no" name="EP_memb_user_no" value="">
        <!-- 가맹점 고객명 // -->
        <input type="hidden" id="P_EP_user_nm"      name="EP_user_nm"      value="{$user_nm}">
        <!-- 가맹점 고객 E-mail // -->
        <input type="hidden" id="P_EP_user_mail"    name="EP_user_mail"    value="{$member_mail}">
        <!-- 가맹점 고객 연락처1 // -->
        <input type="hidden" id="P_EP_user_phone1"  name="EP_user_phone1"  value="">
        <!-- 가맹점 고객 연락처2 // -->
        <input type="hidden" id="P_EP_user_phone2"  name="EP_user_phone2"  value="">
        <!-- 가맹점 고객 주소 // -->
        <input type="hidden" id="P_EP_user_addr"    name="EP_user_addr"    value="">
        <!-- 가맹점 필드1 // -->
        <input type="hidden" id="P_EP_user_define1" name="EP_user_define1" value="">
        <!-- 가맹점 필드2 // -->
        <input type="hidden" id="P_EP_user_define2" name="EP_user_define2" value="">
        <!-- 가맹점 필드3 // -->
        <input type="hidden" id="P_EP_user_define3" name="EP_user_define3" value="">
        <!-- 가맹점 필드4 // -->
        <input type="hidden" id="P_EP_user_define4" name="EP_user_define4" value="">
        <!-- 가맹점 필드5 // -->
        <input type="hidden" id="P_EP_user_define5" name="EP_user_define5" value="">
        <!-- 가맹점 필드6 // -->
        <input type="hidden" id="P_EP_user_define6" name="EP_user_define6" value="">
        <!-- 상품정보구분 // -->
        <input type="hidden" id="P_EP_product_type" name="EP_product_type" value="">
        <!-- 서비스 기간 // (YYYYMMDD) -->
        <input type="hidden" id="P_EP_product_expr" name="EP_product_expr" value="">

        <!--------------------------->
        <!-- ::: 카드 인증 요청 값 -->
        <!--------------------------->

        <!-- 사용가능한 카드 LIST // FORMAT->카드코드:카드코드: ... :카드코드 EXAMPLE->029:027:031 // 빈값 : DB조회-->
        <input type="hidden" id="P_EP_usedcard_code"     name="EP_usedcard_code"     value="">
        <!-- 할부개월 (카드코드-할부개월) -->
        <input type="hidden" id="P_EP_quota"             name="EP_quota"             value="">
        <!-- 해외안심클릭 사용여부(변경불가) // -->
        <input type="hidden" id="P_EP_os_cert_flag"      name="EP_os_cert_flag"      value="2">
        <!-- 무이자 여부 (Y/N) // -->
        <input type="hidden" id="P_EP_noinst_flag"       name="EP_noinst_flag"       value="">
        <!-- 무이자 기간(카드코드-더할할부개월) // -->
        <input type="hidden" id="P_EP_noinst_term"       name="EP_noinst_term"       value="">
        <!-- 카드사포인트 사용여부 (Y/N) // -->
        <input type="hidden" id="P_EP_set_point_card_yn" name="EP_set_point_card_yn" value="">
        <!-- 포인트카드 LIST  // -->
        <input type="hidden" id="P_EP_point_card"        name="EP_point_card"        value="">
        <!-- 조인코드 // -->
        <input type="hidden" id="P_EP_join_cd"           name="EP_join_cd"           value="">
        <!-- 국민앱카드 사용유무 // -->
        <input type="hidden" id="P_EP_kmotion_useyn"     name="EP_kmotion_useyn"     value="">
        <!-- 가맹점아이디 // -->
        <input type="hidden" id="P_EP_mall_id"           name="EP_mall_id"           value="T5102001">
        <!-- 윈도우 타입 // -->
        <input type="hidden" id="P_EP_window_type"       name="EP_window_type"       value="iframe">
        <!--  결제수단 // -->
        <input type="hidden" id="P_EP_pay_type"          name="EP_pay_type"          value="11">
        <!--  상품명 // -->
        <input type="hidden" id="P_EP_product_nm"        name="EP_product_nm"        value="{$product_nm}">
        <!--  상품금액 // -->
        <input type="hidden" id="P_EP_product_amt"       name="EP_product_amt"       value="">

        <!--------------------------------->
        <!-- ::: 인증응답용 인증 요청 값 -->
        <!--------------------------------->

        <!--  응답코드 // -->
        <input type="hidden" id="P_EP_res_cd"         name="EP_res_cd"         value="">
        <!--  응답메세지 // -->
        <input type="hidden" id="P_EP_res_msg"        name="EP_res_msg"        value="">
        <!--  결제창 요청구분 // -->
        <input type="hidden" id="P_EP_tr_cd"          name="EP_tr_cd"          value="">
        <!--  결제수단 // -->
        <input type="hidden" id="P_EP_ret_pay_type"   name="EP_ret_pay_type"   value="">
        <!--  복합결제 여부 (Y/N) // -->
        <input type="hidden" id="P_EP_ret_complex_yn" name="EP_ret_complex_yn" value="">
        <!--  카드코드 (ISP:KVP카드코드 MPI:카드코드) // -->
        <input type="hidden" id="P_EP_card_code"      name="EP_card_code"      value="">
        <!--  MPI인 경우 ECI코드 // -->
        <input type="hidden" id="P_EP_eci_code"       name="EP_eci_code"       value="">
        <!--  거래구분 // -->
        <input type="hidden" id="P_EP_card_req_type"  name="EP_card_req_type"  value="">
        <!--  카드사 세이브 여부 (Y/N) // -->
        <input type="hidden" id="P_EP_save_useyn"     name="EP_save_useyn"     value="">
        <!--  추적번호 // -->
        <input type="hidden" id="P_EP_trace_no"       name="EP_trace_no"       value="">
        <!--  세션키 // -->
        <input type="hidden" id="P_EP_sessionkey"     name="EP_sessionkey"     value="">
        <!--  암호화전문 // -->
        <input type="hidden" id="P_EP_encrypt_data"   name="EP_encrypt_data"   value="">
        <!--  포인트 CP 코드 // -->
        <input type="hidden" id="P_EP_pnt_cp_cd"      name="EP_pnt_cp_cd"      value="">
        <!--  간편결제 CP 코드 // -->
        <input type="hidden" id="P_EP_spay_cp"        name="EP_spay_cp"        value="">
        <!--  신용카드prefix // -->
        <input type="hidden" id="P_EP_card_prefix"    name="EP_card_prefix"    value="">
        <!--  신용카드번호 앞7자리 // -->
        <input type="hidden" id="P_EP_card_no_7"      name="EP_card_no_7"      value="">
    </form>

    <iframe name="p_iframe_pay" id="p_iframe_pay" width="0" height="0" style="display:none;"></iframe>
</article>

HTML;

    return $html;
}
?>
