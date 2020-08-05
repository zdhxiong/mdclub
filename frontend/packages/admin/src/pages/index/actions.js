import extend from 'mdui.jq/es/functions/extend';
import { get as getStats } from 'mdclub-sdk-js/es/StatsApi';
import { Chart } from 'frappe-charts/dist/frappe-charts.min.esm';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import apiCatch from '~/utils/errorHandler';
import { timeFormat } from '~/utils/time';

const dateFormat = 'YYYY-MM-DD';

/**
 * 获取开始日期
 * @param range
 */
const getStartDate = (range) => {
  let year = new Date().getFullYear();
  let month = new Date().getMonth();
  let day;

  switch (range) {
    case '7day':
      day = new Date().getDate() - 6;
      break;
    case 'this-month':
      day = 1;
      break;
    case 'last-month':
      month = new Date().getMonth() - 1;
      day = 1;
      break;
    case '30day':
      day = new Date().getDate() - 30;
      break;
    case 'this-year':
      month = 0;
      day = 1;
      break;
    case 'last-year':
      year = new Date().getFullYear() - 1;
      month = 0;
      day = 1;
      break;
    case '365day':
      day = new Date().getDate() - 365;
      break;
    default:
      break;
  }

  return timeFormat(
    Date.parse(new Date(year, month, day).toString()) / 1000,
    dateFormat,
  );
};

/**
 * 获取结束日期
 * @param range
 */
const getEndDate = (range) => {
  const year = new Date().getFullYear();
  let month = new Date().getMonth();
  let day = new Date().getDate();

  switch (range) {
    case '7day':
    case 'this-month':
    case '30day':
    case 'this-year':
    case '365day':
      break;
    case 'last-month':
      day = 0;
      break;
    case 'last-year':
      month = 0;
      day = 0;
      break;
    default:
      break;
  }

  return timeFormat(
    Date.parse(new Date(year, month, day).toString()) / 1000,
    dateFormat,
  );
};

const as = {
  onCreate: () => (state, actions) => {
    emit('route_update');
    emit('searchbar_state_update', { isNeedRender: false });
    actions.setTitle('管理系统');

    // 加载初始数据
    if (!state.system_info) {
      actions.setState({ loading: true });
      getStats({
        include: [
          'system_info',
          'total_user',
          'total_question',
          'total_article',
          'total_answer',
          'total_comment',
          'new_user',
          'new_question',
          'new_article',
          'new_answer',
          'new_comment',
        ],
        start_date: getStartDate('7day'),
        end_date: getEndDate('7day'),
      })
        .finally(() => {
          actions.setState({ loading: false });
        })
        .then(({ data }) => {
          const fields = [
            'new_user',
            'new_question',
            'new_article',
            'new_answer',
            'new_comment',
          ];

          fields.forEach((field) => {
            data[field] = extend(state[field], { data: data[field] });
          });

          actions.setState(data);
        })
        .catch(apiCatch);
    }
  },

  onDestroy: () => (_, actions) => {
    actions.setState({ loading: false });
  },

  /**
   * 切换图表的显示时间范围
   * @param e
   * @param type
   */
  onDateChange: ({ e, type }) => (state, actions) => {
    const range = e.target.value;
    const newData = state[type];

    newData.range = range;
    newData.loading = true;
    newData.data = null;

    actions.setState({ [type]: newData });

    getStats({
      include: [type],
      start_date: getStartDate(range),
      end_date: getEndDate(range),
    })
      .then(({ data }) => {
        newData.loading = false;
        newData.data = data[type];
        actions.setState({ [type]: newData });
      })
      .catch(apiCatch);
  },

  /**
   * 创建图表
   * @param element
   * @param data
   */
  onChartCreate: ({ element, data }) => {
    // eslint-disable-next-line no-new
    new Chart(element, {
      data: {
        labels: data.map((item) => item.date),
        datasets: [
          {
            values: data.map((item) => item.count),
          },
        ],
      },
      type: 'line',
      height: 346,
      colors: [
        window.matchMedia('(prefers-color-scheme: dark)').matches
          ? '#8ab4f8'
          : '#1a73e8',
      ],
      spaceRatio: 0,
      lineOptions: {
        hideDots: 1,
      },
      axisOptions: {
        xIsSeries: true,
      },
    });
  },
};

export default extend(as, commonActions);
