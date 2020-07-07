DROP TABLE IF EXISTS `mc_answer`;
CREATE TABLE IF NOT EXISTS `mc_answer` (
  `answer_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '回答ID',
  `question_id` int(11) UNSIGNED NOT NULL COMMENT '问题ID',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `content_markdown` text NOT NULL COMMENT '原始的正文内容',
  `content_rendered` text NOT NULL COMMENT '过滤渲染后的正文内容',
  `comment_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数量',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `vote_up_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞成票总数',
  `vote_down_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对票总数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`answer_id`),
  KEY `question_id` (`question_id`),
  KEY `user_id` (`user_id`),
  KEY `vote_count` (`vote_count`),
  KEY `create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='回答表';

DROP TABLE IF EXISTS `mc_article`;
CREATE TABLE IF NOT EXISTS `mc_article` (
  `article_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章ID',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `title` varchar(80) NOT NULL COMMENT '标题',
  `content_markdown` text COMMENT '原始的正文内容',
  `content_rendered` text COMMENT '过滤渲染后的正文内容',
  `comment_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数量',
  `follower_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注者数量',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `vote_up_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞成票总数',
  `vote_down_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对票总数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`article_id`),
  KEY `user_id` (`user_id`),
  KEY `create_time` (`create_time`),
  KEY `vote_count` (`vote_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='文章表';

DROP TABLE IF EXISTS `mc_cache`;
CREATE TABLE IF NOT EXISTS `mc_cache` (
  `name` varchar(180) NOT NULL,
  `value` text NOT NULL,
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `life_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '有效时间',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='缓存表';

DROP TABLE IF EXISTS `mc_comment`;
CREATE TABLE IF NOT EXISTS `mc_comment` (
  `comment_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '回答评论ID',
  `commentable_id` int(11) UNSIGNED NOT NULL COMMENT '评论目标的ID',
  `commentable_type` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '评论目标类型：article、question、answer、comment',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `content` text NOT NULL COMMENT '原始正文内容',
  `reply_count` int(11) NOT NULL DEFAULT '0' COMMENT '回复数量',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `vote_up_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞成票总数',
  `vote_down_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对票总数',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`comment_id`),
  KEY `user_id` (`user_id`),
  KEY `commentable_id` (`commentable_id`),
  KEY `create_time` (`create_time`),
  KEY `vote_count` (`vote_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='回答评论表';

DROP TABLE IF EXISTS `mc_follow`;
CREATE TABLE IF NOT EXISTS `mc_follow` (
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `followable_id` int(11) UNSIGNED NOT NULL COMMENT '关注目标的ID',
  `followable_type` char(10) NOT NULL COMMENT '关注目标类型 user、question、article、topic',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注时间',
  KEY `followable_id` (`followable_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='文章关注关系表';

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

DROP TABLE IF EXISTS `mc_notification`;
CREATE TABLE IF NOT EXISTS `mc_notification` (
  `notification_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '通知ID',
  `receiver_id` int(11) UNSIGNED NOT NULL COMMENT '接收者ID',
  `sender_id` int(11) NOT NULL COMMENT '发送者ID',
  `type` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '消息类型：\r\nquestion_answered, \r\nquestion_commented, \r\nquestion_deleted, \r\narticle_commented, \r\narticle_deleted, \r\nanswer_commented, \r\nanswer_deleted, \r\ncomment_replied, \r\ncomment_deleted',
  `article_id` int(11) NOT NULL COMMENT '文章ID',
  `question_id` int(11) NOT NULL COMMENT '提问ID',
  `answer_id` int(11) NOT NULL COMMENT '回答ID',
  `comment_id` int(11) NOT NULL COMMENT '评论ID',
  `reply_id` int(11) NOT NULL COMMENT '回复ID',
  `content_deleted` text CHARACTER SET utf8mb4 COLLATE utf8mb4_cs_0900_ai_ci NOT NULL COMMENT '被删除的内容的备份',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发送时间',
  `read_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '阅读时间',
  PRIMARY KEY (`notification_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='通知表';

DROP TABLE IF EXISTS `mc_option`;
CREATE TABLE IF NOT EXISTS `mc_option` (
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '字段名',
  `value` text NOT NULL COMMENT '字段值',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='设置表';

INSERT INTO `mc_option` (`name`, `value`) VALUES
('answer_can_delete', 'false'),
('answer_can_delete_before', '0'),
('answer_can_delete_only_no_comment', 'true'),
('answer_can_edit', 'false'),
('answer_can_edit_before', '0'),
('answer_can_edit_only_no_comment', 'false'),
('article_can_delete', 'false'),
('article_can_delete_before', '0'),
('article_can_delete_only_no_comment', 'true'),
('article_can_edit', 'false'),
('article_can_edit_before', '0'),
('article_can_edit_only_no_comment', 'true'),
('cache_memcached_host', ''),
('cache_memcached_password', ''),
('cache_memcached_port', ''),
('cache_memcached_username', ''),
('cache_prefix', 'mdclub.'),
('cache_redis_host', ''),
('cache_redis_password', ''),
('cache_redis_port', ''),
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
('search_third', 'bing'),
('search_type', 'third'),
('site_description', 'MDClub 是一个 Material Design 样式的社区。'),
('site_gongan_beian', ''),
('site_icp_beian', ''),
('site_keywords', 'Material Design,MDUI'),
('site_name', 'MDClub'),
('site_static_url', ''),
('smtp_host', ''),
('smtp_password', ''),
('smtp_port', ''),
('smtp_reply_to', ''),
('smtp_secure', 'ssl'),
('smtp_username', ''),
('storage_aliyun_access_id', ''),
('storage_aliyun_access_secret', ''),
('storage_aliyun_bucket', ''),
('storage_aliyun_dir', ''),
('storage_aliyun_endpoint', ''),
('storage_ftp_host', ''),
('storage_ftp_passive', '1'),
('storage_ftp_password', ''),
('storage_ftp_port', '21'),
('storage_ftp_dir', ''),
('storage_ftp_ssl', '0'),
('storage_ftp_username', ''),
('storage_local_dir', ''),
('storage_qiniu_access_id', ''),
('storage_qiniu_access_secret', ''),
('storage_qiniu_bucket', ''),
('storage_qiniu_dir', ''),
('storage_qiniu_zone', ''),
('storage_sftp_host', ''),
('storage_sftp_password', ''),
('storage_sftp_port', ''),
('storage_sftp_dir', ''),
('storage_sftp_username', ''),
('storage_type', 'local'),
('storage_upyun_bucket', ''),
('storage_upyun_dir', ''),
('storage_upyun_operator', ''),
('storage_upyun_password', ''),
('storage_url', ''),
('theme', 'material');

DROP TABLE IF EXISTS `mc_question`;
CREATE TABLE IF NOT EXISTS `mc_question` (
  `question_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '问题ID',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `title` varchar(80) NOT NULL COMMENT '标题',
  `content_markdown` text COMMENT '原始的正文内容',
  `content_rendered` text COMMENT '过滤渲染后的正文内容',
  `comment_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论数量',
  `answer_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '回答数量',
  `follower_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注者数量',
  `vote_count` int(11) NOT NULL DEFAULT '0' COMMENT '投票数，赞成票-反对票，可以为负数',
  `vote_up_count` int(11) NOT NULL DEFAULT '0' COMMENT '赞成票总数',
  `vote_down_count` int(11) NOT NULL DEFAULT '0' COMMENT '反对票总数',
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

DROP TABLE IF EXISTS `mc_topic`;
CREATE TABLE IF NOT EXISTS `mc_topic` (
  `topic_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '话题ID',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '话题名称',
  `cover` varchar(50) DEFAULT NULL COMMENT '封面图片token',
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '话题描述',
  `article_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章数量',
  `question_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '问题数量',
  `follower_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '关注者数量',
  `delete_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`topic_id`),
  KEY `name` (`name`),
  KEY `follower_count` (`follower_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='话题表';

DROP TABLE IF EXISTS `mc_topicable`;
CREATE TABLE IF NOT EXISTS `mc_topicable` (
  `topic_id` int(11) UNSIGNED NOT NULL COMMENT '话题ID',
  `topicable_id` int(11) UNSIGNED NOT NULL COMMENT '话题关系对应的ID',
  `topicable_type` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '话题关系对应的类型 question、article',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  KEY `topic_id` (`topic_id`),
  KEY `topicable_id` (`topicable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `mc_user`;
CREATE TABLE IF NOT EXISTS `mc_user` (
  `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `email` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '邮箱',
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
  `headline` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT '一句话介绍',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='用户表';

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
