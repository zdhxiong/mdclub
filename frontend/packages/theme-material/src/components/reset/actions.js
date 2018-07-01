import mdui, { JQ as $ } from 'mdui';
import { User, Captcha } from 'mdclub-sdk-js';

let Dialog;
let $email;
let $emailCode;
let $captchaCode;
let $password;
let $passwordRepeat;
let global_actions;

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
  init: props => (state) => {
    const element = props.element;
    global_actions = props.global_actions;

    $(element).mutation();

    Dialog = new mdui.Dialog(element, {
      history: false,
      modal: true,
    });

    $email = Dialog.$dialog.find('[name="email"]');
    $emailCode = Dialog.$dialog.find('[name="email_code"]');
    $password = Dialog.$dialog.find('[name="password"]');
    $passwordRepeat = Dialog.$dialog.find('[name="password_repeat"]');

    $(element).on('open.mdui.dialog', () => {
      if (state.email_valid) {
        $password[0].focus();
      } else {
        $email[0].focus();
      }
    });
  },

  /**
   * 打开重置密码对话框
   */
  open: () => {
    Dialog.open();
  },

  /**
   * 关闭重置密码对话框
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

    User.sendResetEmail(data, (response) => {
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

        return;
      }

      mdui.snackbar(response.message);
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

    User.updatePasswordByEmail(data, (response) => {
      actions.verifyEnd();

      // 仅验证邮箱成功一定返回 100002
      if (response.code === 100002) {
        // 不含 email 和 email_code 即表示邮箱验证通过
        if (typeof response.errors.email === 'undefined' && typeof response.errors.email_code === 'undefined') {
          actions.setState({
            email_valid: true,
          });

          setTimeout(() => {
            $password[0].focus();
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
   * 开始提交新密码
   */
  submitStart: () => ({
    submitting: true,
  }),

  /**
   * 结束提交新密码
   */
  submitEnd: () => ({
    submitting: false,
  }),

  /**
   * 提交新密码
   */
  submit: e => (state, actions) => {
    e.preventDefault();

    // 验证两次密码一致
    if (state.password !== state.password_repeat) {
      actions.setState({
        password_repeat_msg: '两次输入的密码不一致',
      });

      $passwordRepeat[0].focus();

      return;
    }

    const data = {
      email: state.email,
      email_code: state.email_code,
      password: state.password,
    };

    actions.submitStart();

    User.updatePasswordByEmail(data, (response) => {
      actions.submitEnd();

      // 成功
      if (response.code === 0) {
        mdui.snackbar('重置密码成功');

        Dialog.close();
        global_actions.components.login.open();

        return;
      }

      if (response.code === 100002) {
        const email_msg = response.errors.email || '';
        const email_code_msg = response.errors.email_code || '';
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
          password_msg,
        });

        if (password_msg) {
          $password[0].focus();
        }

        return;
      } else if (response.code === 100004) {
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
