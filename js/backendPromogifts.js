$(document).ready(function () {

    $.shopYpromosPluginBackendPromogiftsAction = {

        $container : $('.ui.promo.tab[data-tab="promo-gift"]'),
        $menuItem : $('a.item[data-tab="promo-gift"]'),

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

            this.onBlankModalActivation();

            this.onFilledModalActivation();

            this.onBlankModalSave();

            this.onFilledModalSave();

            this.onPromogiftRemove();

            this.onListItemRemove();
        },

        addElementEventListeners : function () {

        },

        onBlankModalActivation : function () {

            let that = this;

            $(document).on('click', '.promogiftActivateBlankModal:not(.loading)', function (event) {

                let $this = $(this);

                $this.addClass('loading');

                $.ajax({
                           url : '?plugin=ypromos&module=promogift&action=fetchBlankModal',
                           method : 'POST',
                           data : {},
                           async : true,
                           cache : false,
                           dataType : 'json',
                           error : function (jqXHR, textStatus, errorThrown) {
                               alert('Внутренняя ошибка сервера.');
                           },
                           success : function (jsonResponse, textStatus, jqXHR) {

                               if (jsonResponse.status === 'ok') {
                                   that.buildPromogiftModal(jsonResponse.data.modalContent);
                               }

                               if (jsonResponse.status === 'fail') {
                                   that.alertErrors(jsonResponse.errors);
                               }

                           },
                           complete : function (jqXHR, textStatus) {
                               $this.removeClass('loading');
                           }
                       })
                ;
            });
        },

        onFilledModalActivation : function () {

            let that = this;

            $(document).on('click', '.promogiftActivateFilledModal:not(.loading)', function (event) {

                let $this = $(this);

                $this.addClass('loading');

                let data = {
                    promo_id : $this.closest('tr').data('promo-id')
                };

                $.ajax({
                           url : '?plugin=ypromos&module=promogift&action=fetchFilledModal',
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
                                   that.buildPromogiftModal(jsonResponse.data.modalContent);
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

        onBlankModalSave : function () {

            let that = this;

            $(document).on('click', '.promogiftBlankModalSave:not(.loading)', function (event) {

                let $this = $(this);

                $this.addClass('loading');

                let data = $this.closest('.promogiftBlankModal').find('.ui.form input').serialize();

                $.ajax({
                           url : '?plugin=ypromos&module=promogift&action=create',
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

                                   that.addPromogiftToTable(jsonResponse.data.promogift);

                                   that.increasePromogiftsCount();

                                   that.displayBlankModalSuccessDimmer();
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

        onFilledModalSave : function () {

            let that = this;

            $(document).on('click', '.promogiftFilledModalSave:not(.loading)', function (event) {

                let $this = $(this);

                $this.addClass('loading');

                let data = $this.closest('.promogiftFilledModal').find('.ui.form input').serialize();

                $.ajax({
                           url : '?plugin=ypromos&module=promogift&action=update',
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

                                   that.updatePromogiftState(jsonResponse.data.promogift);

                                   that.displayFilledModalSuccessDimmer();
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

        onPromogiftRemove : function () {

            let that = this;

            $(document).on('click', '.promogiftRemovePromo:not(.loading)', function (event) {

                if (window.confirm('Вы уверены, что хотите удалить промоакцию?')) {

                    let $this = $(this);

                    $this.addClass('loading');

                    let data = {
                        promo_id : $this.closest('tr').data('promo-id')
                    };

                    $.ajax({
                               url : '?plugin=ypromos&module=promogift&action=remove',
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

                                       $this.closest('tr').remove();

                                       that.decreasePromogiftsCount();
                                   }

                                   if (jsonResponse.status === 'fail') {
                                       that.alertErrors(jsonResponse.errors);
                                   }

                               },
                               complete : function (jqXHR, textStatus) {
                                   $this.removeClass('loading');
                               }
                           });
                }
            });
        },

        onListItemRemove : function () {

            let that = this;

            $(document).on('click', '.promogiftListItemRemove', function (event) {

                let $this = $(this);

                let $counter = $this.closest('.ui.accordion').find('.counter');

                let decreasedValue = parseInt($counter.data('items-count')) - 1;

                $counter.data('items-count', decreasedValue).html('(' + decreasedValue + ')');

                $this.closest('.item').remove();

            });
        },

        increasePromogiftsCount : function () {

            let $counter = this.$menuItem.find('.ui.label');

            let increasedValue = parseInt($counter.data('promos-count')) + 1;

            $counter.data('promos-count', increasedValue).html(increasedValue);

        },

        decreasePromogiftsCount : function () {

            let $counter = this.$menuItem.find('.ui.label');

            let decreasedValue = parseInt($counter.data('promos-count')) - 1;

            $counter.data('promos-count', decreasedValue).html(decreasedValue);
        },

        addPromogiftToTable : function (promogift) {

            let that = this;

            let rowContent = that.sketchTableRow(promogift);

            $(rowContent).prependTo(that.$container.find('table.ui.table > tbody'));
        },

        updatePromogiftState: function(promogift) {

            let that = this;

            let $row = that.$container.find('table.ui.table > tbody tr[data-promo-id="' + promogift.id + '"]');

            $row.find('td:eq(1) .ui.tiny.header').html(promogift.name);

            $row.find('td:eq(2) a').attr('href', '/webasyst/shop/?action=orders#/coupons/'+promogift.coupon_id+'/').html(promogift.coupon_code);
        },

        displayBlankModalSuccessDimmer : function () {

            $('.ui.promogiftBlankModalSuccessDimmer').dimmer({
                                                                 opacity : 0.8,
                                                                 closable : true,
                                                                 onHide : function () {
                                                                     $(this).closest('.promogiftBlankModal').modal('hide');
                                                                 }
                                                             })
                                                     .dimmer('show')
            ;
        },

        displayFilledModalSuccessDimmer : function () {

            $('.ui.promogiftFilledModalSuccessDimmer').dimmer({
                                                                  opacity : 0.8,
                                                                  closable : true
                                                              })
                                                      .dimmer('show')
            ;
        },

        buildPromogiftModal : function (content) {

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
                                                                          url : '?plugin=ypromos&module=backend&action=itemsSearch&type=' + entity.itemType + '&query={query}',
                                                                          method : 'POST',
                                                                          cache : false,
                                                                          beforeSend : function (settings) {

                                                                              settings.data = $(entity.listSelector).find('input[class="exceptItem"]').serialize();

                                                                              let $promoInput = $modal.find('input[name="promo_id"]');

                                                                              if ($promoInput.length) {
                                                                                  settings.data += '&promo_id=' + encodeURIComponent($promoInput.val())
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

                                                                                  let itemIcon = (entity.itemType === 'product') ? 'file' : 'folder';

                                                                                  // each result
                                                                                  $.each(response[fields.results], function (index, result) {

                                                                                      resultsContent += '<a class="result" data-result-id="' + result[fields.id] + '">' +
                                                                                                        '    <div class="content">' +
                                                                                                        '        <div class="title"><i class="grey ' + itemIcon + ' icon"></i> ' + result[fields.title] + '</div>' +
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

                                                                          that.processSelectedItem($search, result, entity.itemType);

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

            let that = this;

            if (type === 'product') {
                return that.sketchProductItemContent(item);
            }

            if (type === 'category') {
                return that.sketchCategoryItemContent(item);
            }
        },

        sketchProductItemContent : function (item) {

            let itemContent = '<div class="item">' +
                              '   <div class="right floated content">' +
                              '       <i class="remove icon promogiftListItemRemove"></i>' +
                              '   </div>' +
                              '   <i class="file icon"></i>' +
                              '   <div class="content">' +
                              '       <a class="header" href="/webasyst/shop/?action=products#/product/' + item.id + '/" target="_blank">' + item.name + '</a>' +
                              '       <input type="hidden" name="products[]" value="' + item.id + '" class="exceptItem">' +
                              '   </div>' +
                              '</div>'
            ;

            return itemContent;
        },

        sketchCategoryItemContent : function (item) {

            let itemContent = '<div class="item">' +
                              '   <div class="right floated content">' +
                              '       <i class="remove icon promogiftListItemRemove"></i>' +
                              '   </div>' +
                              '   <i class="folder icon"></i>' +
                              '   <div class="content">' +
                              '       <a class="header" href="/webasyst/shop/?action=products#/products/category_id=' + item.id + '/" target="_blank">' + item.name + '</a>' +
                              '       <input type="hidden" name="categories[]" value="' + item.id + '" class="exceptItem">' +
                              '   </div>' +
                              '</div>'
            ;

            return itemContent;
        },

        sketchTableRow : function (promogift) {

            let rowContent = '<tr class="center aligned" data-promo-id="' + promogift.id + '">' +
                             '    <td>' +
                             '        <i class="check circle green icon"></i>' +
                             '    </td>' +
                             '    <td>' +
                             '        <div class="ui tiny header">' + promogift.name + '</div>' +
                             '    </td>' +
                             '    <td>' +
                             '        <button class="ui compact small icon bttn promogiftActivateFilledModal">' +
                             '            <i class="pencil alternate icon"></i>' +
                             '        </button>' +
                             '        <button class="ui compact small icon bttn promogiftRemovePromo">' +
                             '            <i class="trash alternate icon"></i>' +
                             '        </button>' +
                             '     </td>' +
                             '</tr>';

            return rowContent;
        },

        getSearchEntities : function () {

            let entities = [
                {
                    searchSelector : '.promogiftProductSearch',
                    accordionSelector : '.promogiftProductsAccordion',
                    listSelector : '.promogiftProductList',
                    itemType : 'product'
                },
                {
                    searchSelector : '.promogiftCategorySearch',
                    accordionSelector : '.promogiftCategoriesAccordion',
                    listSelector : '.promogiftCategoryList',
                    itemType : 'category'
                }
            ];

            return entities;
        },

        alertErrors : function (errors) {
            $.each(errors, function (index, error) {
                alert(error);
            });
        },

        sampleAJAX : function () {
            $.ajax({
                       url : '',
                       method : 'POST',
                       data : {},
                       async : true,
                       cache : false,
                       dataType : 'json',
                       error : function (jqXHR, textStatus, errorThrown) {
                       },
                       success : function (anything, textStatus, jqXHR) {
                       },
                       complete : function (jqXHR, textStatus) {
                           $this.removeClass('loading');
                       }

                   });
        }

    };

    $.shopYpromosPluginBackendPromogiftsAction.initialize();

});