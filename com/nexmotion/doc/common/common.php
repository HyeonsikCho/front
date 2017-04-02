<?
/**
 * @brief 로그인 상태일 경우 헤더의 고객정보 html 반환
 *
 * @param $info = 사용자 정보
 *
 * @return login html
 */
function getLoginHtml($info) {
    $order_lack_price = number_format(doubleval($info["order_lack_price"]));
    $prepay_price     = number_format(doubleval($info["prepay_price"]));

    $member_name = $info["member_name"];
    $group_id    = $info["group_id"];
    $group_name  = $info["group_name"];

    $login_name = '';

    if (empty($group_id)) {
        $login_name = $member_name;
    } else {
        $login_name = $group_name . ' ' . $member_name;
    }

    $html = <<<html
        <article>
            <h2><strong id="member_name">$login_name</strong>님</h2>
            <dl>
                <!--
                <dt>선입금</dt>
                <dd><span>$prepay_price</span>원</dd>
                -->
                <dt>주문부족금액</dt>
                <dd><em id="head_order_lack_price">$order_lack_price</em>원</dd>
                <dt>$info[bank_name]</dt>
                <dd>$info[ba_num]</dd>
            </dl>
        </article>
        <div class="function btn">
            <strong><a href="/mypage/main.html" style="margin-left:10px;" target="_self">마이페이지</a>
            <a href="#none;" style="margin-top:5px;background: #f36f23;" onclick="showPrepaymentPop();" target="_self">충전하기</a></strong>
            <strong style="margin: 0;">
            <a href="#none" onclick="logout();" style="background: #898989;height: 51px;line-height: 50px;" target="_self">로그아웃</a></strong>
        </div>
html;

    return $html;
}

/**
 * @brief 로그아웃 상태일 경우 헤더의 로그아웃 html 반환
 *
 * @param $cookie = 쿠키 초전역 변수
 *
 * @return logout html
 */
function getLogoutHtml($cookie) {
    $html = <<<html
        <dl>
            <dt>아이디</dt>
            <dd><input id="id" type="text" onkeyup="idkey(event, '');" value="$cookie[id]"></dd>
            <dt>비밀번호</dt>
            <dd><input id="pw" type="password" onkeyup="loginKey(event, '');"></dd>
        </dl>
        <button onclick="login('');" class="login">LOGIN</button>
html;

    return $html;
}

/**
 * @brief 로그인 상태일 때 사이드메뉴 html 추가
 *
 * @param $info    = 사용자 정보
 * @param $summary = 주문요약 배열
 *
 * @return logout html
 */
function getAsideHtml($info, $summary, $order_list, $order_btn) {
    $prepay_price = number_format(doubleval($info["prepay_price"]));
    $point = number_format(doubleval($info["own_point"]));

    $html = <<<html
        <button type="button" title="닫기" class="switch _opened"><img src="/design_template/images/common/aside_member_btn_opened.png" alt="◀"></button>
        <button type="button" title="열기" class="switch _closed"><img src="/design_template/images/common/aside_member_btn_closed.png" alt="▶"></button>
        <div class="wrap">
            <section class="membership">
                <h2 class="grade$info[grade]"><img src="/design_template/images/common/$info[grade_image].png" alt="PLATINUM"></h2>
                <ul class="infomation">
                    <li class="prepaid">
                        <dl>
                            <dt>선입금</dt>
                            <dd><span id="side_prepay_price">$prepay_price</span> 원</dd>
                        </dl>
                    </li>
                    <li class="point">
                        <dl>
                            <dt>포인트</dt>
                            <dd>$point P</dd>
                        </dl>
                    </li>
                    <li class="coupon">
                        <dl>
                            <dt>쿠폰</dt>
                            <dd>$info[cp_count] 매</dd>
                        </dl>
                    </li>
                </ul>
            </section>
            <section class="myOrder">
                <h2 onclick="showAccordion('myOrder'); return false;" style="cursor: pointer;">
                    나의 주문현황
                </h2>
                <div id="myOrder" style="display:none;">
                <br />
                <ul class="_switch">
                    <li class="_on"><button onclick="getOrderSummary('week');"><img src="/design_template/images/common/btn_text_week.png" alt="최근1주일"></button></li>
                <li><button onclick="getOrderSummary('month');"><img src="/design_template/images/common/btn_text_month.png" alt="이번달"></button></li>
                </ul>
                <br />
                <br />
                <ul class="list">
                    <li class="standby">
                        <a href="/mypage/main.html?dvs=입금" target="_self">
                            <dl>
                                <dd id="summary_wait">$summary[입금대기]</dd>
                                <dt>입금대기</dt>
                            </dl>
                        </a>
                    </li>
                    <li class="application">
                        <a href="/mypage/main.html?dvs=접수" target="_self">
                            <dl>
                                <dd id="summary_rcpt">$summary[접수]</dd>
                                <dt>접수</dt>
                            </dl>
                        </a>
                    </li>
                    <li class="manufacture">
                        <a href="/mypage/main.html?dvs=조판" target="_self">
                            <dl>
                                <dd id="summary_prdc">$summary[제작]</dd>
                                <dt>제작</dt>
                            </dl>
                        </a>
                    </li>
                </ul>
                <ul class="list">
                    <li class="release">
                        <a href="/mypage/main.html?dvs=입고" target="_self">
                            <dl>
                                <dd id="summary_rels">$summary[입출고]</dd>
                                <dt>입/출고</dt>
                            </dl>
                        </a>
                    </li>
                    <li class="delivery">
                        <a href="/mypage/main.html?dvs=배송" target="_self">
                            <dl>
                                <dd id="summary_dlvr">$summary[배송]</dd>
                                <dt>배송</dt>
                            </dl>
                        </a>
                    </li>
                    <li class="complete">
                        <a href="/mypage/main.html?dvs=구매확정" target="_self">
                            <dl>
                                <dd id="summary_comp">$summary[완료]</dd>
                                <dt>완료</dt>
                            </dl>
                        </a>
                    </li>
                </ul>
                </div>
            </section>
            <section class="favorite">
                <h2 onclick="showAccordion('favorite');" style="cursor: pointer;">늘 했던 거
                </h2>
                <div id="favorite" style="display:none;">
                <table>
                    <colgroup>
                        <col width="23">
                        <col>
                    </colgroup>
                    <tbody>
                        $order_list
                    </tbody>
                </table>
                $order_btn
                </div>
            </section>
            <section class="contact">
                <h2 onclick="showAccordion('contact');" style="cursor: pointer;">담당자 연락처
                </h2>
                <div id="contact" style="display:none;">
                <dl class="telNum">
                    <dt>통화접수담당 $info[member_mng_name]</dt>
                    <dd>$info[member_mng_tel]</dd>
                    <dt>명함출고담당 $info[nc_release_name]</dt>
                    <dd>$info[nc_release_tel]</dd>
                    <dt>전단출고담당 $info[bl_release_name]</dt>
                    <dd>$info[bl_release_tel]</dd>
                </dl>
                <strong>02.2260.9000</strong>
                <dl class="time">
                    <dt>평일</dt>
                    <dd>09:00~20:00</dd>
                    <dt>접수실 점심시간</dt>
                    <dd>12:30~13:30</dd>
                    <dt>출고실 점심시간</dt>
                    <dd>13:30~14:30</dd>
                    <!--<dt>토요일</dt>
                    <dd>09:00~15:00</dd>-->
                </dl>
                </div>
            </section>
        </div>
        <div class="cover">
            <section class="membership">
                <h2 class="grade$info[grade]"><img src="/design_template/images/common/aside_member_grade$info[grade]_folded.png" alt="platinum"></h2>
                <ul class="infomation">
                    <li class="prepaid">선입금</li>
                    <li class="point">포인트</li>
                    <li class="coupon">쿠폰</li>
                </ul>
            </section>
            <section class="myOrder">
                <h2 onclick="showAccordion('myOrder');" style="cursor: pointer;">나의 주문 현황</h2>
            </section>
            <section class="favorite">
                <h2 onclick="showAccordion('favorite');" style="cursor: pointer;">늘 했던 거</h2>
            </section>
            <section class="contact">
                <h2 onclick="showAccordion('contact');" style="cursor: pointer;">담당자 연락처</h2>
            </section>
        </div>
html;

    return $html;
}
?>
