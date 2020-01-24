<?php

class shopYpromosPluginPromoModel extends waModel
{
    const TYPE_PROMOCODE = 'promocode';
    const TYPE_PROMOFLASH = 'promoflash';
    const TYPE_PROMONPLUSM = 'promonplusm';
    const TYPE_PROMOGIFT = 'promogift';

    protected $table = 'shop_ypromos_promo';

    /**
     * Get full promos count report.
     *
     * @return array
     */
    public function getFullPromosCountReport()
    {
        $promosCount = $this->getActualPromosCountGroupedByType();

        $this->fillupPromosCount($promosCount);

        return $promosCount;
    }

    /**
     * Get actual promos count grouped by type.
     *
     * @return array
     */
    public function getActualPromosCountGroupedByType()
    {
        $sql = "SELECT syp.type, COUNT(*) AS count
                FROM shop_ypromos_promo syp
                GROUP BY syp.type";

        return $this->query($sql)->fetchAll('type');
    }

    /**
     * Fill up missing types count.
     *
     * @param array $promosCount
     *
     * @return void
     */
    protected function fillupPromosCount(&$promosCount)
    {
        foreach ($this->getAvailablePromoTypes() as $promoType) {
            $promosCount[$promoType] = ifset($promosCount[$promoType]['count'], 0);
        }
    }

    /**
     * @param string $query
     * @param string $type
     * @param array  $except
     * @param int    $limit
     *
     * @return array
     */
    public function searchAvailablePromos($query, $type, $except = [], $limit = 20)
    {
        $sql = "SELECT syp.*
                FROM shop_ypromos_promo syp
                WHERE syp.name LIKE s:query AND syp.type = s:type";

        if ($except) {
            $sql .= " AND syp.id NOT IN (:except)";
        }

        $sql .= " LIMIT i:limit";

        $params = [
            'query'  => '%' . $query . '%',
            'type'   => $type,
            'except' => $except,
            'limit'  => $limit
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * @param string $query
     * @param string $type
     * @param int    $profileId
     * @param array  $except
     * @param int    $limit
     *
     * @return array
     */
    public function searchAvailablePromosExcludingAssociated($query, $type, $profileId, $except = [], $limit = 20)
    {
        $sql = "SELECT syp.*
                FROM shop_ypromos_promo syp
                WHERE syp.name LIKE s:query AND syp.type = s:type AND 
                      syp.id NOT IN (SELECT sypp.promo_id FROM shop_ypromos_profile_promos sypp WHERE sypp.profile_id != i:profileId)";

        if ($except) {
            $sql .= " AND syp.id NOT IN (:except)";
        }

        $sql .= " LIMIT i:limit";

        $params = [
            'query'     => '%' . $query . '%',
            'type'      => $type,
            'profileId' => $profileId,
            'except'    => $except,
            'limit'     => $limit
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }


    /**
     * Get promocodes for xml export.
     *
     * @param int $profileId
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getExportablePromocodes($profileId, $offset = 0, $limit = 20)
    {
        $sql = "SELECT syp.*, sc.id AS coupon_id, sc.code AS coupon_code, sc.type AS coupon_type, sc.limit AS coupon_limit, sc.used AS coupon_used, sc.value AS coupon_value, sc.expire_datetime AS coupon_expire_datetime
                FROM shop_ypromos_promo syp
                INNER JOIN shop_ypromos_profile_promos syprofp ON syp.id = syprofp.promo_id
                INNER JOIN shop_ypromos_promocode sypc ON syp.id = sypc.promo_id
                INNER JOIN shop_coupon sc ON sc.id = sypc.coupon_id
                WHERE syprofp.profile_id = i:profileId AND
                      syp.type = s:type AND  
                      ((sc.type = '%' AND (sc.value >= 5 AND sc.value <= 95)) OR (sc.type != '%' AND sc.value % 100 = 0)) AND 
                      ((sc.limit IS NULL OR sc.used < sc.limit) AND (sc.expire_datetime IS NULL OR NOW() < sc.expire_datetime)) AND 
                      sc.type != '\$FS'
                ORDER BY syp.id DESC
                LIMIT i:limit
                OFFSET i:offset";

        $params = [
            'profileId' => $profileId,
            'type'      => self::TYPE_PROMOCODE,
            'offset'    => $offset,
            'limit'     => $limit
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Count promocodes for xml export.
     *
     * @param int $profileId
     *
     * @return int
     */
    public function countExportablePromocodes($profileId)
    {
        $sql = "SELECT COUNT(syp.id) AS count
                FROM shop_ypromos_promo syp
                INNER JOIN shop_ypromos_profile_promos syprofp ON syp.id = syprofp.promo_id
                INNER JOIN shop_ypromos_promocode sypc ON syp.id = sypc.promo_id
                INNER JOIN shop_coupon sc ON sc.id = sypc.coupon_id
                WHERE syprofp.profile_id = i:profileId AND
                      syp.type = s:type AND  
                      ((sc.type = '%' AND (sc.value >= 5 AND sc.value <= 95)) OR (sc.type != '%' AND sc.value % 100 = 0)) AND 
                      ((sc.limit IS NULL OR sc.used < sc.limit) AND (sc.expire_datetime IS NULL OR NOW() < sc.expire_datetime)) AND 
                      sc.type != '\$FS'";

        $params = [
            'profileId' => $profileId,
            'type'      => self::TYPE_PROMOCODE,
        ];

        return (int) $this->query($sql, $params)->fetchField('count');
    }

    /**
     * Get promoflashes for xml export.
     *
     * @param int $profileId
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getExportablePromoflashes($profileId, $offset = 0, $limit = 20)
    {
        $sql = "SELECT syp.*, sypf.start_date, sypf.end_date
                FROM shop_ypromos_promo syp
                INNER JOIN shop_ypromos_profile_promos syprofp ON syp.id = syprofp.promo_id
                INNER JOIN shop_ypromos_promoflash sypf ON syp.id = sypf.promo_id
                WHERE syprofp.profile_id = i:profileId AND syp.type = s:type AND sypf.end_date > NOW()
                ORDER BY syp.id DESC
                LIMIT i:limit
                OFFSET i:offset";

        $params = [
            'profileId' => $profileId,
            'type'      => self::TYPE_PROMOFLASH,
            'offset'    => $offset,
            'limit'     => $limit
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Cont promoflashes for xml export.
     *
     * @param int $profileId
     *
     * @return int
     */
    public function countExportablePromoflashes($profileId)
    {
        $sql = "SELECT COUNT(syp.id) AS count
                FROM shop_ypromos_promo syp
                INNER JOIN shop_ypromos_profile_promos syprofp ON syp.id = syprofp.promo_id
                INNER JOIN shop_ypromos_promoflash sypf ON syp.id = sypf.promo_id
                WHERE syprofp.profile_id = i:profileId AND syp.type = s:type AND sypf.end_date > NOW()";

        $params = [
            'profileId' => $profileId,
            'type'      => self::TYPE_PROMOFLASH,
        ];

        return (int) $this->query($sql, $params)->fetchField('count');
    }

    /**
     * Get promonplusms for xml export.
     *
     * @param int $profileId
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getExportablePromonplusms($profileId, $offset = 0, $limit = 20)
    {
        $sql = "SELECT syp.*
                FROM shop_ypromos_promo syp
                INNER JOIN shop_ypromos_profile_promos syprofp ON syp.id = syprofp.promo_id
                WHERE syprofp.profile_id = i:profileId AND syp.type = s:type
                ORDER BY syp.id DESC
                LIMIT i:limit
                OFFSET i:offset";

        $params = [
            'profileId' => $profileId,
            'type'      => self::TYPE_PROMONPLUSM,
            'offset'    => $offset,
            'limit'     => $limit
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Cont promonplusms for xml export.
     *
     * @param int $profileId
     *
     * @return int
     */
    public function countExportablePromonplusms($profileId)
    {
        $sql = "SELECT COUNT(syp.id) AS count
                FROM shop_ypromos_promo syp
                INNER JOIN shop_ypromos_profile_promos syprofp ON syp.id = syprofp.promo_id
                WHERE syprofp.profile_id = i:profileId AND syp.type = s:type";

        $params = [
            'profileId' => $profileId,
            'type'      => self::TYPE_PROMONPLUSM,
        ];

        return (int) $this->query($sql, $params)->fetchField('count');
    }

    /**
     * Get promogifts for xml export.
     *
     * @param int $profileId
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getExportablePromogifts($profileId, $offset = 0, $limit = 20)
    {
        $sql = "SELECT syp.*
                FROM shop_ypromos_promo syp
                INNER JOIN shop_ypromos_profile_promos syprofp ON syp.id = syprofp.promo_id
                WHERE syprofp.profile_id = i:profileId AND syp.type = s:type
                ORDER BY syp.id DESC
                LIMIT i:limit
                OFFSET i:offset";

        $params = [
            'profileId' => $profileId,
            'type'      => self::TYPE_PROMOGIFT,
            'offset'    => $offset,
            'limit'     => $limit
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Cont promogifts for xml export.
     *
     * @param int $profileId
     *
     * @return int
     */
    public function countExportablePromogifts($profileId)
    {
        $sql = "SELECT COUNT(syp.id) AS count
                FROM shop_ypromos_promo syp
                INNER JOIN shop_ypromos_profile_promos syprofp ON syp.id = syprofp.promo_id
                WHERE syprofp.profile_id = i:profileId AND syp.type = s:type";

        $params = [
            'profileId' => $profileId,
            'type'      => self::TYPE_PROMOGIFT,
        ];

        return (int) $this->query($sql, $params)->fetchField('count');
    }


    /**
     * Get gifts associated to products or categories.
     *
     * @param array $products
     * @param array $categories
     *
     * @return array
     */
    public function getGifts($products, $categories)
    {
        if (empty($products) && empty($categories)) {
            return [];
        }

        $where = [];

        $sql = "SELECT sp.id, sp.name, sp.image_id, sp.image_filename, sp.ext
                FROM shop_product sp ";


        if ($products) {
            $where[] = " sp.id IN (SELECT sg.gift_id FROM shop_addgifts sg WHERE sg.product_id IN (:products))";
        }

        if ($categories) {
            $where[] = " sp.id IN (SELECT sg.gift_id FROM shop_addgifts sg WHERE sg.category_id IN (:categories))";
        }

        if (! empty($where)) {
            $sql .= " WHERE " . implode(' OR ', $where);
        }

        $params = [
            'products'   => $products,
            'categories' => $categories
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }


    /**
     * Get available promo types.
     *
     * @return array
     */
    public function getAvailablePromoTypes()
    {
        return [
            self::TYPE_PROMOCODE,
            self::TYPE_PROMOFLASH,
            self::TYPE_PROMONPLUSM,
            self::TYPE_PROMOGIFT
        ];
    }

    /**
     * Get promo products model.
     *
     * @return shopYpromosPluginPromoProductsModel
     */
    public static function getPromoProductsModel()
    {
        static $promoProductsModel;

        if (! $promoProductsModel) {
            $promoProductsModel = new shopYpromosPluginPromoProductsModel();
        }

        return $promoProductsModel;
    }

    /**
     * Get promo categories model.
     *
     * @return shopYpromosPluginPromoCategoriesModel
     */
    public static function getPromoCategoriesModel()
    {
        static $promoCategoriesModel;

        if (! $promoCategoriesModel) {
            $promoCategoriesModel = new shopYpromosPluginPromoCategoriesModel();
        }

        return $promoCategoriesModel;
    }
}