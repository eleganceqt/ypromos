<?php

class shopYpromosPluginPromoCategoriesModel extends waModel
{
    protected $table = 'shop_ypromos_promo_categories';

    /**
     * Get promo categories.
     *
     * @param int $promoId
     *
     * @return array
     */
    public function getPromoCategories($promoId)
    {
        $sql = "SELECT scat.id, scat.name
                FROM shop_category scat
                INNER JOIN shop_ypromos_promo_categories sypc ON scat.id = sypc.category_id AND sypc.promo_id = i:promoId
                ORDER BY sypc.id";

        $params = [
            'promoId' => $promoId
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Search available categories excluding those that are already associated.
     *
     * @param string   $query
     * @param int|null $promoId
     * @param array    $except
     * @param int      $limit
     *
     * @return array
     */
    public function searchAvailableCategoriesExcludingAssociated($query, $promoId = null, $except = [], $limit = 20)
    {
        $sql = "SELECT sc.id, sc.name
                FROM shop_category sc";

        $subquery = "SELECT sypc.category_id FROM shop_ypromos_promo_categories sypc";

        if ($promoId) {
            $subquery .= " WHERE sypc.promo_id != i:promoId";
        }

        $sql .= " WHERE sc.name LIKE :query AND sc.id NOT IN (" . $subquery . ") AND sc.status = 1 AND sc.type = 0";

        if ($except) {
            $sql .= " AND sc.id NOT IN (:except)";
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
     * Obtain categories that are already used by other promos. (If promo is specified, its occurrences will not be counted)
     *
     * @param array    $categories
     * @param int|null $promoId
     *
     * @return array
     */
    public function extractAlreadyInvolvedCategoriesExcept($categories, $promoId = null)
    {
        if (! $categories) {
            return [];
        }

        $sql = "SELECT sc.id, sc.name
                FROM shop_category sc";

        $subquery = "SELECT sypc.category_id FROM shop_ypromos_promo_categories sypc WHERE sypc.category_id IN (:categories)";

        if ($promoId) {
            $subquery .= " AND sypc.promo_id != i:promoId";
        }

        $sql .= " WHERE sc.id IN (" . $subquery . ")";

        $params = [
            'categories' => $categories,
            'promoId'    => $promoId

        ];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Remove promo category.
     *
     * @param int $promoId
     * @param int $categoryId
     *
     * @return bool
     */
    public function removePromoCategory($promoId, $categoryId)
    {
        $attributes = [
            'promo_id'    => $promoId,
            'category_id' => $categoryId
        ];

        return $this->deleteByField($attributes);
    }

    /**
     * Get valid categories for yandex xml export.
     *
     * @param int $promoId
     *
     * @return array
     */
    public function getExportablePromoCategories($promoId)
    {
        $sql = "SELECT sc.id, sc.name
                FROM shop_ypromos_promo_categories sypc
                INNER JOIN shop_category sc ON sc.id = sypc.category_id
                WHERE sypc.promo_id = i:promoId AND sc.type = 0";

        $params = ['promoId' => $promoId];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Get exportable promogift categories. (select only categories that have gifts)
     *
     * @param int $promoId
     *
     * @return array
     */
    public function getExportablePromogiftCategories($promoId)
    {
        $sql = "SELECT sc.id, sc.name
                FROM shop_ypromos_promo_categories sypc
                INNER JOIN shop_category sc ON sc.id = sypc.category_id
                WHERE sypc.promo_id = i:promoId AND sc.type = 0 AND sypc.category_id IN (SELECT sg.category_id FROM shop_addgifts sg WHERE sg.category_id = sypc.category_id)";

        $params = ['promoId' => $promoId];

        return $this->query($sql, $params)->fetchAll('id');
    }

    /**
     * Get gifts associated to categories.
     *
     * @param int $categoriesIds
     *
     * @return array
     */
    public function getCategoriesGifts($categoriesIds)
    {
        if (! $categoriesIds) {
            return [];
        }

        $sql = "SELECT sp.id, sp.name
                FROM shop_product sp
                WHERE sp.id IN (SELECT sg.gift_id FROM shop_addgifts sg WHERE sg.category_id IN (:categories))";

        $params = [
            'categories' => $categoriesIds
        ];

        return $this->query($sql, $params)->fetchAll('id');
    }
}