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


class UserInfo extends CWidget
{
    const SCOPE_GRID_LOT = 'grid-lot';
    const SCOPE_GRID_LOT_HISTORY_SHOPPING = 'grid-lot-history-shopping';
    const SCOPE_TOP_USER_PANEL = 'top-user-panel';
    const SCOPE_SELLER_PAGE_LOT = 'seller-page-lot';
    const SCOPE_USER_PROFILE_PAGE = 'user-profile-page';
    const SCOPE_USER_SIMPLE = 'user-simple';
    const SCOPE_DISCUSSIONS_GRID = 'discussions-grid';
    const SCOPE_AUTHOR_DISCUSSION_PAGE = 'author-discussion-page';
    const SCOPE_COMMENT_AUTHOR_DISCUSSION_PAGE = 'comment-author-discussion-page';

    /**
     * @var array
     */
    public $userArr;

    /**
     * @var User
     */
    public $userModel;

    /**
     * @var string
     */
    public $scope;

    public function run()
    {
        $this->render('userInfo', [
            'userArr'   => $this->userArr,
            'userModel' => $this->userModel,
            'scope'     => $this->scope
        ]);
    }
}