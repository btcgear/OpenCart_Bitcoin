<!--
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
-->

<?php if(!$error) { ?>
	<div style="font-size:16px; text-align:center;">Please send <input type="text" style="font-size:18px; text-align: right; width:100px; " readonly="readonly" value="<?php echo $bitcoin_total; ?>"> BTC to <input type="text" style="font-size:18px; width:400px; text-align:center; " readonly="readonly" value="<?php echo $bitcoin_send_address; ?>"> to complete the transaction.</div>
	<div class="buttons">
		<div class="right"><a id="button-confirm" class="button"><span><?php echo $button_bitcoin_confirm; ?></span></a></div>
	</div>
<?php } else { ?>
<div class="warning">
<?php echo $error_msg; ?>
</div>
<?php } ?>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({ 
		type: 'GET',
		url: 'index.php?route=payment/bitcoin/confirm_sent',
        timeout: 5000,
        error: function() {
            alert('That didn\'t quite work. Please try again. If you receive this message multiple times, please contact us so we can help finalize your order.');
        },
		success: function(received) {
			if(!received) {
				alert("We have not yet received your full payment. If you already sent it, try again in a few seconds. If you receive this message multiple times, please contact us so we can help finalize your order.");
			}
			else {
				location.href = 'index.php?route=checkout/success';
			}
		}		
	});
});
//--></script> 

