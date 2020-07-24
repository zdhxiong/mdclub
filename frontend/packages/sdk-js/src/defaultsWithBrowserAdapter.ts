import defaults from './defaults';
import BrowserAdapter from './adapter/BrowserAdapter';

defaults.adapter = new BrowserAdapter();

export default defaults;
