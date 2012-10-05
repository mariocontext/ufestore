Concrete5 Module for Vertical Streaming Rest Authorization
===============================================================

*Author: Dreamr*
  
  Vertical Streaming is a web based platform for delivering multimedia content through streaming. This module is plugs in the functionality needed to run Concrete5 as a storefront, and tying in product authorization on your Vertical Streaming site.


Install
==================

  Copy the commerce_vs_purchase folder into you packages directory via ftp or scp.

    
Documentation
=============

Tying products together
----------------------------------
  
  In order to make the remote authorization api simple, and yet as flexible as possible we allow you to "tag" products on the Vertical Streaming side of things. We call it a SKU, but it can relate to anything in your Concrete5 store attributes.
  
  Just create the product in Concrete5, give it an attribute, call it SKU if you want it to match. Then edit the product at Vertical Streaming and add the SKU.
  
  The REST call will bundle up all products after the call has been made to charge the card and pass them over HTTPS to Vertical Streaming for authorization. The user will be sent an email to the address given during checkout that will contain their login and email address to view the content at Vertical Streaming.

Contributors
------------

This module is maintained by Dreamr OKelly (["dreamr", ".", "okelly", "@gmail.com"].join
