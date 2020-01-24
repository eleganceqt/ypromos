<?php

class shopYpromosPluginPromogiftSupporter
{
    /**
     * Get an instance of static.
     *
     * @return shopYpromosPluginPromogiftSupporter
     */
    public static function factory()
    {
        return new static();
    }

    /**
     * Determine if «addgifts» plugin is installed and included.
     *
     * @return bool
     */
    public static function hasAddGiftsPlugin()
    {
        $plugins = wa('shop')->getConfig()->getPlugins();

        return (class_exists('shopAddgiftsPlugin') && isset($plugins['addgifts']));
    }
}