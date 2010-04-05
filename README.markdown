bit.ly Library for CodeIgniter
====================

Installation
------------

1. Add config/bitly.php file to your application/config folder.
2. Add libraries/Bitly.php file into your application/libraries folder.

Requirements
------------

1. CodeIgniter
2. bit.ly login, `http://bit.ly/account/register`
3. bit.ly apiKey, `http://bit.ly/account/your_api_key`

Methods
-------

### Initialize

initialize( array *$params* )

#### Usage
`$params = array(
				'bitly_login' => 'login',
				'bitly_apiKey' => 'apiKey',
				'bitly_x_login' => '',
				'bitly_x_apiKey' => '',
				'bitly_format' => 'json',
				'bitly_domain' => 'bit.ly'
				);
$this->bitly->initialize($params));`

#### Parameters

**params : Values used by the API, normally set in the config file.**

#### Return Values
None

### Shorten

shorten( string *$longUrl*, [ bool *$verbose* = FALSE ] )

#### Usage

`if ($result = $this->bitly->shorten('http://www.example.com'))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**longUrl : The URL that needs to be shortened**

**verbose : Output flag**

#### Return Values
* JSON - The returned value will be the new bit.ly short URL. XML will return the entire XML response without parsing. TXT will return the bit.ly short URL.
* XML - The returned value will be the entire response string.
* TXT - The returned value will be the new bit.ly short URL.

### Expand

expand( array *$targets*, [ bool *$verbose* = FALSE ] )

#### Usage

`if ($result = $this->bitly->expand(array('http://bit.ly/bldm16', 'bldm16')))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**targets : The bit.ly shortUrls or hashes that need to be expanded**

**verbose : Output flag**

#### Return Values
* JSON - The returned value will be the long URL if there is only one URL or hash. If more than one URL or hash are passed, then the returned value is the `json_decoded()` response.
* XML - The returned value will be the entire response string.
* TXT - The returned value will be the long URL.

### Validate

validate( array *$params*, [ bool *$verbose* = FALSE ] )

#### Usage

`if ($result = $this->bitly->validate(array('x_login' => 'notbilytapi', 'x_apiKey' => 'not_apikey')))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**params : x\_login and x\_apiKey to be checked, passed as an array, if not set, config defaults will be used**

**verbose : Output flag**

#### Return Values
* JSON - boolean TRUE or FALSE.
* XML - The returned value will be the entire response string.
* TXT - boolean TRUE or FALSE.

### Clicks

clicks( array *$targets*, [ string *$type = 'user'* [ bool *$verbose* = FALSE ] ] )

#### Usage

`if ($result = $this->bitly->clicks(array('http://bit.ly/bldm16', 'bldm16')))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**targets : The bit.ly shortUrls or hashes to get the click information for**

**type : User will return the number of clicks for the particular user's hash, Global will return the total number of clicks for the URL**

**verbose : Output flag**

#### Return Values
* JSON - The returned value will be the clicks if there is only one URL or hash. If more than one URL or hash are passed, then the returned value is the `json_decoded()` response.
* XML - The returned value will be the entire response string.
* TXT - The returned value will be the clicks.

### bit.ly Pro Domain


pro_domain( string *$domain*, [ bool *$verbose* = FALSE ] )

#### Usage

`if ($result = $this->bitly->pro_domain(array('x_login' => 'notbilytapi', 'x_apiKey' => 'not_apikey')))
{
	// Parameters correctly set, $result will be whatever the API returns
}`

#### Parameters

**domain : Short domain to check**

**verbose : Output flag**

#### Return Values
* JSON - boolean TRUE or FALSE.
* XML - The returned value will be the entire response string.
* TXT - boolean TRUE or FALSE.

### Note: bit.ly API Response and the *verbose* flag
Depending on the return format, the return value will change. If a value is not correctly set, will return FALSE. After a call is executed, the full response text can be accessed by calling `get_response()` if the verbose flag was not set.

For more information on the API, see `http://code.google.com/p/bitly-api/wiki/ApiDocumentation`.