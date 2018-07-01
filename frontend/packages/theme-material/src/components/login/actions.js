import mdui, { JQ as $ } from 'mdui';
import Cookies from 'js-cookie';
import { Token, Captcha } from 'mdclub-sdk-js';

let Dialog;
let $name;
let $password;
let $captchaCode;

export default {
  setState: value => (value),
  getState: () => state => state,

  /**
   * 响应输入框输入事件
   */
  input: e => ({
    [e.target.name]: e.target.value,
    [`${e.target.name}_msg`]: '',
  }),

  /**
   * 初始化
   */
  init: props => () => {
    const element = props.element;

    $(element).mutation();

    Dialog = new mdui.Dialog(element, {
      history: false,
      modal: true,
    });

    $name = Dialog.$dialog.find('[name="name"]');
    $password = Dialog.$dialog.find('[name="password"]');

    $(element).on('open.mdui.dialog', () => {
      $name[0].focus();
    });
  },

  /**
   * 打开登陆框
   */
  open: () => {
    Dialog.open();
  },

  /**
   * 关闭登录界面对话框
   */
  close: () => {
    Dialog.close();
  },

  /**
   * 显示验证码输入行
   */
  showCaptchaLine: () => {
    Dialog.handleUpdate();
    $captchaCode = Dialog.$dialog.find('[name="captcha_code"]');
  },

  /**
   * 隐藏验证码输入行
   */
  hideCaptchaLine: () => {
    setTimeout(() => {
      Dialog.handleUpdate();
    }, 0);
  },

  /**
   * 刷新验证码
   */
  captchaRefresh: () => (state, actions) => {
    Captcha.create((response) => {
      if (response.code === 0) {
        actions.setState({
          captcha_token: response.data.captcha_token,
          captcha_image: response.data.captcha_image,
          captcha_code: '',
        });

        setTimeout(() => {
          $captchaCode[0].focus();
        }, 0);

        return;
      }

      mdui.snackbar(response.message);
    });
  },

  /**
   * 开始提交
   */
  submitStart: () => ({
    submitting: true,
  }),

  /**
   * 结束提交
   */
  submitEnd: () => ({
    submitting: false,
  }),

  /**
   * 执行登录
   */
  login: e => (state, actions) => {
    e.preventDefault();
    actions.submitStart();

    const data = {
      name: state.name,
      password: state.password,
      device: navigator.userAgent,
    };

    if (state.captcha_token) {
      data.captcha_token = state.captcha_token;
      data.captcha_code = state.captcha_code;
    }

    Token.create(data, (response) => {
      // 成功
      if (response.code === 0) {
        Cookies.set('token', response.data.token, { expires: 15 });
        window.location.reload();

        return;
      }

      actions.submitEnd();

      actions.setState({
        captcha_token: response.captcha_token || '',
        captcha_image: response.captcha_image || '',
        captcha_code: '',
      });

      // 字段验证失败
      if (response.code === 100002) {
        const name_msg = response.errors.name || '';
        const password_msg = response.errors.password || '';
        const captcha_code_msg = response.errors.captcha_code || '';

        actions.setState({
          name_msg,
          password_msg,
          captcha_code_msg,
        });

        if (name_msg) {
          $name[0].focus();
        } else if (password_msg) {
          $password[0].focus();
        } else if (captcha_code_msg) {
          // 验证码元素可能是新生成的，dom元素还未取到
          setTimeout(() => {
            $captchaCode[0].focus();
          }, 0);
        }

        return;
      }

      mdui.snackbar(response.message);
    });
  },
};
