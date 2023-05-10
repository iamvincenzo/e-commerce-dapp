// SPDX-License-Identifier: MIT
pragma solidity 0.6.0;

pragma experimental ABIEncoderV2; 

import "@openzeppelin/contracts/access/Ownable.sol"; // The standard for secure blockchain applications

/**
 * @title Purchase products.
 * @author Vincenzo Fraello(299647) & Lorenzo Di Palma(299636).
 * @notice You can use this contract to make purchase of different products.
 * @dev All function calls are currently implemented with Web3.js. 
 **/

contract PurchaseProducts is Ownable {
    
    /**
     * @dev This structure represents an order.
     **/
    
    struct Purchase {
        uint256 id;
        uint256 purchaseDate;
        string content;
        address owner;
        uint256 eth;
        bool isShipped;
    } Purchase purchase;
    

     /**
     * @dev This mapping is used to create an array of purchases that correspond to the different users.
     **/
    
    mapping (address => Purchase[]) private arrayPurchase;
    

     /**
     * @dev This mapping is used to store the value of the last index of the arrayPurchase structure 
     *      that refers to the different users.
     **/

    mapping(address => uint256) private lastIndexArray;
    

     /**
     * @dev This function is used to make purchase. 
     **/
    
    function makePurchase(string calldata _content) external payable {

        purchase = Purchase(lastIndexArray[msg.sender]++, block.timestamp, _content, msg.sender, msg.value, false);

        arrayPurchase[msg.sender].push(purchase);
    }
    
       
     /**
     * @dev This function is used to get orders (used by clients and esmployees)
     **/
    
    function getOrders(address _addr) public view returns (Purchase[] memory) {
        
        return  arrayPurchase[_addr];
    }
    

     /**
     * @dev This function is used to withdraw crypto currency from the contract wallet 
     *      only by the owner of the contract. 
     **/
    
    function withdraw() external onlyOwner {
        
        address payable _owner = address(uint160(owner()));
        
        _owner.transfer(address(this).balance);
    }
    

     /**
     * @dev This function is used to get the balacnce of the contract. 
     **/
    
    function contractBalance() external view returns(uint) {
       
        return address(this).balance;
    }
    
    
     /**
     * @dev This function is used to ship an order. 
     **/
    
    function shipOrder(address _addr, uint256 _index) external onlyOwner {
        
        arrayPurchase[_addr][_index].isShipped = true;
    }
}
