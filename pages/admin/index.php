<?php
/*
 *	Made by SirHyperNova
 *  NamelessMC version 2.0.0-pr3
 *
 *  License: MIT
 *
 *  Admin FileManager Page
 */

if($user->isLoggedIn()){
    if(!$user->canViewACP()){
        // No
        Redirect::to(URL::build('/'));
        die();
    } else {
        // Check the user has re-authenticated
        if(!$user->isAdmLoggedIn()){
            // They haven't, do so now
            Redirect::to(URL::build('/admin/auth'));
            die();
        } else if(!$user->hasPermission('admincp.files') && !$user->data()->id != 1){
            // Can't view this page
            require(ROOT_PATH . '/404.php');
            die();
        }
    }
} else {
    // Not logged in
    Redirect::to(URL::build('/login'));
    die();
}

$page = 'admin';
$admin_page = 'files';


?>
<!DOCTYPE html>
<html lang="<?php echo (defined('HTML_LANG') ? HTML_LANG : 'en'); ?>">
<head>
    <!-- Standard Meta -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <?php
    $title = $language->get('admin', 'admin_cp');
    require(ROOT_PATH . '/core/templates/admin_header.php');
    ?>
    <link rel="stylesheet" href="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/switchery/switchery.min.css">
</head>
<body>
<?php require(ROOT_PATH . '/modules/Core/pages/admin/navbar.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <?php require(ROOT_PATH . '/modules/Core/pages/admin/sidebar.php'); ?>
        </div>
        <?php
        $cache->setCache('fileManager');
        $path = $cache->retrieve('path');
        $size = $cache->retrieve('size');
        $exts = $cache->retrieve('exts');
        if (!strlen($path) > 0) {
            $path = '{ROOT_PATH}/modules/FileManager/files/users/{USERNAME}';
            $cache->store('path',$path);
        }
        if (!strlen($size) > 0) {
            $size = '1024*1024*10';
            $cache->store('size',$size);
        }
        if(!count($exts) > 0) {
            $exts = ['js', 'css', 'txt','html','htm', 'doc', 'docx', 'pdf', 'jpg', 'jpeg', 'png', 'gif','zip','mp3','gz','fmlink'];
            $cache->store('exts',$exts);
        }
        
        if (isset($_GET['delid'])) {
            if (array_key_exists($_GET['delid'],$exts)) {
                unset($exts[$_GET['delid']]);
                $cache->store('exts',$exts);
            }
        }
        
        if (isset($_POST['files-submit'])) {
            $cache->store('path',$_POST['files-path']);
            $cache->store('size',$_POST['files-size']);
            if (strlen($_POST['files-ext']) > 0) {
                if (!in_array($_POST['files-ext'],$exts)) {
                    $exts[] = $_POST['files-ext'];
                    $cache->store('exts',array_values($exts));
                }
            }
            $path = $_POST['files-path'];
            $size = $_POST['files-size'];
        }
        ?>
        <div class="col-md-9">
            <div class="card">
                <div class="card-block">
                    <h3>Files</h3>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="files-path">File Manager Path</label>
                            <input type="text" name="files-path" class="form-control" id="files-path" value="<?php echo $path; ?>">
                        </div>
                        <div class="form-group">
                            <label for="files-size">Max File Size (in bytes)</label>
                            <input type="text" name="files-size" class="form-control" id="files-size" value="<?php echo $size; ?>">
                        </div>
                        <div class="form-group">
                            <label for="files-ext">Add New Allowed File Extension</label>
                            <input type="text" name="files-ext" class="form-control" id="files-ext">
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Submit" name="files-submit" class="btn btn-primary">
                        </div>
                    </form>
                    <table class="table table-striped">
                        <thead>
                            <tr scope="col">Allowed File Extensions</tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exts as $key => $ext) {?>
                                <tr>
                                    <td>.<?php echo $ext; ?></td>
                                    <td><a class="text-danger" href="<?php echo URL::build('/admin/files').'&delid='.$key ?>">Delete</a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require(ROOT_PATH . '/modules/Core/pages/admin/footer.php'); ?>

<?php require(ROOT_PATH . '/modules/Core/pages/admin/scripts.php'); ?>
<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/switchery/switchery.min.js"></script>
<script>
var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
elems.forEach(function(html) {
    var switchery = new Switchery(html);
});
</script>
</body>
</html>