<?php

class shopYpromosPluginBackendItemsSearchController extends waJsonController
{
    const MAX_RESULTS = 20;

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
     * @var array
     */
    private $knownTypes = ['product', 'category'];

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

            $items = ($this->inputs['type'] === 'product')

                ? $this->searchAvailableProducts($this->inputs['query'], $this->inputs['promoId'], $this->inputs['products'])

                : $this->searchAvailableCategories($this->inputs['query'], $this->inputs['promoId'], $this->inputs['categories']);

            $this->setResponseOutput('items', $items);
        }
    }

    protected function searchAvailableProducts($query, $promoId, $except)
    {
        return $this->container->make('shop', 'shopYpromosPluginPromoProductsModel')->searchAvailableProductsExcludingAssociated($query, $promoId, $except, self::MAX_RESULTS);
    }

    protected function searchAvailableCategories($query, $promoId, $except)
    {
        return $this->container->make('shop', 'shopYpromosPluginPromoCategoriesModel')->searchAvailableCategoriesExcludingAssociated($query, $promoId, $except, self::MAX_RESULTS);
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
            'query'      => waRequest::request('query', '', waRequest::TYPE_STRING_TRIM),
            'type'       => waRequest::request('type', '', waRequest::TYPE_STRING_TRIM),
            'promoId'    => waRequest::request('promo_id', null, waRequest::TYPE_INT),
            'products'   => array_unique(waRequest::request('products', [], waRequest::TYPE_ARRAY_INT)),
            'categories' => array_unique(waRequest::request('categories', [], waRequest::TYPE_ARRAY_INT))
        ];
    }

    /**
     * Initiate the validation process.
     *
     * @return void
     */
    public function triggerValidation()
    {
        if (! in_array($this->inputs['type'], $this->knownTypes, true)) {
            $this->errors[] = 'Неизвестный метод поиска.';
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