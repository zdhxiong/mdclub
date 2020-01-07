import defaults from '../es/defaults';
import Adapter from '../es/adapter/JQueryAdapter';

defaults.apiPath = 'http://mdclub.test/api';
defaults.adapter = new Adapter();
defaults.error = (errMsg): void => console.error(errMsg);
