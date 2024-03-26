<?php

return [

    'build_path' => resource_path('docs'),

    'providers' => [
        'common-api'  => [
            'driver'  => 'open_api',
            'uri'     => 'common-api',
            'options' => [
                'schema'        => [
                    'remove_prefix' => 'App\Http\Resources\CommonApi',
                    'remove_suffix' => 'Resource',
                ],
                'info'          => [
                    'title'       => '公共Api',
                    'version'     => '1.0.0.0',
                    'description' => '请求接口地址：' . rtrim(env('APP_URL'), '/') . '/common-api/',
                ],
                'external_docs' => [
                    'description' => '接口接入规范文档',
                    'url'         => '',
                ],
            ],
        ],
        'student-api' => [
            'driver'  => 'open_api',
            'uri'     => 'student-api',
            'options' => [
                'schema'        => [
                    'remove_prefix' => 'App\Http\Resources\StudentApi',
                    'remove_suffix' => 'Resource',
                ],
                'info'          => [
                    'title'       => '学生端Api',
                    'version'     => '1.0.0.0',
                    'description' => '请求接口地址：' . rtrim(env('APP_URL'), '/') . '/student-api/',
                ],
                'external_docs' => [
                    'description' => '接口接入规范文档',
                    'url'         => '',
                ],
            ],
        ],
        'teacher-api' => [
            'driver'  => 'open_api',
            'uri'     => 'teacher-api',
            'options' => [
                'schema'        => [
                    'remove_prefix' => 'App\Http\Resources\TeacherApi',
                    'remove_suffix' => 'Resource',
                ],
                'info'          => [
                    'title'       => '教师端Api',
                    'version'     => '1.0.0.0',
                    'description' => '请求接口地址：' . rtrim(env('APP_URL'), '/') . '/teacher-api/',
                ],
                'external_docs' => [
                    'description' => '接口接入规范文档',
                    'url'         => '',
                ],
            ],
        ],
    ],
];
