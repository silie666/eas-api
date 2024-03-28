<?php

return [

    'build_path' => resource_path('docs'),

    'providers' => [
        'common-api'  => [
            'driver'  => 'open_api',
            'uri'     => 'api/common-api',
            'options' => [
                'schema'        => [
                    'remove_prefix' => 'App\Http\Resources\CommonApi',
                    'remove_suffix' => 'Resource',
                ],
                'info'          => [
                    'title'       => '公共Api',
                    'version'     => '1.0.0.0',
                    'description' => '请求接口地址：' . rtrim(env('APP_URL'), '/') . '/api/common-api/',
                ],
                'external_docs' => [
                    'description' => '接口接入规范文档',
                    'url'         => '',
                ],
            ],
        ],
        'student-api' => [
            'driver'  => 'open_api',
            'uri'     => 'api/student-api',
            'options' => [
                'schema'        => [
                    'remove_prefix' => 'App\Http\Resources\StudentApi',
                    'remove_suffix' => 'Resource',
                ],
                'info'          => [
                    'title'       => '学生端Api',
                    'version'     => '1.0.0.0',
                    'description' => '请求接口地址：' . rtrim(env('APP_URL'), '/') . '/api/student-api/',
                ],
                'external_docs' => [
                    'description' => '接口接入规范文档',
                    'url'         => '',
                ],
            ],
        ],
        'teacher-api' => [
            'driver'  => 'open_api',
            'uri'     => 'api/teacher-api',
            'options' => [
                'schema'        => [
                    'remove_prefix' => 'App\Http\Resources\TeacherApi',
                    'remove_suffix' => 'Resource',
                ],
                'info'          => [
                    'title'       => '教师端Api',
                    'version'     => '1.0.0.0',
                    'description' => '请求接口地址：' . rtrim(env('APP_URL'), '/') . '/api/teacher-api/',
                ],
                'external_docs' => [
                    'description' => '接口接入规范文档',
                    'url'         => '',
                ],
            ],
        ],
    ],
];
