import { get, post, patch, del } from './util/requestAlias';
import { buildURL, buildRequestBody } from './util/requestHandler';
import { ImageResponse, EmptyResponse, ImagesResponse } from './models';

interface DeleteParams {
  /**
   * 图片key
   */
  key: string;
}

interface DeleteMultipleParams {
  /**
   * 用“,”分隔的图片key，最多可提供 40 个 key（IE 的 query 参数最长为 2k，为了不超过这个数值，限制最多可以提交 40 个 key）
   */
  key?: Array<string>;
}

interface GetParams {
  /**
   * 图片key
   */
  key: string;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `question`, `article`, `answer`
   */
  include?: Array<'user' | 'question' | 'article' | 'answer'>;
}

interface GetListParams {
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `question`, `article`, `answer`
   */
  include?: Array<'user' | 'question' | 'article' | 'answer'>;
  /**
   * 图片key
   */
  key?: string;
  /**
   * 图片关联对象的类型
   */
  item_type?: 'question' | 'answer' | 'article';
  /**
   * 图片关联对象的ID
   */
  item_id?: number;
  /**
   * 用户ID
   */
  user_id?: number;
}

interface UpdateParams {
  /**
   * 图片key
   */
  key: string;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `question`, `article`, `answer`
   */
  include?: Array<'user' | 'question' | 'article' | 'answer'>;
  /**
   * 图片文件名
   */
  filename: string;
}

interface UploadParams {
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `question`, `article`, `answer`
   */
  include?: Array<'user' | 'question' | 'article' | 'answer'>;
  /**
   * 图片
   */
  image: any;
}

const className = 'ImageApi';

/**
 * ImageApi
 */
export default {
  /**
   * 🔐删除指定图片
   * 仅管理员可调用该接口
   * @param params.key 图片key
   */
  del: (params: DeleteParams): Promise<EmptyResponse> =>
    del(buildURL(`${className}.del`, '/images/{key}', params)),

  /**
   * 🔐批量删除图片
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有记录被删除，该接口都会返回成功。
   * @param params.key 用“,”分隔的图片key，最多可提供 40 个 key（IE 的 query 参数最长为 2k，为了不超过这个数值，限制最多可以提交 40 个 key）
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> =>
    del(buildURL(`${className}.deleteMultiple`, '/images', params, ['key'])),

  /**
   * 获取指定图片信息
   * 获取指定图片信息
   * @param params.key 图片key
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;question&#x60;, &#x60;article&#x60;, &#x60;answer&#x60;
   */
  get: (params: GetParams): Promise<ImageResponse> =>
    get(buildURL(`${className}.get`, '/images/{key}', params, ['include'])),

  /**
   * 🔐获取图片列表
   * 仅管理员可调用该接口
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;question&#x60;, &#x60;article&#x60;, &#x60;answer&#x60;
   * @param params.key 图片key
   * @param params.item_type 图片关联对象的类型
   * @param params.item_id 图片关联对象的ID
   * @param params.user_id 用户ID
   */
  getList: (params: GetListParams): Promise<ImagesResponse> =>
    get(
      buildURL(`${className}.getList`, '/images', params, [
        'page',
        'per_page',
        'include',
        'key',
        'item_type',
        'item_id',
        'user_id',
      ]),
    ),

  /**
   * 🔐更新指定图片信息
   * 仅管理员可调用该接口
   * @param params.key 图片key
   * @param params.ImageUpdateRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;question&#x60;, &#x60;article&#x60;, &#x60;answer&#x60;
   */
  update: (params: UpdateParams): Promise<ImageResponse> =>
    patch(
      buildURL(`${className}.update`, '/images/{key}', params, ['include']),
      buildRequestBody(params, ['filename']),
    ),

  /**
   * 上传图片
   * 上传图片
   * @param params.ImageUploadRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;question&#x60;, &#x60;article&#x60;, &#x60;answer&#x60;
   */
  upload: (params: UploadParams): Promise<ImageResponse> =>
    post(
      buildURL(`${className}.upload`, '/images', params, ['include']),
      buildRequestBody(params, ['image']),
    ),
};
