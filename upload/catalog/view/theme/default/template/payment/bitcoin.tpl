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
<div class="buttons">
	<div class="right"><a id="button-pay" class="button"><span><?php echo $button_bitcoin_pay; ?></span></a></div>
</div>
<?php } else { ?>
<div class="warning">
<?php echo $error_msg; ?>
</div>
<?php } ?>
<script type="text/javascript"><!--


$('#button-pay').on('click', function() {
	html  = '<div style="font-size:16px; padding:6px; text-align:center;">Please send <span style="font-size:18px; border-style:solid; border-width: 1px; border-radius:3px; padding-top:3px; padding-right:6px; padding-left:6px; padding-bottom:3px;"><?php echo $bitcoin_total; ?></span> BTC to </div><div style="font-size:16px; padding:6px; text-align:center;"><span style="font-size:18px; border-style:solid; border-width: 1px; border-radius:3px; padding-top:3px; padding-right:6px; padding-left:6px; padding-bottom:3px;"><?php echo $bitcoin_send_address; ?></span></div><div style="font-size:16px; padding:6px; text-align:center;"> to complete the transaction.</div>';
	html += '<div class="buttons">';
	html += '	<div class="right"><a id="button-confirm" class="button"><span><?php echo $button_bitcoin_confirm; ?></span></a></div>';
	html += '</div>';
	$.colorbox({
		overlayClose: true,
		opacity: 0.5,
		width: '500px',
		height: '225px',
		href: false,
		html: html,
		onComplete: function() {
			$('#button-confirm').on('click', function() {
				$.ajax({ 
					type: 'GET',
					url: 'index.php?route=payment/bitcoin/confirm_sent',
					timeout: 5000,
					error: function() {
						alert('<?php echo $error_confirm; ?>');
					},
					success: function(received) {
						if(!received) {
							alert('<?php echo $error_incomplete_pay; ?>');
						}
						else {
							location.href = 'index.php?route=checkout/success';
						}
					}		
				});
			});
		}
	});
});
//--></script> 


