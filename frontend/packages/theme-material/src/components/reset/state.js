export default {
  // 邮箱
  email: '',
  email_msg: '',

  // 验证码
  email_code: '',
  email_code_msg: '',

  // 新密码
  password: '',
  password_msg: '',

  // 重复新密码
  password_repeat: '',
  password_repeat_msg: '',

  // 图形验证码
  captcha_code: '',
  captcha_code_msg: '',

  // 图形验证码token和图片
  captcha_token: '',
  captcha_image: '',

  // 邮箱验证是否通过
  email_valid: false,

  // 是否正在发送验证码
  sending: false,

  // 是否显示重新发送倒计时
  show_resend_countdown: false,

  // 重新发送验证码倒计时
  resend_countdown: 60,

  // 是否正在验证中
  verifying: false,

  // 是否正在提交新密码
  submitting: false,
};
