import { Option } from '../../es/models';
import * as OptionApi from '../../es/OptionApi';
import errors from '../utils/errors';
import publicFields from '../utils/publicFields';
import {
  removeDefaultToken,
  setDefaultTokenToManager,
  setDefaultTokenToNormal,
} from '../utils/token';
import models from '../utils/models';
import {
  deepEqual,
  matchModel,
  needLogin,
  needManager,
} from '../utils/validator';
import { failed, successWhen } from '../utils/result';

/**
 * options 完整数据
 */
const optionUpdateData: Option = {
  answer_can_delete: false,
  answer_can_delete_before: 0,
  answer_can_delete_only_no_comment: true,
  answer_can_edit: true,
  answer_can_edit_before: 0,
  answer_can_edit_only_no_comment: true,
  article_can_delete: false,
  article_can_delete_before: 0,
  article_can_delete_only_no_comment: true,
  article_can_edit: true,
  article_can_edit_before: 0,
  article_can_edit_only_no_comment: true,
  cache_memcached_host: '',
  cache_memcached_password: '',
  cache_memcached_port: 1,
  cache_memcached_username: '',
  cache_prefix: '',
  cache_redis_host: '',
  cache_redis_password: '',
  cache_redis_port: 1,
  cache_redis_username: '',
  cache_type: 'pdo',
  comment_can_delete: false,
  comment_can_delete_before: 0,
  comment_can_edit: true,
  comment_can_edit_before: 0,
  language: 'zh-CN',
  question_can_delete: true,
  question_can_delete_before: 0,
  question_can_delete_only_no_answer: true,
  question_can_delete_only_no_comment: true,
  question_can_edit: true,
  question_can_edit_before: 0,
  question_can_edit_only_no_answer: true,
  question_can_edit_only_no_comment: true,
  site_description: 'MDClub 是一个优雅的社区应用',
  site_gongan_beian: '备案号',
  site_icp_beian: '浙-99999999',
  site_keywords: 'mdui, Material Design',
  site_name: 'MDClub',
  site_static_url: 'https://mdclub.org/static',
  smtp_host: '101.1.1.101',
  smtp_password: '123456',
  smtp_port: 56,
  smtp_reply_to: 'test@example.com',
  smtp_secure: 'ssl',
  smtp_username: 'test',
  storage_aliyun_access_id: 'test',
  storage_aliyun_access_secret: 'test',
  storage_aliyun_bucket: 'mdclub',
  storage_aliyun_endpoint: 'test',
  storage_ftp_host: '101.1.1.101',
  storage_ftp_passive: true,
  storage_ftp_password: '123456',
  storage_ftp_port: 45,
  storage_ftp_root: '/static',
  storage_ftp_ssl: true,
  storage_ftp_username: 'test',
  storage_local_dir: '/static',
  storage_qiniu_access_id: 'tttt',
  storage_qiniu_access_secret: 'gggg',
  storage_qiniu_bucket: 'mdclub',
  storage_qiniu_zone: 'z0',
  storage_sftp_host: '102.22.21.12',
  storage_sftp_password: '123456',
  storage_sftp_port: 45,
  storage_sftp_root: '/static',
  storage_sftp_username: 'test',
  storage_type: 'local',
  storage_upyun_bucket: 'test',
  storage_upyun_operator: 'test',
  storage_upyun_password: '123456',
  storage_url: 'https://mdclub.org/static',
  theme: 'material',
};

/**
 * options 的公开部分数据的键名
 */
const optionPubicData = publicFields(optionUpdateData, [
  'answer_can_delete',
  'answer_can_delete_before',
  'answer_can_delete_only_no_comment',
  'answer_can_edit',
  'answer_can_edit_before',
  'answer_can_edit_only_no_comment',
  'article_can_delete',
  'article_can_delete_before',
  'article_can_delete_only_no_comment',
  'article_can_edit',
  'article_can_edit_before',
  'article_can_edit_only_no_comment',
  'comment_can_delete',
  'comment_can_delete_before',
  'comment_can_edit',
  'comment_can_edit_before',
  'language',
  'question_can_delete',
  'question_can_delete_before',
  'question_can_delete_only_no_answer',
  'question_can_delete_only_no_comment',
  'question_can_edit',
  'question_can_edit_before',
  'question_can_edit_only_no_answer',
  'question_can_edit_only_no_comment',
  'site_description',
  'site_gongan_beian',
  'site_icp_beian',
  'site_keywords',
  'site_name',
]);

describe('OptionApi', () => {
  it('update - 未登录', () => needLogin(OptionApi.update, optionUpdateData));
  it('update - 已登录', () => needManager(OptionApi.update, optionUpdateData));

  it('update - 管理员', () => {
    setDefaultTokenToManager();

    return OptionApi.update(optionUpdateData).then(response => {
      matchModel(response.data, models.Option);
      deepEqual(response.data, optionUpdateData);
    });
  });

  it('update - 管理员 - 多余字段', () => {
    setDefaultTokenToManager();

    // 错误的参数被 SDK 自动过滤掉了，所以不会报错。后端也会自动过滤多余参数，也不会报错
    return OptionApi.update({
      // @ts-ignore
      test: 'value',
    }).then((response: { data: Option }) => {
      matchModel(response.data, models.Option);
      deepEqual(response.data, optionUpdateData);
    });
  });

  it('update - 管理员 - 错误的值类型', () => {
    setDefaultTokenToManager();

    return OptionApi.update({
      // @ts-ignore
      answer_can_delete: 'true',
      // @ts-ignore
      answer_can_delete_before: '0',
      site_description: 'test'.repeat(1000),
    })
      .then(() => failed('值类型错误时，应该抛出错误'))
      .catch((response: any) => {
        successWhen(response.code === errors.COMMON_FIELD_VERIFY_FAILED);
        deepEqual(Object.keys(response.errors), [
          'answer_can_delete',
          'answer_can_delete_before',
          'site_description',
        ]);
      });
  });

  it('get - 未登录', () => {
    removeDefaultToken();

    return OptionApi.get().then(response => {
      matchModel(response.data, models.Option);
      deepEqual(response.data, optionPubicData);
    });
  });

  it('get - 已登录', () => {
    setDefaultTokenToNormal();

    return OptionApi.get().then(response => {
      matchModel(response.data, models.Option);
      deepEqual(response.data, optionPubicData);
    });
  });

  it('get - 管理员', () => {
    setDefaultTokenToManager();

    return OptionApi.get().then(response => {
      matchModel(response.data, models.Option);
      deepEqual(response.data, optionUpdateData);
    });
  });
});
