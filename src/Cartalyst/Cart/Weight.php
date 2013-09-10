<?php namespace Cartalyst\Cart;
/**
 * Part of the Cart package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Cart
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

class Weight {

	/**
	 * Weight value.
	 *
	 * @var float
	 */
	protected $value;

	/**
	 * The available weights.
	 *
	 * @var array
	 */
	protected $weights = array(
		'kg' => array(
			'label'  => 'Kilogram',
			'value'  => 1.00000000,
			'format' => '{value} kg'
		),
		'g' => array(
			'label'  => 'Gram',
			'value'  => 1000.00000000,
			'format' => '{value} g'
		),
		'lb' => array(
			'label'  => 'Pound',
			'value'  => 2.20460000,
			'format' => '{value} lb'
		),
		'oz' => array(
			'label'  => 'Ounce',
			'value'  => 35.27400000,
			'format' => '{value} oz'
		),
	);

	/**
	 * Set the value to be converted to.
	 *
	 * @param  float  $value
	 * @return \Cartalyst\Cart\Weight
	 */
	public function value($value)
	{
		$this->value = $value;

		return $this;
	}

	/**
	 * Return the value.
	 *
	 * @return float
	 */
	public function getValue()
	{
		return $this->value;
	}

	public function convert($from, $to)
	{
		// Get the value
		$value = $this->getValue();

		if ($from == $to)
		{
			return $value;
		}

		$from = ! empty($this->weights[$from]) ? $this->weights[$from]['value'] : 1;

		$to = ! empty($this->weights[$to]) ? $this->weights[$to]['value'] : 1;

		$this->value = $value * ($to / $from);

		return $this;
	}

	public function format($weight, $decimal = '.', $thousand = ',')
	{
		// Get the value
		$value = $this->getValue();

		// Get the weight format information
		$data = $this->getWeight($weight);

		// Format the value
		$value = number_format($value, 2, $decimal, $thousand);

		return str_replace('{value}', $value, $data['format']);
	}

	/**
	 * Return the list of available weights.
	 *
	 * @return array
	 */
	public function getWeights()
	{
		return $this->weights;
	}

	/**
	 * Set weights.
	 *
	 * By default it will merge the new weights with the current
	 * weights, you can change this behavior by setting false
	 * as the second parameter.
	 *
	 * @param  array  $weights
	 * @param  bool   $merge
	 * @return array
	 */
	public function setWeights($weights = array(), $merge = true)
	{
		$weights = (array) $weights;

		$currentWeights = $merge ? $this->getWeights() : array();

		return $this->weights = array_merge($currentWeights, $weights);
	}

	/**
	 * Return information about the provided weight.
	 *
	 * @param  string  $weight
	 * @return array
	 */
	public function getWeight($weight)
	{
		$weights = $this->getWeights();

		$weight = strtolower($weight);

		if ( ! array_key_exists($weight, $weights))
		{
			throw new Exception("Weight [{$weight}] was not found.");
		}

		return $weights[$weight];
	}

}
