<?php

class shopYpromosPluginPromoflashCreateController extends waJsonController
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

            $this->assignPromoTimestamps($promoId, $this->inputs['startDate'], $this->inputs['endDate']);

            $this->assignPromoProducts($promoId);

            $this->respPromoflash($promoId);
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
            'type' => shopYpromosPluginPromoModel::TYPE_PROMOFLASH
        ];

        return $this->container->make('shop', 'shopYpromosPluginPromoModel')->insert($record);
    }

    /**
     * Assign promoflash timestamps.
     *
     * @param int    $promoId
     * @param string $startDate
     * @param string $endDate
     *
     * @return void
     */
    protected function assignPromoTimestamps($promoId, $startDate, $endDate)
    {
        $record = [
            'promo_id'   => $promoId,
            'start_date' => date('Y-m-d 00:00:00', strtotime($startDate)),
            'end_date'   => date('Y-m-d 23:59:59', strtotime($endDate))
        ];

        $this->container->make('shop', 'shopYpromosPluginPromoflashModel')->insert($record);
    }

    /**
     * Assign promoflash products.
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

    protected function respPromoflash($promoId)
    {
        $promoflash = [
            'id'        => $promoId,
            'name'      => $this->inputs['name'],
            'startDate' => $this->inputs['startDate'],
            'endDate'   => $this->inputs['endDate']
        ];

        $this->setResponseOutput('promoflash', $promoflash);
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
            'name'      => waRequest::request('name', '', waRequest::TYPE_STRING_TRIM),
            'startDate' => waRequest::request('start_date', '', waRequest::TYPE_STRING_TRIM),
            'endDate'   => waRequest::request('end_date', '', waRequest::TYPE_STRING_TRIM),
            'products'  => array_unique(waRequest::request('products', [], waRequest::TYPE_ARRAY_INT)),
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

        if (! shopYpromosPluginPromoflashSupporter::isStartDateInputValid($this->inputs['startDate'])) {
            $this->errors[] = 'Неправильная дата начала акции.';
        }

        if (! shopYpromosPluginPromoflashSupporter::isEndDateInputValid($this->inputs['startDate'], $this->inputs['endDate'])) {
            $this->errors[] = 'Неправильная дата окончания акции.';
        }

        if (! $this->inputs['products']) {
            $this->errors[] = 'Отсутствует товары на которые действует акция.';
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