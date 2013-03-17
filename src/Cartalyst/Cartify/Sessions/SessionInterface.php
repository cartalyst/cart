<?php namespace Cartalyst\Cartify\Sessions;

interface SessionInterface {

	/**
	 * Returns the session key.
	 *
	 * @return string
	 */
	public function getKey();

	/**
	 * Put a value in the Cartify session.
	 *
	 * @param  mixed   $value
	 * @return void
	 */
	public function put($value);

	/**
	 * Get the Cartify session value.
	 *
	 * @return mixed
	 */
	public function get();

	/**
	 * Remove the Cartify session.
	 *
	 * @return void
	 */
	public function forget();

}
