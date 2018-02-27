<?php

namespace Valda\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Valda\Traits\EncryptsAttributes;

class EncryptedModel extends BaseModel
{
    use EncryptsAttributes;
}
