-- phpMyAdmin SQL Dump
-- version 5.0.0
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1:3306
-- 生成日期： 2020-01-02 11:31:13
-- 服务器版本： 8.0.17
-- PHP 版本： 7.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `mdclub_test`
--

-- --------------------------------------------------------

--
-- 表的结构 `mc_answer`
--

DROP TABLE IF EXISTS `mc_answer`;
CREATE TABLE IF NOT EXISTS `mc_answer` (
  `answer_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '回答ID',
  `question_id` int(11) UNSIGNED NOT NULL COMMENT '问题ID',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `content_markdown` text NOT NULL COMMENT '原始的正文内容',
  `content_rendered` text NOT NULL COMMENT '过滤渲染后的正文内容',
  `comment_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数量',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`answer_id`),
  KEY `question_id` (`question_id`),
  KEY `user_id` (`user_id`),
  KEY `vote_count` (`vote_count`),
  KEY `create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='回答表';

-- --------------------------------------------------------

--
-- 表的结构 `mc_article`
--

DROP TABLE IF EXISTS `mc_article`;
CREATE TABLE IF NOT EXISTS `mc_article` (
  `article_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章ID',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `title` varchar(80) NOT NULL COMMENT '标题',
  `content_markdown` text COMMENT '原始的正文内容',
  `content_rendered` text COMMENT '过滤渲染后的正文内容',
  `comment_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数量',
  `view_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '阅读数量',
  `follower_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注者数量',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`article_id`),
  KEY `user_id` (`user_id`),
  KEY `create_time` (`create_time`),
  KEY `vote_count` (`vote_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='文章表';

-- --------------------------------------------------------

--
-- 表的结构 `mc_cache`
--

DROP TABLE IF EXISTS `mc_cache`;
CREATE TABLE IF NOT EXISTS `mc_cache` (
  `name` varchar(180) NOT NULL,
  `value` text NOT NULL,
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `life_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '有效时间',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='缓存表';

--
-- 转存表中的数据 `mc_cache`
--

INSERT INTO `mc_cache` (`name`, `value`, `create_time`, `life_time`) VALUES
('mdclub.:throttle_create_token_...1.', 'i:4;', 1572859435, 86400);

-- --------------------------------------------------------

--
-- 表的结构 `mc_comment`
--

DROP TABLE IF EXISTS `mc_comment`;
CREATE TABLE IF NOT EXISTS `mc_comment` (
  `comment_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '回答评论ID',
  `commentable_id` int(11) UNSIGNED NOT NULL COMMENT '评论目标的ID',
  `commentable_type` char(10) NOT NULL COMMENT '评论目标类型：article、question、answer',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `content` text NOT NULL COMMENT '原始正文内容',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`comment_id`),
  KEY `user_id` (`user_id`),
  KEY `commentable_id` (`commentable_id`),
  KEY `create_time` (`create_time`),
  KEY `vote_count` (`vote_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='回答评论表';

-- --------------------------------------------------------

--
-- 表的结构 `mc_follow`
--

DROP TABLE IF EXISTS `mc_follow`;
CREATE TABLE IF NOT EXISTS `mc_follow` (
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `followable_id` int(11) UNSIGNED NOT NULL COMMENT '关注目标的ID',
  `followable_type` char(10) NOT NULL COMMENT '关注目标类型 user、question、article、topic',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注时间',
  KEY `followable_id` (`followable_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='文章关注关系表';

-- --------------------------------------------------------

--
-- 表的结构 `mc_image`
--

DROP TABLE IF EXISTS `mc_image`;
CREATE TABLE IF NOT EXISTS `mc_image` (
  `key` varchar(50) NOT NULL COMMENT '图片键名',
  `filename` varchar(255) NOT NULL COMMENT '原始文件名',
  `width` int(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '原始图片宽度',
  `height` int(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '原始图片高度',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上传时间',
  `item_type` char(10) DEFAULT NULL COMMENT '关联类型：question、answer、article',
  `item_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  PRIMARY KEY (`key`),
  KEY `create_time` (`create_time`),
  KEY `item_id` (`item_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 表的结构 `mc_inbox`
--

DROP TABLE IF EXISTS `mc_inbox`;
CREATE TABLE IF NOT EXISTS `mc_inbox` (
  `inbox_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '私信ID',
  `receiver_id` int(11) UNSIGNED NOT NULL COMMENT '接收者ID',
  `sender_id` int(11) UNSIGNED NOT NULL COMMENT '发送者ID',
  `content_markdown` text NOT NULL COMMENT '原始的私信内容',
  `content_rendered` text NOT NULL COMMENT '过滤渲染后的私信内容',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发送时间',
  `read_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '阅读时间',
  PRIMARY KEY (`inbox_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `sender_id` (`sender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='私信表';

-- --------------------------------------------------------

--
-- 表的结构 `mc_notification`
--

DROP TABLE IF EXISTS `mc_notification`;
CREATE TABLE IF NOT EXISTS `mc_notification` (
  `notification_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '通知ID',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `content` text NOT NULL COMMENT '通知内容',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发送时间',
  `read_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '阅读时间',
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='通知表';

-- --------------------------------------------------------

--
-- 表的结构 `mc_option`
--

DROP TABLE IF EXISTS `mc_option`;
CREATE TABLE IF NOT EXISTS `mc_option` (
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '字段名',
  `value` text NOT NULL COMMENT '字段值',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='设置表';

--
-- 转存表中的数据 `mc_option`
--

INSERT INTO `mc_option` (`name`, `value`) VALUES
('answer_can_delete', 'false'),
('answer_can_delete_before', '0'),
('answer_can_delete_only_no_comment', 'true'),
('answer_can_edit', 'false'),
('answer_can_edit_before', '0'),
('answer_can_edit_only_no_comment', 'true'),
('article_can_delete', 'false'),
('article_can_delete_before', '0'),
('article_can_delete_only_no_comment', 'true'),
('article_can_edit', 'true'),
('article_can_edit_before', '0'),
('article_can_edit_only_no_comment', 'true'),
('cache_memcached_host', ''),
('cache_memcached_password', ''),
('cache_memcached_port', '1'),
('cache_memcached_username', ''),
('cache_prefix', ''),
('cache_redis_host', ''),
('cache_redis_password', ''),
('cache_redis_port', '1'),
('cache_redis_username', ''),
('cache_type', 'pdo'),
('comment_can_delete', 'false'),
('comment_can_delete_before', '0'),
('comment_can_edit', 'false'),
('comment_can_edit_before', '0'),
('language', 'zh-CN'),
('question_can_delete', 'false'),
('question_can_delete_before', '0'),
('question_can_delete_only_no_answer', 'true'),
('question_can_delete_only_no_comment', 'true'),
('question_can_edit', 'false'),
('question_can_edit_before', '0'),
('question_can_edit_only_no_answer', 'true'),
('question_can_edit_only_no_comment', 'true'),
('site_description', 'MDClub 是一个优雅的社区应用'),
('site_gongan_beian', '备案号'),
('site_icp_beian', '浙-99999999'),
('site_keywords', 'mdui, Material Design'),
('site_name', 'MDClub'),
('site_static_url', 'https://mdclub.org/static'),
('smtp_host', '101.1.1.101'),
('smtp_password', '123456'),
('smtp_port', '56'),
('smtp_reply_to', 'test@example.com'),
('smtp_secure', 'ssl'),
('smtp_username', 'test'),
('storage_aliyun_access_id', 'test'),
('storage_aliyun_access_secret', 'test'),
('storage_aliyun_bucket', 'mdclub'),
('storage_aliyun_endpoint', 'test'),
('storage_ftp_host', '101.1.1.101'),
('storage_ftp_passive', 'true'),
('storage_ftp_password', '123456'),
('storage_ftp_port', '45'),
('storage_ftp_root', '/static'),
('storage_ftp_ssl', 'true'),
('storage_ftp_username', 'test'),
('storage_local_dir', '/static'),
('storage_qiniu_access_id', 'tttt'),
('storage_qiniu_access_secret', 'gggg'),
('storage_qiniu_bucket', 'mdclub'),
('storage_qiniu_zone', 'z0'),
('storage_sftp_host', '102.22.21.12'),
('storage_sftp_password', '123456'),
('storage_sftp_port', '45'),
('storage_sftp_root', '/static'),
('storage_sftp_username', 'test'),
('storage_type', 'local'),
('storage_upyun_bucket', 'test'),
('storage_upyun_operator', 'test'),
('storage_upyun_password', '123456'),
('storage_url', 'https://mdclub.org/static'),
('theme', 'material');

-- --------------------------------------------------------

--
-- 表的结构 `mc_question`
--

DROP TABLE IF EXISTS `mc_question`;
CREATE TABLE IF NOT EXISTS `mc_question` (
  `question_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '问题ID',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `title` varchar(80) NOT NULL COMMENT '标题',
  `content_markdown` text COMMENT '原始的正文内容',
  `content_rendered` text COMMENT '过滤渲染后的正文内容',
  `comment_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数量',
  `answer_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '回答数量',
  `view_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `follower_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注者数量',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `last_answer_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后回答时间',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`question_id`),
  KEY `user_id` (`user_id`),
  KEY `create_time` (`create_time`),
  KEY `update_time` (`update_time`),
  KEY `vote_count` (`vote_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='问题表';

-- --------------------------------------------------------

--
-- 表的结构 `mc_report`
--

DROP TABLE IF EXISTS `mc_report`;
CREATE TABLE IF NOT EXISTS `mc_report` (
  `report_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `reportable_id` int(11) UNSIGNED NOT NULL COMMENT '举报目标ID',
  `reportable_type` char(10) NOT NULL COMMENT '举报目标类型：question、article、answer、comment、user',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `reason` varchar(200) NOT NULL COMMENT '举报原因',
  `create_time` int(11) UNSIGNED NOT NULL COMMENT '举报时间',
  PRIMARY KEY (`report_id`),
  KEY `reportable_id` (`reportable_id`),
  KEY `reportable_type` (`reportable_type`),
  KEY `create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='举报';

-- --------------------------------------------------------

--
-- 表的结构 `mc_token`
--

DROP TABLE IF EXISTS `mc_token`;
CREATE TABLE IF NOT EXISTS `mc_token` (
  `token` varchar(50) NOT NULL DEFAULT '' COMMENT 'token 字符串',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `device` varchar(600) NOT NULL DEFAULT '' COMMENT '登陆设备，浏览器 UA 等信息',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `expire_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`token`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='用户TOKEN';

--
-- 转存表中的数据 `mc_token`
--

INSERT INTO `mc_token` (`token`, `user_id`, `device`, `create_time`, `update_time`, `expire_time`) VALUES
('21e3c6d510e742a1bbe0b1b654b50535', 10000, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36', 1572859408, 1572859408, 1574155408),
('90cb51bc55c2488ba337e065d59e98d3', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36', 1572859408, 1572859408, 1574155408),
('a9c6700c8ff4449595efb8d0ec5d1553', 10000, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0', 1572859435, 1572859435, 1574155435),
('d4d93950f19b420a8f51f27bb6233314', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0', 1572859435, 1572859435, 1574155435);

-- --------------------------------------------------------

--
-- 表的结构 `mc_topic`
--

DROP TABLE IF EXISTS `mc_topic`;
CREATE TABLE IF NOT EXISTS `mc_topic` (
  `topic_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '话题ID',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '话题名称',
  `cover` varchar(50) DEFAULT NULL COMMENT '封面图片token',
  `description` varchar(1000) NOT NULL DEFAULT '' COMMENT '话题描述',
  `article_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章数量',
  `question_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '问题数量',
  `follower_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注者数量',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`topic_id`),
  KEY `name` (`name`),
  KEY `follower_count` (`follower_count`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='话题表';

--
-- 转存表中的数据 `mc_topic`
--

INSERT INTO `mc_topic` (`topic_id`, `name`, `cover`, `description`, `article_count`, `question_count`, `follower_count`, `delete_time`) VALUES
(1, 'Vue', 'd133be87aeda5f05e3f4b1d7088a9350.png', 'JS 框架', 0, 0, 0, 0),
(2, 'React', 'd133be87aeda5f05e3f4b1d7088a9351.png', 'JS MVVM 框架', 0, 0, 0, 0),
(3, 'Angular', 'd133be87aeda5f05e3f4b1d7088a9352.png', 'Google 的 JS MVVM 框架', 0, 0, 0, 1577964605);

-- --------------------------------------------------------

--
-- 表的结构 `mc_topicable`
--

DROP TABLE IF EXISTS `mc_topicable`;
CREATE TABLE IF NOT EXISTS `mc_topicable` (
  `topic_id` int(11) UNSIGNED NOT NULL,
  `topicable_id` int(11) UNSIGNED NOT NULL,
  `topicable_type` char(10) NOT NULL COMMENT 'question、article',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  KEY `topic_id` (`topic_id`),
  KEY `topicable_id` (`topicable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- 表的结构 `mc_user`
--

DROP TABLE IF EXISTS `mc_user`;
CREATE TABLE IF NOT EXISTS `mc_user` (
  `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `email` varchar(30) NOT NULL COMMENT '邮箱',
  `avatar` varchar(50) DEFAULT NULL COMMENT '头像token',
  `cover` varchar(50) DEFAULT NULL COMMENT '封面图片token',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `create_ip` varchar(80) DEFAULT NULL COMMENT '注册IP',
  `create_location` varchar(100) DEFAULT NULL COMMENT '注册地址',
  `last_login_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` varchar(80) DEFAULT NULL COMMENT '最后登陆IP',
  `last_login_location` varchar(100) DEFAULT NULL COMMENT '最后登录地址',
  `follower_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注我的人数',
  `followee_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我关注的人数',
  `following_article_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我关注的文章数',
  `following_question_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我关注的问题数',
  `following_topic_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我关注的话题数',
  `article_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我发表的文章数量',
  `question_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我发表的问题数量',
  `answer_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '我发表的回答数量',
  `notification_unread` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '未读通知数',
  `inbox_unread` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '未读私信数',
  `headline` varchar(40) DEFAULT NULL COMMENT '一句话介绍',
  `bio` varchar(160) DEFAULT NULL COMMENT '个人简介',
  `blog` varchar(255) DEFAULT NULL COMMENT '个人主页',
  `company` varchar(255) DEFAULT NULL COMMENT '公司名称',
  `location` varchar(255) DEFAULT NULL COMMENT '地址',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '注册时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `disable_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '禁用时间',
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`username`),
  KEY `email` (`email`),
  KEY `follower_count` (`follower_count`),
  KEY `create_time` (`create_time`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='用户表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `mc_user`
--

INSERT INTO `mc_user` (`user_id`, `username`, `email`, `avatar`, `cover`, `password`, `create_ip`, `create_location`, `last_login_time`, `last_login_ip`, `last_login_location`, `follower_count`, `followee_count`, `following_article_count`, `following_question_count`, `following_topic_count`, `article_count`, `question_count`, `answer_count`, `notification_unread`, `inbox_unread`, `headline`, `bio`, `blog`, `company`, `location`, `create_time`, `update_time`, `disable_time`) VALUES
(1, 'zdhxiong', 'zdhxiong@gmail.com', '5ff055ef87d140b2b6d485c8e49001e5.jpg', '1faf93cc1a6eef85c581003599552002.jpg', '$2y$10$eiGrPhHQYhVfflRM.kEqxeIwRd8z9tEKyRulWeYG66JDrhGiTpV46', '127.0.0.1', '本机地址 本机地址', 1572859435, '::1', '', 23, 0, 96, 90, 0, 96, 91, 96, 0, 0, 'MDUI 作者', 'mdui', 'https://mdui.org', 'mdui inc.', 'china', 1512838403, 1572859435, 0),
(10000, '1131699723', '1131699723@qq.com', '80494a2df2d544e5852ea9e251d8a837.png', '1faf93cc1a6eef85c581003599552002.jpg', '$2y$10$fDnrULv1q.gaqiQvMqMLkeT3t/17LD944vck3RmN3onZ.THBa4KQ2', '::1', '', 1572859435, '::1', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '普通用户', 'normal', 'https://mdui.org/user/10000', 'mdui inc.', 'china', 1568953985, 1572859435, 0);

-- --------------------------------------------------------

--
-- 表的结构 `mc_vote`
--

DROP TABLE IF EXISTS `mc_vote`;
CREATE TABLE IF NOT EXISTS `mc_vote` (
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `votable_id` int(11) UNSIGNED NOT NULL COMMENT '投票目标ID',
  `votable_type` char(10) NOT NULL COMMENT '投票目标类型 question、answer、article、comment',
  `type` char(10) NOT NULL COMMENT '投票类型 up、down',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '投票时间',
  KEY `user_id` (`user_id`),
  KEY `voteable_id` (`votable_id`),
  KEY `create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

