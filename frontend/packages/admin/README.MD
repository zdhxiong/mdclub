## [MDClub 官网](https://mdclub.org)

## 项目说明

这是 MDClub 的后台管理系统前端代码。使用 [MDUI](https://mdui.org) 作为 UI 框架，[hyperapp](https://github.com/jorgebucaran/hyperapp) 作为 SPA 框架。

## 相关项目

MDClub 由以下子项目组成：

| 项目  | 说明 |
| ---- | ---- |
| [mdclub](https://github.com/zdhxiong/mdclub) | MDClub 服务端代码 |
| [mdclub-theme-material](https://github.com/zdhxiong/mdclub-theme-material) | MDClub 前端代码 |
| [mdclub-admin](https://github.com/zdhxiong/mdclub-admin) | MDClub 后台管理系统代码 |
| [mdclub-openapi](https://github.com/zdhxiong/mdclub-openapi) | MDClub 的 OpenApi 描述文件代码 |
| [mdclub-sdk-js](https://github.com/zdhxiong/mdclub-sdk-js) | MDClub 的 JavaScript SDK 代码 |

## 在此项目基础上进行二次开发

你需要先在本地安装 [mdclub](https://github.com/zdhxiong/mdclub) 、[mdclub-theme-material](https://github.com/zdhxiong/mdclub-theme-material) 和 [mdclub-admin](https://github.com/zdhxiong/mdclub-admin) 三个项目，且三个项目需要位于同一文件夹内。

因为 mdclub-admin 项目不带登录界面，需要在前台登陆后才能访问后台，所以安装 mdclub-theme-material 仅为了提供登录功能。

mdclub 项目的安装过程详见 https://github.com/zdhxiong/mdclub 。

mdclub-theme-material 项目的安装过程详见 https://github.com/zdhxiong/mdclub-theme-material 。

### mdclub-admin 的安装步骤

**克隆项目源码到本地**

```
git clone https://github.com/zdhxiong/mdclub-admin
```

**安装项目依赖**

```
$ cd mdclub-admin
$ npm install
```

**运行开发环境**

```
npm run dev
```

**打包生产环境**

```
npm run build
```

打包后的资源中，PHP 入口文件将位于 mdclub 项目的 /templates/default/admin.php，静态资源文件将位于 mdclub 项目的 /public/static/admin 目录中。
