<?php

class shopYpromosPluginBackendPromosSearchController extends waJsonController
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
    private $knownTypes = ['promocode', 'promoflash', 'promonplusm', 'promogift'];

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

            $items = $this->searchAvailablePromos($this->inputs['query'], $this->inputs['type']);

            $this->setResponseOutput('items', $items);
        }
    }

    /**
     * Search promos by query.
     *
     * @param string $query
     * @param string $type
     *
     * @return array
     */
    protected function searchAvailablePromos($query, $type)
    {
        if ($this->inputs['type'] === 'promocode') {
            $except = $this->inputs['promocodes'];
        }

        if ($this->inputs['type'] === 'promoflash') {
            $except = $this->inputs['promoflashes'];
        }

        if ($this->inputs['type'] === 'promonplusm') {
            $except = $this->inputs['promonplusms'];
        }

        if ($this->inputs['type'] === 'promogift') {
            $except = $this->inputs['promogifts'];
        }

        return $this->container->make('shop', 'shopYpromosPluginPromoModel')->searchAvailablePromos($query, $type, $except, self::MAX_RESULTS);
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
            'query'        => waRequest::request('query', '', waRequest::TYPE_STRING_TRIM),
            'type'         => waRequest::request('type', '', waRequest::TYPE_STRING_TRIM),
            'profileId'    => waRequest::request('profile_id', null, waRequest::TYPE_INT),
            'promocodes'   => array_unique(waRequest::request('promocodes', [], waRequest::TYPE_ARRAY_INT)),
            'promoflashes' => array_unique(waRequest::request('promoflashes', [], waRequest::TYPE_ARRAY_INT)),
            'promonplusms'  => array_unique(waRequest::request('promonplusms', [], waRequest::TYPE_ARRAY_INT)),
            'promogifts'   => array_unique(waRequest::request('promogifts', [], waRequest::TYPE_ARRAY_INT))
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