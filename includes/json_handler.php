<?php
	
if ( !defined( "LBPATH" ) ) {

    header( 'HTTP/1.0 404 File Not Found', 404 );
    include 'views/404.php';
    exit();

}

class JsonHandler {

    protected static $_messages = array(
        JSON_ERROR_NONE => 'No error has occurred',
        JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => '<h1>JSON Syntax error</h1><ul><li>One or more properties may be missing a valid value.</li><li>Missing (or extra) comma.</li><li>Single quote (<code>\'\'</code>)  marks used instead of double quote (<code>" "</code>) marks.</li><li>Unmatched opening and closing curly brackets.</li></ul><p>Don\'t know what happened? <a href="http://jsonformatter.curiousconcept.com/" target="_blank">Validate your JSON</a>.</p>',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
    );


    public static function decode( $json, $assoc = false ) {

        $result = json_decode( $json, $assoc );

        if( $result ) {

            return $result;

        }

        throw new RuntimeException( static::$_messages[json_last_error()] );
    }

}
?>