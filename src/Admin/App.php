<?php

namespace Modules\Dev\Admin;

use Duxravel\Core\UI\Form;
use Duxravel\Core\UI\Table;
use Modules\Dev\Model\DevApp;
use Modules\Dev\Service\MakeFun;

class App extends \Modules\System\Admin\Expend
{

    public string $model = DevApp::class;

    protected function table(): Table
    {
        $table = new Table(new $this->model());
        $table->title('应用生成');
        $table->action()->button('应用', 'admin.dev.app.generateApp')->icon('plus')->type('dialog');
        $table->action()->button('功能', 'admin.dev.app.generateAdmin', [], 'yellow')->icon('plus');

        $table->column('项目名称', 'title');
        $table->column('项目类型', 'type')->status([
            'app' => '应用模块',
            'fun' => '应用功能',
        ], [
            'app' => 'blue',
            'fun' => 'yellow'
        ]);

        $column = $table->column('操作')->width(180);
        $column->link('生成', 'admin.dev.app.makeApp', ['id' => 'app_id'])->type('ajax')->data(['type' => 'post'])->show(function ($item) {
            return $item->type === 'app';
        });
        $column->link('编辑', 'admin.dev.app.generateApp', ['id' => 'app_id'])->type('dialog')->show(function ($item) {
            return $item->type === 'app';
        });

        $column->link('生成全部', 'admin.dev.app.makeAdmin', ['id' => 'app_id'])->type('ajax')->data(['type' => 'post'])->show(function ($item) {
            return $item->type === 'fun';
        });

        $column->link('生成控制器', 'admin.dev.app.makeAdmin', ['id' => 'app_id', 'type' => 1])->type('ajax')->data(['type' => 'post'])->show(function ($item) {
            return $item->type === 'fun';
        });

        $column->link('编辑', 'admin.dev.app.generateAdmin', ['id' => 'app_id'])->show(function ($item) {
            return $item->type === 'fun';
        });
        $column->link('删除', 'admin.dev.app.del', ['id' => 'app_id'])->type('ajax')->data(['type' => 'post']);

        $table->filter('描述搜索', 'name', function ($query, $value) {
            $query->where('name', 'like', '%' . $value . '%');
        })->text('请输入描述搜索')->quick();

        return $table;
    }

    public function generateAppForm($id = 0) {

        if ($id) {
            $data = $this->model::find($id)->value('data');
        }else {
            $data = [];
        }

        $form = new \Duxravel\Core\UI\Form(collect($data));
        $form->action(route('admin.dev.app.generateApp.save', ['id' => $id]));
        $form->dialog(true);

        $form->text('应用名', 'name')->verify([
            'required',
            'alpha',
        ], [
            'required' => '请填写应用名',
            'alpha' => '应用名只能为纯英文'
        ])->help('请填写英文字母');

        $form->text('应用名称', 'title')->verify([
            'required',
        ], [
            'required' => '请填写应用名称',
        ])->help('请填写中文名称');

        $form->textarea('应用描述', 'description')->verify([
            'required',
        ], [
            'required' => '请填写应用功能描述',
        ]);

        $form->text('应用作者', 'auth')->verify([
            'required',
        ], [
            'required' => '请填写作者名称',
        ]);

        return $form;
    }

    public function generateApp($id = 0)
    {
        return $this->generateAppForm($id)->render();
    }

    public function generateAppSave($id = 0)
    {
        $data = $this->generateAppForm($id)->save();

        $model = new $this->model;
        if ($id) {
            $model = $model->find($id);
        }
        $model->data = $data;
        $model->title = $data['title'];
        $model->type = 'app';
        $model->save();
        return app_success('应用保存成功');
    }

    public function makeApp($id = 0)
    {
        $model = new $this->model();
        $info = $model->find($id);
        (new MakeFun)->makeApp($info->data);
        return app_success('应用生成成功');
    }

    public function generateAdmin(int $id = 0)
    {
        $model = new $this->model();
        $info = $model->find($id);
        $data = glob(base_path('modules'). '/*');
        $appList = [];
        foreach ($data as $vo) {
            $appList[] = \Str::afterLast(str_replace('\\', '/', $vo), '/');
        }

        $this->assign('id', $id);
        $this->assign('info', $info);
        $this->assign('appList', $appList);
        return $this->systemView('vendor/duxphp/duxravel-dev/src/View/Admin/App/generateAdmin');
    }



    public function generateAdminSave($id = 0)
    {
        $data = request()->input('data');

        $model = new $this->model;
        if ($id) {
            $model = $model->find($id);
        }
        $model->data = $data;
        $model->title = $data['info']['name'];
        $model->type = 'fun';
        $model->save();
        return app_success('功能保存成功', [], route('admin.dev.app'));
    }

    public function makeAdmin($id = 0)
    {
        $model = new $this->model();
        $info = $model->find($id);
        $type = request()->get('type');

        (new MakeFun)->makeFun($info->data, $type);
        return app_success('功能生成成功', [], route('admin.dev.app'));

    }

}
