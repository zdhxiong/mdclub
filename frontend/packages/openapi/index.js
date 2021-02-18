const fs = require('fs');
const YAML = require('js-yaml');
const resolve = require('json-refs').resolveRefs;

const srcPath = './src/index.yaml';
const yamlDistPath = './dist/openapi.yaml';
const jsonDistPath = './dist/openapi.json';

const doc = YAML.load(fs.readFileSync(srcPath, 'utf8').toString());
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
  fs.writeFileSync(yamlDistPath, YAML.dump(results.resolved));
  fs.writeFileSync(jsonDistPath, JSON.stringify(results.resolved, null, 2));
});
