<?php
require_once("config.php");
require_once(WWW_DIR."/lib/adminpage.php");
require_once(WWW_DIR."/lib/site.php");
require_once(WWW_DIR."/lib/CollectionsCleaning.php");

$page = new AdminPage();
$s = new Sites();
$site = $s->get();

$page->title = "Collections Regex Test";

$tpg = $page->site->tablepergroup;
$page->smarty->assign('tpg', $tpg);

if ($tpg) {

	$group = trim(isset($_POST['group']) && !empty($_POST['group']) ? $_POST['group'] : '');
	$regex = trim(isset($_POST['regex']) && !empty($_POST['regex']) ? $_POST['regex'] : '');
	$limit = (isset($_POST['limit']) && is_numeric($_POST['limit']) ? $_POST['limit'] : 50);
	$page->smarty->assign(['group' => $group, 'regex' => $regex, 'limit' => $limit]);

	if ($group && $regex) {
		$page->smarty->assign('data', (new CollectionsCleaning(['Settings' => $page->settings]))->testRegex($group, $regex, $limit));
	}

}

$page->content = $page->smarty->fetch('collection_regexes-test.tpl');
$page->render();