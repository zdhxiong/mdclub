// eslint-disable-next-line @typescript-eslint/no-var-requires
const mysql_import = require('mysql-import');

const mydb_importer = mysql_import.config({
  host: 'localhost',
  user: 'root',
  password: '123456',
  database: 'mdclub_test',
  onerror: err => {
    throw new Error(err);
  },
});

mydb_importer.import('test.sql');
