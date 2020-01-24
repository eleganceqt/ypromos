<?php

class shopYpromosPluginPromogiftModel extends waModel
{
    /**
     * Get promogifts.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getPromogifts($offset = 0, $limit = 10)
    {
        $sql = "SELECT syp.* 
                FROM shop_ypromos_promo syp
                WHERE syp.type = s:type
                ORDER BY syp.id DESC
                LIMIT i:limit
                OFFSET i:offset";

        $params = [
            'type'   => shopYpromosPluginPromoModel::TYPE_PROMOGIFT,
            'limit'  => $limit,
            'offset' => $offset,
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Get promogift by id.
     *
     * @param int $promoId
     *
     * @return array
     */
    public function getPromogift($promoId)
    {
        $sql = "SELECT syp.* 
                FROM shop_ypromos_promo syp
                WHERE syp.type = s:type AND syp.id = i:promoId";

        $params = [
            'type'    => shopYpromosPluginPromoModel::TYPE_PROMOGIFT,
            'promoId' => $promoId
        ];

        $promogift = $this->query($sql, $params)->fetchAssoc();

        //@todo REFACTOR THIS

        // means promo doesnt exist
        if (! $promogift) {
            return $promogift;
        }

        $promogift['products']   = shopYpromosPluginPromoModel::getPromoProductsModel()->getPromoProducts($promoId);
        $promogift['categories'] = shopYpromosPluginPromoModel::getPromoCategoriesModel()->getPromoCategories($promoId);

        return $promogift;
    }
}