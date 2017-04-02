<?
function orderStatus($param) {
    $state_arr = $param["state_arr"];

    $tot_cnt = intval($param["tot_cnt"]) - intval($state_arr["주문"]);
    $tot_cnt = $tot_cnt < 0 ?  0 : $tot_cnt;

    $html = <<<HTML

        <dl class="orderNum all" onclick="goPage('/mypage/order_all.html');" style="cursor:pointer;">
            <dt>전체주문</dt>
            <dd id="tot_cnt">$tot_cnt</dd>
        </dl>
        <dl class="orderNum cart" onclick="goPage('/mypage/cart.html');" style="cursor:pointer;">
            <dt>장바구니</dt>
            <dd>$state_arr[주문]</dd>
        </dl>
        <ul class="byStatus _toggle">
            <li class="waiting" id="waiting">
                <button onclick="getOrderList('입금');">
                    <span class="name">입금</span>
                </button>
                <span class="num">
                    <span class="total" id="waiting_cnt">$state_arr[입금]</span>
                </span>
            </li>
            <li class="application" id="application">
                <button onclick="getOrderList('접수');">
                    <span class="name">접수</span>
                </button>
                <span class="num details">
                    <span class="total" id="application_tot_cnt">$state_arr[접수]</span>
                <!--
                    <dl>
                        <dt>대기</dt>
                        <dd id="application_st_cnt">$param[application_st_cnt]</dd>
                        <dt>진행중</dt>
                        <dd id="application_nw_cnt">$param[application_nw_cnt]</dd>
                        <dt>시안확인</dt>
                        <dd id="application_pr_cnt">$param[application_pr_cnt]</dd>
                        <dt>보류</dt>
                        <dd id="application_de_cnt">$param[application_de_cnt]</dd>
                    </dl>
                -->
                </span>
            </li>
            <li class="set" id="set">
                <button onclick="getOrderList('조판');">
                    <span class="name">조판</span>
                </button>
                <span class="num details">
                    <span class="total">$state_arr[조판]</span>
                    <!--
                    <dl>
                        <dt>대기</dt>
                        <dd>$param[set_st_cnt]</dd>
                        <dt>진행중</dt>
                        <dd>$param[set_nw_cnt]</dd>
                        <dt>누락</dt>
                        <dd>$param[set_om_cnt]</dd>
                    </dl>
                    -->
                </span>
            </li>
            <li class="print" id="print">
                <button onclick="getOrderList('출력');">
                    <span class="name">출력</span>
                </button>
                <span class="num details">
                    <span class="total">$state_arr[출력]</span>
                    <!--
                    <dl>
                        <dt>준비</dt>
                        <dd>$param[print_rd_cnt]</dd>
                        <dt>대기</dt>
                        <dd>$param[print_st_cnt]</dd>
                        <dt>진행중</dt>
                        <dd>$param[print_nw_cnt]</dd>
                    </dl>
                    -->
                </span>
            </li>
            <li class="process" id="process">
                <button onclick="getOrderList('인쇄');">
                    <span class="name">인쇄</span>
                </button>
                <span class="num details">
                    <span class="total">$state_arr[인쇄]</span>
                    <!--
                    <dl>
                        <dt>준비</dt>
                        <dd>$param[process_rd_cnt]</dd>
                        <dt>대기</dt>
                        <dd>$param[process_st_cnt]</dd>
                        <dt>진행중</dt>
                        <dd>$param[process_nw_cnt]</dd>
                    </dl>
                    -->
                </span>
            </li>
            <li class="post" id="post">
                <button onclick="getOrderList('후공정');">
                    <span class="name">후공정</span>
                </button>
                <span class="num details">
                    <span class="total">$state_arr[후공정]</span>
                    <!--
                    <dl>
                        <dt>준비</dt>
                        <dd>$param[post_rd_cnt]</dd>
                        <dt>대기</dt>
                        <dd>$param[post_st_cnt]</dd>
                        <dt>진행중</dt>
                        <dd>$param[post_nw_cnt]</dd>
                    </dl>
                    -->
                </span>
            </li>
            <li class="stock" id="stock">
                <button onclick="getOrderList('입고');">
                    <span class="name">입고</span>
                </button>
                <span class="num details">
                    <span class="total">$state_arr[입고]</span>
                    <!--
                    <dl>
                        <dt>대기</dt>
                        <dd>$param[stock_rd_cnt]</dd>
                        <dt>진행중</dt>
                        <dd>$param[stock_nw_cnt]</dd>
                    </dl>
                    -->
                </span>
            </li>
            <li class="release" id="release">
                <button onclick="getOrderList('출고');">
                    <span class="name">출고</span>
                </button>
                <span class="num details">
                    <span class="total">$state_arr[출고]</span>
                    <!--
                    <dl>
                        <dt>대기</dt>
                        <dd>$param[release_st_cnt]</dd>
                        <dt>진행중</dt>
                        <dd>$param[release_nw_cnt]</dd>
                    </dl>
                    -->
                </span>
            </li>
            <li class="delivery" id="delivery">
                <button onclick="getOrderList('배송');">
                    <span class="name">배송</span>
                </button>
                <span class="num details">
                    <span class="total">$state_arr[배송]</span>
                    <!--
                    <dl>
                        <dt>대기</dt>
                        <dd>$param[delivery_st_cnt]</dd>
                        <dt>진행중</dt>
                        <dd>$param[delivery_nw_cnt]</dd>
                    </dl>
                    -->
                </span>
            </li>
            <li class="complete" id="complete">
                <button onclick="getOrderList('구매확정');">
                    <span class="name">완료</span>
                </button>
                <span class="num">
                    <span class="total">$state_arr[구매확정]</span>
                </span>
            </li>
        </ul>
        <table class="list ordered _details" id="order_list">
            $param[list]
        </table>

HTML;

    return $html;
}
?>
