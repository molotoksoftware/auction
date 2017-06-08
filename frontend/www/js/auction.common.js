
// var Auction = Auction != undefined ? Auction : {};

var SendReviews = {

    settings: {
      enable: false 
    },

    init: function (newSettings) {
        this.settings = $.extend(this.settings, newSettings)

        if (this.settings.enable) {
            this.enableSendReviews();
        }
    },

    enableSendReviews: function () {

        $('#create_rewiev').click(function(){

            function addRows (row) {
                return '<input type=hidden name=review[] value=' + row + '>';
            }

            var selectBox = $('.grid_cabinet tbody input:checkbox:checked');

            if (selectBox.length > 0) {
                var addInput = $('#reviews-send');
                $.each(selectBox, function(i, val) {
                    addInput.prepend(addRows($(this).val()));
                });
                $('#reviews-send').submit();
            } else {
                alert('You need to mark some items');
            }

        });

    }

};

var DeletePurchase = {

    settings: {
        enable: false,
        grid_table: false,
    },

    init: function(newSettings) {
        this.settings = $.extend(this.settings, newSettings)

        if (this.settings.enable) {
            this.enableDeletePurchase(this.settings.grid_table);
        }
    },

    enableDeletePurchase: function (grid_table){

        var sale_id = '';
        var id_table = '';

        function addRows (row, id_table) {
                return '<input type=hidden name=sale_id[] value=' + row +'>' +
                       '<input type=hidden name=data-id-table[] value=' + id_table + '>';
        }

        $('.js-delete-purchase').click(function() {

            sale_id = $(this).attr('data-sale_id');
            id_table = $(this).attr('data-id-table');

            $PopUpDeleteSale = $("#deleteSale");
            $PopUpDeleteSale.find('#hidden-form').prepend(addRows(sale_id, id_table));
            $PopUpDeleteSale.modal('show');

            return false;

        });

        $('#bulk_delete_purchases').click(function() {

            var selectBoxForDelete = $('.grid_cabinet tbody input:checkbox:checked');

            if (selectBoxForDelete.length > 0) {

                $PopUpDeleteSale = $("#deleteSale");
                var $addInput = $PopUpDeleteSale.find('#hidden-form');
                $addInput.empty();

                $.each(selectBoxForDelete, function(i, val) {
                    $addInput.prepend(addRows($(this).val(), grid_table));
                });

                $PopUpDeleteSale.modal('show');

            } else {
                alert('You need to mark some items');
            }
        });

    }

};