<?php
use HelpScoutApp\DynamicApp;

class Helpscout_Module extends Core_ModuleBase {

	private $helpscout_api_key = '';

	protected function createModuleInfo() {
		return new Core_ModuleInfo(
			"Helpscout",
			"adds sidebar functionality to Helpscout",
			"Simon Stevens (@snetty)"
		);
	}

	public function register_access_points() {
		return array(
			'helpscout_callback' => 'helpscout_callback',
		);
	}

	public function helpscout_callback() {
		$app = new DynamicApp($this->helpscout_api_key);
		if ($app->isSignatureValid()) {
			$customer = Shop_Customer::create()->find_by_email($app->getCustomer()->getEmail());
			if (!$customer) {
				$html = '<ul class="unstyled"><li>No customer found for <strong>' . $app->getCustomer()->getEmail() . '</strong></li></ul>';
			} else {
				$paid_orders = new Db_DataCollection();
				foreach ($customer->orders as $order) {
					if ($order->is_paid()) {
						$paid_orders[] = $order;
					}
				}
				$html = '
			    <h4><a href="' . site_url('/backdoor/shop/customers/preview/' . $customer->id) . '">' . h($customer->full_name) . '</a></h4>
		        <ul>
		            <li>&pound;' . array_sum($paid_orders->as_array('total')) . ' lifetime value</li>
		            <li>Customer since ' . h($customer->created_at->format('%x')) . '</li>
		            <li>' . $customer->orders->count() . ' orders (' . count($paid_orders) . ' paid)</li>
		        </ul>
			    <h4>Recent Orders (Showing ' . $customer->orders_list->limit(10)->find_all()->count() . ' of ' . $customer->orders->count() . ')</h4>
		        <ul class="unstyled">';
				foreach ($customer->orders_list->limit(10)->find_all() as $order) {
					$html .= '<li><span class="' . ($order->is_paid() ? 'green' : 'red') . '">' . $order->order_datetime->format('%x') . '</span> - &pound;' . $order->total . ' (<a href="' . site_url('/backdoor/shop/orders/preview/' . $order->id) . '">#' . $order->id . '</a>)</li>';
				}
				$html .= '</ul>';
			}
			echo $app->getResponse($html);
		} else {
			echo 'Invalid Request';
		}
	}
}