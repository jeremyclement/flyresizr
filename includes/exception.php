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
    afficheException($e, true);
}

function afficheException($e, $innatendu = false)
{
    if ($innatendu){
        echo "<div class='exception'>\n";
    }
    else{
        echo "<div class='exception catch'>\n";
    }
    echo "\t<h3>" . get_class($e) . "</h3>\n";
    echo "\t<ul>\n";
    echo "\t\t<li class='ex_message'>" . $e->getMessage() . "</li>\n";
    echo "\t\t<li class='ex_file'>in \"<span>" . $e->getFile() . "</span>\" (<span>ligne " . $e->getLine() . "</span>)</li>\n";
    echo "\t\t<li class='ex_trace'><span>Stack Trace :</span><ul>\n";
    foreach ($e->getTrace() as $num => $trace)
    {
        echo "\t\t<li><span>$num</span><ul>\n";
        foreach ($trace as $key => $value)
        {
            echo "\t\t\t<li><span>$key : </span>";
            if (is_array($value))
            {
                echo "\t\t\t\t<pre>\n";
                foreach ($value as $k => $v)
                {
                    $v = print_r($v,true);
                    echo "arg$k : $v\n";
                }
                echo "\t\t\t\t</pre>\n";
            }
            else
            echo "$value";
            echo "</li>\n";
        }
        echo "\t\t</ul></li>\n";
    }
    echo "\t</ul>\n";
    echo "</div>\n";
}
?>