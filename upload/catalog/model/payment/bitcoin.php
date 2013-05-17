<?php 
/*
Copyright (c) 2013 John Atkinson (jga)
*/

class ModelPaymentBitcoin extends Model {
  	public function getMethod($address) {
		$this->load->language('payment/bitcoin');
		
		if ($this->config->get('bitcoin_status')) {
        	$status = TRUE;
		} else {
			$status = FALSE;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'         	=> 'bitcoin',
        		'title'      	=> $this->language->get('text_title'),
				'sort_order' 	=> $this->config->get('bitcoin_sort_order'),
      		);
    	}
   
    	return $method_data;
  	}
}
?>
