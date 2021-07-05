<?php

namespace Modules\Dev\Service;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use \Modules\Dev\Util\Generate;

/**
 * 生成功能接口
 */
class MakeFun
{

    public function makeApp($data)
    {
        Artisan::call("app:make", [
            'name' => $data['name'],
            '--title' => $data['title'],
            '--desc' => $data['description'],
            '--auth' => $data['auth'],
        ]);

    }

    public function makeFun($data, $type = 0)
    {
        $info = $data['info'];
        $app = ucfirst($info['app']);
        if (!is_dir(base_path('/modules/' . $app))) {
            app_error('应用不存在');
        }

        $model = $data['data'];
        $key = '';
        foreach ($model as $vo) {
            if ($vo['preset'] && $vo['index'] == 'PRIMARY' && $vo['unsigned']) {
                $key = $vo['field'];
            }
        }

        $fun = lcfirst($info['class']);
        $class = ucfirst($fun);
        if (!$class) {
            app_error('功能名未设置');
        }

        $title = $info['name'];
        if (!$title) {
            app_error('功能名称未设置');
        }

        if (!$type) {
            $table = strtolower($app) . '_' . $this->nameFormat($data['info']['class']);
            if (Schema::hasTable($table)) {
                app_error('模型表已存在');
            }
        }

        $this->makeAdmin($app, $data, $fun, $key);

        if (!$type) {
            $this->makeModel($app, $data, $table, $fun, $key);
        }

        Artisan::call("app:build");
    }

    public function makeAdmin($app, $data, $fun, $key)
    {
        $class = ucfirst($fun);
        $info = $data['info'];
        $routeBase = 'admin' . '.' . lcfirst($app) . '.' . $fun;
        $form = $data['formData'];
        $formTpl = $this->formTpl($form);
        $formTpl = <<<EOL
                    \$form->card(function (Form \$form) {
                        $formTpl
                    });
                    EOL;

        $table = $data['tableData'];
        $tableTpl = $this->tableTpl($table, $routeBase, $key);

        Artisan::call("app:make-admin", [
            'name' => $app,
            '--class' => $info['class'],
            '--title' => $info['name'],
        ]);
        $this->appendFile("{$app}/Admin/$class.php", $tableTpl, '// Generate Table Make');
        $this->appendFile("{$app}/Admin/$class.php", $formTpl, '// Generate Form Make');
    }

    public function makeModel($app, $data, $table, $fun, $key)
    {
        $modelFun = $data['dataFun'];
        $tmpArr = explode('_', $table);
        $class = implode('', array_map(function ($vo) {
            return ucfirst($vo);
        }, $tmpArr));

        Artisan::call("app:make-model", [
            'name' => $data['info']['app'],
            '--table' => $table,
            '--key' => $key,
            '--del' => $modelFun['del'],
        ]);
        $this->appendFile("{$app}/Model/$class.php", $this->modelUse($modelFun), '// Generate Model Make');

        $data['data'] = array_reverse($data['data']);
        Schema::table($table, function (Blueprint $table) use ($data, $key) {
            foreach ($data['data'] as $vo) {
                if ($vo['field'] === $key) {
                    continue;
                }
                $base = $table;
                $fun = '';
                $len = $vo['len'];
                switch ($vo['type']) {
                    case 'CHAR':
                        $fun = 'char';
                        break;
                    case 'VARCHAR':
                        $fun = 'string';
                        break;
                    case 'BLOB':
                        $fun = 'binary';
                        $len = null;
                        break;
                    case 'TEXT':
                        $fun = 'text';
                        $len = null;
                        break;
                    case 'MEDIUMTEXT':
                        $fun = 'mediumText';
                        $len = null;
                        break;
                    case 'LONGTEXT':
                        $fun = 'longText';
                        $len = null;
                        break;
                    case 'INTEGER':
                        $fun = 'integer';
                        if ($vo['unsigned']) {
                            $fun = 'increments';
                        }
                        $len = null;
                        break;
                    case 'BIGINT':
                        $fun = 'bigInteger';
                        if ($vo['unsigned']) {
                            $fun = 'unsignedBigInteger';
                        }
                        $len = null;
                        break;
                    case 'TINYINT':
                        $fun = 'tinyInteger';
                        if ($vo['unsigned']) {
                            $fun = 'tinyIncrements';
                        }
                        $len = null;
                        break;
                    case 'SMALLINT':
                        $fun = 'smallInteger';
                        if ($vo['unsigned']) {
                            $fun = 'unsignedSmallInteger';
                        }
                        $len = null;
                        break;
                    case 'MEDIUMINT':
                        $fun = 'mediumInteger';
                        if ($vo['unsigned']) {
                            $fun = 'unsignedMediumInteger';
                        }
                        $len = null;
                        break;
                    case 'JSON':
                        $fun = 'json';
                        $len = null;
                        break;
                    case 'FLOAT':
                        $fun = 'float';
                        $len = null;
                        break;
                    case 'DOUBLE':
                        $fun = 'double';
                        $len = null;
                        break;
                    case 'DECIMAL':
                        $fun = 'decimal';
                        $len = null;
                        break;
                    case 'DATE':
                        $fun = 'date';
                        $len = null;
                        break;
                    case 'TIME':
                        $fun = 'time';
                        $len = null;
                        break;
                    case 'YEAR':
                        $fun = 'year';
                        $len = null;
                        break;
                    case 'DATETIME':
                        $fun = 'dateTime';
                        $len = null;
                        break;
                    case 'TIMESTAMP':
                        $fun = 'timestamp';
                        $len = null;
                        break;
                }
                if (!$fun) {
                    continue;
                }

                $base = $base->{$fun}($vo['field'], $len);

                if ($vo['null']) {
                    $base = $base->nullable();
                }
                if ($vo['default']) {
                    $base = $base->default($vo['default']);
                }
                if ($vo['index'] === 'PRIMARY') {
                    $base = $base->primary($vo['field']);
                }
                if ($vo['index'] === 'NORMAL') {
                    $base = $base->index($vo['field']);
                }
                if ($vo['index'] === 'UNIQUE') {
                    $base = $base->unique($vo['field']);
                }
                $base->comment($vo['name'])->after($key);
            }
        });
        $fun = ucfirst($fun);
        $classFile = base_path("modules/{$app}/Admin/{$fun}.php");
        $fileContent = file_get_contents($classFile);
        $fileContent = str_replace('\\Duxravel\\Core\\Model\\Base', "\\Modules\\$app\\Model\\$class", $fileContent);
        file_put_contents($classFile, $fileContent);
    }

    public function modelUse($list)
    {
        $data = [];
        foreach ($list as $key => $vo) {
            if ($key === 'del' && $vo) {
                $data[] = 'use \Illuminate\Database\Eloquent\SoftDeletes;';
            }
            if ($key === 'tree' && $vo) {
                $data[] = 'use \Kalnoy\Nestedset\NodeTrait;';
            }
            if ($key === 'visitor' && $vo) {
                $data[] = 'use \Duxravel\Core\Traits\Visitor;';
            }
            if ($key === 'form' && $vo) {
                $data[] = 'use \Duxravel\Core\Traits\Form;';
            }
            if ($key === 'tag' && $vo) {
                $data[] = 'use  \Conner\Tagging\Taggable;';
            }
        }
        return implode("\n", $data);

    }

    public function formTpl($form)
    {
        $data = [];
        foreach ($form as $vo) {
            $name = 'formGenerate' . ucfirst($vo['type']);
            $data[] = '    ' . $this->{$name}($vo);
        }
        return implode("\n", $data) . "\n";
    }

    public function tableTpl($table, $routeBase, $key)
    {

        $data = [];
        foreach ($table['action'] as $vo) {
            $name = 'tableGenerateAction' . ucfirst($vo['type']);
            $data[] = $this->{$name}($vo, $routeBase, $key);
        }
        foreach ($table['filter'] as $vo) {
            $name = 'tableGenerateFilter' . ucfirst($vo['type']);
            $data[] = $this->{$name}($vo, $routeBase, $key);
        }
        foreach ($table['column'] as $vo) {
            $name = 'tableGenerateColumn' . ucfirst($vo['type']);
            $data[] = $this->{$name}($vo, $routeBase, $key);
        }

        return implode("\n", $data) . "\n";
    }

    public function tableGenerateFilterText($item)
    {
        $base = (new Generate('table'))->method('filter', [$item['name'], $item['field']]);
        if (!$item['data']['type']) {
            $base = $base->method('quick');
        }
        $base = $base->method('text');
        return $base->make();
    }

    public function tableGenerateFilterSelect($item)
    {
        $params = [];
        foreach ($item['data']['options'] as $vo) {
            $params[$vo['key']] = $vo['value'];
        }
        $base = (new Generate('table'))->method('filter', [$item['name'], $item['field']]);
        if (!$item['data']['type']) {
            $base = $base->method('quick');
        }
        $base = $base->method('select', [$params]);
        return $base->make();
    }

    public function tableGenerateColumnText($item)
    {
        return (new Generate('table'))->method('column', [$item['name'], $item['field']])->make();
    }

    public function tableGenerateColumnImageText($item)
    {
        $base = (new Generate('table'))->method('column', [$item['name'], $item['field']]);
        if ($item['data']['desc']) {
            $base = $base->method('desc', [$item['data']['desc']]);
        }
        if ($item['data']['image']) {
            $base = $base->method('image', [$item['data']['image']]);
        }
        return $base->make();
    }

    public function tableGenerateColumnStatus($item)
    {
        $base = (new Generate('table'))->method('column', [$item['name'], $item['field']]);
        $base = $base->method('status', [[
            1 => '正常',
            0 => '禁用'
        ], [
            1 => 'blue',
            0 => 'red'
        ]]);
        return $base->make();
    }

    public function tableGenerateColumnToggle($item, $routeBase, $key)
    {
        $base = (new Generate('table'))->method('column', [$item['name'], $item['field']]);
        $base = $base->method('toggle', [$item['field'], $routeBase . '.status', ['id' => $key]]);
        return $base->make();
    }

    public function tableGenerateColumnProgress($item)
    {
        $base = (new Generate('table'))->method('column', [$item['name'], $item['field']]);
        $base = $base->method('progress');
        return $base->make();
    }

    public function tableGenerateColumnManage($item, $routeBase, $key)
    {
        $html = [];
        $html[] = '$column = ' . (new Generate('table'))->method('column', [$item['name'], $item['field']])->method('width', [100])->make();

        $html[] = (new Generate('column'))->method('link', ['编辑', $routeBase . '.page', ['id' => $key]])->make();
        $html[] = (new Generate('column'))->method('link', ['删除', $routeBase . '.del', ['id' => $key]])->make();

        return implode("\n", $html);
    }

    public function tableGenerateActionAdd($item, $routeBase)
    {
        return (new Generate('table'))->method('action')->method('button', [$item['name'], "$routeBase.page"])->method('icon', ['plus'])->make();
    }

    public function tableGenerateActionExport($item, $routeBase)
    {
        return (new Generate('table'))->method('action')->method('button', [$item['name'], "$routeBase.export"])->method('icon', ['share'])->make();
    }

    public function tableGenerateActionButton($item, $routeBase)
    {
        $params = [];
        foreach ($item['data']['params'] as $vo) {
            $params[$vo['key']] = $vo['value'];
        }
        $params = array_filter($params);
        $base = (new Generate('table'))->method('action')->method('button', [$item['name'], $item['route'], $params]);
        if ($item['icon']) {
            $base = $base->method('icon', $item['icon']);
        }
        return $base->make();
    }

    public function tableGenerateActionMenu($item, $routeBase)
    {
        $html = [];
        $html[] = '$menu = ' . (new Generate('table'))->method('action')->method('menu', [$item['name']])->make();
        foreach ($item['data']['menu'] as $vo) {
            $html[] = (new Generate('menu'))->method('link', [$vo['key'], $vo['value']])->make();
        }
        return implode("\n", $html);
    }

    public function formGenerateText($item)
    {
        return (new Generate('form'))->method('text', [$item['name'], $item['field']])->make();
    }

    public function formGenerateSelect($item)
    {
        return (new Generate('form'))->method('select', [$item['name'], $item['field'], $item['data']['options']])->make();
    }

    public function formGenerateRadio($item)
    {
        return (new Generate('form'))->method('radio', [$item['name'], $item['field'], $item['data']['options']])->make();
    }

    public function formGenerateCheckbox($item)
    {
        return (new Generate('form'))->method('radio', [$item['name'], $item['field'], $item['data']['options']])->make();
    }

    public function formGenerateImage($item)
    {
        return (new Generate('form'))->method('image', [$item['name'], $item['field']])->make();
    }

    public function formGenerateImages($item)
    {
        return (new Generate('form'))->method('images', [$item['name'], $item['field']])->make();
    }

    public function formGenerateFile($item)
    {
        return (new Generate('form'))->method('file', [$item['name'], $item['field']])->make();
    }

    public function formGenerateDate($item)
    {
        if ($item['data']['type'] === 'date') {
            $code = (new Generate('form'))->method('date', [$item['name'], $item['field']]);
        }
        if ($item['data']['type'] === 'time') {
            $code = (new Generate('form'))->method('time', [$item['name'], $item['field']]);
        }
        if ($item['data']['type'] === 'datetime') {
            $code = (new Generate('form'))->method('datetime', [$item['name'], $item['field']]);
        }
        if ($item['data']['type'] === 'daterange') {
            $code = (new Generate('form'))->method('daterange', [$item['name'], $item['field']]);
        }

        if ($item['data']['required']) {
            $code = $code->method('must');
        }
        return $code->make();
    }

    public function formGenerateEditor($item)
    {
        return (new Generate('form'))->method('editor', [$item['name'], $item['field']])->make();
    }

    public function formGenerateColor($item)
    {
        $code = (new Generate('form'))->method('color', [$item['name'], $item['field']]);
        if ($item['data']['type']) {
            $code = $code->method('picker');
        }
        return $code->make();
    }

    public function generatorFile($file, $tpl = '', $data = [])
    {
        $file = base_path('/modules/' . $file);
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($path, 0777, true);
        }
        $content = file_get_contents($tpl);
        foreach ($data as $key => $vo) {
            $content = str_replace('{{' . $key . '}}', $vo, $content);
        }
        file_put_contents($file, $content);
    }

    public function appendFile($file, $content = '', $mark = '')
    {
        $file = base_path('/modules/' . $file);
        $data = [];
        $contentData = explode("\n", $content);
        foreach (file($file) as $line) {
            if (strpos($line, $mark) !== false) {
                $place = substr($line, 0, strrpos($line, $mark));
                foreach ($contentData as $content) {
                    $data[] = $place . $content . "\n";
                }
            }
            $data[] = $line;
        }
        file_put_contents($file, implode("", $data));
    }

    public function nameFormat($name)
    {
        $temp_array = array();
        for ($i = 0; $i < strlen($name); $i++) {
            $ascii_code = ord($name[$i]);
            if ($ascii_code >= 65 && $ascii_code <= 90) {
                if ($i == 0) {
                    $temp_array[] = chr($ascii_code + 32);
                } else {
                    $temp_array[] = '_' . chr($ascii_code + 32);
                }
            } else {
                $temp_array[] = $name[$i];
            }
        }
        return implode('', $temp_array);
    }
}

