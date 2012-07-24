<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* bit.ly REST API v3 library for CodeIgniter
*
* @license Creative Commons Attribution 3.0 <http://creativecommons.org/licenses/by/3.0/>
* @version 1.0
* @author Patrick Popowicz <http://patrickpopowicz.com>
* @copyright Copyright (c) 2010-2011, Patrick Popowicz <http://patrickpopowicz.com>
*/

/*
|--------------------------------------------------------------------------
| bit.ly API Target Url
|--------------------------------------------------------------------------
|
| URL target for the bit.ly API.
|
*/
$config['bitly_api']		= "http://api.bitly.com/v3/";

/*
|--------------------------------------------------------------------------
| bit.ly Login Name
|--------------------------------------------------------------------------
|
| Login name used for your bit.ly account.
|
*/
$config['bitly_login']		= "";

/*
|--------------------------------------------------------------------------
| bit.ly API Key
|--------------------------------------------------------------------------
|
| API key provided by bit.ly after logging in.
|
| Can be found at http://bit.ly/a/your_api_key.
|
*/
$config['bitly_apikey']		= "";

/*
|--------------------------------------------------------------------------
| bit.ly X_login Name
|--------------------------------------------------------------------------
|
| External login name used when you are accessing the API on behalf of
| a different user.
|
*/
$config['bitly_x_login']	= "";

/*
|--------------------------------------------------------------------------
| bit.ly X_API Key
|--------------------------------------------------------------------------
|
| External API key used when you are accessing the API on behalf of
| a different user.
|
*/
$config['bitly_x_apiKey']	= "";

/*
|--------------------------------------------------------------------------
| bit.ly Response Format
|--------------------------------------------------------------------------
|
| Data format of the expected response.
|
| Supported formats ar:
|	* json (default)
|	* xml
|	* txt
|
*/
$config['bitly_format']		= "json";

/*
|--------------------------------------------------------------------------
| bit.ly Domain
|--------------------------------------------------------------------------
|
| Specifies the domain used for Shorten requests. Will change the output
| of the shortened URL.
|
| Supported formats ar:
|	* bit.ly (default)
|	* j.mp
|
*/
$config['bitly_domain']		= "bit.ly";


/* End of file bitly.php */
/* Location: ./application/config/bitly.php */