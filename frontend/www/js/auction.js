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
                    this.showErrors('You must specify your bid');
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
                                    window.location = data.data.returnUrl;
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

                        } else {
                            $('.price-field', '#bid-form').empty().html(priceValue);
                            $(priceField).val('');
                            $(form).find('input[name="start"]').val(priceValue);

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
                                    window.location = data.data.returnUrl;
                                }
                        } else {
                            window.location.reload();
                        }
                    }
                });

            }


};




