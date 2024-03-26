<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Package\View\NullFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (!App::isLocal()) {
            $this->app->singleton('view', function () {
                return new NullFactory();
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!defined('OMISE_PUBLIC_KEY')) {
            define('OMISE_PUBLIC_KEY', config('omise.public_key'));
            define('OMISE_SECRET_KEY', config('omise.secret_key'));
        }
        // 设置日期的语言
        Carbon::setLocale('zh');
        CarbonInterval::setLocale('zh');

        // 扩展数据库定义
        $this->extendBlueprint();

        // 扩展集合
        $this->extendCollect();
    }

    /**
     * 扩展数据库定义
     */
    protected function extendBlueprint()
    {
        // 通用字段
        Blueprint::macro('commonColumns', function ($idComment = 'ID') {
            $table = $this;
            /* @var \Illuminate\Database\Schema\Blueprint $table */
            $table->increments('id')->comment($idComment);
            $table->timestamp('create_time')->useCurrent()->comment('创建时间');
            $table->timestamp('update_time')->useCurrent()->comment('最后更新时间');
            $table->timestamp('delete_time')->nullable()->comment('删除时间');
            $table->text('memo')->nullable()->comment('备注');

            $table->index('delete_time');
        });
    }

    /**
     * 扩展集合
     *
     * @return void
     */
    protected function extendCollect()
    {
        Collection::macro('paginate', function ($perPage = 15, $page = null, $options = []) {
            $page                = $page ?: (Paginator::resolveCurrentPage() ?: 1);
            $options['path']     = $options['path'] ?? Paginator::resolveCurrentPath();
            $options['pageName'] = $options['pageName'] ?? 'page';

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage)->values(),
                $this->count(),
                $perPage,
                $page,
                $options
            );
        });
    }
}
