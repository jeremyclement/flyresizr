<?php
set_error_handler("exception_error_handler",E_ALL|E_STRICT);
set_exception_handler("exception_handler");

//Utilisation de set_error_handler() pour changer tous les messages d'erreurs en ErrorException
function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

//Capture des exceptions non catchées
function exception_handler($e)
{
    echo exception_to_html($e, "unexpected");
}

function exception_to_html($e, $class = "")
{
    $html = "<div class='exception $class'>\n";
    $html .= "\t<h3>" . get_class($e) . "</h3>\n";
    $html .= "\t<ul>\n";
    $html .= "\t\t<li class='ex_message'>" . $e->getMessage() . "</li>\n";
    $html .= "\t\t<li class='ex_file'>in \"<span>" . $e->getFile() . "</span>\" (<span>ligne " . $e->getLine() . "</span>)</li>\n";
    $html .= "\t\t<li class='ex_trace'><span>Stack Trace :</span><ul>\n";
    foreach ($e->getTrace() as $num => $trace)
    {
        $html .= "\t\t<li><span>$num</span><ul>\n";
        foreach ($trace as $key => $value)
        {
            $html .= "\t\t\t<li><span>$key : </span>";
            if (is_array($value))
            {
                $html .= "\t\t\t\t<pre>\n";
                foreach ($value as $k => $v)
                {
                    $v = print_r($v,true);
                    $html .= "arg$k : $v\n";
                }
                $html .= "\t\t\t\t</pre>\n";
            }
            else
            $html .= "$value";
            $html .= "</li>\n";
        }
        $html .= "\t\t</ul></li>\n";
    }
    $html .= "\t</ul>\n";
    $html .= "</div>\n";
    return $html;
}

function exit_message($code, $msg){
    http_response_code($code);
    echo "<pre>".print_r($_SERVER,true)."</pre>";
    die("<pre>$msg</pre>");
}

if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL) {

        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }
}

?>