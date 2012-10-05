<?php 
class VsPurchase {

	/**
	 * @var string
	 */
	protected $api_key = "";
	
	/**
	 * @var string
	 */
	protected $base_url = "";

	/**
	 * @return void
	 */
	public function __construct(){
		$pkg = Package::getByHandle('commerce_vs_purchase');
        $this->api_key 	= trim($pkg->config('VS_API_KEY'));
        $this->base_url = trim($pkg->config('VS_API_URL'));
		if(substr($this->base_url,-1) != "/") {
			$this->base_url .="/";
		}
	}
	
	/**
	 * @param string $email
	 * @param string $first_name
	 * @param string $last_name
	 * @param int[] $products
	 * @return mixed
	 */
	public function sendPurchaseInfo($email, $products, $first_name = "", $last_name = "") {
		
		$args_ary = array("content_authorization_request" => array("rest_token" => $this->api_key,
			'email'      => $email,
			'products'   => $products,
			'first_name' => $first_name,
			'last_name'  => $last_name
			));
		
		$postargs = http_build_query($args_ary);
		//echo var_dump(urldecode($postargs));
		if(!function_exists('curl_init')) {
			throw new Exception('CURL support is required');
		}
		
		$session = curl_init($this->base_url);
		curl_setopt($session, CURLOPT_POST, true);
		curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	
		$response = curl_exec($session);
		if($response===false) {
			throw new Exception('Error posting VS order to: '.$this->base_url);
		} 
		curl_close($session);
		
		return $response;
	}
	
	/**
	 * @param CoreCommerceOrder $order
	 * @param UserInfo $userInfo
	 * @return void
	*/
	public static function onPurchaseComplete($order, $userInfo) {
		if(is_object($order)) {
			$items = $order->getProducts();
			$skus = array();
			foreach ($items as $item) {
				$sku = $item->product->getAttribute('SKU');
				if(strlen($sku)) {
				 $skus[] = $sku;
				}
			}	
		} 
		if(count($skus)) {
			$vs = new VsPurchase();
			$email 		= $order->getOrderEmail();
			$first_name = $order->getAttribute('billing_first_name');
			$last_name 	= $order->getAttribute('billing_last_name');
			try {
				$xmlresp = $vs->sendPurchaseInfo($email, $skus, $first_name, $last_name);		
			} catch (Exception $e) {
				Log::addEntry($e->getMessage());
			}
			if(strlen($xmlresp)){
				$resp = simplexml_load_string($xmlresp);
				if($resp->response_ack == "ACK") {
					// store the string in the order attribute, additional parsing & foramtting can be done..
					$order->setAttribute('vs_order_response',htmlentities($xmlresp));					
				} else {
					Log::addEntry("An error occured".$xmlresp);
				}
			}
		}
	}
}

class vs_product_struct {
    public $sku, $title;
}
?>