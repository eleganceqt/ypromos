<?php

class shopYpromosPluginPromonplusmCreateController extends waJsonController
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

            $promoId = $this->insertPromo();

            $this->assignPromoProducts($promoId);

            $this->respPromonplusm($promoId);
        }
    }

    /**
     * Insert promo and get his id.
     *
     * @return int
     */
    protected function insertPromo()
    {
        $record = [
            'name' => $this->inputs['name'],
            'type' => shopYpromosPluginPromoModel::TYPE_PROMONPLUSM
        ];

        return $this->container->make('shop', 'shopYpromosPluginPromoModel')->insert($record);
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

    protected function respPromonplusm($promoId)
    {
        $promonplusm = [
            'id'          => $promoId,
            'name'        => $this->inputs['name'],
        ];

        $this->setResponseOutput('promonplusm', $promonplusm);
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
            'name'       => waRequest::request('name', '', waRequest::TYPE_STRING_TRIM),
            'products'   => array_unique(waRequest::request('products', [], waRequest::TYPE_ARRAY_INT)),
        ];
    }

    /**
     * Initiate the validation process.
     *
     * @return void
     */
    public function triggerValidation()
    {
        if (! $this->inputs['name']) {
            $this->errors[] = 'Отсутствует внутреннее название акции.';
        }

        if (! $this->inputs['products'] && ! $this->inputs['categories']) {
            $this->errors[] = 'Отсутствует товары и/или категории, на которые действует акция.';
        }

        if ($productsInUse = shopYpromosPluginPromoSupporter::extractAlreadyInvolvedProducts($this->inputs['products'])) {
            $this->errors[] = 'Следующие товары уже участвуют в другой акции: ' . shopYpromosPluginPromoSupporter::surroundEntriesNames($productsInUse)
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