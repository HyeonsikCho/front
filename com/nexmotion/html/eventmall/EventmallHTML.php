<?
/**
 * 오특이 리스트 html 생성
 * @param $conn = connection identifier
 * @param $rs = 검색결과
 *
 * return 오특이 리스트 html
 */
function makeSpecialListHTML($conn, $rs) {
    $ret = "";

    $main_list  = "<section class=\"main\">";
    $main_list .= "  <figure><img src=\"%s%s\"></figure>";
    $main_list .= "  <div class=\"remainTime\">";
    $main_list .= "    <input type=\"hidden\" class=\"Month\" value=\"%s\">";
    $main_list .= "    <input type=\"hidden\" class=\"Day\" value=\"%s\">";
    $main_list .= "    <input type=\"hidden\" class=\"Hour\" value=\"%s\">";
    $main_list .= "    <input type=\"hidden\" class=\"Minute\" value=\"%s\">";
    $main_list .= "  </div>";
    $main_list .= "  <p class=\"name\">%s</p> ";
    $main_list .= "  <ul class=\"feature\">";
    $main_list .= "    <li>%s%s %s %s</li>";
    $main_list .= "    <li>%s</li>";
    $main_list .= "    <li>%s</li>";
    $main_list .= "    <li>%s%s</li>";
    $main_list .= "  </ul>";
    $main_list .= "  <p class=\"price\">%s<span class=\"unit\">원</span></p>";
    $main_list .= "  <div class=\"percentage\">%s";
    $main_list .= "    <span class=\"unit\">%%</span>";
    $main_list .= "  </div>";
    $main_list .= "  <div class=\"function\">";
    $main_list .= "    <strong><button type=\"button\">즉시주문</button></strong>";
    $main_list .= "    <button type=\"button\">장바구니</button>";
    $main_list .= "    <a href=\"#none\" target=\"_blank\" class=\"sub\">상세보기</a>";
    $main_list .= "  </div>";
    $main_list .= "</section>";
    
  
    $list  = "  <li>";
    $list .= "    <figure><img src=\"%s%s\"></figure>";
    $list .= "    <dl class=\"remainTime\">";
    $list .= "      <dt>남은시간</dt>";
    $list .= "      <dd>";
    $list .= "        <input type=\"hidden\" class=\"Month\" value=\"%s\">";
    $list .= "        <input type=\"hidden\" class=\"Day\" value=\"%s\">";
    $list .= "        <input type=\"hidden\" class=\"Hour\" value=\"%s\">";
    $list .= "        <input type=\"hidden\" class=\"Minute\" value=\"%s\">";
    $list .= "        <span class=\"hour\"></span>";
    $list .= "        <span class=\"minute\"></span>";
    $list .= "        <span class=\"second\"></span>";
    $list .= "      </dd>";
    $list .= "    </dl>";
    $list .= "    <p class=\"name\">%s</p>";
    $list .= "    <ul class=\"feature\">";
    $list .= "      <li>%s%s %s %s</li>";
    $list .= "      <li>%s</li>";
    $list .= "      <li>%s</li>";
    $list .= "      <li>%s%s</li>";
    $list .= "    </ul>";
    $list .= "    <p class=\"price\">%s<span class=\"unit\">원</span></p>";
    $list .= "    <div class=\"percentage\">%s<span class=\"unit\">%%</span></div>";
    $list .= "    <div class=\"function\">";
    $list .= "      <strong><button type=\"button\">즉시주문</button></strong>";
    $list .= "      <button type=\"button\">장바구니</button>";
    $list .= "      <a href=\"#none\" target=\"_blank\" class=\"sub\">상세보기</a>";
    $list .= "    </div>";
    $list .= "  </li>";
    
    $i = 1;
    while ($rs && !$rs->EOF) {
    
        $fields = $rs->fields;

        $file_path = $fields["file_path"];
        $save_file_name = $fields["save_file_name"];
        $end_hour = $fields["end_hour"];
        $cate_name = $fields["cate_name"];
        $paper_name = $fields["paper_name"];
       
        //구분값이 없을경우에는 표시없이 넘어감.
        $paper_dvs = $fields["paper_dvs"];
        if($paper_dvs == '-') $paper_dvs="";
        
        $paper_color = $fields["paper_color"];
        $paper_basisweight = $fields["paper_basisweight"];
        $stan_name = $fields["stan_name"];
        $print_name = $fields["print_name"];
        $amt = $fields["amt"];
        $amt_unit = $fields["amt_unit"];
        $sum_price = $fields["sum_price"];
        $sale_price = $fields["sale_price"];
        
        //할인율
        $percentage = $sale_price / ($sum_price + $sale_price) * 100;
        $percentage = ceil($percentage);

        if ($i == 1) {
            $ret = sprintf($main_list, $file_path
                                     , $save_file_name
                                     , substr($end_hour, 5, 2)
                                     , substr($end_hour, 8, 2)
                                     , substr($end_hour, 11, 2)
                                     , substr($end_hour, 14, 2)
                                     , $cate_name
                                     , $paper_name
                                     , $paper_dvs
                                     , $paper_color
                                     , $paper_basisweight
                                     , $stan_name
                                     , $print_name
                                     , $amt
                                     , $amt_unit
                                     , number_format($sum_price)
                                     , $percentage);
        } else {
            if ($i == 2) $ret .= "<ul class=\"list\">";
            $ret .= sprintf($list, $file_path
                                 , $save_file_name
                                 , substr($end_hour, 5, 2)
                                 , substr($end_hour, 8, 2)
                                 , substr($end_hour, 11, 2)
                                 , substr($end_hour, 14, 2)
                                 , $cate_name
                                 , $paper_name
                                 , $paper_dvs
                                 , $paper_color
                                 , $paper_basisweight
                                 , $stan_name
                                 , $print_name
                                 , $amt
                                 , $amt_unit
                                 , number_format($sum_price)
                                 , $percentage);
            if ($rs->EOF) $ret .= "</ul>";
        }
        $i++;
        $rs->MoveNext();
    }
    return $ret;
}

/**
 * 요즘바빠요 ajax 리스트 html 생성
 * @param $conn = connection identifier
 * @param $rs = 검색결과
 *
 * return 요즘바빠요 ajax 리스트 html
 */
function makePopularListHTML($conn, $rs) {
    $ret = "";

    $list  = "  <li>";
    $list .= "    <figure>";
    $list .= "      <img src=\"%s%s\">";  //1,2.filepath, savefile
    $list .= "        <figcaption class=\"percentage\">%s"; //3.percentage
    $list .= "          <span class=\"unit\">%%</span>";
    $list .= "        </figcaption>";
    $list .= "      </figure>";
    $list .= "    <ul class=\"feature\">";
    $list .= "      <li>%s"; //4.cate_name
    $list .= "      /%s"; //5.paper_name
    $list .= "      &nbsp;%s"; //6.paper_dvs
    $list .= "      %s"; //7.paper_color
    $list .= "      %s<br />"; //8.paper_basisweight
    $list .= "      %s"; //9.stan_name
    $list .= "      /%s"; //10.print_name
    $list .= "      /%s%s</li>"; //11,12.amt, amt_unit
    $list .= "    </ul>";
    $list .= "    <p class=\"price\">%s<span class=\"unit\">원</span></p>"; //13.price
    $list .= "  </li>";

    $i = 1;
    while ($rs && !$rs->EOF) {
    
        $fields = $rs->fields;

        $cate_sortcode = $fields["cate_sortcode"];
        $cate_sortcode_desc = "";
        if (substr($cate_sortcode, 0, 3) == "001") {
            $cate_sortcode_desc = "명함";
        } else if (substr($cate_sortcode, 0, 3) == "002") {
            $cate_sortcode_desc = "스티커";
        } else if (substr($cate_sortcode, 0, 3) == "003") {
            $cate_sortcode_desc = "전단";
        } else if (substr($cate_sortcode, 0, 3) == "004") {
            $cate_sortcode_desc = "광고홍보물";
        } else if (substr($cate_sortcode, 0, 3) == "005") {
            $cate_sortcode_desc = "봉투";
        } else if (substr($cate_sortcode, 0, 3) == "006") {
            $cate_sortcode_desc = "마스터";
        } else if (substr($cate_sortcode, 0, 3) == "007") {
            $cate_sortcode_desc = "초소량인쇄";
        } else if (substr($cate_sortcode, 0, 3) == "008") {
            $cate_sortcode_desc = "디지털인쇄";
        } else if (substr($cate_sortcode, 0, 3) == "009") {
            $cate_sortcode_desc = "패키지";
        } else if (substr($cate_sortcode, 0, 3) == "000") {
            $cate_sortcode_desc = "준비중";
            //청첩장, 실사출력, 판촉물은 아직 준비중
        } 
        //파일 
        $file_path = $fields["file_path"];
        $save_file_name = $fields["save_file_name"];
        //가격 
        $sum_price = $fields["sum_price"];
        $sale_price = $fields["sale_price"];
        //할인율
        $percentage = $sale_price / ($sum_price + $sale_price) * 100;
        $percentage = ceil($percentage);
        
        $cate_name = $fields["cate_name"];
        $paper_name = $fields["paper_name"];
        //구분값이 없을경우에는 표시없이 넘어감.
        $paper_dvs = $fields["paper_dvs"];
        if($paper_dvs == '-') $paper_dvs="";
        $paper_color = $fields["paper_color"];
        $paper_basisweight = $fields["paper_basisweight"];
        $stan_name = $fields["stan_name"];
        $print_name = $fields["print_name"];
        $amt = $fields["amt"];
        $amt_unit = $fields["amt_unit"];
        
        if ($i == 1) {
            $ret  = "<h3>". $cate_sortcode_desc ."</h3>";
            $ret .= "<ul class=\"list sub\">";
        }

        $ret .= sprintf($list, $file_path
                             , $save_file_name
                             , $percentage
                             , $cate_name
                             , $paper_name
                             , $paper_dvs
                             , $paper_color
                             , $paper_basisweight
                             , $stan_name
                             , $print_name
                             , $amt
                             , $amt_unit
                             , number_format($sum_price));

        if ($rs->EOF) $ret .= "</ul>";

        $i++;
        $rs->MoveNext();
    }

    return $ret;
}

/**
 * 쿠폰 리스트 html 생성
 * @param $conn = connection identifier
 * @param $rs = 검색결과
 *
 * return 쿠폰 리스트 html
 */
function makeCouponListHTML($conn, $rs, $dao) {
    $ret = "";

    $list  = "<li>";
    $list .= "  <dl style=\"background: url('%s') 13px no-repeat;background-size: 113px 158px;\">";
    $list .= "    <dt>%s%s 할인</dt>"; //val, unit
    $list .= "    <dd>";
    $list .= "      <button type=\"button\" class=\"download\"";
    $list .= "       onclick=\"layerPopup('l_coupon', 'popup/l_coupon.html?seq=%s');\">";
                                                                        //cp_seqno
    $list .= "       쿠폰 다운로드</button>";
    $list .= "      <dl>";
    $list .= "        <dt>남은쿠폰</dt>";
    $list .= "        <dd>%s매</dd>"; //public_amt
    $list .= "      </dl>";
    $list .= "      <dl>";
    $list .= "        <dt>사용기간</dt>";
    $list .= "        <dd>%s %s</dd>";  
                      //public_start_date, use_start_hour
                      //public_end_date, use_end_hour
    $list .= "      </dl>";
    $list .= "    </dd>";
    $list .= "  </dl>";
    $list .= "</li>";
   
    $usehour = ""; 
    while ($rs && !$rs->EOF) {
    
        $fields = $rs->fields;
        
        $file_path = $fields["file_path"];
        $save_file_name = $fields["save_file_name"];

        /*Thumbnail 이미지 만들기
         * background-size 를 하는데 Thumbnail 이미지 만드는 이유
         * - 트래픽 때문에
         */
        $param = array();
        $param["fs"] = $file_path . $save_file_name;
        $param["req_width"] = "113";
        $param["req_height"] = "158";
        $thumb_path = $dao->cpMakeThumbnail($param);
        
        $unit = $fields["unit"];
        if ($unit == '원') {
            $val = number_format($fields["val"]);
        
        } else {
            $val = $fields["val"];
        }
        $cp_seqno = $fields["cp_seqno"];
        $public_amt = $fields["public_amt"];

        $public_period_start_date = $fields["public_period_start_date"];
        $public_period_end_date = $fields["public_period_end_date"];
        $usehour_yn = $fields["usehour_yn"];
        $usehour_start_hour = $fields["usehour_start_hour"];
        $usehour_end_hour= $fields["usehour_end_hour"];
        $cp_extinct_date = $fields["cp_extinct_date"];
        $expire_dvs = $fields["expire_dvs"];
     
        //남은쿠폰
        //  기존쿠폰수 - 발급된 쿠폰수
        $param = array();
        $param["table"] = "cp_issue";
        $param["where"]["cp_seqno"] = $cp_seqno;
        $result = $dao->countData($conn, $param);
        $issueCouponCount = $result->fields["cnt"];
        $leftCouponCount = $public_amt - $issueCouponCount;

        //남은쿠폰수 0 일때 
        if ($leftCouponCount == 0) {
            $rs->MoveNext();
            continue;
        } 

        //사용기간 
        if ($expire_dvs == 3) {
            $period = $public_period_start_date ." ~ 제한없음";
        } else {
            $period = $public_period_start_date ." ~ ". $cp_extinct_date;
        }
        
        //시간 사이에 ':' 넣어줌
        $usehour_start_hour = substr($usehour_start_hour, 0, 2) .":". substr($usehour_start_hour, -2);
        $usehour_end_hour = substr($usehour_end_hour, 0, 2) .":". substr($usehour_end_hour, -2);
        
        //사용기간_시간
        $userhour = "";
        if ($usehour_yn == "Y") {
            $usehour = "(". $usehour_start_hour. " ~ ". $usehour_end_hour.")";
        } else {
            $usehour = "";
        }

        $ret .= sprintf($list, $thumb_path
                             , $val
                             , $unit
                             , $cp_seqno
                             , $leftCouponCount
                             , $period
                             , $usehour);

        $rs->MoveNext();
    }
    return $ret;
}

?>
