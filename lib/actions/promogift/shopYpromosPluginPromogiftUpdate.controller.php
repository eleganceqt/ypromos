<?php

class shopYpromosPluginPromogiftUpdateController extends waJsonController
{
    /**
     * @var array
     */
    protected $inputs = [];

    /**
     * @var array
     */
    protected $outputs = [];

    /**
     * @var waContainer
     */
    protected $container;

    /**
     * @inheritdoc
     */
    protected function preExecute()
    {
        $this->handleRequest();
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (! $this->failsValidation()) {

            $this->updatePromo($this->inputs['promoId']);

            $this->respPromogift($this->inputs['promoId']);
        }
    }

    /**
     * Update promo.
     *
     * @param int $promoId
     *
     * @return void
     */
    protected function updatePromo($promoId)
    {
        $this->updatePromoInfo($promoId);

        $this->cleanupPromoOffers($promoId);

        $this->assignPromoProducts($promoId);

        $this->assignPromoCategories($promoId);
    }

    /**
     * Assign promo products.
     *
     * @param int $promoId
     *
     * @return void
     */
    protected function assignPromoProducts($promoId)
    {
        $records = [
            'promo_id'   => $promoId,
            'product_id' => $this->inputs['products']
        ];

        $this->container->make('shop', 'shopYpromosPluginPromoProductsModel')->multipleInsert($records);
    }

    /**
     * Assign promo categories.
     *
     * @param int $promoId
     *
     * @return void
     */
    protected function assignPromoCategories($promoId)
    {
        $records = [
            'promo_id'    => $promoId,
            'category_id' => $this->inputs['categories']
        ];

        $this->container->make('shop', 'shopYpromosPluginPromoCategoriesModel')->multipleInsert($records);
    }

    /**
     * Cleanup promo offers (before update them).
     *
     * @param int $promoId
     *
     * @return void
     */
    protected function cleanupPromoOffers($promoId)
    {
        $this->container->make('shop', 'shopYpromosPluginPromoProductsModel')->deleteByField('promo_id', $promoId);

        $this->container->make('shop', 'shopYpromosPluginPromoCategoriesModel')->deleteByField('promo_id', $promoId);
    }

    /**
     * Update promo info (only name atm).
     *
     * @param int $promoId
     *
     * @return void
     */
    protected function updatePromoInfo($promoId)
    {
        $this->container->make('shop', 'shopYpromosPluginPromoModel')->updateById($promoId, ['name' => $this->inputs['name']]);
    }

    protected function respPromogift($promoId)
    {
        $promogift = [
            'id'   => $promoId,
            'name' => $this->inputs['name'],
        ];

        $this->setResponseOutput('promogift', $promogift);
    }

    /**
     * Manage default actions.
     *
     * @return void
     */
    protected function handleRequest()
    {
        $this->setContainer();

        $this->defineRequestInputs();

        $this->triggerValidation();
    }

    /**
     * Set container instance.
     *
     * @return void
     */
    protected function setContainer()
    {
        $this->container = new waContainer();
    }

    /**
     * Register request inputs.
     *
     * @return void
     */
    protected function defineRequestInputs()
    {
        $this->inputs = [
            'promoId'    => waRequest::request('promo_id', 0, waRequest::TYPE_INT),
            'name'       => waRequest::request('name', '', waRequest::TYPE_STRING_TRIM),
            'products'   => array_unique(waRequest::request('products', [], waRequest::TYPE_ARRAY_INT)),
            'categories' => array_unique(waRequest::request('categories', [], waRequest::TYPE_ARRAY_INT))
        ];
    }

    /**
     * Initiate the validation process.
     *
     * @return bool|null
     */
    public function triggerValidation()
    {
        if (! $this->inputs['promoId']) {

            $this->errors[] = 'Отсутствует id акции.';

            return false;
        }

        if (! $this->inputs['name']) {
            $this->errors[] = 'Отсутствует внутреннее название акции.';
        }

        if (! $this->inputs['products'] && ! $this->inputs['categories']) {
            $this->errors[] = 'Отсутствует товары и/или категории, на которые действует акция.';
        }

        if ($productsInUse = shopYpromosPluginPromoSupporter::extractAlreadyInvolvedProductsExcept($this->inputs['products'], $this->inputs['promoId'])) {
            $this->errors[] = 'Следующие товары уже участвуют в другой акции: ' . shopYpromosPluginPromoSupporter::surroundEntriesNames($productsInUse)
                              . ', пожалуйста, удалите их из списка.';
        }

        if ($categoriesInUse = shopYpromosPluginPromoSupporter::extractAlreadyInvolvedCategoriesExcept($this->inputs['categories'], $this->inputs['promoId'])) {
            $this->errors[] = 'Следующие категории уже участвуют в другой акции: ' . shopYpromosPluginPromoSupporter::surroundEntriesNames($categoriesInUse)
                              . ', пожалуйста, удалите их из списка.';

        }
    }

    /**
     * Determine if the data passes the validation.
     *
     * @return bool
     */
    public function passesValidation()
    {
        return ! $this->failsValidation();
    }

    /**
     * Determine if the data fails the validation.
     *
     * @return bool
     */
    public function failsValidation()
    {
        return $this->hasUnsuccessfulAttempts();
    }

    /**
     * Returns whether errors were found.
     *
     * @return bool
     */
    protected function hasUnsuccessfulAttempts()
    {
        return ! empty($this->errors);
    }

    /**
     * Set an array of response outputs.
     *
     * @param array $outputs
     *
     * @return void
     */
    protected function setMultipleOutputs($outputs = [])
    {
        foreach ($outputs as $key => $value) {
            $this->setResponseOutput($key, $value);
        }
    }

    /**
     * Set a response output. (Used for json response)
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    protected function setResponseOutput($key, $value)
    {
        $this->response[$key] = $value;
    }
}