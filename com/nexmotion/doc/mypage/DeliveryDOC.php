<?
function getAddressMain($param) {

    $html = <<<HTML

    <section class="searchCondition">
        <div class="rowWrap">
            <dl class="date">
                <dt>등록일</dt>
                <dd>
                    <input type="text" class="_date" id="from" name="from" value="">
                    ~
                    <input type="text" class="_date" id="to" name="to" value="">
                    <ul class="preset">
                        <li><button onclick="dateSet(0); return false;">오늘</button></li>
                        <li><button onclick="dateSet(1); return false;">어제</button></li>
                        <li><button onclick="dateSet(7); return false;">일주일</button></li>
                        <li><button onclick="dateSet(30); return false;">한달</button></li>
                        <li><button onclick="dateSet('all'); return false;">전체</button></li>
                    </ul>
                </dd>
            </dl>
        </div>
        <div class="rowWrap">
            <dl class="delivery full">
                <dt>검색조건</dt>
                <dd>
                    <select id="category" name="category">
                        <option value="dlvr_name">배송지별칭</option>
                        <option value="recei">받으시는분</option>
                    </select>
                </dd>
                <dd class="last">
                    <div class="inputWrap"><input type="text" id="searchkey" name="searchkey"></div>
                </dd>
            </dl>
        </div>
        <button onclick="searchDlvrList();" class="search">검색</button>
    </section>
    <form name="frm" id="frm">
    <table class="list searchResult favorite">
        <caption>
            <div class="resultNum" id="resultNum">
                $param[html]
            </div>
            <select class="listNum" onchange="changeListNum(this.value);">
                <option value="10">10개씩 보기</option>
                <option value="20">20개씩 보기</option>
                <option value="50">50개씩 보기</option>
            </select>
        </caption>
        <colgroup>
            <col width="40">
            <col width="80">
            <col width="100">
            <col width="100">
            <col>
            <col width="100">
            <col width="80">
        </colgroup>
        <thead>
            <tr>
                <th><input type="checkbox" class="_general" onclick="allCheck();"></th>
                <th>등록일</th>
                <th>배송지별칭</th>
                <th>받으시는 분</th>
                <th>주소</th>
                <th>연락처</th>
                <th>관리</th>
            </tr>
        </thead>
        <tbody id="list">
            $param[list]
        </tbody>
    </table>
    </form>
    <ul class="paging" id="paging">
        $param[paging]
    </ul>
    <div class="function narrow">
        <button class="function" onclick="updateBasicDlvr(); return false;">기본 배송지 설정</button>
        <button class="function" onclick="multiDel(); return false;">선택 배송지 삭제</button>
        <div class="right">
            <strong><button class="function" onclick="layerPopup('l_address', 'popup/l_address_regi.html')">나의 배송지 추가</button></strong>
        </div>
    </div>
</section>

HTML;

    echo $html;
}

function getAddressTypeMain() {

    $html = <<<HTML

    <section class="deliveryType">
        <dl class="home">
            <dt>무료 택배</dt>
            <dd>
                <p>신청 후 일일 일회 묶음배송이 40,000원을 초과하여 주문하시는 고객에 한하여 무료로 택배를 발송해 드립니다.<br>단, 명함/스티커의 품목에 한하여 적용이 되고 제주도 및 해외는 제외입니다. 당사의 묶음 포장 기준을 참고 하십시요.
                </p>
                <em>묶음배송 기준<br>40,000원 초과 시<br> 자동 적용</em>
            </dd>
        </dl>
        <dl class="direct">
            <dt>무료 직배송</dt>
            <dd>
                <p>신청 후 월 거래금액이 330,000원(VAT 포함) 이상 시에는 무료배송 기준이 되어 다음달에는 자동으로 55,000원(VAT포함) 이 이월됩니다. 이월된 55,000원은 환불되지 않습니다.<br>다만 이월된 금액의 사용은 고객의 요청에 따라 일시정지나 포인트 이월금으로 활용할 수 있습니다.
                </p>
                <em>55,000(VAT포함)</em>
                <!--<p class="freeStatus">무료 직배송 혜택까지<br><strong>&#8361;130,000</strong><br>남았습니다.</p>-->
                <p class="freeStatus">준비중 입니다.</p>
                <!--
                <button class="applicated" onclick="layerPopup('l_directApplication', 'popup/l_directapplication.html')" disabled>이용 중</button>
                -->
               <!-- <button onclick="layerPopup('l_directApplication', 'popup/l_directapplication.html')">신청</button>-->
            </dd>
        </dl>
        <dl class="friend">
            <dt>배송친구</dt>
            <dd>
                <p>배송친구를 신청하시면 가까운 근처의 배송친구 메인 업체에서 물건을 수령하실 수 있습니다.<br>물량이 주기적이지 않거나 적은 소규모의 기획사무실에 적합하며 월 11,000원의 저렴한 비용으로 물건을 받아 보실 수 있습니다.</p>
                <em>11,000원(VAT포함)</em>
                <!--<button onclick="layerPopup('l_friendApplication', 'popup/l_friendapplication.html');">배송친구 신청</button>-->
                <button onclick="alert('준비중 입니다.');">배송친구 신청</button>
                <p>배송친구 메인업체는 자사에서 엄격한 심사를 거쳐 선정되며 선정된 배송친구 메인업체는 포인트나 쿠폰 등 다양한 혜택을 드립니다.</p>
                <!--<button onclick="layerPopup('l_friendMainApplication', 'popup/l_friendmainapplication.html');">배송친구 메인 업체 신청</button>-->
                <button onclick="alert('준비중 입니다.');">배송친구 메인 업체 신청</button>
            </dd>
        </dl>
    </section>

HTML;

    echo $html;
}
?>
