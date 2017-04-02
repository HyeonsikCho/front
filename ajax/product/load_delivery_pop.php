<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new ProductCommonDAO();
$fb  = new FormBean();

$fb = $fb->getForm();

$cate_sortcode = $fb["cs"];
$cate_name     = $fb["cn"];

$param = array();
$param["cate_sortcode"] = $cate_sortcode;

$rs = $dao->selectCateOrderList($conn, $param);

$tr_form = "<tr><td>%s</td><td>%s</td><td>%s</td><td>%s일</td></tr>";

$tr = '';
while ($rs && !$rs->EOF) {
    $fields = $rs->fields;

    $tr .= sprintf($tr_form, $fields["member_name"]
                           , $fields["depo_finish_date"]
                           , $fields["release_date"]);

    $rs->MoveNext();
}

$html = <<<html
    <header>
        <h2>출고 예정일</h2>
        <button class="close" title="닫기"><img src="/design_template/images/common/btn_circle_x_white.png" alt="X"></button>
    </header>
    <article>
        <ul class="hyphen notice">
            <li>제작일정확인은 선택하신 품목에 대한 <strong>일반적인 출고일을 평균으로 계산</strong>하여 보여드리는 것입니다.</li>
            <li>당사의 출고 기준에 따라 <strong>다소 차이가 있을 수 있습니다.</strong></li>
            <li>제작일정확인 기간 산정은 <strong>토요일, 일요일, 휴무일은 제외</strong>합니다.</li>
            <li>출고 확정일은 <strong>출고실에 입고 확정일 기준</strong>을 표시한 것입니다.</li>
        </ul>
        <h3>선택하신 상품: <strong>{$cate_name}</strong></h3>
        <table class="list narrow">
            <col width="140">
            <col width="140">
            <col width="140">
            <col>
            <thead>
                <tr>
                    <th>아이디</th>
                    <th>접수일</th>
                    <th>출고일</th>
                    <th>출고기간</th>
                </tr>
            </thead>
            <tbody>
                {$tr}
            </tbody>
        </table>
    </article>
html;

echo $html;
?>

