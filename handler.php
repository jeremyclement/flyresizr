<?php
include_once("includes/http.php");
@include_once("packages/autoload.php");
@include_once("../../autoload.php");

function printImage($path){
    if(!is_file($path)){
        return FALSE;
    }
    $buffer = file_get_contents($path);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->buffer($buffer);
    header("Content-Type: $mime");
    echo $buffer;
    return filesize($path);
}

// set pretty debug
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

// analyse $_REQUEST
if(!isset($_GET["name"]) || !isset($_GET["size"]) || !isset($_GET["ext"])){
    http_exit_message(400,"input filename and size must be specified");
}
$img_src_name = $_GET["name"];
$img_out_size = $_GET['size'];
$img_out_ext = $_GET["ext"];

// get requested image directory
preg_match("/^\/(.*\/)?[^\/]+(\?.*)?$/", $_SERVER['REQUEST_URI'], $matches);
$img_src_dir = $matches[1];

// check if image already exists
$img_out = "$img_src_name-$img_out_size.$img_out_ext";
$img_local_path = $_SERVER['DOCUMENT_ROOT'] . "/$img_src_dir$img_out";
if(printImage($img_local_path)){
    exit(0);
}

// get source image (original size)
$img_src = "$img_src_name.$img_out_ext";
$img_src_path = $_SERVER['DOCUMENT_ROOT'] . "/$img_src_dir$img_src";
if(!is_file($img_src_path)){
    http_exit_message(400,"input files '$img_out' and '$img_src' do not exist");
}

// set cache
$cache_dir = ".cache/".$_SERVER['HTTP_HOST'].'/';
$cache = new \Gregwar\Cache\Cache;
$cache->setCacheDirectory($cache_dir);
$cache->setPrefixSize(2);

$old = umask(0002);
// get cache or convert 
$img_out_path = $cache->getOrCreateFile($img_out,
    array(
        'younger-than' => $img_src_path
    ),
    function($cached_file){
        // resize image
        global $img_out_size, $img_src_path;
        $command = "convert -filter Lanczos -background none -resize "
            . escapeshellarg($img_out_size) ." "
            . escapeshellarg($img_src_path) . " " 
            . escapeshellarg($cached_file);
        return exec($command);
    });
umask($old);

// return image
if(!printImage($img_out_path)){
    http_exit_message(500,"unable to convert '$img_src' to '$img_out'",false);
}

ob_flush();
flush();

//empty old cache
\Gregwar\Cache\GarbageCollect::dropOldFiles($cache_dir, 30);
?>
