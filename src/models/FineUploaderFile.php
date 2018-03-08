<?php

namespace Baijunyao\LaravelFineUploader\Models;

use Illuminate\Database\Eloquent\Model;

class FineUploaderFile extends Model
{

    /**
     * 关闭递增 否则查询的时候 uuid 会被转 init
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * 禁止被批量赋值的字段
     *
     * @var array
     */
    protected $guarded = [];
}
