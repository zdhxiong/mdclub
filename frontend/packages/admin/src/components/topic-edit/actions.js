import mdui, { JQ as $ } from 'mdui';
import { Topic } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions/component';

let global_actions;
let dialog;
let $name;
let $description;
let $cover;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: (props) => {
    global_actions = props.global_actions;

    const $wrapper = $('.mc-topic-edit');
    $name = $wrapper.find('input[name="name"]');
    $description = $wrapper.find('textarea[name="description"]');
    $cover = $wrapper.find('input[name="cover"]');

    dialog = new mdui.Dialog($wrapper);

    // 对话框打开后，自动聚焦到第一个输入框
    dialog.$dialog.on('open.mdui.dialog', () => $name[0].focus());
  },

  /**
   * 打开对话框
   * @param topic 若topic为null，表示添加数据；若topic为整型，则需要先根据该参数获取话题信息；若topic为对象，则不需要再获取话题信息
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

      dialog.open();

      return;
    }

    const isComplete = typeof topic === 'object';

    actions.setState({
      topic_id: isComplete ? topic.topic_id : topic,
      name: isComplete ? topic.name : '',
      description: isComplete ? topic.description : '',
      cover: isComplete ? topic.cover.s : '',
      loading: !isComplete,
    });

    dialog.open();

    if (isComplete) {
      return;
    }

    Topic.getOne(topic, (response) => {
      actions.setState({ loading: false });

      if (response.code) {
        dialog.close();
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({
        topic_id: response.data.topic_id,
        name: response.data.name,
        description: response.data.description,
        cover: response.data.cover.s,
      });

      setTimeout(() => dialog.handleUpdate());
    });
  },

  /**
   * 关闭对话框
   */
  close: () => dialog.close(),

  /**
   * 响应输入框输入事件
   */
  input: e => (state, actions) => {
    // 移除错误信息
    actions.setState({ [`${e.target.name}_msg`]: '' });

    return { [e.target.name]: e.target.value };
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
  coverSelected: e => (state, actions) => {
    actions.setState({ cover_msg: '' });

    const fr = new FileReader();

    fr.onload = (frEvent) => {
      actions.setState({ cover: frEvent.target.result });
    };

    fr.readAsDataURL(e.target.files[0]);
  },

  /**
   * 添加话题
   */
  submit: () => (state, actions) => {
    $.loadStart();

    const handleValidationError = (response) => {
      const errors = {};

      Object.keys(response.errors).forEach((key) => {
        errors[`${key}_msg`] = response.errors[key];
      });

      actions.setState(errors);

      // 光标聚焦到错误字段
      if (typeof errors.name_msg !== 'undefined') {
        $name[0].focus();
      } else if (typeof errors.description_msg !== 'undefined') {
        $description[0].focus();
      }
    };

    const success = (response, handler) => {
      $.loadEnd();

      // 字段验证失败
      if (response.code === 100002) {
        handleValidationError(response);

        return;
      }

      // 其他错误
      if (response.code) {
        mdui.snackbar(response.message);

        return;
      }

      handler();
    };

    if (state.topic_id) { // 修改
      const { name, description } = state;
      const params = { name, description };

      if ($cover[0].files.length) {
        params.cover = $cover[0].files[0];
      }

      Topic.updateOne(state.topic_id, params, response => success(response, () => {
        // 修改成功，直接修改列表中的数据，不刷新
        actions.close();
        mdui.snackbar('修改成功');

        global_actions.components.datatable.updateOne(response.data);
      }));
    } else { // 新增
      const { name, description } = state;
      const file = $cover[0].files[0];

      Topic.create(name, description, file, response => success(response, () => {
        // 添加成功，刷新列表
        actions.close();
        mdui.snackbar('添加成功');

        global_actions.topics.loadData();
      }));
    }
  },
});
