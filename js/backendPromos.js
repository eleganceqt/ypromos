$(document).ready(function () {

    $.shopYpromosPluginBackendPromosAction = {

        initialize : function () {
            this.initializeSemanticUI();
        },

        initializeSemanticUI : function () {
            this.initializeTabComponent();
        },

        initializeTabComponent : function () {

            let that = this;

            let tabs = that.getPromoTabs();

            $.each(tabs, function (index, tab) {

                $('.ui.promos.menu .item[data-tab="' + tab.id + '"]').tab({
                                                                              auto : false,
                                                                              cache : true,
                                                                              loadOnce : true,
                                                                              context : '#promoTabsContext',
                                                                              apiSettings : {
                                                                                  url : '?plugin=ypromos&module=backend&action=' + tab.action,
                                                                                  cache : false
                                                                              }
                                                                          });


            });
        },

        getPromoTabs : function () {

            let tabs = [
                {
                    id : 'promo-code',
                    action : 'promocodes'
                },
                {
                    id : 'promo-flash',
                    action : 'promoflashes'
                },
                {
                    id : 'promo-n-plus-m',
                    action : 'promonplusms'
                },
                {
                    id : 'promo-gift',
                    action : 'promogifts'
                }
            ];

            return tabs;
        }
    };

    $.shopYpromosPluginBackendPromosAction.initialize();

});