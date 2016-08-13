var defaults = {
    csrfToken: null,
    csrfTokenName: null,
    bidUrl: '',
    bidBlitzUrl: '',
    bidSpecUrl: '',
    bidExchangeUrl: ''
};

var Auction = function(options) {
    var opts = $.extend({}, defaults, options);

    this.init = function() {
    },
            this.showErrors = function(messages) {
                if (typeof (messages) == 'object') {
                    message = '';
                    $.each(messages, function(i, val) {
                        message += val + "\n";
                    });
                    alert(message);
                } else {
                    alert(messages);
                }
            },
            this.bid = function(form) {
                var obj = this;
                var priceField = $(form).find('input[name="price"]');
                var priceValue = $(priceField).val();
                var lotId = $(form).find('input[name="lotId"]').val();
                
                var start = $(form).find('input[name="start"]').val();
                
                if (priceValue == '' || priceValue <= 0) {
                    this.showErrors('Вы не указали ставку');
                    return false;
                }

                var data = {
                    'price': priceValue,
                    'lotId': lotId
                };

                if (opts.csrfToken)
                    data[opts.csrfTokenName] = opts.csrfToken;

                $.ajax({
                    type: 'POST',
                    url: opts.bidUrl,
                    data: data,
                    dataType: 'json',
                    success: function(data) {
                        var data = data.response;
                        if (data.status == 'error') {
                            if (data.data != null) {
                                if (data.data.type == 'NOT_AUTHORIZED') {
                                    // window.location = data.data.returnUrl;
                                    $('a.login').trigger('click');
                                    $(window).scrollTop(0);
                                }

                                if (data.data.type == 'COMMON_ERROR') {
                                    $(priceField).val('');
                                    alert(data.data.message);
                                }

                                if (data.data.type == 'LOT_COMPLETED') {
                                    $(priceField).val('');
                                    alert(data.data.message);
                                    window.location.reload(true);
                                }

                                if (data.data.type == 'rebid') {
                                    $('.price-field').text(data.data.price);
                                    alert(data.data.message);
                                    location.reload();
                                }

                                if (data.data.type == 'MAX_SET') {
                                    $(priceField).val('');
                                    alert(data.data.message);
                                    location.reload();
                                }
                            } else {
                                alert(data.messages[0]);
                            }


                            //obj.showErrors(data.messages);
                        } else {
                            $('.price-field', '#bid-form').empty().html(priceValue);
                            $(priceField).val('');
                            $(form).find('input[name="start"]').val(priceValue);
                            $('#min_stap').html(Math.round(window.minStepRatePercentage * parseInt(priceValue) / 100));
                            $('#value_stap').val((Math.round(window.minStepRatePercentage * parseInt(priceValue) / 100)) + parseInt(priceValue));
                            // Скрываем возможность делать своё предложение
                            $('.my_bid_title, .my_bid').hide();
                            location.reload();
                        }
                    }
                });

            },
            this.bidBlitz = function(form) {
                var obj = this;
                var lotId = $(form).find('input[name="lotId"]').val();
                var $quantityInput = $(form).find('input[name="quantity"]');
                var quantity = $quantityInput.size ? parseInt($quantityInput.val()) : 1;

                var data = {
                    'lotId': lotId,
                    'quantity' : quantity
                };

                if (opts.csrfToken)
                    data[opts.csrfTokenName] = opts.csrfToken;

                $.ajax({
                    type: 'POST',
                    url: opts.bidBlitzUrl,
                    data: data,
                    dataType: 'json',
                    success: function(data) {
                        data = data.response;
                        if (data.status == 'error') {

                            if (data.data != null)
                                if (data.data.type == 'NOT_AUTHORIZED') {
//                                window.location = data.data.returnUrl;
                                    $('a.login').trigger('click');
                                    $(window).scrollTop(0);
                                }
                        } else {
                            window.location.reload();
                        }
                    }
                });

            },
            this.bidSpec = function(form) {
                var obj = this;
                var priceField = $(form).find('input[name="price"]');
                var priceValue = $(priceField).val();
                
                var lotId = $(form).find('input[name="lotId"]').val();

                if (priceValue == '' || priceValue <= 0) {
                    this.showErrors('Вы не указали ставку');
                    return false;
                } 
                
                var data = {
                    'price': priceValue,
                    'lotId': lotId
                };

                if (opts.csrfToken)
                    data[opts.csrfTokenName] = opts.csrfToken;
                
                $.ajax({
                    type: 'POST',
                    url: opts.bidSpecUrl,
                    data: data,
                    dataType: 'json',
                    success: function(data) {
                        window.location.reload();
                        var data = data.response;
                        
                        if (data.status == 'error') {
                            if (data.data != null) {
                                if (data.data.type == 'NOT_AUTHORIZED') {
                                    // window.location = data.data.returnUrl;
                                    $('a.login').trigger('click');
                                    $(window).scrollTop(0);
                                    return false;
                                }

                                if (data.data.type == 'COMMON_ERROR') {
                                    $(priceField).val('');
                                    alert(data.data.message);
                                }
                            } else {
                                alert(data.messages[0]);
                            }
                            
                            //obj.showErrors(data.messages);
                        } else {
                            window.location.reload();
                            
                        }
                    }
                });
            },
            this.bidExchange = function(form) {
                var obj = this;
                var priceField = $(form).find('input[name="price"]');
                var priceValue = $(priceField).val();
                var priceValue = parseInt(priceValue);
                if (!priceValue) {priceValue = 0;}
                if (priceValue > 0) {priceValue = priceValue;} else {priceValue = 0;}
                var lotId = $(form).find('input[name="lotId"]').val();
                var doplata = $('input[name=doplata]:checked').val();
                if (!doplata) {doplata = 0;}
                var auc_id = $("#auc_id option:selected").val();
                
                if (priceValue > 0 && doplata == 0) {alert('Раз вы планируете доплату, то укажите её тип'); return false;}
                
                if (priceValue == 0 && doplata > 0) {alert('Раз вы планируете доплату, то укажите её размер'); return false;}
                
                var data = {
                    'price': priceValue,
                    'lotId': lotId,
                    'doplata': doplata,
                    'auc_id': auc_id
                };

                if (opts.csrfToken)
                    data[opts.csrfTokenName] = opts.csrfToken;
                
                $.ajax({
                    type: 'POST',
                    url: opts.bidExchangeUrl,
                    data: data,
                    dataType: 'json',
                    success: function(data) {
                        if (data === true) {window.location.reload();}
                        else
                        {
                            alert('Нельзя предлагать уже предложенный лот для обмена, или сделать более 5 предложений обмена для одного лота'); 
                        }
                    }
                });
                
            }
};
/*
$(document).ready(function () {

    function loadBidsTable() {
        var auctionId = $('input[name="auction_id_hidden"]').val();
        if ($('.table-wrp').hasClass('loading')) return;

        $('.table-wrp').addClass('loading').find('.items').remove();
        $('.table-wrp').fadeIn(200);

        $.ajax({
            type: 'GET',
            url: '/auction/showBidsTable',
            data: {'auction_id': auctionId},
            success: function (data) {
                $('.table-wrp').append(data);
                $('.table-wrp').removeClass('loading');
            }
        });
        return false;
    }
    if (window.bidsCount > 0 && !window.isLotOwnerUser) {
        loadBidsTable();
    }
    $('.bids-list .refresh-popup').click(function () {
        loadBidsTable();
    });
});
*/




