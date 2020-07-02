import mdui from 'mdui';

export function apiCatch(response) {
  let message;

  console.log(response);
  if (response.code === 999999) {
    message = '网络错误';
  } else if (!response.message) {
    message = '未知错误';
  } else {
    message = response.message;
  }

  mdui.snackbar(message);
}
