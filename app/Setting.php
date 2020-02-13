<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps =false;
    public static function getSettings($key=null){
        $settigs=$key ? self::where('key',$key)->first() : self::get();
        $collect=collect();
        foreach ($settigs as $settig){
            $collect->put($settig->key,$settig->value );
        }
        return $collect;

    }


}
