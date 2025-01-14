# 项目概述

- 项目中文名：教务管理系统-后端
- 项目英文名：Education Administration System
- 项目代号：EAS

前后端分离的教务管理系统的后端，给前端提供接口服务。
前端项目地址：[教务管理系统-前端](https://github.com/Shirolin/eas-frontend)

同时，本项目的用户管理由BAS后台管理系统提供。
BAS后台管理系统地址：[BAS后台管理系统](https://github.com/Shirolin/Backend-Administration-System)

# 功能如下

- 用户认证——登录、退出、获取用户信息
- 教师端
  - 课程管理——课程增删改查、管理上课的学生
  - 账单管理——账单增删改查、发送账单通知学生
- 学生端
  - 课程管理——查看课程
  - 账单管理——查看账单、支付账单

# 运行环境要求

- PHP = 8.2.27
- PostgreSQL = 13.0 +

# 开发环境部署/安装

本项目代码使用 PHP 框架 Laravel 10 开发，开发前请确保本地环境已经安装以下应用/服务：

- PHP = 8.2.27
- Composer
- PostgreSQL = 13.0 +

### 基础安装

1. 克隆源代码到本地：

```shell
git clone https://github.com/Shirolin/Education-Administration-System
```

2. 进入项目目录安装依赖：

```shell
composer install
```

3. 复制 `.env.example` 为 `.env`：

```shell
cp .env.example .env
```

4. 生成应用秘钥：

```shell
php artisan key:generate
```

※ 如果要和BAS后台管理系统共用登录认证，可以将BAS的`.env`文件中的`APP_KEY`复制到本项目的`.env`文件中。

5. 创建数据库，数据库配置如下：

```shell
DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

※ 如果要和BAS后台管理系统共用数据库，可以将BAS的.env文件中的`DB_DATABASE`、`DB_USERNAME`、`DB_PASSWORD`复制到本项目的`.env`文件中。

8. 本地开发运行：

```shell
php artisan serve
```

1. 浏览器访问 `http://localhost:8000` 即可看到项目运行效果。

教师账号密码如下：

```shell
username: teacher01
password: password
```

学生账号密码如下：

```shell
username: student01
password: password
```

至此，安装完成。

# 扩展包使用情况

| 扩展包名称 | 一句话描述 | 本项目应用场景 |
| --- | --- | --- |
| laravel/framework | Laravel 框架 | 项目基础框架 |
| laravel/passport | Laravel Passport | 用户认证 |
| predis/predis | Redis 官方首推的 PHP 客户端开发包 | 缓存驱动 Redis 基础扩展包 |
| omise/omise-php | Omise PHP SDK | Omise 支付网关 SDK |

# 自定义 Artisan 命令

- 无

# 队列清单

| 队列名称 | 作用 | 调用时机 |
| --- | --- | --- |
| ProcessPaymentSuccess | 处理支付成功后的创建支付记录、更新账单状态、添加学生购课记录等操作 | 学生支付账单成功后 |

