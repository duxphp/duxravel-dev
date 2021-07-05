<?php

namespace Modules\Dev\Model;

/**
 * class DevApp
 * @package Modules\Dev\Model
 */
class DevApp extends \Duxravel\Core\Model\Base
{

    protected $table = 'dev_app';

    protected $primaryKey = 'app_id';

    protected $casts = [
        'data' => 'array',
    ];

}
