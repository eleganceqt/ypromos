<?php

class shopYpromosPluginProfilePromosModel extends waModel
{
    protected $table = 'shop_ypromos_profile_promos';

    /**
     * @param int $profileId
     *
     * @return array
     */
    public function getProfilePromos($profileId)
    {
        $sql = "SELECT sypp.*, syp.type AS promo_type, syp.name AS promo_name
                FROM shop_ypromos_profile_promos sypp
                INNER JOIN shop_ypromos_promo syp ON syp.id = sypp.promo_id
                WHERE sypp.profile_id = i:profileId";

        $params = [
            'profileId' => $profileId
        ];

        $profilePromos = $this->query($sql, $params)->fetchAll('promo_type', 2);

        $this->fillupPromosTypes($profilePromos);

        return $profilePromos;
    }

    /**
     * Fill up missing types.
     *
     * @param array $profilePromos
     *
     * @return void
     */
    protected function fillupPromosTypes(&$profilePromos)
    {
        $availablePromoTypes = (new shopYpromosPluginPromoModel())->getAvailablePromoTypes();

        foreach ($availablePromoTypes as $promoType) {
            $profilePromos[$promoType] = ifset($profilePromos[$promoType], []);
        }
    }
}