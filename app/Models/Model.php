<?php

namespace Valda\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Valda\Traits\EncryptsAttributes;
use Valda\Traits\MasksAttributes;
use Valda\Traits\SilencesModelEvents;

class Model extends EncryptedModel
{
    use SilencesModelEvents, MasksAttributes;
}
