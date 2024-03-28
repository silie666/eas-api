教务管理系统api

运行队列
> php artisan queue:work redis --queue=listen-course --timeout=1200 --sleep=3 --tries=1 --memory=1024