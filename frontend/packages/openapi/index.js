const fs = require('fs');
const YAML = require('js-yaml');
const resolve = require('json-refs').resolveRefs;

const srcPath = './src/index.yaml';
const distPath = './dist/openapi.yaml';

const doc = YAML.safeLoad(fs.readFileSync(srcPath, 'utf8').toString());
const options = {
  filter: ['relative', 'remote'],
  location: srcPath,
  loaderOptions: {
    processContent: (res, callback) => {
      callback(null, YAML.load(res.text));
    }
  },
};

resolve(doc, options).then((results) => {
  fs.writeFileSync(distPath, YAML.safeDump(results.resolved));
});
