<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://molotoksoftware.com/
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

class UserPageLabel extends CWidget {


	public $user;

	public function init() {
            $cs = Yii::app()->clientScript;
            $cs->registerScript(
                'track_owner', '

                $("#add_track").click(function()
                {
                    var owner = $(this).data("idItem");
                    var fav = $(this);

                    $.ajax({
                        url      : "' . Yii::app()->createUrl('/auction/track_owner') . '",
                        data     : {"owner": owner},
                        type     : "GET",
                        dataType : "json",
                        success : function(data) 
                        {
                            if (data.response.data.stat == 0)
                            {
                                $(fav).text("'.Yii::t('basic', 'Following this seller').'");
                            }
                            else
                            {
                                $(fav).text("'.Yii::t('basic', 'Follow this seller').'");
                            }
                        }
                    });

                    return false;
                });
                ',
                    CClientScript::POS_READY
            );
        }

	public function run() {
		$this->render('userPageLabel', array(
			'user' => $this->user,
		));
	}
}