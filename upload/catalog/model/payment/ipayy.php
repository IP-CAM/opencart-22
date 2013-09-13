<?php 
class ModelPaymentIpayy extends Model {
  	public function getMethod($address, $total) {
  		$convertedTotal = $total;
		$this->load->language('payment/ipayy');
		
		$status = true;
		
		if ($this->currency->getCode() != "INR") {
			$convertedTotal = $this->currency->convert($total, $this->currency->getCode(), 'INR');
		}
		
		if (!$this->config->get('ipayy_status')) {
			$status = false;
		} elseif ($convertedTotal > $this->config->get('ipayy_total')) {
			$status = false;
		} else {
			$status = true;
		}

		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'ipayy',
        		'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('ipayy_sort_order')
      		);
		}
		
    	return $method_data;
  	}
}
?>