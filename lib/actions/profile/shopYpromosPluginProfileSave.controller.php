<?php

class shopYpromosPluginProfileSaveController extends waJsonController
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

            $this->cleanupProfilePromos($this->inputs['profileId']);

            $this->setProfilePromos($this->inputs['profileId']);
        }
    }

    /**
     * Set profile promos.
     *
     * @param int $profileId
     *
     * @return void
     */
    protected function setProfilePromos($profileId)
    {
        $promos = array_unique(array_merge($this->inputs['promocodes'], $this->inputs['promoflashes'], $this->inputs['promonplusms'], $this->inputs['promogifts']));

        $records = [
            'profile_id' => $profileId,
            'promo_id'   => $promos
        ];

        $this->container->make('shop', 'shopYpromosPluginProfilePromosModel')->multipleInsert($records);
    }

    /**
     * Cleanup profile promos. (before update)
     *
     * @param int $profileId
     *
     * @return void
     */
    protected function cleanupProfilePromos($profileId)
    {
        $this->container->make('shop', 'shopYpromosPluginProfilePromosModel')->deleteByField('profile_id', $profileId);
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
            'profileId'    => waRequest::request('profile_id', 0, waRequest::TYPE_INT),
            'promocodes'   => array_unique(waRequest::request('promocodes', [], waRequest::TYPE_ARRAY_INT)),
            'promoflashes' => array_unique(waRequest::request('promoflashes', [], waRequest::TYPE_ARRAY_INT)),
            'promonplusms' => array_unique(waRequest::request('promonplusms', [], waRequest::TYPE_ARRAY_INT)),
            'promogifts'   => array_unique(waRequest::request('promogifts', [], waRequest::TYPE_ARRAY_INT))
        ];
    }

    /**
     * Initiate the validation process.
     *
     * @return mixed
     */
    public function triggerValidation()
    {
        if (! $this->inputs['profileId']) {

            $this->errors[] = 'Отсутствует id профиля.';

            return false;
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