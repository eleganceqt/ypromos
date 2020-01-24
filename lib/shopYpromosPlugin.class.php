<?php

class shopYpromosPlugin extends shopPlugin
{
    public function backendMenu()
    {
        return [
            'core_li' => '<li class="no-tab s-ypromos-plugin"><a href="?plugin=ypromos&module=backend&action=promos">Промоакции</a></li>',
        ];
    }
}
