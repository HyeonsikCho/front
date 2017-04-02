<?
//회원가입 항목 HTML
function getJoinHtml($param) {

    $html = <<<HTML
        <h4>$param[member_dvs] 회원 가입 정보</h4>
        <table class="line input">
            <colgroup>
                <col style="width:130px">
                <col>
                <col style="width:130px">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th>아이디<span style="color: #f36f23;">(필수)</span></th>
                    <td colspan="3">
                        <input type="text" id="member_id" name="member_id" maxlength="20" onkeydown="showBtn(this);">
                        <button type="button" onclick="getIdOver(this);">아이디 중복확인</button>
                        <span class="description _idMessage"></span>
                        <p class="note">8~20자의 영문 소문자, 숫자와 특수기호(_),(-)만 사용 사능합니다.</p>
                    </td>
                </tr>
                <tr>
                    <th>비밀번호<span style="color: #f36f23;">(필수)</span></th>
                    <td colspan="3">
                        <input type="password" id="passwd" name="passwd" maxlength="20">
                        <p class="note">8~20자의 영문 대소문자 또는 숫자 또는 특수문자를 사용하세요.</p>
                    </td>
                </tr>
                <tr>
                    <th>비밀번호 확인<span style="color: #f36f23;">(필수)</span></th>
                    <td colspan="3">
                        <input type="password" id="passwd_re" name="passwd_re" maxlength="20">
                        <p class="note">비밀번호를 한번 더 입력해주세요.</p>
                    </td>
                </tr>
                $param[etprs_html]
                <tr>
                    <th>$param[member_name]<span style="color: #f36f23;">(필수)</span></th>
                    <td><input type="text" id="$param[member_name_val]" name="$param[member_name_val]"></td>
                    <th>생년월일<span style="color: #f36f23;">(필수)</span></th>
                    <td class="date">
                      <select id="birth_year" name="birth_year" class="sel_highlight" onchange="selectYear(this.value)">
                        $param[year_html]
                      </select>
                      <select id="birth_month" name="birth_month" class="sel_highlight" onchange="selectMon(this.value)">
                        $param[month_html]
                      </select>
                      <select id="birth_day" name="birth_day" class="sel_highlight">
                        $param[day_html] 
                      </select>
                    </td>
                </tr>
                $param[tob_html]
                <tr>
                    <th rowspan="2">이메일<span style="color: #f36f23;">(필수)</span></th>
                    <td colspan="3" class="email _replyToEmail">
                        <input type="text" id="email_addr" name="email_addr">
                        <span class="symbol">@</span>
                        <input type="text" class="_domain" id="email_domain" name="email_domain">
                        <select class="sel_highlight">
                            <option class="_custom" value="">직접입력</option>
                            $param[email_html]
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="border-top: none;">
                        <span class="description">이메일을 통한 상품 및 이벤트 정보 수신에 동의합니다.<span style="color: #f36f23;">(선택)</span></span>
                        <label><input type="radio" name="mailing_yn" value="Y" checked> 예</label>
                        <label><input type="radio" name="mailing_yn" value="N"> 아니오</label>
                    </td>
                </tr>
                <tr>
                    <th>전화번호<span style="color: #f36f23;">(필수)</span></th>
                    <td colspan="3" class="telNum" style="border-left: 1px solid #d7d7d7;">
                        <select id="tel_num1" name="tel_num1" class="sel_highlight">
                            $param[tel_html]
                        </select>
                        <input type="text" id="tel_num2" name="tel_num2" maxlength="4">
                        <input type="text" id="tel_num3" name="tel_num3" maxlength="4">
                    </td>
                </tr>
                <tr>
                    <th rowspan="2">휴대전화<span style="color: #f36f23;">(필수)</span></th>
                    <td colspan="3" class="telNum">
                        <select id="cel_num1" name="cel_num1" class="sel_highlight">
                            $param[cel_html]
                        </select>
                        <input type="text" id="cel_num2" name="cel_num2" maxlength="4">
                        <input type="text" id="cel_num3" name="cel_num3" maxlength="4">
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="border-top: none;">
                        <span class="description">문자(SMS)를 통한 상품 및 이벤트 정보 수신에 동의합니다.<span style="color: #f36f23;">(선택)</span></span>
                        <label><input type="radio" name="sms_yn" value="Y" checked> 예</label>
                        <label><input type="radio" name="sms_yn" value="N"> 아니오</label>
                    </td>
                </tr>
                <tr>
                    <th>주소<span style="color: #f36f23;">(필수)</span></th>
                    <td colspan="3" class="address">
                        <div class="rowWrap postNum">
                            <input type="text" id="zipcode" name="zipcode" readonly>
                            <button type="button" onclick="getPostcode();">우편번호 찾기</button>
                        </div>
                        <div class="rowWrap">
                            <input type="text" class="address" id="addr" name="addr" readonly>
                            <input type="text" class="address" id="addr_detail" name="addr_detail">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        $param[note_html]
        <input type="hidden" id="id_over_yn" name="id_over_yn" value="N">
        <input type="hidden" id="certi_yn" name="certi_yn" value="N">
HTML;

    return $html;
}
?>
