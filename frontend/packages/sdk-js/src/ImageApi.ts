import defaults from './defaults';
import { get, post, put, patch, del } from './util/requestAlias';
import { urlParamReplace } from './util/url';
import {
  ImageUpdateRequestBody,
  ImageResponse,
  ImageUploadRequestBody,
  EmptyResponse,
  ImagesResponse,
} from './models';

interface DeleteParams {
  key: string;
}

interface DeleteMultipleParams {
  key?: Array<string>;
}

interface GetParams {
  key: string;
  include?: Array<string>;
}

interface GetListParams {
  page?: number;
  perPage?: number;
  include?: Array<string>;
  key?: string;
  itemType?: 'question' | 'answer' | 'article';
  itemId?: string;
  userId?: number;
}

interface UpdateParams {
  key: string;
  imageUpdateRequestBody: ImageUpdateRequestBody;
  include?: Array<string>;
}

interface UploadParams {
  imageUploadRequestBody: ImageUploadRequestBody;
  include?: Array<string>;
}

/**
 * ImageApi
 */
export default {
  /**
   * 🔐删除指定图片
   * 仅管理员可调用该接口
   * @param params.key 图片key
   */
  del: (params: DeleteParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ImageApi.del', '/images/{key}', params, []);

    return del(url);
  },

  /**
   * 🔐批量删除图片
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有记录被删除，该接口都会返回成功。
   * @param params.key 用“,”分隔的图片key，最多可提供 40 个 key（IE 的 query 参数最长为 2k，为了不超过这个数值，限制最多可以提交 40 个 key）
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ImageApi.deleteMultiple', '/images', params, ['key']);

    return del(url);
  },

  /**
   * 获取指定图片信息
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;question&#x60;、&#x60;article&#x60;、&#x60;answer&#x60;
   * @param params.key 图片key
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  get: (params: GetParams): Promise<ImageResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ImageApi.get', '/images/{key}', params, ['include']);

    return get(url);
  },

  /**
   * 🔐获取图片列表
   * 仅管理员可调用该接口  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;question&#x60;、&#x60;article&#x60;、&#x60;answer&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.key 图片key
   * @param params.itemType 图片关联对象的类型
   * @param params.itemId 图片关联对象的ID
   * @param params.userId 用户ID
   */
  getList: (params: GetListParams): Promise<ImagesResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ImageApi.getList', '/images', params, [
        'page',
        'per_page',
        'include',
        'key',
        'item_type',
        'item_id',
        'user_id',
      ]);

    return get(url);
  },

  /**
   * 🔐更新指定图片信息
   * 仅管理员可调用该接口  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;question&#x60;、&#x60;article&#x60;、&#x60;answer&#x60;
   * @param params.key 图片key
   * @param params.imageUpdateRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  update: (params: UpdateParams): Promise<ImageResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ImageApi.update', '/images/{key}', params, ['include']);

    return patch(url, params.imageUpdateRequestBody || {});
  },

  /**
   * 上传图片
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;question&#x60;、&#x60;article&#x60;、&#x60;answer&#x60;
   * @param params.imageUploadRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  upload: (params: UploadParams): Promise<ImageResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ImageApi.upload', '/images', params, ['include']);

    return post(url, params.imageUploadRequestBody || {});
  },
};
