<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    //

    public function getTableDataAttribute(  ) {
        return json_decode($this->attributes['table_data'], true);
    }

    public function setTableDataAttribute( $value ) {
        $this->attributes['table_data'] = json_encode($value);
    }

    public function isFinished(  ) {
        return $this->turn == -1;
    }
}
