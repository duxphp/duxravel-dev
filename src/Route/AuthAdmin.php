<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'   => 'dev',
    'auth_app' => '开发工具'
], function () {
    Route::group([
        'auth_group' => '应用生成'
    ], function () {
        Route::get('app', ['uses' => 'Modules\Dev\Admin\App@index', 'desc' => '列表'])->name('admin.dev.app');
        Route::get('app/generateApp/{id?}', ['uses' => 'Modules\Dev\Admin\App@generateApp', 'desc' => 'app生成'])->name('admin.dev.app.generateApp');
        Route::post('app/generateApp/save/{id?}', ['uses' => 'Modules\Dev\Admin\App@generateAppSave', 'desc' => '生成数据'])->name('admin.dev.app.generateApp.save');
        Route::get('app/generateAdmin/{id?}', ['uses' => 'Modules\Dev\Admin\App@generateAdmin', 'desc' => '功能生成'])->name('admin.dev.app.generateAdmin');
        Route::post('app/generateAdmin/save/{id?}', ['uses' => 'Modules\Dev\Admin\App@generateAdminSave', 'desc' => '生成数据'])->name('admin.dev.app.generateAdmin.save');
        Route::post('app/makeApp/{id?}', ['uses' => 'Modules\Dev\Admin\App@makeApp', 'desc' => '生成APP'])->name('admin.dev.app.makeApp');
        Route::post('app/makeAdmin/{id?}', ['uses' => 'Modules\Dev\Admin\App@makeAdmin', 'desc' => '生成功能'])->name('admin.dev.app.makeAdmin');
        Route::post('app/del/{id?}', ['uses' => 'Modules\Dev\Admin\App@del', 'desc' => '删除生成'])->name('admin.dev.app.del');
    });
    // Generate Route Make
});

