const fs = require("fs");
const ejs = require("ejs");
const template = fs.readFileSync(__dirname + "/random-data.sql.ejs").toString();
const file = ejs.render(template, {});
fs.writeFileSync(__dirname + "/random-data.sql", file, "utf8");