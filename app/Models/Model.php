<?php

namespace Valda\Models;

use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Valda\Traits\SilencesModelEvents;

class Model extends BaseModel
{
    use CascadeSoftDeletes, SilencesModelEvents;
}
