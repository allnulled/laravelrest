const path = require("path");
const fs = require("fs-extra");
const ejs = require("ejs");
const template = require("es6-template-strings");
const chalk = require("chalk");

class Codegen {
  constructor(args) {
    this.options = {};
    if (!args.input) {
      throw new Error("Must specify args.input");
    }
    if (!args.output) {
      throw new Error("Must specify args.output");
    }
    this.options.input = path.resolve(args.input);
    this.options.output = path.resolve(args.output);
    this.settings = JSON.parse(fs.readFileSync(this.options.input).toString());
    this.items = [];
  }

  overrideKeysIfNotNull(parent, item) {
    const out = { ...parent };
    Object.keys(item).forEach(key => {
      if((typeof item[key] === "object") && (!Array.isArray(item[key])) && (item[key] !== null)) {
        out[key] = this.overrideKeysIfNotNull(out[key] || {}, item[key]);
      } else if (item[key] !== null) {
        out[key] = item[key];
      }
    });
    return out;
  }

  getOnlyName(namespace) {
    return namespace.split("\\").pop();
  }

  getOnlyNamespace(namespace) {
    const parts = namespace.split("\\");
    parts.pop();
    return parts.join("\\");
  }

  createContext(parameters = {}) {
    return { ...global, __dirname, __filename, ejs, fs, context: this, ...parameters };
  }

  formatString(text, ...parameters) {
    return template(text, this.createContext(...parameters));
  }

  getPathFromNamespace(namespace) {
    return path.resolve(this.options.output, namespace.replace(/^App/g, "app").replace(/\\/g, "/")) + ".php";
  }

  run() {
    const templateTypes = Object.keys(this.settings.default);
    this.settings.items.forEach(itemChild => {
      // Extend items by inheritance:
      const item = {};
      templateTypes.forEach(templateType => {
        if (templateType in itemChild) {
          const parentType = this.settings.default[templateType];
          const childType = itemChild[templateType];
          const itemType = this.overrideKeysIfNotNull(parentType, childType);
          item[templateType] = itemType;
        }
      });
      this.items.push(item);
      // Generate items outputs:
      templateTypes.forEach(templateType => {
        if (templateType in itemChild) {
          const templateFile = this.formatString(item[templateType].templatePath, this.createContext({item}));
          const template = fs.readFileSync(templateFile).toString();
          const output = ejs.render(template, this.createContext({item}));
          const outputFile = this.getPathFromNamespace(item[templateType].namespace);
          console.log(chalk.green.bold(`[*] Writting file: ${outputFile}`));
          fs.ensureFileSync(outputFile);
          fs.writeFileSync(outputFile, output, "utf8");
        }
      });
    });
    // Generate other outputs:
    Object.keys(this.settings.others).forEach(key => {
      const other = this.settings.others[key];
      const templateFile = this.formatString(other.templatePath, this.createContext({item:other}));
      const template = fs.readFileSync(templateFile).toString();
      const output = ejs.render(template, this.createContext({item:other}));
      const outputFile = this.formatString(other.outputFile, this.createContext({item:other}));
      console.log(chalk.green.bold(`[*] Writting file: ${outputFile}`));
      fs.ensureFileSync(outputFile);
      fs.writeFileSync(outputFile, output, "utf8");
    });
  }

  static run(...args) {
    return new this(...args).run();
  }
}

module.exports = Codegen;
