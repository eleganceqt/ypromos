<?php

class shopYpromosPluginPromoProductsModel extends waModel
{
    protected $table = 'shop_ypromos_promo_products';

    /**
     * Get promo products.
     *
     * @param int $promoId
     *
     * @return array
     */
    public function getPromoProducts($promoId)
    {
        $sql = "SELECT sp.id, sp.name
                FROM shop_product sp
                INNER JOIN shop_ypromos_promo_products sypp ON sp.id = sypp.product_id AND sypp.promo_id = i:promoId
                ORDER BY sypp.id ";

        $params = [
            'promoId' => $promoId
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Search available products excluding those that are already associated.
     *
     * @param string   $query
     * @param int|null $promoId
     * @param array    $except
     * @param int      $limit
     *
     * @return array
     */
    public function searchAvailableProductsExcludingAssociated($query, $promoId = null, $except = [], $limit = 20)
    {
        $sql = "SELECT sp.id, sp.name
                FROM shop_product sp";

        $subquery = "SELECT sypp.product_id FROM shop_ypromos_promo_products sypp";

        if ($promoId) {
            $subquery .= " WHERE sypp.promo_id != i:promoId";
        }

        $sql .= " WHERE sp.name LIKE :query AND sp.id NOT IN (" . $subquery . ") AND sp.status = 1";

        if ($except) {
            $sql .= " AND sp.id NOT IN (:except)";
        }

        $sql .= " LIMIT i:limit";

        $params = [
            'query'   => '%' . $query . '%',
            'promoId' => $promoId,
            'except'  => $except,
            'limit'   => $limit
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Obtain products that are already used by other promos. (If promo is specified, its occurrences will not be counted)
     *
     * @param array    $products
     * @param int|null $promoId
     *
     * @return array
     */
    public function extractAlreadyInvolvedProductsExcept($products, $promoId = null)
    {
        if (! $products) {
            return [];
        }

        $sql = "SELECT sp.id, sp.name
                FROM shop_product sp";

        $subquery = "SELECT sypp.product_id FROM shop_ypromos_promo_products sypp WHERE sypp.product_id IN (:products)";

        if ($promoId) {
            $subquery .= " AND sypp.promo_id != i:promoId";
        }

        $sql .= " WHERE sp.id IN (" . $subquery . ")";

        $params = [
            'products' => $products,
            'promoId'  => $promoId
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Remove promo product.
     *
     * @param int $promoId
     * @param int $productId
     *
     * @return bool
     */
    public function removePromoProduct($promoId, $productId)
    {
        $attributes = [
            'promo_id'   => $promoId,
            'product_id' => $productId
        ];

        return $this->deleteByField($attributes);
    }

    /**
     * Get valid products for yandex xml export.
     *
     * @param int $promoId
     *
     * @return array
     */
    public function getExportablePromoProducts($promoId)
    {
        $sql = "SELECT sp.id, sp.name, sp.price, sp.currency
                FROM shop_ypromos_promo_products syppr
                INNER JOIN shop_product sp ON sp.id = syppr.product_id
                WHERE syppr.promo_id = i:promoId";

        $params = ['promoId' => $promoId];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Get exportable promonplusm products. (Checks that each products has itself as gift, to meet requirements)
     *
     * @param int $promoId
     *
     * @return array
     */
    public function getExportablePromonplusmProducts($promoId)
    {
        $sql = "SELECT sp.id, sp.name, sp.price, sp.currency
                FROM shop_ypromos_promo_products syppr
                INNER JOIN shop_product sp ON sp.id = syppr.product_id
                WHERE syppr.promo_id = i:promoId AND syppr.product_id IN (SELECT sg.gift_id FROM shop_addgifts sg WHERE sg.gift_id = sg.product_id)";

        $params = ['promoId' => $promoId];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Get exportable promogift products. (Check products that have gift)
     *
     * @param int $promoId
     *
     * @return array
     */
    public function getExportablePromogiftProducts($promoId)
    {
        $sql = "SELECT sp.id, sp.name, sp.price, sp.currency
                FROM shop_ypromos_promo_products syppr
                INNER JOIN shop_product sp ON sp.id = syppr.product_id
                WHERE syppr.promo_id = i:promoId AND syppr.product_id IN (SELECT sg.product_id FROM shop_addgifts sg WHERE sg.product_id = syppr.product_id)";

        $params = ['promoId' => $promoId];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Get gifts associated to products.
     *
     * @param int $categoriesIds
     *
     * @return array
     */
    public function getProductsGifts($productsIds)
    {
        if (! $productsIds) {
            return [];
        }

        $sql = "SELECT sp.id, sp.name
                FROM shop_product sp
                WHERE sp.id IN (SELECT sg.gift_id FROM shop_addgifts sg WHERE sg.product_id IN (:products))";

        $params = [
            'products' => $productsIds
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }
}