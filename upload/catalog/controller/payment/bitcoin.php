<?php

class ControllerPaymentBitcoin extends Controller {

    private $payment_module_name  = 'bitcoin';
	protected function index() {
        $this->language->load('payment/'.$this->payment_module_name);
    	$this->data['button_bitcoin_confirm'] = $this->language->get('button_bitcoin_confirm');
		
		
		$this->checkUpdate();
	
        $this->load->model('checkout/order');
		$order_id = $this->session->data['order_id'];
		$order = $this->model_checkout_order->getOrder($order_id);
        $this->model_checkout_order->confirm($order_id, $this->config->get('bitcoin_order_status_id'));
		//order created

		$current_default_currency = "USD";
		
		$this->data['bitcoin_total'] = round($this->currency->convert($order['total'], $current_default_currency, "BTC"),4);
		
    	require_once('jsonRPCClient.php');
		
		$bitcoin = new jsonRPCClient('http://'.$this->config->get('bitcoin_rpc_username').':'.$this->config->get('bitcoin_rpc_password').'@'.$this->config->get('bitcoin_rpc_address').':8332/');
		
		try {
			$bitcoin_info = $bitcoin->getinfo();
		} catch (Exception $e) {
			$error = true;
		}
		
		$this->data['bitcoin_send_address'] = $bitcoin->getaccountaddress($this->config->get('bitcoin_prefix').'_'.$order_id);
		

		//not sure I need this
		$this->data['continue'] = $this->url->link('checkout/success');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/bitcoin.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/bitcoin.tpl';
		} else {
			$this->template = 'default/template/payment/bitcoin.tpl';
		}	
		
		$this->render();
	}

    public function send() {
	


    }

    public function callback() {
        $json = json_decode(file_get_contents("php://input"));

        if (property_exists($json, 'posData')) {
            $salt = $this->config->get($this->payment_module_name.'_secret');
            $posData = json_decode($json->{'posData'});
            if($posData->{'hash'} == crypt($posData->{'id'}, $salt)) {
                if(($json->{'status'} == 'confirmed') || ($json->{'status'} == 'complete')) {
                    $this->load->model('checkout/order');
                    $order_id = $posData->{'id'};
                    $order = $this->model_checkout_order->getOrder($order_id);
                    $this->model_checkout_order->confirm($order_id, $this->config->get('bitpay_confirmed_status_id'));
                }
                if($json->{'status'} == 'invalid') {
                    $this->load->model('checkout/order');
                    $order_id = $posData->{'id'};
                    $order = $this->model_checkout_order->getOrder($order_id);
                    $this->model_checkout_order->confirm($order_id, $this->config->get('bitpay_invalid_status_id'));
                }
            } else {
                file_put_contents('php://stderr', "bitpay interface error: invalid order confirmation (hash was invalid)");
            }
        } else {
            file_put_contents('php://stderr', "bitpay interface error: malformed order confirmation");
        }
    }
	
	public function checkUpdate() {
		if (extension_loaded('curl')) {
			$data = array();
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code = 'BTC'");
						
			if(!$query->row) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "currency (title, code, symbol_right, decimal_place, status) VALUES ('Bitcoin', 'BTC', ' BTC', '4', '1')");
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
	//	print "update running\n";
		$path = "1/BTCUSD/public/ticker";
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
		curl_setopt($ch, CURLOPT_URL, 'https://mtgox.com/api/'.$path);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	 
		// run the query
		$res = curl_exec($ch);
		if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
		$dec = json_decode($res, true);
		if (!$dec) throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
		$btcdata = $dec;
		
		$currency = "BTC";
		$value = $btcdata['return']['avg']['value'];
		
		if ((float)$value) {
			$value = 1/$value;
			$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '" . (float)$value . "', date_modified = '" .  $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->db->escape($currency) . "'");
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '1.00000', date_modified = '" .  $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->db->escape($this->config->get('config_currency')) . "'");
		$this->cache->delete('currency');
	}
}
?>
