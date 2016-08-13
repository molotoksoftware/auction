$(document).ready(function() {
    $('.update-my-lots-address').click(function() {
        if(confirm('Вы действительно хотите обновить местоположение всех ваших активных лотов?')) {
            if ($('.city-selector .city-select').val()) {
                $.ajax({
                    url: '/editor/setAuctionCity/id_city/' + $('.city-selector .city-select').val(),
                    complete: function () {
                        alert('Лоты успешно обновлены')
                    }
                });
            } else {
                alert('Выберите город, который хотите присвоить лотам');
            }
        }
    })
});