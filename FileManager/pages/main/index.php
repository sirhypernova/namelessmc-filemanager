<?php
/*
 *	Made by SirHyperNova
 *  NamelessMC version 2.0.0-pr3
 *
 *  License: MIT
 *
 *  File Manager Module
 */
?>
<?php include_once('includes/init.php'); ?>
<?php 
if (!$user->isLoggedIn() || !$user->hasPermission('files.view') && !$user->data()->id != 1) {
    Redirect::to(URL::build('/'));
	die();
}
// Define Constants
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
}
if (!strlen($size) > 0) {
    $size = 1024*1024*10;
}
if(!count($exts) > 0) {
    $exts = serialize(['js', 'css', 'txt','html','htm', 'doc', 'docx', 'pdf', 'jpg', 'jpeg', 'png', 'gif','zip','mp3','gz','fmlink']);
}
if (!in_array($color,$allowedColors)) {
    $color = 'blue';
}
if(!in_array($colorModifier,$allowedColorsModifiers)) {
    $colorModifier = 'accent-2';
}
$path = str_replace('{ROOT_PATH}',ROOT_PATH,$path);
$path = str_replace('{USERNAME}',$user->data()->username,$path);
$path = str_replace('{USERID}',$user->data()->id,$path);

define('FM_ROOT_DIR',$path);
define('FM_ALLOWED_EXTENSIONS',serialize($exts));
define('FM_MAX_SIZE',$size);

$title = $fm_lang->get('files', 'name');
$filedir = FM_ROOT_DIR;
$afiles = new Files (false,FM_ROOT_DIR,unserialize(FM_ALLOWED_EXTENSIONS),FM_MAX_SIZE);

if (isset($_GET['delete']) && $user->hasPermission('files.write') || $user->data()->id == 1) {
    $file = $afiles->get($_GET['delete']);
    if ($file) {
        $file->delete();
    }
}

if (isset($_GET['edit']) && $user->hasPermission('files.write') || $user->data()->id == 1) {
    $file = $afiles->get($_GET['edit']);
    if ($file && isset($_POST['edit-file-sub']) && is_a($file,'File') && $file->editable) {
        $file->edit((isset($_POST['edit-file-content'])?$_POST['edit-file-content']:null),(isset($_POST['edit-file-name'])?$_POST['edit-file-name']:null));
    } elseif ($file && !$file->editable && is_a($file,'File')) {
        $file->edit($file->data(),(isset($_POST['edit-file-name'])?$_POST['edit-file-name']:null));
    } elseif ($file && is_a($file,'Folder') && isset($_POST['edit-file-name'])) {
        $file->rename($_POST['edit-file-name']);
    }
}

if (isset($_GET['rndir']) && $user->hasPermission('files.write') || $user->data()->id == 1) {
    $dir = $afiles->get($_GET['rndir']);
    if ($dir && isset($_POST['edit-file-name']) && is_a($dir,'Folder')) {
        $dir->rename($_POST['edit-file-name']);
    }
}

if (isset($_GET['unzip']) && $user->hasPermission('files.write') || $user->data()->id == 1) {
    $file = $afiles->get($_GET['unzip']);
    if ($file->ext == 'zip') {
        $file->unzip();
    }
}

if (isset($_POST['file-upload-sub']) && $user->hasPermission('files.write') || $user->data()->id == 1) {
    if (isset($_GET['dir'])) {
        $dir = $afiles->get($_GET['dir']);
        $dir->upload($_FILES['file-upload']);
    } else {
        $afiles->upload($_FILES['file-upload']);
    }
}

if (isset($_POST['new-file-sub']) && $user->hasPermission('files.write') || $user->data()->id == 1) {
    if (!isset($_POST['new-file-type'])) {
        $type = 'file';
    } else {
        $type = 'dir';
    }
    if (isset($_GET['dir'])) {
        $dir = $afiles->get($_GET['dir']);
        $dir->create($_POST['new-file-name'],$type,null,($type=='file'?$_POST['new-file-content']:null));
    } else {
        $afiles->create($_POST['new-file-name'],$type,null,($type=='file'?$_POST['new-file-content']:null));
    }
}

if (isset($_GET['download'])) {
    $file = $afiles->get($_GET['download']);
    if (is_a($file,'Folder')) {
        $lfile = $file->zip();
    } else {
        $lfile = $file;
    }
    header("Content-Type: text/plain");
    header('Content-Disposition: attachment; filename="'.$lfile->fullname.'"');
    header("Content-Length: " . $lfile->sizeb);
    if (is_a($file,'Folder')) {
        echo $lfile->data();
        $lfile->delete();
    } else {
        echo $lfile->data();
    }
    die();
}

if (isset($_GET['zip']) && $user->hasPermission('files.write') || $user->data()->id == 1) {
    $file = $afiles->get($_GET['zip']);
    if (is_a($file,'Folder')) {
        $lfile = $file->zip();
    }
}

$allFiles = $afiles->getAll((isset($_GET['dir'])?($_GET['dir']=='/'?null:$_GET['dir']):null));
if (isset($allFiles['folders'])) {
    $folders = $allFiles['folders'];
} else {
    $folders = null;
}
if (isset($allFiles['files'])) {
    $files = $allFiles['files'];
} else {
    $files = null;
}
?>
<?php include_once('includes/header.php'); ?>
<div class="modal" id="delete-modal">
    <div class="modal-content">
        <h4>Delete File/Folder</h4>
        <p>Are you sure you want to delete <span id="delete-name" class="red-text"></span>?</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="modal-action modal-close btn waves-effect waves-green <?php echo $color; ?><?php echo ($colorModifier!='normal'?' '.$colorModifier:null) ?>">Keep File</a>
        <a href="?delete=" id="modal-delete" class="modal-action modal-close btn waves-effect waves-red <?php echo $color; ?><?php echo ($colorModifier!='normal'?' '.$colorModifier:null) ?>">Delete</a>
    </div>
</div>
<div class="modal" id="upload-modal">
    <form action="<?php echo (isset($_GET['dir'])?'?route=/files/&?dir='.$_GET['dir']:null); ?>" method="POST" enctype="multipart/form-data">
        <div class="modal-content">
            <h4>Upload File</h4>
            
                <div class="file-field input-field">
                    <div class="btn <?php echo $color; ?><?php echo ($colorModifier!='normal'?' '.$colorModifier:null) ?>">
                        <span>File</span>
                        <input type="file" name="file-upload">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" placeholder="Upload a file">
                    </div>
                </div>
        </div>
        <div class="modal-footer">
            <a class="modal-action modal-close btn waves-effect waves-orange <?php echo $color; ?><?php echo ($colorModifier!='normal'?' '.$colorModifier:null) ?>">Cancel</a>
            <input type="submit" value="Upload" name="file-upload-sub" class="modal-action modal-close btn waves-effect waves-green <?php echo $color; ?><?php echo ($colorModifier!='normal'?' '.$colorModifier:null) ?>">
        </div>
    </form>
</div>
<?php
    if (isset($_GET['medit'])) {
        $edit = $afiles->get($_GET['medit']);
    }
?>
<div class="modal" id="edit-modal">
    <form action="<?php echo (isset($_GET['medit'])?'?route=/files/&edit='.$_GET['medit']:null).(isset($_GET['dir'])?'&dir='.$_GET['dir']:null); ?>" method="POST" enctype="multipart/form-data">
        <div class="modal-content">
            <h4>Edit</h4>
            <div class="input-field">
                <input type="text" name="edit-file-name" id="edit-file-name" value="<?php echo (isset($_GET['medit'])&&is_a($edit,'File')?$edit->fullname:(is_a($edit,'Folder')?$edit->name:null)); ?>">
                <label for="editfile-name">File Name</label>
            </div>
            <?php if ($edit->editable) { ?>
            <div class="input-field" id="edit-file-content-div">
                <textarea name="edit-file-content" id="edit-file-content" class="materialize-textarea"><?php echo (isset($_GET['medit'])?($edit->editable?$edit->data():null):null); ?></textarea>
                <label for="edit-file-content">Content</label>
            </div>
            <?php } ?>
        </div>
        <div class="modal-footer">
            <a class="modal-action modal-close btn waves-effect waves-orange <?php echo $color; ?><?php echo ($colorModifier!='normal'?' '.$colorModifier:null) ?>">Cancel</a>
            <input type="submit" value="Save" name="edit-file-sub" class="modal-action modal-close btn waves-effect waves-green <?php echo $color; ?><?php echo ($colorModifier!='normal'?' '.$colorModifier:null) ?>">
        </div>
    </form>
</div>
<div class="modal" id="new-modal">
    <form action="<?php echo (isset($_GET['dir'])?'?route=/files/&dir='.$_GET['dir']:null); ?>" method="POST" enctype="multipart/form-data">
        <div class="modal-content">
            <h4>Create File</h4>
            <div class="input-field">
                <input type="text" name="new-file-name" id="new-file-name">
                <label for="new-file-name">File Name</label>
            </div>
            <div class="input-field" id="new-file-content-div">
                <textarea name="new-file-content" id="new-file-content" class="materialize-textarea"></textarea>
                <label for="new-file-content">Content</label>
            </div>
            <div class="switch">
                <label>
                    File
                    <input type="checkbox" name="new-file-type" id="new-file-type">
                    <span class="lever"></span>
                    Folder
                </label>
            </div>
        </div>
        <div class="modal-footer">
            <a class="modal-action modal-close btn waves-effect waves-orange <?php echo $color; ?><?php echo ($colorModifier!='normal'?' '.$colorModifier:null) ?>">Cancel</a>
            <input type="submit" value="Create" name="new-file-sub" class="modal-action modal-close btn waves-effect waves-green <?php echo $color; ?><?php echo ($colorModifier!='normal'?' '.$colorModifier:null) ?>">
        </div>
    </form>
</div>
<div class="container">
    <div class="row">
        <div class="s12 l12">
            <?php if ($user->hasPermission('files.write') || $user->data()->id == 1) { ?>
            <a href="#upload-modal" class="modal-trigger btn <?php echo $color; ?><?php echo ($colorModifier!='normal'?' '.$colorModifier:null) ?> right">Upload</a>
            <a href="#new-modal" class="modal-trigger btn <?php echo $color; ?><?php echo ($colorModifier!='normal'?' '.$colorModifier:null) ?> right">New</a>
            <?php } ?>
            <h3>Files</h3>
            <hr>
            <ul class="collection">
                <?php
                    if (isset($_GET['dir']) && $_GET['dir'] != '/' && $_GET['dir'] != '') { ?>
                        <li class="collection-item">
                            <?php $newdir = str_replace(realpath($filedir),'',realpath($filedir.'/'.$_GET['dir'].'/../')); ?>
                            <a href="<?php echo ($newdir == ''?'?route=/files':'?route=/files/&dir='.$newdir); ?>"><i class="material-icons secondary-content left <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">folder</i></a>
                            ..
                        </li>
                    <?php }
                    if (count($files) == 0 && count($folders) == 0) { ?>
                        <li class="collection-item">
                            No files or folders could be found in this directory
                        </li>
                    <?php }
                    if (count($folders) > 0) {
                        foreach ($folders as $folder) { ?>
                            <li class="collection-item">
                                <a href="?route=/files/&dir=<?php echo (isset($_GET['dir'])?str_replace($filedir,'',realpath($filedir.'/'.$_GET['dir'])):null).'/'.$folder->name; ?>"><i class="material-icons secondary-content left <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">folder</i></a>
                                <?php
                                    if ($folder->size()/1024 >= 1024) {
                                        $size = round($folder->size()/1048576,3).' megabytes';
                                    } elseif ($folder->size() >= 1024) {
                                        $size = round($folder->size()/1024,3).' kilobytes';
                                    } else {
                                        $size = $folder->size().' bytes';
                                    }
                                ?>
                                <?php echo $folder->name; ?> - <?php echo $size; ?>
                                <div class="secondary-content">
                                    <?php if ($user->hasPermission('files.write') || $user->data()->id == 1) { ?>
                                    <a href="?route=/files/&medit=<?php echo (isset($_GET['dir'])?$_GET['dir']:null); ?>/<?php echo $folder->name; ?><?php echo (isset($_GET['dir'])?'&dir='.$_GET['dir']:null); ?>"><i class="material-icons <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">edit</i></a>
                                    <a href="#delete-modal" class="modal-trigger" onclick="$('#delete-name').text('<?php echo $folder->name; ?>');$('#modal-delete').attr('href','?route=/files/&delete=<?php echo (isset($_GET['dir'])?$_GET['dir']:null); ?>/<?php echo $folder->name; ?><?php echo (isset($_GET['dir'])?'&dir='.$_GET['dir']:null); ?>');"><i class="material-icons <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">delete</i></a>
                                    <a href="?route=/files/&zip=<?php echo (isset($_GET['dir'])?$_GET['dir']:null); ?>/<?php echo $folder->name; ?><?php echo (isset($_GET['dir'])?'&dir='.$_GET['dir']:null); ?>"><i class="material-icons <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">archive</i></a>
                                    <?php } ?>
                                    <a href="?route=/files/&download=<?php echo (isset($_GET['dir'])?$_GET['dir']:null); ?>/<?php echo $folder->name; ?><?php echo (isset($_GET['dir'])?'&dir='.$_GET['dir']:null); ?>"><i class="material-icons <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">file_download</i></a>
                                </div>
                            </li>
                        <?php }
                    }
                    if (count($files) > 0) {
                    foreach ($files as $file) { ?>
                        <li class="collection-item">
                            <i class="material-icons secondary-content left <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">insert_drive_file</i>
                            <?php
                            if (isset($file->sizemb)) {
                                $size = $file->sizemb.' megabytes';
                            } elseif (isset($file->sizekb)) {
                                $size = $file->sizekb.' kilobytes';
                            } else {
                                $size = $file->sizeb.' bytes';
                            }
                            ?>
                            <?php echo $file->fullname; ?> - <?php echo $size; ?>
                            <div class="secondary-content">
                                <?php if ($user->hasPermission('files.write') || $user->data()->id == 1) { ?>
                                <a href="?route=/files/&medit=<?php echo (isset($_GET['dir'])?$_GET['dir']:null); ?>/<?php echo $file->fullname; ?><?php echo (isset($_GET['dir'])?'&dir='.$_GET['dir']:null); ?>"><i class="material-icons <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">edit</i></a>
                                <a href="#delete-modal" class="modal-trigger" onclick="$('#delete-name').text('<?php echo $file->fullname; ?>');$('#modal-delete').attr('href','?route=/files/&delete=<?php echo (isset($_GET['dir'])?$_GET['dir']:null); ?>/<?php echo $file->fullname; ?><?php echo (isset($_GET['dir'])?'&dir='.$_GET['dir']:null); ?>');"><i class="material-icons <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">delete</i></a>
                                <?php if ($file->ext == 'zip') { ?>
                                <a href="?route=/files/&unzip=<?php echo (isset($_GET['dir'])?$_GET['dir']:null); ?>/<?php echo $file->fullname; ?><?php echo (isset($_GET['dir'])?'&dir='.$_GET['dir']:null); ?>"><i class="material-icons <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">unarchive</i></a>
                                <?php } ?>
                                <?php } ?>
                                <?php if ($file->ext == 'png' || $file->ext == 'jpg' || $file->ext == 'jpeg') { ?>
                                <a href="?route=/files/&image=<?php echo (isset($_GET['dir'])?$_GET['dir']:null); ?>/<?php echo $file->fullname; ?><?php echo (isset($_GET['dir'])?'&dir='.$_GET['dir']:null); ?>"><i class="material-icons <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">open_in_new</i></a>
                                <?php } ?>
                                <?php if ($file->ext == 'pdf') { ?>
                                    <a href="?route=/files/&pdf=<?php echo (isset($_GET['dir'])?$_GET['dir']:null); ?>/<?php echo $file->fullname; ?><?php echo (isset($_GET['dir'])?'&dir='.$_GET['dir']:null); ?>"><i class="material-icons <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">open_in_new</i></a>
                                <?php } ?>
                                <?php if ($file->ext == 'fmlink') { ?>
                                <?php
                                    $data = $file->data();
                                    if (preg_match(Files::$regurl,$data)) { ?>
                                        <a target="_blank" href="<?php echo $data; ?>"><i class="material-icons <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">open_in_new</i></a>
                                    <?php }
                                ?>
                                <?php } ?>
                                <a href="?route=/files/&download=<?php echo (isset($_GET['dir'])?$_GET['dir']:null); ?>/<?php echo $file->fullname; ?><?php echo (isset($_GET['dir'])?'&dir='.$_GET['dir']:null); ?>"><i class="material-icons <?php echo $color.'-text'; ?><?php echo ($colorModifier!='normal'?' text-'.$colorModifier:null) ?>">file_download</i></a>
                            </div>
                        </li>
                    <?php }
                    }
                    ?>
            </ul>
            
    </div>
</div>
<?php include_once('includes/footer.php'); ?>