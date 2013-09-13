<?php

include_once 'CryptoUtils.php';

class ControllerPaymentIpayy extends Controller {
	protected function index() {
		$this->language->load('payment/ipayy');

		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['action'] = 'http://api.ipayy.com/v001/c/oc/dopayment';

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {
			
			$total = $order_info['total'];
			if ($order_info['currency_code'] != 'INR') {
				$total = $this->currency->convert($total, $order_info['currency_code'], 'INR');
			}
			
			switch ($this->config->get('ipayy_item_display')) {
				
				case "Item Name":
					$products = array();
					foreach ($this->cart->getProducts() as $product) {
						$products[]=$product['name'];
					}
					$item_name = implode(", ", $products);
					break;
				case "Subdomain Name":
					$item_name = $this->get_subdomain($order_info['store_url']);
					if (!$item_name) {
						$item_name = $order_info['store_name'];
					}
					break;
				case "Domain Name":
					$item_name = $this->get_domain($order_info['store_url']);
					if (!$item_name) {
						$item_name = $order_info['store_name'];
					}
					break;
				case "Custom":
					$item_name = $this->config->get('ipayy_item_display_other');
					break;
				default:
				case "Store Name":
					$item_name = $order_info['store_name'];
					break;
			}
			
			$params = array(
					in\verse\ipayy\crypto\CryptoUtils::APPLICATION_KEY_PARAM => $this->config->get('ipayy_application_id'),
					in\verse\ipayy\crypto\CryptoUtils::MERCHANT_KEY_PARAM => $this->config->get('ipayy_merchant_id'),
					in\verse\ipayy\crypto\CryptoUtils::REQUEST_TOKEN_PARAM => $order_info['order_id'],
					in\verse\ipayy\crypto\CryptoUtils::ITEM_PRICE_PARAM => $total,
					in\verse\ipayy\crypto\CryptoUtils::ITEM_NAME_PARAM => $item_name,
					in\verse\ipayy\crypto\CryptoUtils::MSISDN_PARAM => $order_info['telephone'],
					in\verse\ipayy\crypto\CryptoUtils::CURRENCY_PARAM => 'INR',
					in\verse\ipayy\crypto\CryptoUtils::REDIRECT_URL_PARAM => $this->url->link('payment/ipayy/callback')
			);

			$this->data['encrypted_string'] = in\verse\ipayy\crypto\CryptoUtils::encrypt($params);

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ipayy.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/ipayy.tpl';
			} else {
				$this->template = 'default/template/payment/ipayy.tpl';
			}

			$this->render();
		}
	}
	
	function get_domain($url)
	{
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
			return $regs['domain'];
		}
		return false;
	}
	
	function get_subdomain($url)
	{
		$pieces = parse_url($url);
		return $pieces['host'];
	}

	public function callback() {
		$this->language->load('payment/ipayy');
		$success_link = $this->url->link('checkout/success');
		$fail_link = $this->url->link('checkout/checkout');
		if (isset($this->request->get['gh'])) {

			$encrypted_string = $this->request->get['gh'];

			$this->load->model('checkout/order');

			if (isset($this->session->data['order_id'])) {
				$order_id = $this->session->data['order_id'];

				$order_info = $this->model_checkout_order->getOrder($order_id);

				if ($order_info) {
					try {
						$params = in\verse\ipayy\crypto\CryptoUtils::decrypt($encrypted_string);
						if (array_key_exists("ec", $params)) {
							$payment_error = $this->language->get('ERROR_OTHER');
							$order_status_id = $this->config->get('ipayy_failed_status_id');
						} elseif ($params[in\verse\ipayy\crypto\CryptoUtils::REQUEST_TOKEN_PARAM] != $order_id) {
							$payment_error = $this->language->get('ERROR_FRAUD');
							$order_status_id = $this->config->get('ipayy_denied_status_id');
						} elseif (array_key_exists("ts", $params) && $params["ts"] == "S") {
							$order_status_id = $this->config->get('ipayy_completed_status_id');
						} elseif (array_key_exists("ts", $params) && $params["ts"] == "F") {
							$payment_error = $this->language->get('ERROR_' . $params["tf"]);
							if (!$payment_error) {
								$$payment_error = $this->language->get('ERROR_OTHER');
							}
							$order_status_id = $this->config->get('ipayy_failed_status_id');
						} else {
							$payment_error = $this->language->get('ERROR_OTHER');
							$order_status_id = $this->config->get('ipayy_failed_status_id');
						}
					} catch (in\verse\ipayy\crypto\CryptoException $e) {
						$payment_error = $this->language->get('ERROR_EXCEPTION');
						$order_status_id = $this->config->get('ipayy_failed_status_id');
					}

				} else {
					$payment_error = $this->language->get('ERROR_INVALID_PARAMS');
				}
			} else {
				$payment_error = $this->language->get('ERROR_INVALID_PARAMS');
			}
		} else {
			$payment_error = $this->language->get('ERROR_INVALID_PARAMS');
		}

		if (isset($order_status_id) && !isset($payment_error)) {
			if (!$order_info['order_status_id']) {
				$this->model_checkout_order->confirm($order_id, $order_status_id);
			} else {
				$this->model_checkout_order->update($order_id, $order_status_id);
			}
			$this->redirect($success_link);
		} else {
			if (isset($order_status_id)) {
				if (!$order_info['order_status_id']) {
					$this->model_checkout_order->confirm($order_id, $order_status_id, $payment_error, true);
				} else {
					$this->model_checkout_order->update($order_id, $order_status_id, $payment_error, true);
				}
			}
			if ( isset($this->session->data['order_id']) && ( ! empty($this->session->data['order_id']))  ) {
				$this->session->data['last_order_id'] = $this->session->data['order_id'];
			}


			if (! empty($this->session->data['last_order_id']) ) {
				$this->document->setTitle(sprintf($this->language->get('text_payment_title'), $this->session->data['last_order_id']));
			} else {
				$this->document->setTitle($this->language->get('text_payment_title'));
			}

			$this->data['breadcrumbs'] = array();

			$this->data['breadcrumbs'][] = array(
					'href'      => $this->url->link('common/home'),
					'text'      => $this->language->get('text_home'),
					'separator' => false
			);

			$this->data['breadcrumbs'][] = array(
					'href'      => $this->url->link('checkout/cart'),
					'text'      => $this->language->get('text_basket'),
					'separator' => $this->language->get('text_separator')
			);

			$this->data['breadcrumbs'][] = array(
					'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
					'text'      => $this->language->get('text_checkout'),
					'separator' => $this->language->get('text_separator')
			);

			$this->data['heading_title'] = $payment_error;

			$this->data['text_message'] = sprintf($this->language->get('text_payment_error'), $this->url->link('information/contact'));

			$this->data['button_continue'] = $this->language->get('button_continue');

			$this->data['continue'] = $this->url->link('checkout/checkout');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ipayy_error.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/ipayy_error.tpl';
			} else {
				$this->template = 'default/template/payment/ipayy_error.tpl';
			}

			$this->children = array(
					'common/column_left',
					'common/column_right',
					'common/content_top',
					'common/content_bottom',
					'common/footer',
					'common/header'
			);

			$this->response->setOutput($this->render());
		}
	}
}
?>