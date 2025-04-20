# admin_php

## 技术

使用目前最流行的技术：

- PHP8
- TypeScript
- ThinkPHP8
- Vue3
- vite5
- element-plus 2（ElementUI）

## 文档

[完全开发手册](https://doc.thinkphp.cn)

## 安装

~~~
composer create-project topthink/think tp
~~~

启动服务

~~~
cd tp
php think run
~~~

然后就可以在浏览器中访问

~~~
http://localhost:8000
~~~

如果需要更新框架使用
~~~
composer update topthink/framework
~~~

## 命名规范

`ThinkPHP`遵循PSR-2命名规范和PSR-4自动加载规范。

## 生成文档

php vendor/zircote/swagger-php/bin/openapi app/front_api/controller -o public/swagger-ui/front_api-swagger.json --format json