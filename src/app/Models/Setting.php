<?php

namespace App\Models;

/**
 * @property string $name
 * @property string $value
 */
class Setting extends BriskLimbs
{
    protected $fillable = ['name', 'value'];
}
