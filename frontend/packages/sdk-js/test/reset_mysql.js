// eslint-disable-next-line @typescript-eslint/no-var-requires
const Importer = require('mysql-import');

const mydb_importer = new Importer({
  host: 'localhost',
  user: 'root',
  password: '123456',
  database: 'mdclub_test',
});

mydb_importer.import('test.sql').catch((err) => {
  console.error(err);
});
