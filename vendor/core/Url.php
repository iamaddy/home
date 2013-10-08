<?php
/**
 * Project:     DDClick Project
 * File:        Url.php
 *
 * URL管理类
 *
 */

class Url {
	/**
     * Base URL of the application.
     *
     * @var string
     */
	private static $baseUrl;

	/**
     * Actual request URL, independent of the platform.
     *
     * @var string
     */
	private static $requestUrl;

	/**
	 * Actual request URL PATH, independent of the platform.
	 *
	 * @var string
	 */
	private static $requestPath;

	/**
	 * Set the base URL.
	 *
	 * @param  string $baseUrl
	 * @return self
	 */
	public static function setBaseUrl($baseUrl)
	{
		self::$baseUrl = rtrim($baseUrl, '/');
	}

	/**
	 * Get the base URL.
	 *
	 * @return string
	 */
	public static function getBaseUrl()
	{
		if (self::$baseUrl === null) {
			self::setBaseUrl(self::detectBaseUrl());
		}
		return self::$baseUrl;
	}

	/**
	 * Auto-detect the base path from the request environment
	 *
	 * Uses a variety of criteria in order to detect the base URL of the request
	 * (i.e., anything additional to the document root).
	 *
	 * The base URL includes the schema, host, and port, in addition to the path.
	 *
	 * @return string
	 */
	private static function detectBaseUrl() {
		$baseUrl        = '';
		$filename       = isset($_SERVER['SCRIPT_FILENAME'])? $_SERVER['SCRIPT_FILENAME']: '';
		$scriptName     = isset($_SERVER['SCRIPT_NAME'])? $_SERVER['SCRIPT_NAME']: '';
		$phpSelf        = isset($_SERVER['PHP_SELF'])? $_SERVER['PHP_SELF']: '';
		$origScriptName = isset($_SERVER['ORIG_SCRIPT_NAME'])? $_SERVER['ORIG_SCRIPT_NAME']: '';

		if ($scriptName !== null && basename($scriptName) === $filename) {
			$baseUrl = $scriptName;
		} elseif ($phpSelf !== null && basename($phpSelf) === $filename) {
			$baseUrl = $phpSelf;
		} elseif ($origScriptName !== null && basename($origScriptName) === $filename) {
			// 1and1 shared hosting compatibility.
			$baseUrl = $origScriptName;
		} else {
			// Backtrack up the SCRIPT_FILENAME to find the portion
			// matching PHP_SELF.

			$baseUrl  = '/';
			$basename = basename($filename);
			if ($basename) {
				$path     = ($phpSelf ? trim($phpSelf, '/') : '');
				$baseUrl .= substr($path, 0, strpos($path, $basename)) . $basename;
			}
		}

		// Does the base URL have anything in common with the request URI?
		$requestUri = self::detectRequestUrl();

		// Full base URL matches.
		if (0 === strpos($requestUri, $baseUrl)) {
			return $baseUrl;
		}

		// Directory portion of base path matches.
		$baseDir = str_replace('\\', '/', dirname($baseUrl));
		if (0 === strpos($requestUri, $baseDir)) {
			return $baseDir;
		}

		$truncatedRequestUri = $requestUri;

		if (false !== ($pos = strpos($requestUri, '?'))) {
			$truncatedRequestUri = substr($requestUri, 0, $pos);
		}

		$basename = basename($baseUrl);

		// No match whatsoever
		if (empty($basename) || false === strpos($truncatedRequestUri, $basename)) {
			return '';
		}

		// If using mod_rewrite or ISAPI_Rewrite strip the script filename
		// out of the base path. $pos !== 0 makes sure it is not matching a
		// value from PATH_INFO or QUERY_STRING.
		if (strlen($requestUri) >= strlen($baseUrl)
				&& (false !== ($pos = strpos($requestUri, $baseUrl)) && $pos !== 0)
		) {
			$baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
		}

		return $baseUrl;
	}

	/**
	 * Set the base URL.
	 *
	 * @param  string $baseUrl
	 * @return self
	 */
	public static function setRequestUrl($requestUrl)
	{
		self::$requestUrl = rtrim($requestUrl, '/');
	}

	/**
	 * Get the base URL.
	 *
	 * @return string
	 */
	public static function getRequestUrl()
	{
		if (self::$requestUrl === null) {
			self::setRequestUrl(self::detectRequestUrl());
		}
		return self::$requestUrl;
	}

	/**
	 * Detect the base URI for the request
	 *
	 * Looks at a variety of criteria in order to attempt to autodetect a base
	 * URI, including rewrite URIs, proxy URIs, etc.
	 *
	 * @return string
	 */
	private static function detectRequestUrl() {
		$requestUrl = null;

		// Check this first so IIS will catch.
		$httpXRewriteUrl = isset($_SERVER['HTTP_X_REWRITE_URL'])? $_SERVER['HTTP_X_REWRITE_URL']: '';
		if ($httpXRewriteUrl !== null) {
			$requestUrl = $httpXRewriteUrl;
		}

		// Check for IIS 7.0 or later with ISAPI_Rewrite
		$httpXOriginalUrl = isset($_SERVER['HTTP_X_ORIGINAL_URL'])? $_SERVER['HTTP_X_ORIGINAL_URL']: '';
		if ($httpXOriginalUrl !== null) {
			$requestUrl = $httpXOriginalUrl;
		}

		// IIS7 with URL Rewrite: make sure we get the unencoded url
		// (double slash problem).
		$iisUrlRewritten = isset($_SERVER['IIS_WasUrlRewritten'])? $_SERVER['IIS_WasUrlRewritten']: '';
		$unencodedUrl    = isset($_SERVER['UNENCODED_URL'])? $_SERVER['UNENCODED_URL']: '';
		if ('1' == $iisUrlRewritten && '' !== $unencodedUrl) {
			return $unencodedUrl;
		}

		// HTTP proxy requests setup request URI with scheme and host [and port]
		// + the URL path, only use URL path.
		if (!$httpXRewriteUrl) {
			$requestUrl = isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI']: '';
		}

		if ($requestUrl !== null) {
			return preg_replace('#^[^:]+://[^/]+#', '', $requestUrl);
		}

		// IIS 5.0, PHP as CGI.
		$origPathInfo = isset($_SERVER['ORIG_PATH_INFO'])? $_SERVER['ORIG_PATH_INFO']: '';
		if ($origPathInfo !== null) {
			$queryString = isset($_SERVER['QUERY_STRING'])? $_SERVER['QUERY_STRING']: '';
			if ($queryString !== '') {
				$origPathInfo .= '?' . $queryString;
			}
			return $origPathInfo;
		}

		return '/';
	}

	/**
	 * Set the base URL.
	 *
	 * @param  string $baseUrl
	 * @return self
	 */
	public static function setRequestPath($requestPath)
	{
		self::$requestPath = trim($requestPath, '/');
	}

	/**
	 * Get the base URL.
	 *
	 * @return string
	 */
	public static function getRequestPath()
	{
		if (self::$requestPath === null) {
			self::setRequestPath(self::detectRequestPath());
		}
		return self::$requestPath;
	}

	private static function detectRequestPath() {
		$baseUrl    = self::getBaseUrl();
		$requestUrl = self::getRequestUrl();

		$requestPath = str_replace($baseUrl, '', $requestUrl);
		$arr = parse_url($requestPath);
		if(is_array($arr) && isset($arr['path'])) {
			return $arr['path'];
		}
		return 'error';
	}
}
