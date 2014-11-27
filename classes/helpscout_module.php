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
			try {
				$customer = Shop_Customer::create()->find_by_email($app->getCustomer()->getEmail());
				if (!$customer) {
					throw new Exception('Customer not found for <strong>' . $app->getCustomer()->getEmail() . '</strong>');
				}

				$paid_orders = new Db_DataCollection();
				foreach ($customer->orders as $order) {
					if ($order->is_paid()) {
						$paid_orders[] = $order;
					}
				}

				$filename = PATH_APP . '/modules/helpscout/partials/helpscout.htm';
				ob_start();
				include $filename;
				$html = ob_get_contents();
				ob_end_clean();
			} catch (Exception $e) {
				ob_end_clean();
				$html = '<ul class="unstyled"><li>' . $e->getMessage() . '</li></ul>';
			}
			echo $app->getResponse($html);
		} else {
			echo 'Invalid Request';
		}
	}
}