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
        $color = $cache->retrieve('color');
        $colorModifier = $cache->retrieve('colorModifier');
        
        $allowedColors = ['red','pink','purple','deep-purple','indigo','blue','light-blue','cyan','teal','green','light-green','lime','yellow','amber','orange','deep-orange','brown','grey','blue-grey','black','white'];
        $allowedColorsModifiers = ['lighten-1','lighten-2','lighten-3','lighten-4','normal','accent-1','accent-2','accent-3','accent-4','darken-1','darken-2','darken-3','darken-4'];
        
        if (!strlen($path) > 0) {
            $path = '{ROOT_PATH}/modules/FileManager/files/users/{USERID}';
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
        if (!in_array($color,$allowedColors)) {
            $color = 'blue';
            $cache->store('color',$color);
        }
        if(!in_array($colorModifier,$allowedColorsModifiers)) {
            $colorModifier = 'accent-2';
            $cache->store('colorModifier',$colorModifier);
        }
        
        if (isset($_GET['delid'])) {
            if (array_key_exists($_GET['delid'],$exts)) {
                unset($exts[$_GET['delid']]);
                $cache->store('exts',$exts);
            }
        }
        
        if (isset($_POST['files-submit'])) {
            $cache->store('path',preg_replace('/([^a-zA-Z0-9{}.\/()\-_ ])/','',preg_replace('/\/$/','',preg_replace('/^\//','',$_POST['files-path']))));
            $cache->store('size',preg_replace('/([^0-9+\-*])/','',$_POST['files-size']));
            if (strlen($_POST['files-ext']) > 0) {
                if (!in_array(preg_replace('/([^a-zA-Z])/','',$_POST['files-ext']),$exts) && strlen(preg_replace('/([^a-zA-Z])/','',$_POST['files-ext'])) >= 2) {
                    $exts[] = preg_replace('/([^a-zA-Z])/','',$_POST['files-ext']);
                    $cache->store('exts',$exts);
                }
            }
            if (in_array($_POST['files-color'],$allowedColors)) {
                $cache->store('color',$_POST['files-color']);
                $color = $_POST['files-color'];
            }
            if (in_array($_POST['files-color-modifier'],$allowedColorsModifiers)) {
                $cache->store('colorModifier',$_POST['files-color-modifier']);
                $colorModifier = $_POST['files-color-modifier'];
            }
            $path = preg_replace('/([^a-zA-Z0-9{}.\/()\-_ ])/','',preg_replace('/\/+$/','',preg_replace('/^\/+/','',$_POST['files-path'])));
            $size = preg_replace('/([^0-9+\-*])/','',$_POST['files-size']);
        }
        ?>
        <div class="col-md-9">
            <div class="card">
                <div class="card-block">
                    <h3>Files</h3>
                    <form action="" method="post">
                        <div style="text-align:center;">
                            <select class="custom-select" name="files-color" style="width:49%;">
                              <option value="red"<?php echo ($color=='red'?' selected':null) ?>>Red</option>
                              <option value="pink"<?php echo ($color=='pink'?' selected':null) ?>>Pink</option>
                              <option value="purple"<?php echo ($color=='purple'?' selected':null) ?>>Purple</option>
                              <option value="deep-purple"<?php echo ($color=='deep-purple'?' selected':null) ?>>Deep Purple</option>
                              <option value="indigo"<?php echo ($color=='indigo'?' selected':null) ?>>Indigo</option>
                              <option value="blue"<?php echo ($color=='blue'?' selected':null) ?>>Blue</option>
                              <option value="light-blue"<?php echo ($color=='light-blue'?' selected':null) ?>>Light Blue</option>
                              <option value="cyan"<?php echo ($color=='cyan'?' selected':null) ?>>Cyan</option>
                              <option value="teal"<?php echo ($color=='teal'?' selected':null) ?>>Teal</option>
                              <option value="green"<?php echo ($color=='green'?' selected':null) ?>>Green</option>
                              <option value="light-green"<?php echo ($color=='light-green'?' selected':null) ?>>Light Green</option>
                              <option value="lime"<?php echo ($color=='lime'?' selected':null) ?>>Lime</option>
                              <option value="yellow"<?php echo ($color=='yellow'?' selected':null) ?>>Yellow</option>
                              <option value="amber"<?php echo ($color=='amber'?' selected':null) ?>>Amber</option>
                              <option value="orange"<?php echo ($color=='orange'?' selected':null) ?>>Orange</option>
                              <option value="deep-orange"<?php echo ($color=='deep-orange'?' selected':null) ?>>Deep Orange</option>
                              <option value="brown"<?php echo ($color=='brown'?' selected':null) ?>>Brown</option>
                              <option value="grey"<?php echo ($color=='grey'?' selected':null) ?>>Grey</option>
                              <option value="blue-grey"<?php echo ($color=='blue-grey'?' selected':null) ?>>Blue-Grey</option>
                              <option value="black"<?php echo ($color=='black'?' selected':null) ?>>Black</option>
                              <option value="white"<?php echo ($color=='white'?' selected':null) ?>>White</option>
                            </select>
                            <select class="custom-select" name="files-color-modifier" style="width:49%;">
                              <option value="lighten-1"<?php echo ($colorModifier=='lighten-1'?' selected':null) ?>>Lighten-1</option>
                              <option value="lighten-2"<?php echo ($colorModifier=='lighten-2'?' selected':null) ?>>Lighten-2</option>
                              <option value="lighten-3"<?php echo ($colorModifier=='lighten-3'?' selected':null) ?>>Lighten-3</option>
                              <option value="lighten-4"<?php echo ($colorModifier=='lighten-4'?' selected':null) ?>>Lighten-4</option>
                              <option value="normal"<?php echo ($colorModifier=='normal'?' selected':null) ?>>Normal</option>
                              <option value="accent-1"<?php echo ($colorModifier=='accent-1'?' selected':null) ?>>Accent-1</option>
                              <option value="accent-2"<?php echo ($colorModifier=='accent-2'?' selected':null) ?>>Accent-2</option>
                              <option value="accent-3"<?php echo ($colorModifier=='accent-3'?' selected':null) ?>>Accent-3</option>
                              <option value="accent-4"<?php echo ($colorModifier=='accent-4'?' selected':null) ?>>Accent-4</option>
                              <option value="darken-1"<?php echo ($colorModifier=='darken-1'?' selected':null) ?>>Darken-1</option>
                              <option value="darken-2"<?php echo ($colorModifier=='darken-2'?' selected':null) ?>>Darken-2</option>
                              <option value="darken-3"<?php echo ($colorModifier=='darken-3'?' selected':null) ?>>Darken-3</option>
                              <option value="darken-4"<?php echo ($colorModifier=='darken-4'?' selected':null) ?>>Darken-4</option>
                            </select>
                        </div>
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