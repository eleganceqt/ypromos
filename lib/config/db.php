<?php

return array(
    'shop_ypromos_profile_promos'   => array(
        'id'         => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'profile_id' => array('int', 11, 'null' => 0),
        'promo_id'   => array('int', 11, 'null' => 0),
        ':keys'      => array(
            'PRIMARY'    => 'id',
            'profile_id' => 'profile_id',
        ),
    ),
    'shop_ypromos_promo'            => array(
        'id'    => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'name'  => array('varchar', 255, 'null' => 0),
        'type'  => array('enum', "'promocode','promoflash','promonplusm','promogift'", 'null' => 0),
        ':keys' => array(
            'PRIMARY' => 'id',
            'type'    => 'type',
        ),
    ),
    'shop_ypromos_promo_categories' => array(
        'id'          => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'promo_id'    => array('int', 11, 'null' => 0),
        'category_id' => array('int', 11, 'null' => 0),
        ':keys'       => array(
            'PRIMARY'  => 'id',
            'promo_id' => 'promo_id',
        ),
    ),
    'shop_ypromos_promo_products'   => array(
        'id'         => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'promo_id'   => array('int', 11, 'null' => 0),
        'product_id' => array('int', 11, 'null' => 0),
        ':keys'      => array(
            'PRIMARY'  => 'id',
            'promo_id' => 'promo_id',
        ),
    ),
    'shop_ypromos_promocode'        => array(
        'id'        => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'promo_id'  => array('int', 11, 'null' => 0),
        'coupon_id' => array('int', 11, 'null' => 0),
        ':keys'     => array(
            'PRIMARY'  => 'id',
            'promo_id' => 'promo_id',
        ),
    ),
    'shop_ypromos_promoflash'       => array(
        'id'         => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'promo_id'   => array('int', 11, 'null' => 0),
        'start_date' => array('datetime', 'null' => 0),
        'end_date'   => array('datetime', 'null' => 0),
        ':keys'      => array(
            'PRIMARY'  => 'id',
            'promo_id' => 'promo_id',
        ),
    ),
);
