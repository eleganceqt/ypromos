<?php

class shopYpromosPluginPromocodeModel extends waModel
{
    protected $table = 'shop_ypromos_promocode';

    /**
     * Get promocodes.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getPromocodes($offset = 0, $limit = 10)
    {
        $sql = "SELECT syp.*, sc.id AS coupon_id, sc.code AS coupon_code, sc.type AS coupon_type, sc.limit AS coupon_limit, sc.used AS coupon_used, sc.value AS coupon_value, sc.expire_datetime AS coupon_expire_datetime  
                FROM shop_ypromos_promo syp
                INNER JOIN shop_ypromos_promocode sypc ON syp.id = sypc.promo_id
                LEFT JOIN shop_coupon sc ON sc.id = sypc.coupon_id
                WHERE syp.type = s:type
                ORDER BY syp.id DESC
                LIMIT i:limit
                OFFSET i:offset";

        $params = [
            'type'   => shopYpromosPluginPromoModel::TYPE_PROMOCODE,
            'limit'  => $limit,
            'offset' => $offset,
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Get promocode by id.
     *
     * @param int $promoId
     *
     * @return array
     */
    public function getPromocode($promoId)
    {
        $sql = "SELECT syp.*, sc.id AS coupon_id, sc.code AS coupon_code, sc.type AS coupon_type, sc.limit AS coupon_limit, sc.used AS coupon_used, sc.value AS coupon_value, sc.expire_datetime AS coupon_expire_datetime
                FROM shop_ypromos_promo syp
                INNER JOIN shop_ypromos_promocode sypc ON syp.id = sypc.promo_id
                LEFT JOIN shop_coupon sc ON sc.id = sypc.coupon_id
                WHERE syp.type = s:type AND syp.id = i:promoId";

        $params = [
            'type'    => shopYpromosPluginPromoModel::TYPE_PROMOCODE,
            'promoId' => $promoId
        ];

        $promocode = $this->query($sql, $params)->fetchAssoc();

        //@todo REFACTOR THIS

        // means promocode doesnt exist
        if (! $promocode) {
            return $promocode;
        }

        $promocode['products']   = shopYpromosPluginPromoModel::getPromoProductsModel()->getPromoProducts($promoId);
        $promocode['categories'] = shopYpromosPluginPromoModel::getPromoCategoriesModel()->getPromoCategories($promoId);

        return $promocode;
    }

    /**
     * Get appropriate available coupons. (If promo is specified, its coupon will be included)
     *
     * @param int|null $promoId
     *
     * @return array
     */
    public function getAvailableCouponsWith($promoId = null)
    {
        $sql = "SELECT sc.*
                FROM shop_coupon sc
                WHERE 
                      ((sc.type = '%' AND (sc.value >= 5 AND sc.value <= 95)) OR (sc.type != '%' AND sc.value % 100 = 0)) AND 
                      ((sc.limit IS NULL OR sc.used < sc.limit) AND (sc.expire_datetime IS NULL OR NOW() < sc.expire_datetime)) AND 
                      sc.type != '\$FS'";

        $subquery = "SELECT sypc.coupon_id FROM shop_ypromos_promocode sypc";

        if ($promoId) {
            $subquery .= " WHERE sypc.promo_id != i:promoId";
        }

        $sql .= " AND sc.id NOT IN (" . $subquery . ") ORDER BY sc.id DESC";

        $params = ['promoId' => $promoId];

        return $this->getCouponModel()->query($sql, $params)->fetchAll('id');
    }

    /**
     * Determine if coupon is already in use by other promos. (If promo is specified, its occurrence will not be counted)
     *
     * @param int      $couponId
     * @param int|null $promoId
     *
     * @return bool
     */
    public function isCouponAlreadyAssignedExcept($couponId, $promoId = null)
    {
        $sql = "SELECT syp.*
                FROM shop_ypromos_promocode syp
                WHERE syp.coupon_id = i:couponId";

        if ($promoId) {
            $sql .= " AND syp.promo_id != i:promoId";
        }

        $params = [
            'couponId' => $couponId,
            'promoId'  => $promoId
        ];

        $record = $this->query($sql, $params)->fetchAssoc();

        return $record ? true : false;
    }

    /**
     * Get coupon model.
     *
     * @return shopCouponModel
     */
    protected function getCouponModel()
    {
        static $couponModel;

        if (! $couponModel) {
            $couponModel = new shopCouponModel();
        }

        return $couponModel;
    }
}