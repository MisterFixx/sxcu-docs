<?php
echo "Loading base and components...\n";
$components   = glob('components/*.md', GLOB_BRACE);
$endpoints    = glob('endpoints/*.json', GLOB_BRACE);
$base         = file_get_contents("base.json");
$output       = "";
$allEndpoints = "";

echo "Building swagger file...\n";
foreach($endpoints as $endpoint){
    $content = file_get_contents($endpoint);
    $content = sanitizeFile($content);

    $allEndpoints .= "{$content}, ";
}

$allEndpoints = substr($allEndpoints, 0, -2);
$base         = str_replace("{{paths}}", $allEndpoints, $base);

foreach($components as $component){
    $content = file_get_contents($component);
    $content = sanitizeFile($content);
    $content = strtr($content, ["\n" => "\\n",  "\r" => "\\r"]);
    
    $name    = basename($component, '.md');
    
    $base = str_replace("{{{$name}}}", $content, $base);
}

echo "Creating file...\n";
$json = json_decode($base, true);
echo "JSON status: ".json_last_error_msg()."\n";

//$output = $base;
$output = json_encode($json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
file_put_contents("../swagger.json", $output);
 
echo "Done!\n";

function sanitizeFile($text){
    //Removes multi-line comments and does not create
    //a blank line, also treats white spaces/tabs 
    $text = preg_replace('!^[ \t]*/\*.*?\*/[ \t]*[\r\n]!s', '', $text);

    //Removes single line '//' comments, treats blank characters
    $text = preg_replace('#^\s*//.+$#m', '', $text);

    //Strip blank lines
    $text = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $text);
    
    return $text;
}