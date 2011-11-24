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
	protected $_bitly_api = '';
	protected $_response;

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
		$this->_ci->config->load('bitly');

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
		$this->_response = '';
		foreach ($params as $key => $val)
		{
			$this->{'_'.$key} = (isset($this->{'_'.$key}) ? $val : $this->_ci->config->item($key));
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
	 * @return mixed
	 * @author Patrick Popowicz
	 */
	public function shorten($long_url)
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
				return ($this->_bitly_format != 'json') ? $this->_response : $this->_response['data']['url'];
			}
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Expands a bit.ly shortUrl or hash
	 *
	 * @param array $targets Target shortUrls or hashes to expand in numerically indexed array
	 * @return mixed
	 * @author Patrick Popowicz
	 */
	public function expand($targets = array())
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
				return (count($targets) == 1 && $this->_bitly_format == 'json') ? $this->_response['data']['expand'][0]['long_url'] : $this->_response;
			}
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Validates a 3rd party login and apiKey pair
	 *
	 * @param array $params 3rd party login and apiKey
	 * @return mixed
	 * @author Patrick Popowicz
	 */
	public function validate($params = array())
	{
		// Check the targets and build the params array
		if (count($params))
		{
			$params['format']	= $this->_bitly_format;

			if ($this->_execute('validate', $params))
			{
				// Determine what to return
				return ($this->_bitly_format == 'json') ? $this->_response['data']['valid'] : $this->_response;
			}
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Returns click information for one or more shortUrls or hashes
	 *
	 * @param array $targets Target shortUrls or hashes
	 * @param string $type type of click returned, either user (passed url or hash) or global
	 * @return mixed
	 * @author Patrick Popowicz
	 */
	public function clicks($targets = array(), $type = 'user')
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
				return (count($targets) == 1 && $this->_bitly_format == 'json') ? $this->_response['data']['clicks'][0][$type.'_clicks'] : $this->_response;
			}
		}
		return FALSE;
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Provides a list of referrers and click totals for a specific bit.ly shortUrl or hash
	 *
	 * @param string $target Target shortUrl or hash
	 * @return mixed
	 * @author Patrick Popowicz
	 */
	public function referrers($target = '')
	{
		// Check the targets and build the params array
		if (isset($target))
		{
			$url = parse_url($target);
			if ( ! isset($url['host']) && !preg_match('/.ly|.mp/i',$url['path']))
			{
				// Target is a hash
				$params['hash'][] = $target;
			}
			else
			{
				// Target is a shortUrl, make sure we have a full url
				$params['shortUrl'][] = (isset($url['scheme'])) ? $target : 'http://'.$target;
			}
			
			$params['format']	= ($this->_bitly_format == 'txt') ? 'json' : $this->_bitly_format;

			if ($this->_execute('referrers', $params))
			{
				// Determine what to return
				return ($this->_bitly_format == 'json') ? $this->_response['data']['referrers'] : $this->_response;
			}
		}
		return FALSE;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Provides a list of countries and click totals originating from that country for a specific bit.ly shortUrl or hash
	 *
	 * @param string $target Target shortUrl or hash
	 * @return mixed
	 * @author Patrick Popowicz
	 */
	public function countries($target = '')
	{
		// Check the targets and build the params array
		if (isset($target))
		{
			$url = parse_url($target);
			if ( ! isset($url['host']) && !preg_match('/.ly|.mp/i',$url['path']))
			{
				// Target is a hash
				$params['hash'][] = $target;
			}
			else
			{
				// Target is a shortUrl, make sure we have a full url
				$params['shortUrl'][] = (isset($url['scheme'])) ? $target : 'http://'.$target;
			}
			
			$params['format']	= ($this->_bitly_format == 'txt') ? 'json' : $this->_bitly_format;

			if ($this->_execute('countries', $params))
			{
				// Determine what to return
				return ($this->_bitly_format == 'json') ? $this->_response['data']['countries'] : $this->_response;
			}
		}
		return FALSE;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns click information for the last hour (by minute) for one or more shortUrls or hashes
	 *
	 * @param array $targets Target shortUrls or hashes
	 * @return mixed
	 * @author Patrick Popowicz
	 */
	public function clicks_by_minute($targets = array())
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

			$params['format']	= ($this->_bitly_format == 'txt') ? 'json' : $this->_bitly_format;

			if ($this->_execute('clicks_by_minute', $params))
			{
				// Determine what to return
				return (count($targets) == 1 && $this->_bitly_format == 'json') ? $this->_response['data']['clicks_by_minute'][0]['clicks'] : $this->_response;
			}
		}
		return FALSE;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Returns click information for the last n days for one or more shortUrls or hashes
	 *
	 * @param array $targets Target shortUrls or hashes
	 * @param int $days number of days from 1-30 to return data for
	 * @return mixed
	 * @author Patrick Popowicz
	 */
	public function clicks_by_day($targets = array(), $days = 7)
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
			
			$params['days']		= (is_numeric($days) && $days >= 1 && $days <= 30) ? $days : 7;
			$params['format']	= ($this->_bitly_format == 'txt') ? 'json' : $this->_bitly_format;

			if ($this->_execute('clicks_by_day', $params))
			{
				// Determine what to return
				return (count($targets) == 1 && $this->_bitly_format == 'json') ? $this->_response['data']['clicks_by_day'][0]['clicks'] : $this->_response;
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
			return ($verbose) ? $this->_response : (($this->_bitly_format == 'json') ? $this->_response['data']['bitly_pro_domain'] : $this->_response);
		}
		return FALSE;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Performs a lookup for existing shortUrls based on one or more URLs
	 *
	 * @param array $targets Target URLs
	 * @return mixed
	 * @author Patrick Popowicz
	 */
	public function lookup($targets = array())
	{
		// Check the targets and build the params array
		if (count($targets))
		{
			foreach ($targets as $key => $value)
			{
				$url = parse_url($value);
				$params['url'][] = (isset($url['scheme'])) ? trim($value) : trim('http://'.$value);
			}

			$params['format']	= ($this->_bitly_format == 'txt') ? 'json' : $this->_bitly_format;

			if ($this->_execute('lookup', $params))
			{
				// Determine what to return
				return (count($targets) == 1 && $this->_bitly_format == 'json') ? $this->_response['data']['lookup'][0]['short_url'] : $this->_response;
			}
		}
		return FALSE;
	}
	
	// ------------------------------------------------------------------------

	/**
	 * Retrieves title information on one or more bit.ly shortUrls or hashes
	 *
	 * @param array $targets Target shortUrls or hashes to retrieve information in numerically indexed array
	 * @return mixed
	 * @author Patrick Popowicz
	 */
	public function info($targets = array())
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

			if ($this->_execute('info', $params))
			{
				// Determine what to return
				return (count($targets) == 1 && $this->_bitly_format == 'json') ? $this->_response['data']['info'][0]['title'] : $this->_response;
			}
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
		$target = $this->_bitly_api . $method . '?';

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

		if ($_response = curl_exec($curl))
		{
			$this->_response = ($this->_bitly_format == 'json') ? json_decode($_response, TRUE) : $_response;
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
		return $this->_response;
	}
}

/* End of file Bitly.php */
/* Location: ./application/libraries/Bitly.php */