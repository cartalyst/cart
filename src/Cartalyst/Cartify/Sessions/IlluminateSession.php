<?php namespace Cartalyst\Cartify\Sessions;

use Illuminate\Session\Store as SessionStore;

class IlluminateSession implements SessionInterface {

	/**
	 * The key used in the Session.
	 *
	 * @var string
	 */
	protected $key = 'cartify_session';

	/**
	 * Session store object.
	 *
	 * @var Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Creates a new Illuminate based Session driver for Cartify.
	 *
	 * @param  Illuminate\Session\Store  $session
	 * @param  string  $key
	 * @return void
	 */
	public function __construct(SessionStore $session, $key = null)
	{
		$this->session = $session;

		if (isset($key))
		{
			$this->key = $key;
		}
	}

	/**
	 * Returns the session key.
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Put a value in the session.
	 *
	 * @param  mixed  $value
	 * @return void
	 */
	public function put($value)
	{
		$this->session->put($this->getKey(), $value);
	}

	/**
	 * Get the session value.
	 *
	 * @return mixed
	 */
	public function get()
	{
		return $this->session->get($this->getKey());
	}

	/**
	 * Remove the session.
	 *
	 * @return void
	 */
	public function forget()
	{
		$this->session->forget($this->getKey());
	}

}
