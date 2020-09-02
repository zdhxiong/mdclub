## [MDClub 官网](https://mdclub.org) | [在线体验](https://community.mdclub.org)

## 关于 MDClub

[MDClub](https://mdclub.org) 是一个漂亮、轻量的开源社区系统。它运行快速且易于使用，完全具备一个成熟社区所需的功能。它的特性包括：

* **Material Design 设计语言**
* **响应式设计**
* **超级轻量级**
* **自动适配暗色模式**
* **单页面应用，且完美支持 SEO**
* **定制的富文本编辑器**
* **方便安装**
* **强大的 Restful API 接口**
* **OpenAPI 规范的文档**
* **多平台 JavaScript SDK**

MDClub 项目由以下子项目组成：

| 项目  | 说明 |
| ---- | ---- |
| [mdclub](https://github.com/zdhxiong/mdclub) | MDClub 服务端代码 |
| [mdclub-theme-material](https://github.com/zdhxiong/mdclub-theme-material) | MDClub 前端代码 |
| [mdclub-admin](https://github.com/zdhxiong/mdclub-admin) | MDClub 后台管理系统代码 |
| [mdclub-openapi](https://github.com/zdhxiong/mdclub-openapi) | MDClub 的 OpenApi 描述文件代码 |
| [mdclub-sdk-js](https://github.com/zdhxiong/mdclub-sdk-js) | MDClub 的 JavaScript SDK 代码 |

## 安装

安装 MDClub 需要 PHP 7.2+ 和 MySQL 5.6+。请阅读 [安装说明](https://community.mdclub.org/articles/2) 了解安装过程。

若安装过程遇到困难，可以在 MDClub 的 [问答区](https://community.mdclub.org/questions) 进行搜索，或发帖寻求帮助。

## 在此项目基础上进行二次开发

你需要在本地安装 mdclub。如果你还需要修改前端代码或后台管理系统代码，则还需要安装 [mdclub-theme-material](https://github.com/zdhxiong/mdclub-theme-material) 和 [mdclub-admin](https://github.com/zdhxiong/mdclub-admin) 。

### mdclub 的安装步骤

**克隆项目源码到本地**

```
git clone https://github.com/zdhxiong/mdclub
```

**安装项目依赖**

```
cd mdclub
composer install
```

安装完依赖后，再根据 [MDClub 安装说明](https://community.mdclub.org/articles/2) 进行安装。

## 安全漏洞

如果你发现 MDClub 中存在安全漏洞，请给作者 [发送电子邮件](mailto:zdhxiong@gmail.com)。安全漏洞会最优先得到解决。
