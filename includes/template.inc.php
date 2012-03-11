<?php
require_once('smarty/Smarty.class.php');
$smarty = new Smarty();

$smarty->template_dir = "{$config['app_path']}/public/templates/{$config['template_name']}";
$smarty->compile_dir = $config['cache_path'];

if (!isset($config['pagetitle'])) $config['pagetitle'] = 'Network Status';
if (!isset($config['footer_links'])) $config['footer_links'] = null;
if (!isset($config['textarea'])) $config['textarea'] = null;
$smarty->assign('pagetitle', $config['pagetitle']);
$smarty->assign('footer_links', $config['footer_links']);
$smarty->assign('textarea', $config['textarea']);

if ($config['smarty_debug']) $smarty->debugging = true;
?>