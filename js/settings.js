$(document).ready(function () {

    $.shopYpromosPluginSettingsAction = {

        $container : $('.settingsContainer'),

        initialize : function () {

            this.initializeSemanticUI();

            this.addEventListeners();
        },

        initializeSemanticUI : function () {

        },

        addEventListeners : function () {

            this.addDocumentEventListeners();

            this.addElementEventListeners();
        },

        addDocumentEventListeners : function () {

            this.onProfileModalActivation();

            this.onProfileModalSave();

            this.onListItemRemove();
        },

        addElementEventListeners : function () {

        },

        onProfileModalActivation : function () {

            let that = this;

            $(document).on('click', '.activateProfileModal:not(.loading)', function (event) {

                let $this = $(this);

                $this.addClass('loading');

                let data = {
                    profile_id : $this.closest('tr').data('profile-id')
                };

                $.ajax({
                           url : '?plugin=ypromos&module=profile&action=fetchModal',
                           method : 'POST',
                           data : data,
                           async : true,
                           cache : false,
                           dataType : 'json',
                           error : function (jqXHR, textStatus, errorThrown) {
                               alert('Внутренняя ошибка сервера.');
                           },
                           success : function (jsonResponse, textStatus, jqXHR) {

                               if (jsonResponse.status === 'ok') {
                                   that.buildProfileModal(jsonResponse.data.modalContent);
                               }

                               if (jsonResponse.status === 'fail') {
                                   that.alertErrors(jsonResponse.errors);
                               }

                           },
                           complete : function (jqXHR, textStatus) {
                               $this.removeClass('loading');
                           }
                       });


            });
        },

        onProfileModalSave : function () {

            let that = this;

            $(document).on('click', '.profileModalSave:not(.loading)', function (event) {

                let $this = $(this);

                $this.addClass('loading');

                let data = $this.closest('.ui.modal').find('.ui.form input').serialize();

                $.ajax({
                           url : '?plugin=ypromos&module=profile&action=save',
                           method : 'POST',
                           data : data,
                           async : true,
                           cache : false,
                           dataType : 'json',
                           error : function (jqXHR, textStatus, errorThrown) {
                               alert('Внутренняя ошибка сервера.');
                           },
                           success : function (jsonResponse, textStatus, jqXHR) {

                               if (jsonResponse.status === 'ok') {
                                   that.displayProfileModalSuccessDimmer();
                               }

                               if (jsonResponse.status === 'fail') {
                                   that.alertErrors(jsonResponse.errors);
                               }

                           },
                           complete : function (jqXHR, textStatus) {
                               $this.removeClass('loading');
                           }

                       });

            });
        },

        onListItemRemove : function () {

            let that = this;

            $(document).on('click', '.profileListItemRemove', function (event) {

                let $this = $(this);

                let $counter = $this.closest('.ui.accordion').find('.counter');

                let decreasedValue = parseInt($counter.data('items-count')) - 1;

                $counter.data('items-count', decreasedValue).html('(' + decreasedValue + ')');

                $this.closest('.item').remove();

            });
        },

        displayProfileModalSuccessDimmer : function () {

            $('.ui.profileModalSuccessDimmer').dimmer({
                                                          opacity : 0.8,
                                                          closable : true
                                                      })
                                              .dimmer('show')
            ;
        },

        buildProfileModal : function (content) {

            this.$container.append(content);

            this.activateModalComponent();
        },

        activateModalComponent : function () {

            let that = this;

            let $modal = that.$container.find('.ui.modal');

            $modal.modal({
                             autofocus : false,
                             observeChanges : true,
                             keyboardShortcuts : false,
                             closable : false,
                             duration : 400,
                             onVisible : function () {
                                 that.configureModalComponents($modal);
                             },
                             onHidden : function () {
                                 $modal.remove();
                             }
                         })
                  .modal('show');
        },

        configureModalComponents : function ($modal) {

            this.configureDropdownComponent($modal);

            this.configureSearchComponent($modal);

            this.configureAccordionComponent($modal);
        },

        configureDropdownComponent : function ($modal) {
            $modal.find('.ui.dropdown').dropdown();
        },

        configureSearchComponent : function ($modal) {

            let that = this;

            let searchEntities = that.getSearchEntities();

            $.each(searchEntities, function (index, entity) {

                $modal.find('.ui' + entity.searchSelector).search({
                                                                      minCharacters : 1,
                                                                      searchOnFocus : false,
                                                                      maxResults : false,
                                                                      cache : false,
                                                                      showNoResults : true,
                                                                      transition : false,
                                                                      duration : 400,
                                                                      apiSettings : {
                                                                          url : '?plugin=ypromos&module=backend&action=promosSearch&type=' + entity.promoType + '&query={query}',
                                                                          method : 'POST',
                                                                          cache : false,
                                                                          beforeSend : function (settings) {

                                                                              settings.data = $(entity.listSelector).find('input[class="exceptItem"]').serialize();

                                                                              let $promoInput = $modal.find('input[name="profile_id"]');

                                                                              if ($promoInput.length) {
                                                                                  settings.data += '&profile_id=' + encodeURIComponent($promoInput.val())
                                                                              }

                                                                              return settings;
                                                                          },
                                                                          onResponse : function (jsonResponse) {
                                                                              return jsonResponse.data;
                                                                          }
                                                                      },
                                                                      fields : {
                                                                          results : 'items',
                                                                          id : 'id',
                                                                          title : 'name'
                                                                      },
                                                                      templates : {
                                                                          standard : function (response, fields) {

                                                                              let resultsContent = '';

                                                                              if (response[fields.results] !== undefined) {

                                                                                  // each result
                                                                                  $.each(response[fields.results], function (index, result) {

                                                                                      resultsContent += '<a class="result" data-result-id="' + result[fields.id] + '">' +
                                                                                                        '    <div class="content">' +
                                                                                                        '        <div class="title"><i class="archive icon"></i> ' + result[fields.title] + '</div>' +
                                                                                                        '    </div>' +
                                                                                                        '</a>'
                                                                                      ;
                                                                                  });

                                                                                  return resultsContent;
                                                                              }

                                                                              return false;
                                                                          },
                                                                          message : function (message, type) {

                                                                              let html = '';

                                                                              if (message !== undefined && type !== undefined) {
                                                                                  html += '<div class="message ' + type + '">';

                                                                                  // message type
                                                                                  if (type == 'empty') {

                                                                                      html += '<div class="header">Нет результатов</div class="header">' +
                                                                                              '<div class="description">' + message + '</div class="description">'
                                                                                      ;

                                                                                  } else {
                                                                                      html += ' <div class="description">' + message + '</div>';
                                                                                  }

                                                                                  html += '</div>';
                                                                              }

                                                                              return html;
                                                                          }
                                                                      },
                                                                      error : {
                                                                          noResults : 'По вашему запросу нет результатов'
                                                                      },
                                                                      onSelect : function (result, response) {

                                                                          let $search = $(this);

                                                                          that.processSelectedItem($search, result, entity.promoType);

                                                                          return false;
                                                                      }
                                                                  })
                ;

            });

        },

        configureAccordionComponent : function ($modal) {
            $modal.find('.ui.accordion').accordion({ animateChildren : false, duration : 400 });
        },

        processSelectedItem : function ($search, item, type) {

            this.addItemToList($search, item, type);

            this.removeItemFromResults($search, item);

            this.increaseListCounter($search);
        },

        addItemToList : function ($search, item, type) {

            let itemContent = this.sketchListItemContent(item, type);

            $search.next('.ui.accordion').find('.ui.list').append(itemContent);
        },

        removeItemFromResults : function ($search, item) {

            let $resultsContainer = $search.find('.results');

            $resultsContainer.find('a.result[data-result-id="' + item.id + '"]').remove();

            if ($resultsContainer.find('a').length === 0) {
                $resultsContainer.hide();
            }
        },

        increaseListCounter : function ($search) {

            let $counter = $search.next('.ui.accordion').find('.counter');

            let increasedValue = parseInt($counter.data('items-count')) + 1;

            $counter.data('items-count', increasedValue).html('(' + increasedValue + ')');
        },

        sketchListItemContent : function (item, type) {

            let inputName = this.getInputName(type);

            let itemContent = '<div class="item">' +
                              '   <div class="right floated content">' +
                              '       <i class="remove icon profileListItemRemove"></i>' +
                              '   </div>' +
                              '   <i class="archive icon"></i>' +
                              '   <div class="content">' +
                              '       <a class="header">' + item.name + '</a>' +
                              '       <input type="hidden" name="' + inputName + '[]" value="' + item.id + '" class="exceptItem">' +
                              '   </div>' +
                              '</div>'
            ;

            return itemContent;
        },

        getSearchEntities : function () {

            let entities = [
                {
                    searchSelector : '.profilePromocodeSearch',
                    accordionSelector : '.profilePromocodeAccordion',
                    listSelector : '.profilePromocodeList',
                    promoType : 'promocode'
                },
                {
                    searchSelector : '.profilePromoflashSearch',
                    accordionSelector : '.profilePromoflashAccordion',
                    listSelector : '.profilePromoflashList',
                    promoType : 'promoflash'
                },
                {
                    searchSelector : '.profilePromonplusmSearch',
                    accordionSelector : '.profilePromonplusmAccordion',
                    listSelector : '.profilePromonplusmList',
                    promoType : 'promonplusm'
                },
                {
                    searchSelector : '.profilePromogiftSearch',
                    accordionSelector : '.profilePromogiftAccordion',
                    listSelector : '.profilePromogiftList',
                    promoType : 'promogift'
                }
            ];

            return entities;
        },

        getInputName : function (type) {

            let inputName = '';

            if (type === 'promocode') {
                inputName = 'promocodes';
            }

            if (type === 'promoflash') {
                inputName = 'promoflashes';
            }

            if (type === 'promonplusm') {
                inputName = 'promonplusms';
            }

            if (type === 'promogift') {
                inputName = 'promogifts';
            }

            return inputName;
        },

        alertErrors : function (errors) {
            $.each(errors, function (index, error) {
                alert(error);
            });
        }
    };

    $.shopYpromosPluginSettingsAction.initialize();

});