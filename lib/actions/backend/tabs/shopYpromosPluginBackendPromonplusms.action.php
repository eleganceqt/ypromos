<?php

class shopYpromosPluginBackendPromonplusmsAction extends waViewAction
{
    const PER_PAGE = 20;

    /**
     * @var waContainer
     */
    protected $container;

    /**
     * @inheritdoc
     */
    protected function preExecute()
    {
        $this->handleAction();
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $this->setMultipleVariables($this->buildTemplateComposition());
    }

    /**
     * Manage default actions.
     *
     * @return void
     */
    protected function handleAction()
    {
        $this->setContainer();
    }

    /**
     * Constitute view composition.
     *
     * @return array
     */
    protected function buildTemplateComposition()
    {
        $boundaries = $this->getBoundaries();

        return [
            'promonplusms' => $this->container->make('shop', 'shopYpromosPluginPromonplusmModel')->getPromonplusms($boundaries['offset'], $boundaries['limit'])
        ];
    }

    /**
     * Get boundaries.
     *
     * @return array
     */
    protected function getBoundaries()
    {
        $page = waRequest::request('page', 1, waRequest::TYPE_INT);

        return [
            'limit'  => self::PER_PAGE,
            'offset' => ($page - 1) * self::PER_PAGE
        ];
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
     * Get view engine instance.
     *
     * @return waSmarty3View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set an array of template variables.
     *
     * @param array $variables
     *
     * @return void
     */
    public function setMultipleVariables($variables = [])
    {
        foreach ($variables as $name => $value) {
            $this->setTemplateVariable($name, $value);
        }
    }

    /**
     * Set template variable.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function setTemplateVariable($name, $value)
    {
        $this->getView()->assign($name, $value);
    }

    /**
     * Render view and return the content.
     *
     * @return string
     */
    public function render()
    {
        return $this->display(false);
    }

    /**
     * Get rendered content with custom variables.
     *
     * @param array $customVariables
     *
     * @return string
     */
    public function renderWith($customVariables = [])
    {
        $this->setMultipleVariables($customVariables);

        return $this->getView()->fetch($this->getTemplate());
    }
}