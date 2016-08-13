<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://www.molotoksoftware.com/
 * @copyright 2016 MolotokSoftware
 * @license GNU General Public License, version 3
 */

/**
 * 
 * This file is part of MolotokSoftware.
 *
 * MolotokSoftware is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MolotokSoftware is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with MolotokSoftware.  If not, see <http://www.gnu.org/licenses/>.
 */



$script = "
    $(document).ready(function() {
        //event change transaction
        changeTransaction();
        $('input[name=\"Auction[type_transaction]\"]:radio').change(changeTransaction);
    });
    
    
    //action change transaction
    function changeTransaction(e) {
        var val = $(\"input[name='Auction[type_transaction]']:checked\").val();
        switch(val){
            case '0'://Стандартный
                $('label[for=\"Auction_starting_price\"]')
                    .html('Начальная цена')
                    .append(' <span class=\"required\">*</span>')
                    .addClass('required');
                 
                $('label[for=\"Auction_price\"]')
                    .html('Блиц-цена').removeClass('required').find('span.required');
                
                $('#Auction_starting_price').removeAttr('style');
                $('#Auction_price').parent().parent().show();
                $('#Auction_starting_price').parent().parent().show();
                $('#Auction_starting_price').val(parseInt($('#Auction_starting_price').val()));
                break;
            case '1'://Фиксированная цена
                $('label[for=\"Auction_price\"]').html('Цена').append(' <span class=\"required\">*</span>').addClass('required');
                $('#Auction_starting_price').parent().parent().hide();
                
                break;
            case '2'://С 1 рубля
                $('#Auction_starting_price').css({'border':'none','font-weight':'bold','color':'#009900','background' : 'none'}).val('1 рубль');
                $('label[for=\"Auction_starting_price\"]').removeClass('required').find('span.required').remove();
                $('#Auction_price').parent().parent().show();
                $('#Auction_starting_price').parent().parent().show();
                break;
            
        }
    }
    
    function hideOptions() {
        content = $('#content-options');
        content.hide().empty();
    }
    
    function downloadOptions(id) {
        content = $('#content-options');
        $.ajax({
            url:'/catalog/auction/getOptions',
            type:'GET',
            dataType:'json',
            data:{'cat_id':id},
            success:function(data) {
                if (data.isOptions) {
                    content.html(data.options).show();
                }
            },
            beforeSend:function(){
                hideOptions();
            }
        });
        
    } 
";
Yii::app()->clientScript->registerScript('scripts-lot', $script, CClientScript::POS_END);
?>