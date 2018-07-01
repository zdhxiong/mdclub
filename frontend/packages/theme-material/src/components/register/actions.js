import mdui from 'mdui';
import $ from 'mdui.JQ';
import sha1 from 'sha-1';
import Cookies from 'js-cookie';
import UserRegisterService from '../../service/UserRegister';
import CaptchaService from '../../service/Captcha';

let Dialog;
let $email;
let $emailCode;
let $password;
let $username;
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
  init: props => (state, actions) => {
    const element = props.element;

    $(element).mutation();

    Dialog = new mdui.Dialog(element, {
      history: false,
      modal: true,
    });

    $email = Dialog.$dialog.find('[name="email"]');
    $emailCode = Dialog.$dialog.find('[name="email_code"]');
    $username = Dialog.$dialog.find('[name="username"]');
    $password = Dialog.$dialog.find('[name="password"]');

    $(element).on('open.mdui.dialog', () => {
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
    Dialog.open();
  },

  /**
   * 关闭注册界面对话框
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
    CaptchaService.post((response) => {
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
   * 60秒倒计时
   */
  countdown: () => (state, actions) => {
    if (state.re_send_countdown > 0) {
      actions.setState({
        re_send_countdown: state.re_send_countdown - 1,
      });
      setTimeout(actions.countdown, 1000);
    } else {
      actions.setState({
        show_re_send_countdown: false,
      });
    }
  },

  /**
   * 开始重新发送倒计时
   */
  sendCountdown: () => (state, actions) => {
    actions.setState({
      show_re_send_countdown: true,
      re_send_countdown: 60,
    });

    actions.countdown();
  },

  /**
   * 开始发送邮件
   */
  sendStart: () => ({
    sending: true,
  }),

  /**
   * 结束发送邮件
   */
  sendEnd: () => ({
    sending: false,
  }),

  /**
   * 发送邮件
   */
  sendEmail: () => (state, actions) => {
    if (!$email[0].validity.valid) {
      $email[0].focus();
      return;
    }

    if (state.captcha_token && !$captchaCode[0].validity.valid) {
      actions.setState({
        captcha_code_msg: '请输入图形验证码',
      });
      $captchaCode[0].focus();
      return;
    }

    actions.sendStart();

    const data = {
      email: state.email,
    };

    if (state.captcha_token) {
      data.captcha_token = state.captcha_token;
      data.captcha_code = state.captcha_code;
    }

    UserRegisterService.sendEmail(data, (response) => {
      actions.sendEnd();

      actions.setState({
        captcha_token: response.captcha_token || '',
        captcha_image: response.captcha_image || '',
        captcha_code: '',
        email_code: '',
        email_code_msg: '',
      });

      if (response.code === 0) {
        mdui.snackbar('邮箱验证码已发送');
        actions.sendCountdown();
        return;
      }

      if (response.code === 100002) {
        const email_msg = response.errors.email || '';
        const captcha_code_msg = response.errors.captcha_code || '';

        actions.setState({
          email_msg,
          captcha_code_msg,
        });

        if (email_msg) {
          $email[0].focus();
        } else if (captcha_code_msg) {
          setTimeout(() => {
            $captchaCode[0].focus();
          }, 0);
        }
      }
    });
  },

  /**
   * 开始验证邮箱验证码
   */
  verifyStart: () => ({
    verifying: true,
  }),

  /**
   * 结束验证邮箱验证码
   */
  verifyEnd: () => ({
    verifying: false,
  }),

  /**
   * 回到上一步
   */
  prevStep: () => (state, actions) => {
    actions.setState({
      email_valid: false,
    });
  },

  /**
   * 点击下一步
   */
  nextStep: e => (state, actions) => {
    e.preventDefault();
    actions.verifyStart();

    const data = {
      email: state.email,
      email_code: state.email_code,
    };

    UserRegisterService.create(data, (response) => {
      actions.verifyEnd();

      // 仅邮箱验证成功一定返回 100002
      if (response.code === 100002) {
        // 不含 email 和 email_code 即表示邮箱验证通过
        if (typeof response.errors.email === 'undefined' && typeof response.errors.email_code === 'undefined') {
          actions.setState({
            email_valid: true,
          });

          setTimeout(() => {
            $username[0].focus();
          }, 0);

          return;
        }

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
      } else if (response.code === 100004) {
        // 验证码已失效，需要重新验证邮箱
        mdui.snackbar('验证码已失效，请重新发送邮箱验证码', {
          timeout: 10000,
        });

        actions.setState({
          email_code: '',
          email_code_msg: '',
        });

        return;
      }

      mdui.snackbar(response.message);
    });
  },

  /**
   * 开始提交注册
   */
  registerStart: () => ({
    submitting: true,
  }),

  /**
   * 结束提交注册
   */
  registerEnd: () => ({
    submitting: false,
  }),

  /**
   * 提交注册
   */
  register: e => (state, actions) => {
    e.preventDefault();
    actions.registerStart();

    const data = {
      email: state.email,
      email_code: state.email_code,
      username: state.username,
      password: sha1(state.password),
      device: navigator.userAgent,
    };

    UserRegisterService.create(data, (response) => {
      // 成功
      if (response.code === 0) {
        Cookies.set('token', response.data.token, { expires: 15 });
        window.location.reload();

        return;
      }

      actions.registerEnd();

      // 字段验证失败
      if (response.code === 100002) {
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
      } else if (response.code === 100004) {
        // 验证码已失效，需要重新验证邮箱
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

      mdui.snackbar(response.message);
    });
  },
};
