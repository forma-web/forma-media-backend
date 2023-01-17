<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

final class MovieSelection extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'movies_selections';
}
