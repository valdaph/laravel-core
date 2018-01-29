<?php

namespace Valda\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Valda\Traits\SilencesModelEvents;

class Model extends BaseModel
{
    use SilencesModelEvents;
}
