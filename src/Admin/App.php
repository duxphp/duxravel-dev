<?php

namespace Modules\Dev\Admin;

use Duxravel\Core\UI\Form;
use Duxravel\Core\UI\Table;
use Modules\Dev\Model\DevApp;
use Modules\Dev\Service\MakeFun;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class App extends \Modules\System\Admin\Common
{
    public function index()
    {
        return $this->systemView('vendor/duxphp/duxravel-dev/src/View/Admin/App/index');
    }

    public function generateForm()
    {
        $formData = request()->input('formData');
        if (!$formData) {
            app_error('暂无设计元素');
        }
        $class = request()->input('info.class');
        if (!$class) {
            app_error('请输入控制器路径');
        }
        $class = str_replace('.php', '', $class);
        $manageClass = module_path(trim(str_replace('\\', '/', $class), '/') . '.php');

        if (!is_file($manageClass)) {
            app_error('控制器不存在');
        }

        $make = new MakeFun;
        $formTpl = $make->formTpl($formData);
        $make->appendFile($manageClass, $formTpl, '// Generate Form Make');
        return app_success('代码生成成功');

    }

    public function copyForm()
    {
        $formData = request()->input('formData');
        if (!$formData) {
            app_error('暂无设计元素');
        }
        $code = (new MakeFun)->formTpl($formData);
        return app_success('请复制生成代码', [
            'code' => $code
        ]);

    }

    public function generateTable()
    {
        $tableData = request()->input('tableData');
        if (!$tableData) {
            app_error('暂无设计元素');
        }

        $class = request()->input('info.class');
        if (!$class) {
            app_error('请输入控制器路径');
        }
        $class = trim(str_replace('\\', '/', str_replace('.php', '', $class)), '/');
        $manageClass = module_path($class . '.php');

        if (!is_file($manageClass)) {
            app_error('控制器不存在');
        }
        list($app, $layer, $name) = explode('/', $class);

        $routeBase = implode('.', [lcfirst($layer), lcfirst($app), lcfirst($name)]);
        $nameArr = array_filter(preg_split("/(?=[A-Z])/", $name));
        $key = strtolower(end($nameArr)) . '_id';
        $make = new MakeFun;
        $tableTpl = $make->tableTpl($tableData, $routeBase, $key);
        $make->appendFile($manageClass, $tableTpl, '// Generate Table Make');
        return app_success('代码生成成功');

    }

    public function copyTable()
    {
        $routeBase = '';
        $key = 'id';
        $class = request()->input('info.class');
        if ($class) {
            $class = trim(str_replace('\\', '/', str_replace('.php', '', $class)), '/');
            $manageClass = module_path($class . '.php');
            if (is_file($manageClass)) {
                list($app, $layer, $name) = explode('/', $class);
                $routeBase = implode('.', [lcfirst($layer), lcfirst($app), lcfirst($name)]);
                $nameArr = array_filter(preg_split("/(?=[A-Z])/", $name));
                $key = strtolower(end($nameArr)) . '_id';
            }
        }

        $tableData = request()->input('tableData');
        if (!$tableData) {
            app_error('暂无设计元素');
        }
        $code = (new MakeFun)->tableTpl($tableData, $routeBase, $key);
        return app_success('请复制生成代码', [
            'code' => $code
        ]);

    }

    public function saveData()
    {
        $data = request()->input('data');
        $fun = request()->input('fun');

        $class = request()->input('info.model');
        if (!$class) {
            app_error('请输入模型路径');
        }
        $class = trim(str_replace('\\', '/', str_replace('.php', '', $class)), '/');
        $modelClass = module_path($class . '.php');
        if (!is_file($modelClass)) {
            app_error('模型不存在');
        }
        $className = file_class($modelClass);

        $model = new $className;
        $table = $model->getTable();
        $make = new MakeFun;

        if ($data) {
            Schema::table($table, function (Blueprint $table) use ($data, $make) {
                foreach ($data as $item) {
                    $make->dataColumn($table, $item);
                }
            });
        }

        $make->appendFile($modelClass, $make->modelUse($fun), '// Generate Model Make');

        return app_success('更新模型成功');
    }

}
