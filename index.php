<?php
include_once("includes/exception.php");

preg_match("/^(\/.*\/)?([^\/]+)$/", $_SERVER['REDIRECT_SCRIPT_URL'], $matches);
$PATH = $matches[1];
$FILENAME = $matches[2];

// CHECK IF ALREADY EXISTS
$img_local_path = $_SERVER['DOCUMENT_ROOT'] . $PATH . $FILENAME;
if(is_file($img_local_path)){
    $buffer = file_get_contents($img_local_path);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->buffer($buffer);
    header("Content-Type: $mime");
    echo $buffer;
    exit();
}

// CHECK REQUEST
if(!isset($_GET["name"]) || !isset($_GET["size"]) || !isset($_GET["ext"])){
    header("HTTP/1.0 404 Not Found");
    exit();
}
$img_src_name = $_GET["name"];
$img_src_ext = $_GET["ext"];

$res = explode('x',$_GET['size']);
$t_width = $res[0];
$t_height = count($res) > 1 ? $res[1] : null;

// CHECK SOURCE IMAGE
$img_src_path = $_SERVER['DOCUMENT_ROOT'] . "$PATH$img_src_name.$img_src_ext";
if(!is_file($img_src_path)){
    header("HTTP/1.0 404 Not Found");
    exit();
}




?>


<pre>
<?php
print_r($matches);
print_r($_GET);
print_r($_SERVER);
?>
</pre>
