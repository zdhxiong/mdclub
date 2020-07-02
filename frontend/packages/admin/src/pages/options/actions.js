import mdui from 'mdui';
import $ from 'mdui.jq';
import extend from 'mdui.jq/es/functions/extend';
import each from 'mdui.jq/es/functions/each';
import {
  get as getOptions,
  update as updateOptions,
} from 'mdclub-sdk-js/es/OptionApi';
import { send as sendEmail } from 'mdclub-sdk-js/es/EmailApi';
import { COMMON_FIELD_VERIFY_FAILED } from 'mdclub-sdk-js/es/errors';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import { apiCatch } from '~/utils/errorHandlers';

// options 和 option 共用
const as = {
  onCreate: ({ element }) => (state, actions) => {
    emit('route_update');
    emit('searchbar_state_update', { isNeedRender: false });
    actions.setTitle('系统设置');

    // 滚动时，应用栏添加阴影
    $(element).on('scroll', (e) =>
      emit('appbar_state_update', { shadow: !!e.target.scrollTop }),
    );

    // 加载初始数据
    if (!state.data) {
      actions.setState({ loading: true });
      getOptions()
        .finally(() => {
          actions.setState({ loading: false });
        })
        .then(({ data }) => {
          actions.setState({ data });
        })
        .catch(apiCatch);
    }
  },

  onDestroy: ({ element }) => (_, actions) => {
    $(element).off('scroll');
    emit('appbar_state_update', { shadow: false });
    actions.setState({ loading: false });
  },

  /**
   * 提交保存
   * @param e
   */
  onSubmit: (e) => (state, actions) => {
    e.preventDefault();

    const $form = $(e.target);

    actions.setState({ submitting: true });

    const dataArr = $form.serializeArray();

    // 未选中的 checkbox 值应为 false
    $form.find('input[type=checkbox]:not(:checked)').map(function () {
      dataArr.push({
        name: this.name,
        value: this.checked ? this.value : 'false',
      });
    });

    const dataObj = {};
    const integerFields = [
      'answer_can_delete_before',
      'answer_can_edit_before',
      'article_can_delete_before',
      'article_can_edit_before',
      'cache_memcached_port',
      'cache_redis_port',
      'comment_can_delete_before',
      'comment_can_edit_before',
      'question_can_delete_before',
      'question_can_edit_before',
      'smtp_port',
      'storage_ftp_port',
      'storage_sftp_port',
    ];
    const booleanFields = [
      'answer_can_delete',
      'answer_can_delete_only_no_comment',
      'answer_can_edit',
      'answer_can_edit_only_no_comment',
      'article_can_delete',
      'article_can_delete_only_no_comment',
      'article_can_edit',
      'article_can_edit_only_no_comment',
      'comment_can_delete',
      'comment_can_edit',
      'question_can_delete',
      'question_can_delete_only_no_answer',
      'question_can_delete_only_no_comment',
      'question_can_edit',
      'question_can_edit_only_no_answer',
      'question_can_edit_only_no_comment',
      'storage_ftp_passive',
      'storage_ftp_ssl',
    ];

    each(dataArr, (_, item) => {
      if (integerFields.indexOf(item.name) > -1) {
        dataObj[item.name] = parseInt(item.value, 10);
      } else if (booleanFields.indexOf(item.name) > -1) {
        dataObj[item.name] = item.value === 'true' || item.value === 'on';
      } else {
        dataObj[item.name] = item.value;
      }
    });

    updateOptions(dataObj)
      .finally(() => {
        actions.setState({ submitting: false });
      })
      .then(({ data }) => {
        actions.setState({ data });
        mdui.snackbar('保存成功');
      })
      .catch(apiCatch);
  },

  /**
   * 发送测试邮件
   */
  sendTestEmail: () => (state) => {
    const onConfirm = (email) => {
      const emailData = {
        email: [email],
        subject: `${state.data.site_name}的测试邮件`,
        content: '你收到了这封邮件，表示你的邮件服务器已设置成功',
      };

      const waitAlert = mdui.alert('正在发送邮件，请稍候…');

      setTimeout(() => {
        sendEmail(emailData)
          .finally(() => waitAlert.close())
          .then(() => {
            mdui.alert(`请登录邮箱：${email}，查看是否收到了测试邮件`);
          })
          .catch(({ code, message, extra_message, errors }) => {
            if (code === COMMON_FIELD_VERIFY_FAILED) {
              mdui.alert(Object.values(errors).join('<br/>'), message);
            } else {
              mdui.alert(extra_message, message);
            }
          });
      }, 300);
    };

    const onCancel = () => {};

    const options = {
      confirmText: '发送',
      cancelText: '取消',
    };

    mdui.prompt(
      '请输入用于接收测试邮件的邮箱地址',
      onConfirm,
      onCancel,
      options,
    );
  },
};

export default extend(as, commonActions);
