<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recruiters extends Model
{
    use HasFactory;

     /**
     * defining DB relationships
     *
     * @return void
     */

    public function users() {
        return $this->hasOne(Users::class);
    }
}
