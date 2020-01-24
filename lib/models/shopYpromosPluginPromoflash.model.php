<?php

class shopYpromosPluginPromoflashModel extends waModel
{
    protected $table = 'shop_ypromos_promoflash';

    /**
     * Get promoflashes.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getPromoflashes($offset = 0, $limit = 10)
    {
        $sql = "SELECT syp.*, sypf.start_date, sypf.end_date
                FROM shop_ypromos_promo syp
                INNER JOIN shop_ypromos_promoflash sypf ON syp.id = sypf.promo_id
                WHERE syp.type = s:type
                ORDER BY syp.id DESC
                LIMIT i:limit
                OFFSET i:offset";

        $params = [
            'type'   => shopYpromosPluginPromoModel::TYPE_PROMOFLASH,
            'limit'  => $limit,
            'offset' => $offset,
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Get promoflash by id.
     *
     * @param int $promoId
     *
     * @return array
     */
    public function getPromoflash($promoId)
    {
        $sql = "SELECT syp.*, sypf.start_date, sypf.end_date
                FROM shop_ypromos_promo syp
                INNER JOIN shop_ypromos_promoflash sypf ON syp.id = sypf.promo_id
                WHERE syp.type = s:type AND syp.id = i:promoId";

        $params = [
            'type'    => shopYpromosPluginPromoModel::TYPE_PROMOFLASH,
            'promoId' => $promoId
        ];

        $promoflash = $this->query($sql, $params)->fetchAssoc();

        //@todo REFACTOR THIS

        // means promo doesnt exist
        if (! $promoflash) {
            return $promoflash;
        }

        $promoflash['products'] = shopYpromosPluginPromoModel::getPromoProductsModel()->getPromoProducts($promoId);

        return $promoflash;
    }

}