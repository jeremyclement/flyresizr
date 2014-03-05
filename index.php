<?php
include_once("includes/exception.php");

function exit_message($code, $msg){
    http_response_code($code);
    die($msg);
}

preg_match("/^\/(.*\/)?([^\/]+)$/", $_SERVER['REDIRECT_SCRIPT_URL'], $matches);
$PATH = $matches[1];
$FILENAME = $matches[2];
preg_match("/^\/(.*\/)?[^\/]+$/", $_SERVER['PHP_SELF'], $matches);
$CACHE = $matches[1].".cache/";

// CHECK IF ALREADY EXISTS
$img_local_path = $_SERVER['DOCUMENT_ROOT'] . "/$PATH$FILENAME";
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
    exit_message(400,"input filename and size must be specified");
}
$img_src_name = $_GET["name"];
$img_dest_size = str_replace("*x", "%", $_GET['size']);
$img_dest_ext = $_GET["ext"];

// CHECK SOURCE IMAGE
$img_src_path = $_SERVER['DOCUMENT_ROOT'] . "/$PATH$img_src_name.$img_dest_ext";
if(!is_file($img_src_path)){
    exit_message(400,"input file '$PATH$img_src_name.$img_dest_ext' does not exist");
}

// SET DESTINATION IN CACHE
$img_dest_dir = $_SERVER['DOCUMENT_ROOT'] . "/$CACHE$PATH";
if(!is_dir($img_dest_dir)){
    $old = umask(0);
    mkdir($img_dest_dir,02775,true);
    umask($old);
}
$img_dest_path = $img_dest_dir . "$img_src_name-$img_dest_size.$img_dest_ext";

// RESIZE
$command = "convert -filter Lanczos -background none -resize "
    . escapeshellarg($img_dest_size) ." "
    . escapeshellarg($img_src_path) . " " 
    . escapeshellarg($img_dest_path);
if(!is_file($img_dest_path)){
    $return = exec($command);
}
if(!is_file($img_dest_path)){
    exit_message(500,"unable to convert '$img_src_name.$img_dest_ext' to '$img_src_name-$img_dest_size.$img_dest_ext' ($command)");
}

// RETURN RESIZED IMAGE
$buffer = file_get_contents($img_dest_path);
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->buffer($buffer);
header("Content-Type: $mime");
echo $buffer;
?>
