package sooh.fastCGI;

public final class Args {
	public static final String REQUEST_METHOD = "REQUEST_METHOD";
	public static final String SCRIPT_FILENAME = "SCRIPT_FILENAME";// /var/www/xxx/yy.php
	public static final String QUERY_STRING = "QUERY_STRING";// a=1&b=URLEncoder.encode("中文", "utf-8")
	public static final String GATEWAY_INTERFACE="GATEWAY_INTERFACE"; //FastCGI/1.0
	public static final String SERVER_NAME="SERVER_NAME"; //php_uname('n')
	public static final String CONTENT_TYPE="CONTENT_TYPE"; // 'application/x-www-form-urlencoded'
	public static final String CONTENT_LENGTH="CONTENT_LENGTH";
	
}