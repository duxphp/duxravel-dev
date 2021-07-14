<?php

namespace Modules\Dev\Admin;

use Duxravel\Core\UI\Form;
use Duxravel\Core\UI\Table;
use Modules\Dev\Model\DevApp;
use Modules\Dev\Service\MakeFun;

class App extends \Modules\System\Admin\Common
{
    public function index()
    {
        return $this->systemView('vendor/duxphp/duxravel-dev/src/View/Admin/App/index');
    }

}
