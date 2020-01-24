<?php

class shopYpromosPluginBackendPromosAction extends waViewAction
{
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

        $this->setBackendLayout();
    }

    /**
     * Set layout.
     *
     * @return void
     */
    protected function setBackendLayout()
    {
        $this->setLayout(new shopBackendLayout());
    }

    /**
     * Constitute view composition.
     *
     * @return array
     */
    protected function buildTemplateComposition()
    {
        return [

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