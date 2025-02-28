<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeFormation extends Pivot
{
    protected $table = 'employe_formation';

    protected $casts = [
        'date_obtention' => 'date',
        'date_recyclage' => 'date',
        'last_recyclage' => 'date',
    ];
}
