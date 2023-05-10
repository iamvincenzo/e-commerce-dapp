const _smartContr = artifacts.require("PurchaseProducts");
// const _owner = artifacts.require("Owner");

module.exports = function (deployer) {
    
  // deployer.deploy(_owner);
  // deployer.link(_owner, _smartContr);
  deployer.deploy(_smartContr);
};
