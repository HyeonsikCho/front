<?
class OrderInfoHtml {
    // 명함, 스티커, 전단, 봉투, 초소량 전단지, 디지털 명함
    // 디지털 스티커, 디지털 전단, 디지털 봉투
    // 카다로그/브로슈어, 디지털 카다로그/브로슈어
    const ORDER_INFO_TYPE_1 =
"<label class=\"control-label fix_width75 tar\">○ 주문정보</label>
<br />
<label class=\"control-label cp\">카테고리 : %s</label>
<br />
<label class=\"control-label cp\">인쇄유형 : %s</label>
<br />
<label class=\"control-label cp\">페이지 : %s</label>
<br />
<label class=\"control-label cp\">종이 : %s</label>
<br />
<label class=\"control-label cp\">사이즈 : %s</label>
<br />
<label class=\"control-label cp\">인쇄도수 : %s</label>
<br />
<label class=\"control-label cp\">수량 : %s%s</label>
<br />
<label class=\"control-label cp\">건수 : %s</label>
<br />
<label class=\"control-label cp\">고객메모 : %s</label>
<br /><br />";

    // 책자, 마스터, 디지털 메모지
    const ORDER_INFO_TYPE_2 =
"<label class=\"control-label fix_width75 tar\">○ 주문정보</label>
<br />
<label class=\"control-label cp\">카테고리 : %s</label>
<br />
<label class=\"control-label cp\">인쇄유형 : %s</label>
<br />
<label class=\"control-label cp\">종이 : %s</label>
<br />
<label class=\"control-label cp\">사이즈 : %s</label>
<br />
<label class=\"control-label cp\">인쇄도수 : %s</label>
<br />
<label class=\"control-label cp\">수량 : %s</label>
<br />
<label class=\"control-label cp\">건수 : %s</label>
<br />
<label class=\"control-label cp\">고객메모 : %s</label>
<br /><br />";

    // 책자형 표지/내지 등
    const ORDER_INFO_BOOKLET =
"<label class=\"control-label cp\"> - %s</label>
<br />
<label class=\"control-label cp\">인쇄유형 : %s</label>
<br />
<label class=\"control-label cp\">페이지 : %s</label>
<br />
<label class=\"control-label cp\">종이 : %s</label>
<br />
<label class=\"control-label cp\">인쇄도수 : %s</label>
<br />
<label class=\"control-label cp\">베다유무 : %s</label>
<br /><br />";

    // 디지털 리플렛/팜플렛
    const ORDER_INFO_TYPE_4 =
"<label class=\"control-label fix_width75 tar\">○ 주문정보</label>
<br />
<label class=\"control-label cp\">카테고리 : %s</label>
<br />
<label class=\"control-label cp\">인쇄유형 : %s</label>
<br />
<label class=\"control-label cp\">종이 : %s</label>
<br />
<label class=\"control-label cp\">사이즈 : %s</label>
<br />
<label class=\"control-label cp\">인쇄도수 : %s</label>
<br />
<label class=\"control-label cp\">수량 : %s</label>
<br />
<label class=\"control-label cp\">건수 : %s</label>
<br /><br />";

    // 혼합형
    const ORDER_INFO_TYPE_5 =
"<label class=\"control-label fix_width75 tar\">○ 주문정보</label>
<br />
<label class=\"control-label cp\">카테고리 : %s</label>
<br />
<label class=\"control-label cp\">고객메모 : %s</label>
<br /><br />";

    // 후공정, 옵션 정보
    const ORDER_ADDITIONAL_INFO = 
"<label class=\"control-label cp\">기본후공정 : %s</label>
<br />
<label class=\"control-label cp\">추가후공정 : %s</label>
<br />
<label class=\"control-label cp\">기본옵션 : %s</label>
<br />
<label class=\"control-label cp\">추가옵션 : %s</label>
<br />
<label class=\"control-label cp\">배송 : %s</label>
<br /><br />";

    // 금액 정보
    const ORDER_PRICE_INFO =
"<label class=\"control-label fix_width75 tar\">○ 가격정보</label>
<br />
<label class=\"control-label cp\">기본가격 %s + 추가후공정 %s + 추가옵션 %s + 배송비 %s = 상품가격 %s원</label>
<br />
<label class=\"control-label cp\">- 회원등급할인 %s원</label>
<br />
<label class=\"control-label cp\">- 사용포인트 %s - 적용쿠폰 %s - 이벤트할인 %s = 실결제 %s 원</label>
<br /><br />";

    // 결제 정보
    const ORDER_PAY_INFO =
"<label class=\"control-label fix_width75 tar\">○ 결제정보</label>
<br />
<label class=\"control-label cp\">%s %s원, 쿠폰 %s, 포인트 %s, 할인 %s</label>
<br />";
}
?>
