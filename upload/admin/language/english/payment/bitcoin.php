<?php
/*
Copyright (c) 2012 John Atkinson (jga)

Permission is hereby granted, free of charge, to any person obtaining a copy of this 
software and associated documentation files (the "Software"), to deal in the Software 
without restriction, including without limitation the rights to use, copy, modify, 
merge, publish, distribute, sublicense, and/or sell copies of the Software, and to 
permit persons to whom the Software is furnished to do so, subject to the following 
conditions:

The above copyright notice and this permission notice shall be included in all copies 
or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR 
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE 
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR 
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
DEALINGS IN THE SOFTWARE.
*/
$_['heading_title']      = 'Bitcoin';

// Text
$_['text_payment']       = 'Payment';
$_['text_success']       = 'Success: You have modified the Bitcoin payment module!';
$_['text_bitcoin'] = '<img src="view/image/payment/bitcoin.jpg" alt="Bitcoin" title="Bitcoin" />';
$_['text_yes']       = 'Yes';
$_['text_no']       = 'No';

// Entry
$_['entry_username']      = 'Bitcoin RPC Username:';
$_['entry_address']      = 'Bitcoin RPC Host Address:';
$_['entry_password']     = 'Bitcoin RPC Password:';
$_['entry_port']     = 'Bitcoin RPC Port:<br /><span class="help">The default is 8332</span>';
$_['entry_prefix']     = 'The prefix for the address labels:<br /><span class="help">The account will be in the form [prefix]_[order_id]</span>';
$_['entry_order_status'] = 'Status of a new order:';
$_['entry_show_btc']       = 'Show BTC as a store currency:';
$_['entry_status']       = 'Status:';
$_['entry_sort_order']   = 'Sort Order:';

// Error
$_['error_permission']   = 'Warning: You do not have permission to modify the Bitcoin payment module.';
$_['error_username']      = 'Warning: Bitcoin RPC Username is required';
$_['error_address']      = 'Warning: Bitcoin RPC Host Address is required';
$_['error_password']     = 'Warning: Bitcoin RPC Password is required';
$_['error_port']     = 'Warning: Bitcoin RPC Port is required';
$_['error_prefix']     = 'Warning: Label prefix is required';
?>
