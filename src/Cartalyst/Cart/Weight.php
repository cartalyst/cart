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

	public function convert($value, $from, $to)
	{
		if ($from == $to)
		{
			return $value;
		}

		$from = ! empty($this->weights[$from]) ? $this->weights[$from]['value'] : 1;

		$to = ! empty($this->weights[$to]) ? $this->weights[$to]['value'] : 1;

		return $value * ($to / $from);
	}

	public function format($value, $weight_class_id, $decimal_point = '.', $thousand_point = ',')
	{
		$value = number_format($value, 2, $decimal_point, $thousand_point);

		if ( ! empty($this->weights[$weight_class_id]))
		{
			return str_replace('{value}', $value, $this->weights[$weight_class_id]['format']);
		}

		return $value;
	}

}
