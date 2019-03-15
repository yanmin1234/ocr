<?php
/**
 * Error defination.
 */
namespace QcloudImage;

class Error {

    /**
     * Create reusable signature.
     * This signature will expire at time()+$howlong timestamp.
     * Return the signature on success.
	 * Return false on fail.
     */
    public static function json($code, $message, $httpcode = 0) {
        echo  $message;
        $array = array(
            'code' => $code,
            'message' => utf8_encode($message), //避免非 UTF - 8 导致 $json_encode 为 null
            'httpcode' => $httpcode,
            'data' => json_decode('{}', true)
        );
        
        $json_encode = json_encode($array);
        
        switch (json_last_error()) {//如果有json异常则打印
            case JSON_ERROR_NONE:
                // echo 'JSON_ERROR - No errors';
                break;
            case JSON_ERROR_DEPTH:
                echo 'JSON_ERROR - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                echo 'JSON_ERROR - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                echo 'JSON_ERROR - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                echo 'JSON_ERROR - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                echo 'JSON_ERROR - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                echo 'JSON_ERROR - Unknown error';
                break;
        }

        return $json_encode;
    }
		
	public static $Param = -1;
	public static $Network = -2;
	public static $FilePath = -3;
	public static $Unknown = -4;
}
