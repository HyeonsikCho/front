<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/CommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/eventmall/EventmallHTML.php');
class EventmallDAO extends CommonDAO {

    function __construct() {
    }

    /*
     * 오특이 이벤트 list select
     * $conn : db connection
     * return : resulttest
     */
    function selectSpecialList($conn) {
        
        if (!$this->connectioncheck($conn)) return false; 

        $query  = "\n    SELECT   OE.name";
        $query .= "\n           , OE.end_hour";
        $query .= "\n           , OE.amt";
        $query .= "\n           , OE.amt_unit";
        $query .= "\n           , OE.sum_price";
        $query .= "\n           , OE.sale_price";
        $query .= "\n           , OF.origin_file_name";
        $query .= "\n           , OF.save_file_name";
        $query .= "\n           , OF.file_path";
        $query .= "\n           , OE.cate_sortcode";
        $query .= "\n           , OE.cate_paper_mpcode";
        $query .= "\n           , OE.cate_stan_mpcode";
        $query .= "\n           , OE.cate_print_mpcode";
        $query .= "\n           , C.cate_name";
        $query .= "\n           , CP.name AS paper_name";
        $query .= "\n           , CP.dvs AS paper_dvs";
        $query .= "\n           , CP.color AS paper_color";
        $query .= "\n           , CP.basisweight AS paper_basisweight";
        $query .= "\n           , PS.name AS stan_name";
        $query .= "\n           , PP.name as print_name";
        $query .= "\n      FROM   oevent_event OE";
        $query .= "\n             LEFT JOIN oevent_file OF";
        $query .= "\n             ON OE.oevent_event_seqno = OF.oevent_event_seqno";
        $query .= "\n           , cate C";
        $query .= "\n           , cate_paper CP";
        $query .= "\n           , cate_stan CS";
        $query .= "\n           , prdt_stan PS";
        $query .= "\n           , cate_print CPR";
        $query .= "\n           , prdt_print PP";
        $query .= "\n     WHERE   OE.dsply_yn = \"Y\"";
        $query .= "\n       AND   OE.end_hour >= date_format(now(), '%Y-%m-%d %H:%i:%s')";
        $query .= "\n       AND   OE.cate_sortcode = C.sortcode";
        $query .= "\n       AND   OE.cate_paper_mpcode = CP.mpcode";
        $query .= "\n       AND   OE.cate_stan_mpcode = CS.mpcode";
        $query .= "\n       AND   CS.prdt_stan_seqno = PS.prdt_stan_seqno";
        $query .= "\n       AND   OE.cate_print_mpcode = CPR.mpcode";
        $query .= "\n       AND   CPR.prdt_print_seqno = PP.prdt_print_seqno";
        $query .= "\n  ORDER BY   end_hour        ";

        $result = $conn->Execute($query);

        return $result;
    }

    /*
     * 요즘바빠요 이벤트 list select
     * $conn : db connection
     * return : resulttest
     */
    function selectPopularList($conn, $sortcode) {
        
        if (!$this->connectioncheck($conn)) return false;


        $query  = "\n    SELECT   NE.name";
        $query .= "\n           , NE.amt";
        $query .= "\n           , NE.amt_unit";
        $query .= "\n           , NE.sum_price";
        $query .= "\n           , NE.sale_price";             
        $query .= "\n           , NF.origin_file_name";
        $query .= "\n           , NF.save_file_name";
        $query .= "\n           , NF.file_path";              
        $query .= "\n           , NE.cate_sortcode";
        $query .= "\n           , NE.cate_paper_mpcode";
        $query .= "\n           , NE.cate_stan_mpcode";
        $query .= "\n           , NE.cate_print_mpcode";      
        $query .= "\n           , C.cate_name";
        $query .= "\n           , CP.name AS paper_name";
        $query .= "\n           , CP.dvs AS paper_dvs";
        $query .= "\n           , CP.color AS paper_color";           
        $query .= "\n           , CP.basisweight AS paper_basisweight";
        $query .= "\n           , PS.name AS stan_name";          
        $query .= "\n           , PP.name as print_name";
        $query .= "\n      FROM   nowadays_busy_event NE";
        $query .= "\n             LEFT JOIN nowadays_busy_file NF";  
        $query .= "\n             ON NE.nowadays_busy_event_seqno = ";
        $query .= "\n                NF.nowadays_busy_event_seqno";
        $query .= "\n           , cate C";
        $query .= "\n           , cate_paper CP";
        $query .= "\n           , cate_stan CS";
        $query .= "\n           , prdt_stan PS";
        $query .= "\n           , cate_print CPR";
        $query .= "\n           , prdt_print PP";
        $query .= "\n     WHERE   NE.dsply_yn = \"Y\"";
        //이 값을 인자로 받아와야함
        $query .= "\n       AND   NE.cate_sortcode like '%s%%'";
        $query .= "\n       AND   NE.cate_sortcode = C.sortcode";
        $query .= "\n       AND   NE.cate_paper_mpcode = CP.mpcode";
        $query .= "\n       AND   NE.cate_stan_mpcode = CS.mpcode";
        $query .= "\n       AND   CS.prdt_stan_seqno = PS.prdt_stan_seqno";
        $query .= "\n       AND   NE.cate_print_mpcode = CPR.mpcode";
        $query .= "\n       AND   CPR.prdt_print_seqno = PP.prdt_print_seqno";
        $query .= "\n  ORDER BY   cate_sortcode";

        $query = sprintf($query, $sortcode);
        $result = $conn->Execute($query);

        return $result;
    }
    
    /*
     * 쿠폰 이벤트 list select
     * $conn : db connection
     * $seqno : seqno
     * return : resulttest
     */
    function selectCouponList($conn, $seqno="") {
    
        if (!$this->connectioncheck($conn)) return false;


        $query .= "\n   SELECT  C.cp_name";          
        $query .= "\n          ,C.val";
        $query .= "\n          ,C.max_sale_price";
        $query .= "\n          ,C.min_order_price";
        $query .= "\n          ,C.unit";
        $query .= "\n          ,C.regi_date";
        $query .= "\n          ,C.object_appoint_way";
        $query .= "\n          ,C.use_yn";
        $query .= "\n          ,C.public_amt";
        $query .= "\n          ,C.cp_seqno";
        $query .= "\n          ,C.public_period_start_date";
        $query .= "\n          ,C.public_period_end_date";
        $query .= "\n          ,C.usehour_yn";
        $query .= "\n          ,C.usehour_start_hour";
        $query .= "\n          ,C.usehour_end_hour";
        $query .= "\n          ,C.expire_dvs";
        $query .= "\n          ,C.expire_public_day";
        $query .= "\n          ,C.expire_extinct_date";
        $query .= "\n          ,C.cp_extinct_date";
        $query .= "\n          ,C.cp_expo_yn";
        $query .= "\n          ,CF.file_path";               
        $query .= "\n          ,CF.save_file_name";               
        $query .= "\n     FROM  cp C";                     
        $query .= "\n          ,cp_file CF";           
        $query .= "\n    WHERE  C.cp_seqno = CF.cp_seqno";
        $query .= "\n      AND  C.object_appoint_way = 'N'";
        $query .= "\n      AND  C.use_yn = 'Y'"; 
        $query .= "\n      AND  C.cp_expo_yn = 'Y'"; 
        $query .= "\n      AND  DATE_FORMAT(sysdate(), '%Y-%c-%d') >= ";
        $query .= "             DATE_FORMAT(C.public_period_start_date, '%Y-%c-%d')";
        $query .= "\n      AND  DATE_FORMAT(sysdate(), '%Y-%c-%d') <= ";
        $query .= "             DATE_FORMAT(C.public_period_end_date, '%Y-%c-%d')";

        if ($seqno != "") {
            $query .= "\n      AND  C.cp_seqno = '". $seqno ."'"; 
        }

        $result = $conn->Execute($query);

        return $result;
    }

	/**
	 * 썸네일 생성
	 */
    function cpMakeThumbnail ($param) {
		$fs = $param["fs"];
		/*썸네일 생성*/
		$arrImgInfo = getimagesize($_SERVER["DOCUMENT_ROOT"] . $fs);
		$width_orig = $arrImgInfo[0];
		$height_orig = $arrImgInfo[1];
		$w_offset = 0;
		$h_offset = 0;

		//썸네일 사이즈
		$req_width = $param["req_width"];
		$req_height = $param["req_height"];

        //썸네일 PATH 
		$arrPath = explode(".", $fs);
		$thumb_path = $arrPath[0] . "_" . $req_width . "_" . $req_height . "." . $arrPath[1];

		if($height_orig > $width_orig){
			$h_offset = ((($req_width * ($height_orig/$width_orig))-$req_width)/2);
			$thumbCmd = $req_width.'x';
		}
		if($height_orig < $width_orig){
			$w_offset = ((($req_height * ($width_orig/$height_orig))-$req_height)/2);
			$thumbCmd = 'x'.$req_height;
		}
		if($height_orig == $width_orig){
			$thumbCmd = $req_width.'x'.$req_height;
		} 


		//썸내일 파일명 설정
		$size = "[".$req_width."-".$req_height."]";

		//cut offset설정
		$crop = $req_width.'x'.$req_height.'+'.$w_offset."+".$h_offset;

		$convertString = sprintf("convert %s -resize '%s>' -crop %s +repage  -quality 100 %s",
				$_SERVER["DOCUMENT_ROOT"] . $fs,
				$thumbCmd,
				$crop,
				$_SERVER["DOCUMENT_ROOT"] . $thumb_path
				);

        if (is_file($_SERVER["DOCUMENT_ROOT"] . $thumb_path) === true) {
            return $thumb_path;
        }
		
        @exec($convertString);
		/*썸네일 생성 끝*/ 

        return $thumb_path;
    }

    /*
     * 카테고리 list select
     * $conn : db connection
     * $sortcode : sortcode
     * return : resulttest
     */
    function selectCateList($conn, $seqno) {
    
        if (!$this->connectioncheck($conn)) return false;
        
        $query .= "\n   SELECT cate_name";
        $query .= "\n     FROM cate c, cp_cate cc";
        $query .= "\n    WHERE c.sortcode = cc.cp_cate_sortcode";
        $query .= "\n      AND  cc.cp_seqno = %d";
    
        $query = sprintf($query, $seqno);

        $result = $conn->Execute($query);
        return $result;
    }
    /*
     * 포인트/쿠폰 이벤트 쿠폰 다운로드시 발행수량 -1 업데이트
     * $conn : db connection
     * return : resulttest
     */
    function updatePointCouponDownload($conn) {

        if (!$this->connectioncheck($conn)) return false;
        
        $query  = "\n    UPDATE  cp";
        $query .= "\n       SET  public_amt = public_amt - 1";
        $query .= "\n     WHERE  cp_seqno = %s";
        
        $query = sprintf($query, $cp_seqno);

        $result = $conn->Execute($query);
        return $result;
        
    }
}
?>
