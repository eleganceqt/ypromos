<?php

class shopYpromosPluginProfileFetchModalController extends waJsonController
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

            if (! $vars['profile']) {

                $this->setProfileNotFoundError();

            } else {

                $vars['profile']['promos'] = $this->getProfilePromos($this->inputs['profileId']);

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
        return $this->container->make('shop', 'shopYpromosPluginProfileModalAction')->renderWith($vars);
    }

    /**
     * Gather template vars.
     *
     * @return array
     */
    protected function gatherTemplateVars()
    {
        return [
            'profileId' => $this->inputs['profileId'],
            'profile'   => $this->getProfile($this->inputs['profileId'])
        ];
    }

    /**
     * Get ymarket export profile data.
     *
     * @param int $profileId
     *
     * @return array
     */
    protected function getProfile($profileId)
    {
        $profiles = $this->container->makeWith('shop', 'shopImportexportHelper', ['yandexmarket'])->getList();

        return ifset($profiles[$profileId], []);
    }

    /**
     * Get profile promos.
     *
     * @param int $profileId
     *
     * @return array
     */
    protected function getProfilePromos($profileId)
    {
        return $this->container->make('shop', 'shopYpromosPluginProfilePromosModel')->getProfilePromos($profileId);
    }

    /**
     * Set profile not found error.
     *
     * @return void
     */
    protected function setProfileNotFoundError()
    {
        $this->errors[] = 'Профиль с таким идентификатором не была найден, возможно он был удален, пожалуйста перезагрузите страницу и попробуйте снова.';
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
            'profileId' => waRequest::request('profile_id', 0, waRequest::TYPE_INT)
        ];
    }

    /**
     * Initiate the validation process.
     *
     * @return void
     */
    public function triggerValidation()
    {
        if (! $this->inputs['profileId']) {
            $this->errors[] = 'Отсутствует id профиля.';
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