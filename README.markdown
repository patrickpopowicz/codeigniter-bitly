bit.ly Library for CodeIgniter
====================

Installation
------------

1. Add config/bitly.php file to your application/config folder.
2. Add libraries/Bitly.php file into your application/libraries folder.

Requirements
------------

1. CodeIgniter
2. bit.ly login, `http://bit.ly/a/sign_up`
3. bit.ly apiKey, `http://bit.ly/a/your_api_key`

Initializing the Library
------------------------

Values used by the API can be set one of three ways

* by entering the values in the library's config file (i.e. config/bitly.php)
* by passing the values in an array when loading the library (i.e. $this->load->library('bitly', $params))
* by manually overriding prior values by passing an array to the set_config() method

#### set_config( array *$params* )

`$params = array(
				'bitly_login' => 'login',
				'bitly_apiKey' => 'apiKey',
				'bitly_x_login' => '',
				'bitly_x_apiKey' => '',
				'bitly_format' => 'json',
				'bitly_domain' => 'bit.ly'
				);
$this->bitly->set_config($params));`

API Methods
-----------

## Shorten

shorten( string *$long_url*)

#### Usage

`if ($result = $this->bitly->shorten('http://www.example.com'))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**long_url : The URL that needs to be shortened**

#### Return Values
* JSON - The returned value will be the new bit.ly short URL. XML will return the entire XML response without parsing. TXT will return the bit.ly short URL.
* XML - The returned value will be the entire response string.
* TXT - The returned value will be the new bit.ly short URL.

## Expand

expand( array *$targets* )

#### Usage

`if ($result = $this->bitly->expand(array('http://bit.ly/bldm16', 'bldm16')))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**targets : The bit.ly shortUrls or hashes that need to be expanded**

#### Return Values
* JSON - The returned value will be the long URL if there is only one URL or hash. If more than one URL or hash are passed, then the returned value is the `json_decoded()` response.
* XML - The returned value will be the entire response string.
* TXT - The returned value will be the long URL.

## Validate

validate( array *$params* )

#### Usage

`if ($result = $this->bitly->validate(array('x_login' => 'notbilytapi', 'x_apiKey' => 'not_apikey')))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**params : x\_login and x\_apiKey to be checked, passed as an array, if not set, config defaults will be used**

#### Return Values
* JSON - boolean TRUE or FALSE.
* XML - The returned value will be the entire response string.
* TXT - boolean TRUE or FALSE.

## Clicks

clicks( array *$targets*, [ string *$type = 'user'* ] )

#### Usage

`if ($result = $this->bitly->clicks(array('http://bit.ly/bldm16', 'bldm16')))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**targets : The bit.ly shortUrls or hashes to get the click information for**

**type : User will return the number of clicks for the particular user's hash, Global will return the total number of clicks for the URL**

#### Return Values
* JSON - The returned value will be the clicks if there is only one URL or hash. If more than one URL or hash are passed, then the returned value is the `json_decoded()` response.
* XML - The returned value will be the entire response string.
* TXT - The returned value will be the clicks.

## Referrers

referrers( string *$target* )

#### Usage

`if ($result = $this->bitly->referrers('http://bit.ly/bldm16')
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**target : The bit.ly shortUrl or hash to get the referrer information for**

#### Return Values
* JSON - The returned value will be the referrers listed for the URL or hash.
* XML - The returned value will be the entire response string.

##### Note: 'txt' is not a supported format for this endpoint, if the default or passed format is 'txt', it will be changed to 'json'.

## Countries

countries( string *$target* )

#### Usage

`if ($result = $this->bitly->countries('http://bit.ly/bldm16')
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**target : The bit.ly shortUrl or hash to get the country information for**

#### Return Values
* JSON - The returned value will be the countries listed for the URL or hash.
* XML - The returned value will be the entire response string.

##### Note: 'txt' is not a supported format for this endpoint, if the default or passed format is 'txt', it will be changed to 'json'.

## Clicks by Minute

clicks\_by\_minute( array *$targets* )

#### Usage

`if ($result = $this->bitly->clicks_by_minute(array('http://bit.ly/bldm16', 'bldm16')))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**targets : The bit.ly shortUrls or hashes to get the click information for**

#### Return Values
* JSON - The returned value will be the clicks if there is only one URL or hash. If more than one URL or hash are passed, then the returned value is the `json_decoded()` response.
* XML - The returned value will be the entire response string.

##### Note: 'txt' is not a supported format for this endpoint, if the default or passed format is 'txt', it will be changed to 'json'.

## Clicks by Day

clicks\_by\_day( array *$targets*, [ int *$days = 7* ] )

#### Usage

`if ($result = $this->bitly->clicks_by_minute(array('http://bit.ly/bldm16', 'bldm16')))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**targets : The bit.ly shortUrls or hashes to get the click information for**

**days : The number of days of data that should be returned per URL or hash, limited from 1-30**

#### Return Values
* JSON - The returned value will be the clicks if there is only one URL or hash. If more than one URL or hash are passed, then the returned value is the `json_decoded()` response.
* XML - The returned value will be the entire response string.

##### Note: 'txt' is not a supported format for this endpoint, if the default or passed format is 'txt', it will be changed to 'json'.

## bit.ly Pro Domain

pro_domain( string *$domain* )

#### Usage

`if ($result = $this->bitly->pro_domain(array('x_login' => 'notbilytapi', 'x_apiKey' => 'not_apikey')))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**domain : Short domain to check**

#### Return Values
* JSON - boolean TRUE or FALSE.
* XML - The returned value will be the entire response string.
* TXT - boolean TRUE or FALSE.

## Lookup

lookup( array *$targets* )

#### Usage

`if ($result = $this->bitly->lookup(array('http://example.com', 'example.com')))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**targets : The URLs that need to be looked up**

#### Return Values
* JSON - The returned value will be the shortURL if there is only one URL. If more than one URL is passed, then the returned value is the `json_decoded()` response.
* XML - The returned value will be the entire response string.

##### Notes: The HTTP scheme is automatically added onto a URL that has no scheme, as bit.ly always adds this when they shorten links. Also, while bit.ly will shorten a URL without a trailing slash, passing a URL with or without a trailing slash for Lookup may produce varied results. 'txt' is not a supported format for this endpoint, if the default or passed format is 'txt', it will be changed to 'json'.

## Info

info( array *$targets* )

#### Usage

`if ($result = $this->bitly->info(array('http://bit.ly/bldm16', 'bldm16')))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**targets : The bit.ly shortUrls or hashes to be queried**

#### Return Values
* JSON - The returned value will be the title of the website if there is only one URL or hash. If more than one URL or hash are passed, then the returned value is the `json_decoded()` response.
* XML - The returned value will be the entire response string.
* TXT - The returned value will be the title of the website.

### Note: bit.ly API Response
Depending on the return format, the return value will change. If a value is not correctly set, will return FALSE. After a call is executed, the full response text can be accessed by calling `response()`.

For more information on the API, see `http://code.google.com/p/bitly-api/wiki/ApiDocumentation`.

#### Changes

2011-11-23

* Moved API target from library to config file
* Updated library with all additional functionality from API after original commit in April 2010 (see Revision history of API for details).
* Removed the 'verbose' flag from all methods. Verbose output (i.e. the full response) can be accessed by calling `response()`.