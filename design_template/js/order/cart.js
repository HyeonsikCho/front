/**
 * @brief 선택된 장바구니 목록 삭제
 */
var delCart = function() {
    var seqArr = new Array();

    $("input[name='seq']:checked").each(function(i) {
        seqArr[i] = $(this).val();
    });

    if (seqArr.length === 0) {
        alert("삭제할 항목을 선택해주세요.");
        return false;
    }

    if (confirm("선택하신 항목을 삭제하시겠습니까?") === false) {
        return false;
    }

    var url = "/proc/order/delete_order.php";
    var data = {
        "seq" : seqArr
    };
    var callback = function(result) {
        if (result === 'F') {
            alert("항목 삭제에 실패했습니다.");
            return false;
        }

        location.reload();
    };

    ajaxCall(url, "text", data, callback);
};

/**
 * @brief 주문서 작성 페이지로 이동
 *
 * @param dvs = 선택주문인지 전체주문인지 구분값
 */
var goSheet = function(dvs) {
    if (dvs === true) {
        location.href = "/order/sheet.html";
        return false;
    }

    var method = "post";
    var seq = '';
    $("input[name='seq']:checked").each(function(i) {
        seq += $(this).val() + '|';
    });

    if(seq == "") {
        alert("선택된 상품이 없습니다.");
        return;
    }

    var form = document.createElement("form");
    form.setAttribute("action", "/order/sheet.html");
    form.setAttribute("method", method);

    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", "seq");
    hiddenField.setAttribute("value", seq);

    form.appendChild(hiddenField);
    document.body.appendChild(form);

    form.submit();
};
