$(document).ready(function() {

    $('.city-selector').on('change', '.country-select', function() {
        var self = this;
        $(this).parent().parent().find('.region-select, .city-select').parent().remove();
        if(!$(this).val()) return;

        $.ajax({ url: $('.city-selector').data('baseUrl')+'/country/regions_select/id_country/' + $(this).val(),
            complete: function(xhr) {
                var select = $(xhr.responseText);
                select.attr('name', $(self).attr('name').replace('id_country', 'id_region'));
                $('<div class="select_cat_id_cont"></div>').append(select).insertAfter($(self).parent());
          //      initSelectBox(select);
            }
        });
    });

    $('.city-selector').on('change', '.region-select', function() {
        var self = this;
        $(this).parent().parent().find('.city-select').parent().remove();
     //   if(!$(this).val()) return;

        $.ajax({ url: $('.city-selector').data('baseUrl')+'/country/cities_select/id_region/' + $(this).val(),
            complete: function(xhr) {
                var select = $(xhr.responseText);
                select.attr('name', $(self).attr('name').replace('id_region', 'id_city'));
                $('<div class="select_cat_id_cont"></div>').append(select).insertAfter($(self).parent());
         //       initSelectBox(select);
            }
        });
    });
});