<?php

namespace Baijunyao\LaravelFineUploader\Models;

use Illuminate\Database\Eloquent\Model;

class FineUploaderFile extends Model
{
    /**
     * 禁止被批量赋值的字段
     *
     * @var array
     */
    protected $guarded = [];
}
