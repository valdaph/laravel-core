<?php

namespace Valda\Models;

use Illuminate\Support\Facades\Schema;
use Valda\Traits\HasAssets;
use Valda\Traits\HasColumns;
use Valda\Traits\MasksAttributes;
use Valda\Traits\SilencesModelEvents;

class Model extends EncryptedModel
{
    use HasAssets, HasColumns, MasksAttributes, SilencesModelEvents;
}
