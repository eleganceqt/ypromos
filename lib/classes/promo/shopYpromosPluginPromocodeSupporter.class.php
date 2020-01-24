<?php

class shopYpromosPluginPromocodeSupporter
{
    /**
     * Get an instance of static.
     *
     * @return shopYpromosPluginPromocodeSupporter
     */
    public static function factory()
    {
        return new static();
    }

    /**
     * Get available coupons.
     *
     * @return array
     */
    public static function getAvailableCoupons()
    {
        return static::factory()->getPromocodeModel()->getAvailableCouponsWith();
    }

    /**
     * Get available coupons, including promo coupon.
     *
     * @param int $promoId
     *
     * @return array
     */
    public static function getAvailableCouponsWith($promoId)
    {
        return static::factory()->getPromocodeModel()->getAvailableCouponsWith($promoId);
    }

    /**
     * Determine if coupon is already assigned.
     *
     * @param int $couponId
     *
     * @return bool
     */
    public static function isCouponAlreadyAssigned($couponId)
    {
        return static::factory()->getPromocodeModel()->isCouponAlreadyAssignedExcept($couponId);
    }

    /**
     * Determine if coupon is assigned to an another promo, except that one.
     *
     * @param int $couponId
     * @param int $promoId
     *
     * @return bool
     */
    public static function isCouponAlreadyAssignedExcept($couponId, $promoId)
    {
        return static::factory()->getPromocodeModel()->isCouponAlreadyAssignedExcept($couponId, $promoId);
    }

    /**
     * Check if promocode is valid.
     *
     * @param array $promocode
     *
     * @return bool
     */
    public static function isPromocodeValid($promocode)
    {
        if ($promocode['coupon_id'] === null) {
            return false;
        }

        if ($promocode['coupon_type'] === '$FS') {
            return false;
        }

        if (($promocode['coupon_type'] === '%' && ((float) $promocode['coupon_value'] < 5 || (float) $promocode['coupon_value'] > 95)) ||
            ($promocode['coupon_type'] !== '%' && ((float) $promocode['coupon_value']) % 100 !== 0)) {
            return false;
        }

        if ($promocode['coupon_limit'] !== null && ((int) $promocode['coupon_used'] >= (int) $promocode['coupon_limit'])) {
            return false;
        }

        if ($promocode['coupon_expire_datetime'] !== null && (date('Y-m-d H:i:s') > $promocode['coupon_expire_datetime'])) {
            return false;
        }

        return true;
    }

    /**
     * Return formatted coupon value. (Coupon types accepted: '%' & 'RUB')
     *
     * @param array $coupon
     *
     * @return string|null
     */
    public static function formatCouponValue($coupon)
    {
        if ($coupon['type'] === '%') {
            return waCurrency::format('%0', $coupon['value'], 'USD') . '%';
        }

        if ($coupon['type'] !== '%') {
            return waCurrency::format('%0{s}', $coupon['value'], $coupon['type']);
        }
    }

    /**
     * Get promocode model.
     *
     * @return shopYpromosPluginPromocodeModel
     */
    private function getPromocodeModel()
    {
        static $promocodeModel;

        if (! $promocodeModel) {
            $promocodeModel = new shopYpromosPluginPromocodeModel();
        }

        return $promocodeModel;
    }
}