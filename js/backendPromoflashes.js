$(document).ready(function () {

    $.shopYpromosPluginBackendPromoflashesAction = {

        $container : $('.ui.promo.tab[data-tab="promo-flash"]'),
        $menuItem : $('a.item[data-tab="promo-flash"]'),

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

            this.onPromoflashRemove();

            this.onListItemRemove();
        },

        addElementEventListeners : function () {

        },

        onBlankModalActivation : function () {

            let that = this;

            $(document).on('click', '.promoflashActivateBlankModal:not(.loading)', function (event) {

                let $this = $(this);

                $this.addClass('loading');

                $.ajax({
                           url : '?plugin=ypromos&module=promoflash&action=fetchBlankModal',
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
                                   that.buildPromoflashModal(jsonResponse.data.modalContent);
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

            $(document).on('click', '.promoflashActivateFilledModal:not(.loading)', function (event) {

                let $this = $(this);

                $this.addClass('loading');

                let data = {
                    promo_id : $this.closest('tr').data('promo-id')
                };

                $.ajax({
                           url : '?plugin=ypromos&module=promoflash&action=fetchFilledModal',
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
                                   that.buildPromoflashModal(jsonResponse.data.modalContent);
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

            $(document).on('click', '.promoflashBlankModalSave:not(.loading)', function (event) {

                let $this = $(this);

                $this.addClass('loading');

                let data = $this.closest('.promoflashBlankModal').find('.ui.form input').serialize();

                $.ajax({
                           url : '?plugin=ypromos&module=promoflash&action=create',
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

                                   that.addPromoflashToTable(jsonResponse.data.promoflash);

                                   that.increasePromoflashesCount();

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

            $(document).on('click', '.promoflashFilledModalSave:not(.loading)', function (event) {

                let $this = $(this);

                $this.addClass('loading');

                let data = $this.closest('.promoflashFilledModal').find('.ui.form input').serialize();

                $.ajax({
                           url : '?plugin=ypromos&module=promoflash&action=update',
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

                                   that.updatePromoflashState(jsonResponse.data.promoflash);

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

        onPromoflashRemove : function () {

            let that = this;

            $(document).on('click', '.promoflashRemovePromo:not(.loading)', function (event) {

                if (window.confirm('Вы уверены, что хотите удалить промоакцию?')) {

                    let $this = $(this);

                    $this.addClass('loading');

                    let data = {
                        promo_id : $this.closest('tr').data('promo-id')
                    };

                    $.ajax({
                               url : '?plugin=ypromos&module=promoflash&action=remove',
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

                                       that.decreasePromoflashesCount();
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

            $(document).on('click', '.promoflashListItemRemove', function (event) {

                let $this = $(this);

                let $counter = $this.closest('.ui.accordion').find('.counter');

                let decreasedValue = parseInt($counter.data('items-count')) - 1;

                $counter.data('items-count', decreasedValue).html('(' + decreasedValue + ')');

                $this.closest('.item').remove();

            });
        },

        increasePromoflashesCount : function () {

            let $counter = this.$menuItem.find('.ui.label');

            let increasedValue = parseInt($counter.data('promos-count')) + 1;

            $counter.data('promos-count', increasedValue).html(increasedValue);

        },

        decreasePromoflashesCount : function () {

            let $counter = this.$menuItem.find('.ui.label');

            let decreasedValue = parseInt($counter.data('promos-count')) - 1;

            $counter.data('promos-count', decreasedValue).html(decreasedValue);
        },

        addPromoflashToTable : function (promoflash) {

            let that = this;

            let rowContent = that.sketchTableRow(promoflash);

            $(rowContent).prependTo(that.$container.find('table.ui.table > tbody'));
        },

        updatePromoflashState : function (promoflash) {

            let that = this;

            let $row = that.$container.find('table.ui.table > tbody tr[data-promo-id="' + promoflash.id + '"]');

            $row.find('td:eq(0) i').removeClass().addClass('check circle green icon');

            $row.find('td:eq(1) .ui.tiny.header').html(promoflash.name);

            $row.find('td:eq(2)').html(promoflash.startDate);

            $row.find('td:eq(3)').html(promoflash.endDate);
        },

        displayBlankModalSuccessDimmer : function () {

            $('.ui.promoflashBlankModalSuccessDimmer').dimmer({
                                                                  opacity : 0.8,
                                                                  closable : true,
                                                                  onHide : function () {
                                                                      $(this).closest('.promoflashBlankModal').modal('hide');
                                                                  }
                                                              })
                                                      .dimmer('show')
            ;
        },

        displayFilledModalSuccessDimmer : function () {

            $('.ui.promoflashFilledModalSuccessDimmer').dimmer({
                                                                   opacity : 0.8,
                                                                   closable : true
                                                               })
                                                       .dimmer('show')
            ;
        },

        buildPromoflashModal : function (content) {

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

            this.configureDatepickerComponent($modal);

            this.configureSearchComponent($modal);

            this.configureAccordionComponent($modal);
        },

        configureDatepickerComponent : function ($modal) {

            this.configureStartDatePicker($modal);

            this.configureEndDatePicker($modal);
        },

        configureStartDatePicker : function ($modal) {

            let $startDateInput = $modal.find('input[name="start_date"]');
            let $endDateInput   = $modal.find('input[name="end_date"]');

            $startDateInput.datepicker({
                                           format : 'dd.mm.yyyy',
                                           autoclose : true,
                                           todayHighlight : true,
                                           language : 'ru',
                                           weekStart : 1,
                                           startDate : 'today',
                                           maxViewMode : 2
                                       })
                           .on('changeDate', function (event) {

                               let days      = 7;
                               let startDate = event.date;
                               let endDate   = new Date(startDate.getTime() + (days * 24 * 60 * 60 * 1000));

                               $endDateInput
                                   .datepicker('setStartDate', startDate)
                                   .datepicker('setEndDate', endDate)
                                   .val('')
                                   .datepicker('update');
                           })
                           .datepicker('update', $startDateInput.val());

        },

        configureEndDatePicker : function ($modal) {

            let $startDateInput = $modal.find('input[name="start_date"]');
            let $endDateInput   = $modal.find('input[name="end_date"]');

            let days      = 7;
            let startDate = ($startDateInput.val() === '') ? new Date() : $startDateInput.datepicker('getDate');
            let endDate   = new Date(startDate.getTime() + (days * 24 * 60 * 60 * 1000));

            $endDateInput
                .datepicker({
                                format : 'dd.mm.yyyy',
                                autoclose : true,
                                todayHighlight : true,
                                language : 'ru',
                                weekStart : 1,
                                startDate : startDate,
                                endDate : endDate,
                                maxViewMode : 2
                            })
                .datepicker('update', $endDateInput.val());
        },

        configureSearchComponent : function ($modal) {
            this.configureProductSearchComponent($modal);
        },

        configureProductSearchComponent : function ($modal) {

            let that = this;

            $modal.find('.ui.promoflashProductSearch').search({
                                                                  minCharacters : 1,
                                                                  searchOnFocus : false,
                                                                  maxResults : false,
                                                                  cache : false,
                                                                  showNoResults : true,
                                                                  transition : false,
                                                                  duration : 400,
                                                                  apiSettings : {
                                                                      url : '?plugin=ypromos&module=backend&action=itemsSearch&type=product&query={query}',
                                                                      method : 'POST',
                                                                      cache : false,
                                                                      beforeSend : function (settings) {

                                                                          settings.data = $modal.find('.promoflashProductList input[class="exceptItem"]').serialize();

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

                                                                              // each result
                                                                              $.each(response[fields.results], function (index, result) {

                                                                                  resultsContent += '<a class="result" data-result-id="' + result[fields.id] + '">' +
                                                                                                    '    <div class="content">' +
                                                                                                    '        <div class="title"><i class="grey file icon"></i> ' + result[fields.title] + '</div>' +
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

                                                                      that.processSelectedProduct($search, result);

                                                                      return false;
                                                                  }
                                                              })
            ;
        },

        configureAccordionComponent : function ($modal) {
            $modal.find('.ui.accordion').accordion({ animateChildren : false, duration : 400 });
        },

        processSelectedProduct : function ($search, item) {

            this.addProductToList($search, item);

            this.removeProductFromResults($search, item);

            this.increaseProductCounter($search);
        },

        addProductToList : function ($search, item) {

            let itemContent = this.sketchProductItemContent(item);

            $search.next('.ui.accordion').find('.ui.list').append(itemContent);
        },

        removeProductFromResults : function ($search, item) {

            let $resultsContainer = $search.find('.results');

            $resultsContainer.find('a.result[data-result-id="' + item.id + '"]').remove();

            if ($resultsContainer.find('a').length === 0) {
                $resultsContainer.hide();
            }
        },

        increaseProductCounter : function ($search) {

            let $counter = $search.next('.ui.accordion').find('.counter');

            let increasedValue = parseInt($counter.data('items-count')) + 1;

            $counter.data('items-count', increasedValue).html('(' + increasedValue + ')');
        },

        sketchProductItemContent : function (item) {

            let itemContent = '<div class="item">' +
                              '   <div class="right floated content">' +
                              '       <i class="remove icon promoflashListItemRemove"></i>' +
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

        sketchTableRow : function (promoflash) {

            let rowContent = '<tr class="center aligned" data-promo-id="' + promoflash.id + '">' +
                             '    <td>' +
                             '        <i class="check circle green icon"></i>' +
                             '    </td>' +
                             '    <td>' +
                             '        <div class="ui tiny header">' + promoflash.name + '</div>' +
                             '    </td>' +
                             '    <td>' + promoflash.startDate + '</td>' +
                             '    <td>' + promoflash.endDate + '</td>' +
                             '    <td>' +
                             '        <button class="ui compact small icon bttn promoflashActivateFilledModal">' +
                             '            <i class="pencil alternate icon"></i>' +
                             '        </button>' +
                             '        <button class="ui compact small icon bttn promoflashRemovePromo">' +
                             '            <i class="trash alternate icon"></i>' +
                             '        </button>' +
                             '     </td>' +
                             '</tr>';

            return rowContent;
        },

        alertErrors : function (errors) {
            $.each(errors, function (index, error) {
                alert(error);
            });
        }
    };

    $.shopYpromosPluginBackendPromoflashesAction.initialize();

});