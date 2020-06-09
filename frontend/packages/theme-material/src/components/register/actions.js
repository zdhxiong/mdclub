import $ from 'mdui.jq';
import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import {
  COMMON_FIELD_VERIFY_FAILED,
  COMMON_EMAIL_VERIFY_EXPIRED,
} from 'mdclub-sdk-js/es/errors';
import { generate as generateCaptcha } from 'mdclub-sdk-js/es/CaptchaApi';
import { register, sendRegisterEmail } from 'mdclub-sdk-js/es/UserApi';
import { login } from 'mdclub-sdk-js/es/TokenApi';
import { setCookie } from '~/utils/cookie';
import { emit } from '~/utils/pubsub';
import apiCatch from '~/utils/errorHandler';
import commonActions from '~/utils/actionsAbstract';

let dialog;
let $email;
let $emailCode;
let $username;
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
  onCreate: (props) => (state) => {
    const $element = $(props.element).mutation();

    dialog = new mdui.Dialog($element, {
      modal: true,
      history: false,
    });

    $email = $element.find('[name="email"]');
    $emailCode = $element.find('[name="email_code"]');
    $username = $element.find('[name="username"]');
    $password = $element.find('[name="password"]');
    $captchaCode = $element.find('[name="captcha_code"]');

    $element.on('open.mdui.dialog', () => {
      if (state.email_valid) {
        $username[0].focus();
      } else {
        $email[0].focus();
      }
    });
  },

  /**
   * 打开注册界面对话框
   */
  open: () => {
    dialog.open();
  },

  /**
   * 关闭注册界面对话框
   */
  close: () => {
    if (dialog) {
      dialog.close();
    }
  },

  /**
   * 打开登录对话框
   */
  toLogin: () => {
    dialog.close();
    emit('login_open');
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
        }, 0);
      })
      .catch(apiCatch);
  },

  /**
   * 60秒倒计时
   */
  countdown: () => (state, actions) => {
    if (state.resend_countdown > 0) {
      actions.setState({
        resend_countdown: state.resend_countdown - 1,
      });

      setTimeout(actions.countdown, 1000);
    } else {
      actions.setState({
        show_resend_countdown: false,
      });
    }
  },

  /**
   * 开始重新发送倒计时
   */
  sendCountdown: () => (_, actions) => {
    actions.setState({
      show_resend_countdown: true,
      resend_countdown: 60,
    });

    actions.countdown();
  },

  /**
   * 发送邮件
   */
  sendEmail: () => (state, actions) => {
    if (!$email[0].validity.valid) {
      $email[0].focus();
      return;
    }

    if (state.captcha_token && !$captchaCode[0].validity.valid) {
      actions.setState({ captcha_code_msg: '请输入图形验证码' });
      $captchaCode[0].focus();
      return;
    }

    actions.setState({ sending: true });

    const formData = {
      email: state.email,
    };

    if (state.captcha_token) {
      formData.captcha_token = state.captcha_token;
      formData.captcha_code = state.captcha_code;
    }

    sendRegisterEmail(formData)
      .finally(() => {
        actions.setState({
          sending: false,
          captcha_token: '',
          captcha_image: '',
          captcha_code: '',
          email_code: '',
          email_code_msg: '',
        });
      })
      .then(() => {
        mdui.snackbar('邮箱验证码已发送');
        actions.sendCountdown();
      })
      .catch((response) => {
        actions.setState({
          captcha_token: response.captcha_token || '',
          captcha_image: response.captcha_image || '',
        });

        // 字段验证失败
        if (response.code === COMMON_FIELD_VERIFY_FAILED) {
          const email_msg = response.errors.email || '';
          const captcha_code_msg = response.errors.captcha_code || '';

          actions.setState({
            email_msg,
            captcha_code_msg,
          });

          if (email_msg) {
            $email[0].focus();
          } else if (captcha_code_msg) {
            $captchaCode[0].focus();
          }

          return;
        }

        // 其他错误
        apiCatch(response);
      });
  },

  /**
   * 回到上一步
   */
  prevStep: () => (_, actions) => {
    actions.setState({ email_valid: false });
  },

  /**
   * 点击下一步
   */
  nextStep: (e) => (state, actions) => {
    e.preventDefault();
    actions.setState({ verifying: true });

    const formData = {
      email: state.email,
      email_code: state.email_code,
    };

    // 没有传入 username 和 password 参数，调用接口一定会返回错误
    // 若返回字段验证失败，且不含 email 和 email_code，则表示邮箱验证通过
    register(formData)
      .finally(() => {
        actions.setState({ verifying: false });
      })
      .catch((response) => {
        if (response.code === COMMON_FIELD_VERIFY_FAILED) {
          // 不含 email 和 email_code 即表示邮箱验证通过
          if (!response.errors.email && !response.errors.email_code) {
            actions.setState({ email_valid: true });

            setTimeout(() => {
              $username[0].focus();
            }, 0);

            return;
          }

          // 邮箱验证未通过
          const email_msg = response.errors.email || '';
          const email_code_msg = response.errors.email_code || '';

          actions.setState({
            email_msg,
            email_code_msg,
          });

          if (email_msg) {
            $email[0].focus();
          } else if (email_code_msg) {
            $emailCode[0].focus();
          }

          return;
        }

        // 邮箱验证码已失效，需要重新发送邮件
        if (response.code === COMMON_EMAIL_VERIFY_EXPIRED) {
          mdui.snackbar('验证码已失效，请重新发送邮箱验证码', {
            timeout: 10000,
          });

          actions.setState({
            email_code: '',
            email_code_msg: '',
          });

          return;
        }

        // 其他错误
        apiCatch(response);
      });
  },

  /**
   * 提交注册
   */
  register: (e) => (state, actions) => {
    e.preventDefault();
    actions.setState({ submitting: true });

    const registerData = {
      email: state.email,
      email_code: state.email_code,
      username: state.username,
      password: state.password,
    };

    const loginData = {
      name: state.username,
      password: state.password,
      device: window.navigator.userAgent,
    };

    register(registerData)
      .then((response) => {
        emit('user_update', response.data);
        return login(loginData);
      })
      .finally(() => {
        actions.setState({ submitting: false });
      })
      .then(({ data }) => {
        setCookie('token', data.token);
        window.location.reload();
      })
      .catch((response) => {
        // 字段验证失败
        if (response.code === COMMON_FIELD_VERIFY_FAILED) {
          const email_msg = response.errors.email || '';
          const email_code_msg = response.errors.email_code || '';
          const username_msg = response.errors.username || '';
          const password_msg = response.errors.password || '';

          // 如果是邮箱或验证码错误，回到上一步
          if (email_msg || email_code_msg) {
            actions.setState({
              email_valid: false,
              email_msg,
              email_code_msg,
            });

            if (email_msg) {
              $email[0].focus();
            } else if (email_code_msg) {
              $emailCode[0].focus();
            }

            return;
          }

          // 用户名或密码错误
          actions.setState({
            username_msg,
            password_msg,
          });

          if (username_msg) {
            $username[0].focus();
          } else if (password_msg) {
            $password[0].focus();
          }

          return;
        }

        // 验证码失效，重新验证邮箱，回到上一步
        if (response.code === COMMON_EMAIL_VERIFY_EXPIRED) {
          mdui.snackbar('验证码已失效，请重新发送邮箱验证码', {
            timeout: 10000,
          });

          actions.setState({
            email_valid: false,
            email_code: '',
            email_code_msg: '',
          });

          return;
        }

        apiCatch(response);
      });
  },
};

export default extend(as, commonActions);
