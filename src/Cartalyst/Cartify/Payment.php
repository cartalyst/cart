<?php namespace Cartalyst\Cartify;

use Omnipay\Common\GatewayFactory;

class Payment {

	/**
	 *
	 *
	 * @param  string  $gateway
	 * @return
	 */
	public function setGateway($gateway)
	{
		return GatewayFactory::create($gateway);
	}

}
