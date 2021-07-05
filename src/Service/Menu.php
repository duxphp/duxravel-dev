<?php

namespace Modules\Dev\Service;

/**
 * 系统菜单接口
 */

class Menu
{
    /**
     * 获取菜单结构
     */
    public function getAdminMenu()
    {
        return [
            'dev' => [
                'name'  => '生成',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" class="h-full w-full" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>',
                'order' => 1000,
                'url'   => 'admin.dev.app',
                'hidden' => true
            ],

        ];
    }

    public function getAppMenu()
    {
        return [
            [
                'name' => '应用生成',
                'desc' => '应用模块自动生成工具',
                'type' => 'tools',
                'url' => 'admin.dev.app',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-full w-full" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>'
            ]
        ];
    }

}

