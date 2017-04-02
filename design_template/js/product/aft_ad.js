/**
 * @brief 코팅 가격 처리 함수
 */
var getCoatingPlySheetPrice = function(aft, dvs) {
    getAfterPriceCommon(aft, dvs, null);
};

/**
 * @brief 오시 가격 처리 함수
 */
var getImpressionPlySheetPrice = function(aft, dvs) {
    getDotlinePrice(aft, dvs);
};

/**
 * @brief 가공 가격 처리 함수
 */
var getManufacturePlySheetPrice = function(aft, dvs) {
    getAfterPriceCommon(aft, dvs, null);
};

/**
 * @brief 타공 가격 처리 함수
 */
var getPunchingPlySheetPrice = function(aft, dvs) {
    getAfterPriceCommon(aft, dvs, null);
};

/**
 * @brief 접착 가격 처리 함수
 */
var getBondingPlySheetPrice = function(aft, dvs) {
    getAfterPriceCommon(aft, dvs, null);
};

/**
 * @brief 라미넥스 가격 처리 함수
 */
var getLaminexPlySheetPrice = function(aft, dvs) {
    getLaminexPrice(aft, dvs);
};
