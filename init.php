<?php 
/*
 *	Made by SirHyperNova
 *  NamelessMC version 2.0.0-pr3
 *
 *  License: MIT
 *
 *  File Manager Module
 */

// Ensure module has been installed
$module_installed = $cache->retrieve('FileManager');
if(!$module_installed){

} else {
	// Installed
}

// Initialise FileManager languages
$fm_lang = new Language(ROOT_PATH.'/modules/FileManager/languages', LANGUAGE);

PermissionHandler::registerPermissions('Files', array(
    'admincp.files' => $language->get('admin', 'admin_cp') . ' &raquo; ' . $fm_lang->get('files', 'name'),
    'files.view' => $fm_lang->get('files','name') . ' &raquo; ' . $fm_lang->get('files','view'),
    'files.write' => $fm_lang->get('files','name') . ' &raquo; ' . $fm_lang->get('files','write')
));

if ($user->isLoggedIn() && $user->hasPermission('files.view') || $user->isLoggedIn() && $user->data()->id == 1) {
    // Add link to navbar
    $navigation->add('FileManager', $fm_lang->get('files', 'name'), URL::build('/files'));
    // Define URLs which belong to this module
    $pages->add('FileManager', '/files','pages/main/index.php');
    $pages->add('FileManager', '/admin/files','pages/admin/index.php');
}
if($user->hasPermission('admincp.files') || $user->data()->id == 1){
    if(!isset($admin_sidebar)) $admin_sidebar = array();
    $admin_sidebar['files'] = array(
        'title' => $fm_lang->get('files', 'name'),
        'url' => URL::build('/admin/files')
    );
}