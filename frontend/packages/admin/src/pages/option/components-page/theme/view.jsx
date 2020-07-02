import { h } from 'hyperapp';

import Select from '../../components/select/view.jsx';
import SaveBtn from '../../components/save-btn/view.jsx';

export default ({ state, actions }) => {
  const { data, submitting } = state;
  const { onSubmit } = actions;

  return (
    <form method="post" onsubmit={onSubmit}>
      <Select
        label="主题"
        name="theme"
        value={data.theme}
        data={{
          material: 'material',
        }}
      />
      <SaveBtn submitting={submitting} />
    </form>
  );
};
