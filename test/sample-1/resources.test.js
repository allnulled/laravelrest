const { assert, expect } = require("chai");

describe("Codegen API", function() {

  const Codegen = require(__dirname + "/../../src/codegen.js");

  it("can run from files", function() {
    Codegen.run({
      input: __dirname + "/sample-1.json",
      output: __dirname + "/results"
    });
    // @TODOS: Assertions...
    
  });

});