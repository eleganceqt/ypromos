<?php

class shopYpromosPluginPromocodeFetchFilledModalController extends waJsonController
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

            $vars = $this->gatherTemplateVars();

            if (! $vars['promocode']) {

                $this->setPromoNotFoundError();

            } else {

                $this->setResponseOutput('modalContent', $this->fetchModal($vars));

            }
        }
    }

    /**
     * Fetch modal content.
     *
     * @param array $vars
     *
     * @return string
     */
    protected function fetchModal($vars)
    {
        return $this->container->make('shop', 'shopYpromosPluginPromocodeFilledModalAction')->renderWith($vars);
    }

    /**
     * Gather template vars.
     *
     * @return array
     */
    protected function gatherTemplateVars()
    {
        return [
            'promocode' => $this->getPromocode($this->inputs['promoId']),
            'coupons'   => shopYpromosPluginPromocodeSupporter::getAvailableCouponsWith($this->inputs['promoId'])
        ];
    }

    /**
     * Get promocode.
     *
     * @param int $promoId
     *
     * @return array
     */
    protected function getPromocode($promoId)
    {
        return $this->container->make('shop', 'shopYpromosPluginPromocodeModel')->getPromocode($promoId);
    }

    /**
     * Set promo not found error.
     *
     * @return void
     */
    protected function setPromoNotFoundError()
    {
        $this->errors[] = 'Промоакция с таким идентификатором не была найдена, возможно она была удалена, пожалуйста перезагрузите страницу и попробуйте снова.';
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
            'promoId' => waRequest::request('promo_id', 0, waRequest::TYPE_INT)
        ];
    }

    /**
     * Initiate the validation process.
     *
     * @return void
     */
    public function triggerValidation()
    {
        if (! $this->inputs['promoId']) {
            $this->errors[] = 'Отсутствует id акции.';
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