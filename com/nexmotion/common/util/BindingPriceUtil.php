<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/CalcPriceUtil.php");

class BindingPriceUtil {
    var $cate_sortcode;
    var $amt;
    var $page;
    var $price;
    var $coating_yn;
    var $depth1;
    var $stan_name;
    var $pos_num;

    function __construct($param) {
        $this->cate_sortcode = $param["cate_sortcode"];
        $this->amt           = intval($param["amt"]);
        $this->page          = intval($param["page"]);
        $this->price         = intval($param["price"]);
        $this->coating_yn    = $param["coating_yn"];
        $this->depth1        = $param["depth1"];
        $this->stan_name     = $param["stan_name"];
        $this->pos_num       = $param["pos_num"];
    }

    /**
     * @brief 제본비 계산
     *
     * @return 제본비
     */
    function calcBindingPrice() {
        if ($this->stan_name === "8절" ||
                $this->stan_name === "A3") {
            return $this->calc8cutA3BindingPrice();
        } else {
            return $this->calcEtcBindingPrice();
        }
    }

    /**
     * @brief 8절, A3일 때 제본비 계산
     *
     * @return 제본비
     */
    function calc8cutA3BindingPrice() {
        $util = new CalcPriceUtil(array());

        $amt = $this->amt;
        $price = 0;

        if ($this->depth1 === "중철제본") {
            $standard_price = 126000;

            $base_price = 0;
            if ($amt <= 1000) {
                $base_price = 42;
            } else if (1000 < $amt && $amt <= 2000) {
                $base_price = 36;
            } else if (2000 < $amt && $amt <= 4000) {
                $base_price = 32;
            } else if (4000 < $amt) {
                $base_price = 21;
            }

            $mac_cnt = $util->getMachineCount($this->page, $this->pos_num);
            $mac_cnt = $mac_cnt["hong"] + $util->getDonMachineCount($mac_cnt["don"]);

            $price = $base_price * $mac_cnt * $amt;

            if ($price < $standard_price) {
                $price = $standard_price;
            }
        } else if ($this->depth1 === "무선제본") {
            $standard_price = 420000;

            $page = $this->page;

            if ($this->coating_yn) {
                $page += 12;
            }

            $weight = 2.52;
            if ($this->stan_name === "A3") {
                $weight = 3.15;
            }
            
            $price = $page * $weight * $this->amt;

            if ($price < $standard_price) {
                $price = $standard_price;
            }
        }

        return $price;
    }

    /**
     * @brief 8절, A3가 아닐 때 제본비 계산
     *
     * @return 제본비
     */
    function calcEtcBindingPrice() {
        // 제본별 페이지 늘어날 경우 가산금액
        $ADD_PRICE_ARR = array(
            "중철제본" => array(
                50    => 5000,   100   => 5000,   200   => 5000,   300   => 5000,
                400   => 5000,   500   => 5000,   600   => 6000,   700   => 7000,
                800   => 8000,   900   => 9000,   1000  => 10000,  1200  => 12000,
                1400  => 14000,  1600  => 15000,  1800  => 18000,  2000  => 20000,
                2500  => 24000,  3000  => 28000,  3500  => 33000,  4000  => 38000,
                4500  => 41000,  5000  => 46000,  6000  => 54000,  7000  => 62000,
                8000  => 70000,  9000  => 76000,  10000 => 84000,  11000 => 90000,
                12000 => 96000,  13000 => 102000, 14000 => 108000, 15000 => 112000,
                16000 => 120000, 17000 => 128000, 18000 => 135000, 19000 => 142000,
                20000 => 150000, 22000 => 165000, 24000 => 180000, 26000 => 195000,
                28000 => 210000, 30000 => 225000, 32000 => 240000, 34000 => 255000,
                36000 => 270000, 38000 => 285000, 40000 => 300000, 42000 => 315000,
                44000 => 330000, 46000 => 345000, 48000 => 360000, 50000 => 375000
            ),
            "무선제본" => array(
                50    => 4200,   100   => 4200,   200   => 4200,   300   => 4200,
                400   => 4200,   500   => 4200,   600   => 4200,   700   => 4200,
                800   => 4800,   900   => 5300,   1000  => 6000,   1200  => 7100,
                1400  => 8300,   1600  => 9400,   1800  => 10500,  2000  => 11700,
                2500  => 14500,  3000  => 17300,  3500  => 20100,  4000  => 22800,
                4500  => 25400,  5000  => 28200,  6000  => 33300,  7000  => 38300,
                8000  => 43200,  9000  => 48000,  10000 => 52500,  11000 => 56900,
                12000 => 61200,  13000 => 65400,  14000 => 69300,  15000 => 73100,
                16000 => 76800,  17000 => 80400,  18000 => 83700,  19000 => 86900,
                20000 => 90000,  22000 => 99000,  24000 => 108000, 26000 => 117000,
                28000 => 126000, 30000 => 135000, 32000 => 144000, 34000 => 153000,
                36000 => 162000, 38000 => 171000, 40000 => 180000, 42000 => 189000,
                44000 => 198000, 46000 => 207000, 48000 => 216000, 50000 => 225000 
            )
        );

        // 제본별 기본가격 유지할 페이지
        $DEFAULT_PRICE_PAGE_ARR = array(
            "중철제본" => array(
                50    => 4, 100   => 4, 200   => 4, 300   => 4,
                400   => 4, 500   => 4, 600   => 4, 700   => 4,
                800   => 4, 900   => 4, 1000  => 4, 1200  => 4,
                1400  => 4, 1600  => 4, 1800  => 4, 2000  => 4,
                2500  => 4, 3000  => 4, 3500  => 4, 4000  => 4,
                4500  => 4, 5000  => 4, 6000  => 4, 7000  => 4,
                8000  => 4, 9000  => 4, 10000 => 4, 11000 => 4,
                12000 => 4, 13000 => 4, 14000 => 4, 15000 => 4,
                16000 => 4, 17000 => 4, 18000 => 4, 19000 => 4,
                20000 => 4, 22000 => 4, 24000 => 4, 26000 => 4,
                28000 => 4, 30000 => 4, 32000 => 4, 34000 => 4,
                36000 => 4, 38000 => 4, 40000 => 4, 42000 => 4,
                44000 => 4, 46000 => 4, 48000 => 4, 50000 => 4
            ),
            "무선제본" => array(
                50    => 140, 100  => 140,  200   => 140, 300   => 140,
                400   => 140, 500  => 140,  600   => 140, 700   => 140,
                800   => 124, 900  => 112,  1000  => 100, 1200  => 84,
                1400  => 84,  1600  => 84,  1800  => 84,  2000  => 84,
                2500  => 84,  3000  => 84,  3500  => 84,  4000  => 84,
                4500  => 84,  5000  => 84,  6000  => 84,  7000  => 84,
                8000  => 84,  9000  => 84,  10000 => 84,  11000 => 84,
                12000 => 84,  13000 => 84,  14000 => 84,  15000 => 84,
                16000 => 84,  17000 => 84,  18000 => 84,  19000 => 84,
                20000 => 84,  22000 => 84,  24000 => 84,  26000 => 84,
                28000 => 84,  30000 => 84,  32000 => 84,  34000 => 84,
                36000 => 84,  38000 => 84,  40000 => 84,  42000 => 84,
                44000 => 84,  46000 => 84,  48000 => 84,  50000 => 84
            )
        );

        // 제본비 추가 페이지
        $BINDING_PAGE_ARR = array(
            "중철제본" => 8,
            "무선제본" => 4
        );

        $depth1 = $this->depth1;
        $price  = $this->price;
        $page   = $this->page;
        $amt    = $this->amt;

        $default_price_page = $DEFAULT_PRICE_PAGE_ARR[$depth1][$amt];

        $binding_page = floor(($page - $default_price_page) / $BINDING_PAGE_ARR[$depth1]);

        $add_price = 0;
        // 기본가격 페이지보다 선택한 페이지가 커야지 추가금액 붙음
        if ($default_price_page < $page) {
            $add_price = intval($ADD_PRICE_ARR[$depth1][$amt] * 1.1);
        }

        $price += $binding_page * $add_price;

        return $price;
    }
}


?>
