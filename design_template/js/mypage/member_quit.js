$(document).ready(function() {
});

//회원탈퇴 신청
var makeWithdrawal = function(seqno) {

    if (!confirm("정말 탈퇴하시겠습니까?"))
        return false;

    showMask();
    var data = {
        "seqno"  : seqno,
        "reason" : $("#withdraw_reason").val()
    };

    var withdraw_code = "";

    for (var i = 1; i <= 14; i++) {
        if ($("input:checkbox[id='reduce_" + i + "']").is(":checked") == true) {
            withdraw_code += "," + i;   
        }
    }
  
    withdraw_code = withdraw_code.substring(1);
    data.withdraw_code = withdraw_code;

    $.ajax({
        type: "POST",
        data: data,
        url: "/proc/mypage/member_quit/regi_member_reduce_info.php",
        success: function(result) {

            hideMask();
            if (result == 1) {
                alert("탈퇴신청 되었습니다.");
                logout();
            } else {
                alert("탈퇴신청을 실패 하였습니다.");
            }
        },
        error: getAjaxError 
    });
}
