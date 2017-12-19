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
$cache->setCache('modulescache');

$module_installed = $cache->retrieve('FileManager');
if(!$module_installed){
	// Update main admin group permissions
	$group = $queries->getWhere('groups', array('id', '=', 2));
	$group = $group[0];
	
	$group_permissions = json_decode($group->permissions, TRUE);
	$group_permissions['admincp.files'] = 1;
	$group_permissions['files.view'] = 1;
	$group_permissions['files.write'] = 1;
	
	$group_permissions = json_encode($group_permissions);
	$queries->update('groups', 2, array('permissions' => $group_permissions));
	
	$cache->store('FileManager', 1);
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