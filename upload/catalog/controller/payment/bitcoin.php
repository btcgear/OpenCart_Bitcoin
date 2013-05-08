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

class ControllerPaymentBitcoin extends Controller {

    private $payment_module_name  = 'bitcoin';
	protected function index() {
        $this->language->load('payment/'.$this->payment_module_name);
    	$this->data['button_bitcoin_pay'] = $this->language->get('button_bitcoin_pay');
    	$this->data['text_please_send'] = $this->language->get('text_please_send');
    	$this->data['text_btc_to'] = $this->language->get('text_btc_to');
    	$this->data['text_to_complete'] = $this->language->get('text_to_complete');
    	$this->data['text_click_pay'] = $this->language->get('text_click_pay');
    	$this->data['text_uri_compatible'] = $this->language->get('text_uri_compatible');
    	$this->data['text_click_here'] = $this->language->get('text_click_here');
    	$this->data['text_if_not_redirect'] = $this->language->get('text_if_not_redirect');
		$this->data['error_msg'] = $this->language->get('error_msg');
		$this->data['error_confirm'] = $this->language->get('error_confirm');
		$this->data['error_incomplete_pay'] = $this->language->get('error_incomplete_pay');
				
		$this->checkUpdate();
	
        $this->load->model('checkout/order');
		$order_id = $this->session->data['order_id'];
		$order = $this->model_checkout_order->getOrder($order_id);

		$current_default_currency = "USD";
		
		$this->data['bitcoin_total'] = round($this->currency->convert($order['total'], $current_default_currency, "BTC"),4);
		
		require_once('jsonRPCClient.php');
		
		$bitcoin = new jsonRPCClient('http://'.$this->config->get('bitcoin_rpc_username').':'.$this->config->get('bitcoin_rpc_password').'@'.$this->config->get('bitcoin_rpc_address').':'.$this->config->get('bitcoin_rpc_port').'/');
		
		$this->data['error'] = false;
		try {
			$bitcoin_info = $bitcoin->getinfo();
		} catch (Exception $e) {
			$this->data['error'] = true;
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/bitcoin.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/bitcoin.tpl';
			} else {
				$this->template = 'default/template/payment/bitcoin.tpl';
			}	
			$this->render();
			return;
		}
		$this->data['error'] = false;
		
		$this->data['bitcoin_send_address'] = $bitcoin->getaccountaddress($this->config->get('bitcoin_prefix').'_'.$order_id);
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/bitcoin.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/bitcoin.tpl';
		} else {
			$this->template = 'default/template/payment/bitcoin.tpl';
		}	
		
		$this->render();
	}
	
	
	public function confirm_sent() {
        $this->load->model('checkout/order');
		$order_id = $this->session->data['order_id'];
        $order = $this->model_checkout_order->getOrder($order_id);
		$current_default_currency = "USD";		
		$bitcoin_total = round($this->currency->convert($order['total'], $current_default_currency, "BTC"),4);
		require_once('jsonRPCClient.php');
		$bitcoin = new jsonRPCClient('http://'.$this->config->get('bitcoin_rpc_username').':'.$this->config->get('bitcoin_rpc_password').'@'.$this->config->get('bitcoin_rpc_address').':'.$this->config->get('bitcoin_rpc_port').'/');
	
		try {
			$bitcoin_info = $bitcoin->getinfo();
		} catch (Exception $e) {
			$this->data['error'] = true;
		}

		try {
			$received_amount = $bitcoin->getreceivedbyaccount($this->config->get('bitcoin_prefix').'_'.$order_id,0);
		} catch (Exception $e) {
			echo false;
		}
		if(round((float)$received_amount,4) >= round((float)$bitcoin_total,4)) {
			$order = $this->model_checkout_order->getOrder($order_id);
			$this->model_checkout_order->confirm($order_id, $this->config->get('bitcoin_order_status_id'));
			echo true;
		}
		else {
			echo false;
		}
	}
	
	public function checkUpdate() {
		if (extension_loaded('curl')) {
			$data = array();
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code = 'BTC'");
						
			if(!$query->row) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "currency (title, code, symbol_right, decimal_place, status) VALUES ('Bitcoin', 'BTC', ' BTC', '4', ".$this->config->get('bitcoin_show_btc').")");
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code = 'BTC'");
			}
			
			$format = '%Y-%m-%d %H:%M:%S';
			$last_string = $query->row['date_modified'];
			$current_string = strftime($format);
			$last_time = strptime($last_string,$format);
			$current_time = strptime($current_string,$format);
		
			$num_seconds = 60; //every [this many] seconds, the update should run.
			
			if($last_time['tm_year'] != $current_time['tm_year']) {
				$this->runUpdate();
			}
			else if($last_time['tm_yday'] != $current_time['tm_yday']) {
				$this->runUpdate();
			}
			else if($last_time['tm_hour'] != $current_time['tm_hour']) {
				$this->runUpdate();
			}
			else if(($last_time['tm_min']*60)+$last_time['tm_sec'] + $num_seconds < ($current_time['tm_min'] * 60) + $current_time['tm_sec']) {
				$this->runUpdate();
			}
		}
	}
	
	public function runUpdate() {
		$path = "1/BTCUSD/ticker";
		$req = array();
		
		// API settings
		$key = '';
		$secret = '';
	 
		// generate a nonce as microtime, with as-string handling to avoid problems with 32bits systems
		$mt = explode(' ', microtime());
		$req['nonce'] = $mt[1].substr($mt[0], 2, 6);
	 
		// generate the POST data string
		$post_data = http_build_query($req, '', '&');
	 
		// generate the extra headers
		$headers = array(
			'Rest-Key: '.$key,
			'Rest-Sign: '.base64_encode(hash_hmac('sha512', $post_data, base64_decode($secret), true)),
		);
	 
		// our curl handle (initialize if required)
		static $ch = null;
		if (is_null($ch)) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MtGox PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
		}
		curl_setopt($ch, CURLOPT_URL, 'http://data.mtgox.com/api/'.$path);
	 
		// run the query
		$res = curl_exec($ch);
		if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
		$dec = json_decode($res, true);
		if (!$dec) throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
		$btcdata = $dec;
		
		$currency = "BTC";
		$avg_value = $btcdata['return']['avg']['value'];
		$last_value = $btcdata['return']['last']['value'];
				
		if ((float)$avg_value && (float)$last_value) {
			if($avg_value < $last_value) {
				$value = $avg_value;
			}
			else {
				$value = $last_value;
			}
			$value = 1/$value;
			$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '" . (float)$value . "', date_modified = '" .  $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->db->escape($currency) . "'");
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '1.00000', date_modified = '" .  $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->db->escape($this->config->get('config_currency')) . "'");
		$this->cache->delete('currency');
	}
}
?>
