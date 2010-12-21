<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * bit.ly REST API v3 library for CodeIgniter
 *
 * @license Creative Commons Attribution 3.0 <http://creativecommons.org/licenses/by/3.0/>
 * @version 1.0
 * @author Patrick Popowicz <http://patrickpopowicz.com>
 * @author Eric Barnes <http://ericlbarnes.com>
 * @copyright Copyright (c) 2010, Patrick Popowicz <http://patrickpopowicz.com>
 */
class Bitly {

	protected $_ci;
	protected $_bitly_login = '';
	protected $_bitly_apikey = '';
	protected $_bitly_x_login = '';
	protected $_bitly_x_apikey = '';
	protected $_bitly_format = '';
	protected $_bitly_domain = '';
	protected $_api = "http://api.bit.ly/v3/";	// bit.ly API target URL
	public $response;

	// ------------------------------------------------------------------------

	/**
	 * Construct
	 *
	 * Setup bitly lib
	 *
	 * @param	array
	 * @return 	void
	 */
	function __construct($params = array())
	{
		$this->_ci =& get_instance();

		log_message('debug', 'bit.ly Class Initialized');

		$this->_initialize($params);
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @param	array
	 * @return	void
	 */
	public function _initialize($params = array())
	{
		$this->response = '';
		foreach ($params as $key => $val)
		{
			$this->_{$key} = (isset($this->_{$key}) ? $val : $this->_ci->config->item($key));
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Manually Set Config
	 *
	 * Pass an array of config vars to override previous setup
	 *
	 * @param	array
	 * @return 	void
	 */
	public function set_config($config = array())
	{
		if ( ! empty($config))
		{
			$this->_initialize($config);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Shortens a URL
	 *
	 * @param string $longUrl Target url to be shortened
	 * @param bool $verbose Output flag
	 * @return mixed (!verbose) string | (verbose) array
	 * @author Patrick Popowicz
	 */
	public function shorten($long_url, $verbose = FALSE)
	{
		// Make sure all of the required parameters are set and longUrl is a URL
		if (isset($long_url) AND filter_var($long_url, FILTER_VALIDATE_URL))
		{
			$params = array(
				'longUrl'	=>	trim($long_url),
				'format'	=>	$this->_bitly_format,
				'domain'	=>	$this->_bitly_domain
			);

			if ($this->_bitly_x_login && $this->_bitly_x_apikey)
			{
				$params['x_login']	= $this->_bitly_x_login;
				$params['x_apiKey']	= $this->_bitly_x_apikey;
			}

			if ($this->_execute('shorten', $params))
			{
				return ($verbose || $this->_bitly_format != 'json') ? $this->response : $this->response['data']['url'];
			}
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Expands a bit.ly shortUrl or hash
	 *
	 * @param array $targets Target shortUrls or hashes to expand in numerically indexed array
	 * @param bool $verbose Output flag
	 * @return mixed (format != json) string | (format == json) array
	 * @author Patrick Popowicz
	 */
	public function expand($targets = array(), $verbose = FALSE)
	{
		// Check the targets and build the params array
		if (count($targets))
		{
			foreach ($targets as $key => $value)
			{
				$url = parse_url($value);
				if ( ! isset($url['host']) && !preg_match('/.ly|.mp/i',$url['path']))
				{
					// Target is a hash
					$params['hash'][] = $value;
				}
				else
				{
					// Target is a shortUrl, make sure we have a full url
					$params['shortUrl'][] = (isset($url['scheme'])) ? $value : 'http://'.$value;
				}
			}

			$params['format']	= $this->_bitly_format;

			if ($this->_execute('expand', $params))
			{
				// Determine what to return
				return ($verbose) ? $this->response : ((count($targets) == 1 && $this->_bitly_format == 'json') ? $this->response['data']['expand'][0]['long_url'] : $this->response);
			}
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Validates a 3rd party login and apiKey pair
	 *
	 * @param array $params 3rd party login and apiKey
	 * @return mixed (format != json || txt) string | (format == json || txt) bool
	 * @author Patrick Popowicz
	 */
	public function validate($params = array(), $verbose = FALSE)
	{
		// Check the targets and build the params array
		if (count($params))
		{
			$params['format']	= $this->_bitly_format;

			if ($this->_execute('validate', $params))
			{
				// Determine what to return
				return ($verbose) ? $this->response : (($this->_bitly_format == 'json') ? $this->response['data']['valid'] : $this->response);
			}
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Returns click information for one or more shortUrls or hashes
	 *
	 * @param array $targets Target shortUrls or hashes to expand in numerically indexed array
	 * @param string $type type of click returned, either user (passed url or hash) or global
	 * @param bool $verbose Output flag
	 * @return mixed (format != json) string | (format == json) array
	 * @author Patrick Popowicz
	 */
	public function clicks($targets = array(), $type = 'user', $verbose = FALSE)
	{
		// Check the targets and build the params array
		if (count($targets))
		{
			foreach ($targets as $key => $value)
			{
				$url = parse_url($value);
				if ( ! isset($url['host']) && !preg_match('/.ly|.mp/i',$url['path']))
				{
					// Target is a hash
					$params['hash'][] = $value;
				}
				else
				{
					// Target is a shortUrl, make sure we have a full url
					$params['shortUrl'][] = (isset($url['scheme'])) ? $value : 'http://'.$value;
				}
			}

			$params['format']	= $this->_bitly_format;

			if ($this->_execute('clicks', $params))
			{
				// Determine what to return
				return ($verbose) ? $this->response : ((count($targets) == 1 && $this->_bitly_format == 'json') ? $this->response['data']['clicks'][0][$type.'_clicks'] : $this->response);
			}
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Validates a Pro Domain
	 *
	 * @param string bit.ly pro domain
	 * @return mixed (format != json) string | (format == json) bool
	 * @author Patrick Popowicz
	 */
	public function pro_domain($domain = '', $verbose = FALSE)
	{
		// Check the targets and build the params array
		$params['domain']	= $domain;
		$params['format']	= $this->_bitly_format;

		if ($this->_execute('bitly_pro_domain', $params))
		{
			// Determine what to return
			return ($verbose) ? $this->response : (($this->_bitly_format == 'json') ? $this->response['data']['bitly_pro_domain'] : $this->response);
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Executes the API request using cURL
	 *
	 * @param string $method API method being used
	 * @param bool $verbose Output flag
	 * @return bool
	 * @author Patrick Popowicz
	 */
	private function _execute($method, $params)
	{
		// Add in the primary login and apiKey
		$params = array_merge(array('login' => $this->_bitly_login, 'apiKey' => $this->_bitly_apikey), $params);

		// Create the argument string
		$target = $this->api . $method . '?';

		foreach ($params as $key => $value)
		{
			if ( ! is_array($value))
			{
				$target .= http_build_query(array($key => $value)) . '&';
			}
			else
			{
				foreach ($value as $sub)
				{
					$target .= http_build_query(array($key => $sub)) . '&';
				}
			}
		}

		// Use cURL to fetch
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl, CURLOPT_URL, $target);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		if ($response = curl_exec($curl))
		{
			$this->response = ($this->_bitly_format == 'json') ? json_decode($response, TRUE) : $response;
			return TRUE;
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	* Returns the response value
	*
	* @return mixed
	* @author Patrick Popowicz
	*/
	public function response()
	{
		return $this->response;
	}
}

/* End of file Bitly.php */
/* Location: ./application/libraries/Bitly.php */