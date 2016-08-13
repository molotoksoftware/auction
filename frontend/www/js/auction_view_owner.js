//****************************************************************
$('document').ready(function () {

    if (window.bidsCount > 0 && window.isLotOwnerUser) {
        loadBidsTableForOwner();
    }

    function loadBidsTableForOwner() {
        var auctionId = $('input[name="auction_id_hidden"]').val();
        $.ajax({
            type: 'GET',
            url: '/auction/showBidsTable',
            data: {'auction_id': auctionId},
            success: function (data) {
                $('#bids-list.table-wrp').empty().append(data);
                $('#bids-list.table-wrp').fadeIn(200);
            }
        });
    }

    
    $('#buy_promotion input[type="submit"]').click(function () {
        if ($('#buy_promotion select[name="id_duration_promotion"]').val() == '') {
            return false;
        }
    });


});