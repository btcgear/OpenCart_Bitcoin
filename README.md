# OpenCart_Bitcoin
### by John Atkinson (jga) from [BTC Gear](http://btcgear.com/)

Donations can be paid here: **12ctRXVVPAXQ6CQyEXkBhvi33K7kP4CMB5**

Initial bounty paid by cablepair.

This is an OpenCart payment module that communicates with a bitcoin client using JSON RPC.

This code accurately converts USD to BTC using the up-to-the-minute MtGox average.  It is completely self contained and requires no cron jobs or external hardware other than a properly configured bitcoind server.  Every order creates a new bitcoin address for payment and gives it a label corresponding to the order_id of the order.  It installs like any other OpenCart plugin and it is completely integrated with OpenCart.

This extension has been tested with OpenCart versions between 1.5.2.x and 1.5.4.

Any questions or comments can be sent to support@btcgear.com.

# Installation

1. Upload all files maintaining OpenCart folder structure.
2. Install the payment module in the admin console (Extensions > Payments > Bitcoin > Install).
3. Edit the payment module settings (Extensions > Payments > Bitcoin > Edit).
4. Run at least one test order through checkout up until payment (no payment required).  The first order initializes the Bitcoin currency and will return 0 BTC for the order total.

## Explanation of Settings

* *Bitcoin RPC Username*: This is the username in the "rpcuser" line of your bitcoin.conf file.
* *Bitcoin RPC Host Address*: This is the IP address of the computer bitcoind is running on.
* *Bitcoin RPC Password*: This is the password in the "rpcpassword" line of your bitcoin.conf file.
* *Bitcoin RPC Port*: This is the port number in the "rpcport" line of your bitcoin.conf file.  The default port is 8332.
* *The prefix for the address labels*: The addresses will be assigned to accounts named with the format [prefix]_[order_id].
* *Show BTC as a store currency*: If you select yes, your customers will be able to view prices in BTC.
* *Status of a new order*: Choose a status for an order that has received payment with 0 confirmations.
* *Status*: Enable the Bitcoin payment module here.
* *Sort Order*: Where you want this module to show up in relation to the other payment modules on the checkout page.


### New in version 1.1.0

Orders are only confirmed (and created in the admin console) once initial payment has been received (0 confirmations).

* * *

Copyright (c) 2012 John Atkinson (jga)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
