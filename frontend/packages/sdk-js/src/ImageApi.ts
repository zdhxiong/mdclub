import {
  getRequest,
  postRequest,
  patchRequest,
  deleteRequest,
} from './util/requestAlias';
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
   * 多个用 `,` 分隔的评论ID，最多可提供 100 个 ID
   */
  keys: string;
}

interface GetParams {
  /**
   * 图片key
   */
  key: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `article`, `answer`
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
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `article`, `answer`
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
   * 图片文件名
   */
  filename: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `article`, `answer`
   */
  include?: Array<'user' | 'question' | 'article' | 'answer'>;
}

interface UploadParams {
  /**
   * 图片
   */
  image: File;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `article`, `answer`
   */
  include?: Array<'user' | 'question' | 'article' | 'answer'>;
}

/**
 * 🔐删除指定图片
 *
 * 删除指定图片。
 */
export const del = (params: DeleteParams): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/images/{key}', params));

/**
 * 🔐批量删除图片
 *
 * 批量删除图片。  只要没有错误异常，无论是否有记录被删除，该接口都会返回成功。
 */
export const deleteMultiple = (
  params: DeleteMultipleParams,
): Promise<EmptyResponse> => deleteRequest(buildURL('/images/{keys}', params));

/**
 * 获取指定图片信息
 *
 * 获取指定图片信息。
 */
export const get = (params: GetParams): Promise<ImageResponse> =>
  getRequest(buildURL('/images/{key}', params, ['include']));

/**
 * 🔐获取图片列表
 *
 * 获取图片列表。
 */
export const getList = (params: GetListParams = {}): Promise<ImagesResponse> =>
  getRequest(
    buildURL('/images', params, [
      'page',
      'per_page',
      'include',
      'key',
      'item_type',
      'item_id',
      'user_id',
    ]),
  );

/**
 * 🔐更新指定图片信息
 *
 * 更新指定图片信息。
 */
export const update = (params: UpdateParams): Promise<ImageResponse> =>
  patchRequest(
    buildURL('/images/{key}', params, ['include']),
    buildRequestBody(params, ['filename']),
  );

/**
 * 🔑上传图片
 *
 * 上传图片。
 */
export const upload = (params: UploadParams): Promise<ImageResponse> => {
  const formData = new FormData();
  formData.append('image', params.image);

  return postRequest(buildURL('/images', params, ['include']), formData);
};
