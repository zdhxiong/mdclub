import mdui from 'mdui';
import $ from 'mdui.jq';
import extend from 'mdui.jq/es/functions/extend';
import each from 'mdui.jq/es/functions/each';
import { isNumber, isUndefined } from 'mdui.jq/es/utils';
import { unescape } from 'html-escaper';
import {
  get as getTopic,
  create as createTopic,
  update as updateTopic,
} from 'mdclub-sdk-js/es/TopicApi';
import { COMMON_FIELD_VERIFY_FAILED } from 'mdclub-sdk-js/es/errors';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import { loadEnd, loadStart } from '~/utils/loading';
import apiCatch from '~/utils/errorHandler';

let dialog;
let $name;
let $description;
let $cover;

const as = {
  onCreate: ({ element }) => {
    const $wrapper = $(element);
    $name = $wrapper.find('input[name="name"]');
    $description = $wrapper.find('textarea[name="description"]');
    $cover = $wrapper.find('input[name="cover"]');

    dialog = new mdui.Dialog($wrapper, {
      history: false,
    });

    // 对话框打开后，自动聚焦到第一个输入框
    dialog.$element.on('open.mdui.dialog', () => $name[0].focus());
  },

  /**
   * 打开对话框
   * @param topic
   * 若topic为null，表示添加数据；
   * 若topic为整型，则需要先根据该参数获取话题信息；
   * 若topic为对象，则不需要再获取话题信息
   */
  open: (topic = null) => (state, actions) => {
    actions.setState({
      name_msg: '',
      description_msg: '',
      cover_msg: '',
    });

    // 添加数据
    if (topic === null) {
      actions.setState({
        topic_id: 0,
        name: '',
        description: '',
        cover: '',
        loading: false,
      });
      $cover.val('');

      dialog.open();

      return;
    }

    const isComplete = !isNumber(topic);

    if (isComplete) {
      actions.setState({
        topic_id: topic.topic_id,
        name: unescape(topic.name),
        description: unescape(topic.description),
        cover: topic.cover.middle,
        loading: false,
      });
    } else {
      actions.setState({
        topic_id: topic,
        name: '',
        description: '',
        cover: '',
        loading: true,
      });
    }

    dialog.open();

    if (isComplete) {
      return;
    }

    // 只传入了 ID，获取话题详情
    getTopic({ topic_id: topic })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then(({ data }) => {
        actions.setState({
          topic_id: data.topic_id,
          name: unescape(data.name),
          description: unescape(data.description),
          cover: data.cover.small,
        });

        setTimeout(() => dialog.handleUpdate());
      })
      .catch((response) => {
        dialog.close();
        apiCatch(response);
      });
  },

  /**
   * 关闭对话框
   */
  close: () => {
    dialog.close();
  },

  /**
   * 输入框失去焦点时，更新状态
   */
  onInput: (e) => {
    const { name, value } = e.target;

    return {
      [`${name}_msg`]: '',
      [name]: value,
    };
  },

  /**
   * 点击图片
   */
  clickCover: (e) => {
    let $placeholder = $(e.target);

    if ($placeholder.is('i')) {
      $placeholder = $placeholder.parent();
    }

    $placeholder.prev().trigger('click');
  },

  /**
   * 选择图片后
   */
  coverSelected: (e) => (state, actions) => {
    actions.setState({ cover_msg: '' });

    const fr = new FileReader();

    fr.onload = (frEvent) => {
      actions.setState({ cover: frEvent.target.result });
    };
    fr.readAsDataURL(e.target.files[0]);
  },

  /**
   * 提交添加或修改话题
   */
  submit: () => (state, actions) => {
    loadStart();

    const handleValidationError = (errors) => {
      const errorsMsg = {};

      each(errors, (key, value) => {
        errorsMsg[`${key}_msg`] = value;
      });

      actions.setState(errorsMsg);

      // 光标聚焦到错误字段
      if (!isUndefined(errorsMsg.name_msg)) {
        $name[0].focus();
      } else if (!isUndefined(errorsMsg.description_msg)) {
        $description[0].focus();
      }
    };

    const submitFail = (response) => {
      loadEnd();

      if (response.code === COMMON_FIELD_VERIFY_FAILED) {
        handleValidationError(response.errors);
      } else {
        apiCatch(response);
      }
    };

    // 修改话题
    if (state.topic_id) {
      const { name, description } = state;
      const params = {
        name,
        description,
        topic_id: state.topic_id,
      };

      if ($cover[0].files.length) {
        [params.cover] = $cover[0].files;
      }

      updateTopic(params)
        .then(({ data }) => {
          // 修改成功，直接修改列表中的数据，不刷新
          loadEnd();
          actions.close();
          mdui.snackbar('修改成功');

          emit('datatable_update_row', data);
        })
        .catch(submitFail);
    }

    // 新增话题
    else {
      const { name, description } = state;
      const params = { name, description };
      [params.cover] = $cover[0].files;

      createTopic(params)
        .then(() => {
          // 添加成功，刷新列表
          loadEnd();
          actions.close();
          mdui.snackbar('添加成功');

          window.app.topics.loadData();
        })
        .catch(submitFail);
    }
  },
};

export default extend(as, commonActions);
