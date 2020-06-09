import $ from 'mdui.jq';
import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { COMMON_FIELD_VERIFY_FAILED } from 'mdclub-sdk-js/es/errors';
import { generate as generateCaptcha } from 'mdclub-sdk-js/es/CaptchaApi';
import { login } from 'mdclub-sdk-js/es/TokenApi';
import commonActions from '~/utils/actionsAbstract';
import { setCookie } from '~/utils/cookie';
import { emit } from '~/utils/pubsub';
import apiCatch from '~/utils/errorHandler';

let dialog;
let $name;
let $password;
let $captchaCode;

const as = {
  /**
   * 响应输入框输入事件
   */
  onInput: (e) => {
    const input = e.target;

    return {
      [input.name]: input.value,
      [`${input.name}_msg`]: '',
    };
  },

  /**
   * 初始化
   */
  onCreate: (props) => {
    const $element = $(props.element).mutation();

    dialog = new mdui.Dialog($element, {
      modal: true,
      history: false,
    });

    $name = $element.find('[name="name"]');
    $password = $element.find('[name="password"]');
    $captchaCode = $element.find('[name="captcha_code"]');

    $element.on('open.mdui.dialog', () => {
      $name[0].focus();
    });
  },

  /**
   * 打开登陆框
   */
  open: () => {
    dialog.open();
  },

  /**
   * 关闭登录界面对话框
   */
  close: () => {
    if (dialog) {
      dialog.close();
    }
  },

  /**
   * 打开注册对话框
   */
  toRegister: () => {
    dialog.close();
    emit('register_open');
  },

  /**
   * 打开重置密码对话框
   */
  toReset: () => {
    dialog.close();
    emit('reset_open');
  },

  /**
   * 刷新验证码
   */
  refreshCaptcha: () => (_, actions) => {
    generateCaptcha()
      .then(({ data: { captcha_token, captcha_image } }) => {
        actions.setState({
          captcha_token,
          captcha_image,
          captcha_code: '',
        });

        setTimeout(() => {
          $captchaCode[0].focus();
        });
      })
      .catch(apiCatch);
  },

  /**
   * 执行登录
   */
  login: (e) => (state, actions) => {
    e.preventDefault();
    actions.setState({ submitting: true });

    const loginData = {
      name: state.name,
      password: state.password,
      device: navigator.userAgent,
    };

    if (state.captcha_token) {
      loginData.captcha_token = state.captcha_token;
      loginData.captcha_code = state.captcha_code;
    }

    login(loginData)
      .then(({ data }) => {
        setCookie('token', data.token);
        window.location.reload();
      })
      .catch((response) => {
        actions.setState({
          submitting: false,
          captcha_token: response.captcha_token || '',
          captcha_image: response.captcha_image || '',
          captcha_code: '',
        });

        // 字段验证失败
        if (response.code === COMMON_FIELD_VERIFY_FAILED) {
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
            $captchaCode[0].focus();
          }

          return;
        }

        // 其他错误
        apiCatch(response);
      });
  },
};

export default extend(as, commonActions);
