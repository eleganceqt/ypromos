<?php

class shopYpromosPluginPromonplusmModel extends waModel
{
    /**
     * Get promonplusms.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getPromonplusms($offset = 0, $limit = 10)
    {
        $sql = "SELECT syp.* 
                FROM shop_ypromos_promo syp
                WHERE syp.type = s:type
                ORDER BY syp.id DESC
                LIMIT i:limit
                OFFSET i:offset";

        $params = [
            'type'   => shopYpromosPluginPromoModel::TYPE_PROMONPLUSM,
            'limit'  => $limit,
            'offset' => $offset,
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Get promonplusm by id.
     *
     * @param int $promoId
     *
     * @return array
     */
    public function getPromonplusm($promoId)
    {
        $sql = "SELECT syp.* 
                FROM shop_ypromos_promo syp
                WHERE syp.type = s:type AND syp.id = i:promoId";

        $params = [
            'type'    => shopYpromosPluginPromoModel::TYPE_PROMONPLUSM,
            'promoId' => $promoId
        ];

        $promonplusm = $this->query($sql, $params)->fetchAssoc();

        //@todo REFACTOR THIS

        // means promo doesnt exist
        if (! $promonplusm) {
            return $promonplusm;
        }

        $promonplusm['products']   = shopYpromosPluginPromoModel::getPromoProductsModel()->getPromoProducts($promoId);
        $promonplusm['categories'] = shopYpromosPluginPromoModel::getPromoCategoriesModel()->getPromoCategories($promoId);

        return $promonplusm;
    }
}