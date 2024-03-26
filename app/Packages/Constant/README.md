# laravel项目常量库

用于管理常量。

### 使用说明

resources/constants/user.php
```php
return [
    'status' => [
        'disable' => [0, '禁用'],
        'enabled' => [1, '启用'],
    ],
];
```

1. 获取常量值
```php
    $statusKey = 'disable';
    $status = cons('user.status.' . $statusKey);
    // $status == 0
    // 也可以写成 cons()->get('user.status.' . $statusKey);
```

2. 根据常量值获取key
```php
    $status = 1;
    $statusKey = cons()->key('user.status', $status);
    // $statusKey == 'enabled'

    // 获取 id => key 方式的数组
    $statusKeys = cons()->key('user.status');
    /*
        $statusKeys == [
            0 => 'disable',
            1 => 'enabled',
        ];
    */
```

3. 获取常量对应语言
```php
    $statusKey = 'disable';
    $statusName = cons()->lang('user.status.' . $statusKey);
    // $statusName == '禁用'
```

4. 根据常量值获取对应语言
```php
    $statusKey = 1;
    $statusName = cons()->valueLang('user.status', $statusKey);
    // $statusName == '启用'

    // 获取 id => key 方式的数组
    $statusNames = cons()->valueLang('user.status');
    /*
        $statusNames == [
            0 => '禁用',
            1 => '启用',
        ];
    */
```