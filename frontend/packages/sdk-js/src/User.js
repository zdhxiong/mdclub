import { get } from 'util/request';

export default {
  getList(data, success, error) {
    get('', data, function (response) {
      if (response.code) {

      } else {
        success(response.data);
      }
    });
  }
};
