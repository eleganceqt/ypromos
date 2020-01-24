<?php

class shopYpromosPluginPromoSupporter
{
    /**
     * Get an instance of static.
     *
     * @return shopYpromosPluginPromoSupporter
     */
    public static function factory()
    {
        return new static();
    }

    /**
     * Get promos count by type.
     *
     * @return array
     */
    public static function getPromosCountByType()
    {
        return static::factory()->getPromoModel()->getFullPromosCountReport();
    }

    /**
     * Return products that are already associated to other promos.
     *
     * @param array $products
     *
     * @return array
     */
    public static function extractAlreadyInvolvedProducts($products)
    {
        return static::factory()->getPromoProductsModel()->extractAlreadyInvolvedProductsExcept($products);
    }

    /**
     * Return products that are already associated to other promos, except specified one.
     *
     * @param array $products
     * @param int   $promoId
     *
     * @return array
     */
    public static function extractAlreadyInvolvedProductsExcept($products, $promoId)
    {
        return static::factory()->getPromoProductsModel()->extractAlreadyInvolvedProductsExcept($products, $promoId);
    }

    /**
     * Return categories that are already associated to other promos.
     *
     * @param array $categories
     *
     * @return array
     */
    public static function extractAlreadyInvolvedCategories($categories)
    {
        return static::factory()->getPromoCategoriesModel()->extractAlreadyInvolvedCategoriesExcept($categories);
    }

    /**
     * Return categories that are already associated to other promos, except specified one.
     *
     * @param array $categories
     * @param int   $promoId
     *
     * @return array
     */
    public static function extractAlreadyInvolvedCategoriesExcept($categories, $promoId)
    {
        return static::factory()->getPromoCategoriesModel()->extractAlreadyInvolvedCategoriesExcept($categories, $promoId);
    }

    /**
     * Surround entries names with «».
     *
     * @param array $entries
     *
     * @return string
     */
    public static function surroundEntriesNames($entries)
    {
        $surrounded = [];

        foreach ($entries as $entry) {
            $surrounded[] = '«' . $entry['name'] . '»';
        }

        return implode(', ', $surrounded);
    }

    /**
     * Get promo model.
     *
     * @return shopYpromosPluginPromoModel
     */
    private function getPromoModel()
    {
        static $promoModel;

        if (! $promoModel) {
            $promoModel = new shopYpromosPluginPromoModel();
        }

        return $promoModel;
    }

    /**
     * Get promo products model.
     *
     * @return shopYpromosPluginPromoProductsModel
     */
    private function getPromoProductsModel()
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
    private function getPromoCategoriesModel()
    {
        static $promoCategoriesModel;

        if (! $promoCategoriesModel) {
            $promoCategoriesModel = new shopYpromosPluginPromoCategoriesModel();
        }

        return $promoCategoriesModel;
    }
}