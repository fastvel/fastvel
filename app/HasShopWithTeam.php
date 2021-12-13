<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace App;

use App\Models\Shop;

trait HasShopWithTeam
{
    /**
     * 用户在当前团队下，有授权的店铺
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_user')
            ->where('shops.team_id', $this->current_team_id);
    }
}
