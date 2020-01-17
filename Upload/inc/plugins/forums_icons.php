<?php
/*
 * MyBB: forums_icons
 *
 * File: forums_icons.php
 * 
 * Authors: Edson Ordaz, Vintagedaddyo
 *
 * MyBB Version: 1.8
 *
 * Plugin Version: 1.2
 * 
 */


$plugins->add_hook("admin_forum_menu", "forums_icons_admin_forum_menu");
$plugins->add_hook("admin_forum_action_handler", "forums_icons_admin_forum_action_handler");
$plugins->add_hook("admin_load", "forums_icons_admin_load");

function forums_icons_info()
{
    global $lang;

    $lang->load("forums_icons");
    
    $lang->forums_icons_Desc = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="AZE6ZNZPBPVUL">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $lang->forums_icons_Desc;

    return Array(
        'name' => $lang->forums_icons_Name,
        'description' => $lang->forums_icons_Desc,
        'website' => $lang->forums_icons_Web,
        'author' => $lang->forums_icons_Auth,
        'authorsite' => $lang->forums_icons_AuthSite,
        'version' => $lang->forums_icons_Ver,
        'guid' => 'f92dead66311a7197c5c0038f1bc6737',
        'compatibility' => $lang->forums_icons_Compat
    );
}

function forums_icons_activate()
{
	global $mybb, $db, $cache;
	
	$db->query("ALTER TABLE `".TABLE_PREFIX."forums` ADD `icon` VARCHAR(120) NOT NULL DEFAULT 'folder.png' AFTER `defaultsortorder`");

// Add
	require_once MYBB_ROOT."inc/adminfunctions_templates.php"; 
	find_replace_templatesets("forumbit_depth1_cat", "#".preg_quote("<td class=\"thead{\$expthead}\" colspan=\"5\">")."#i", "<td class=\"thead{\$expthead}\" colspan=\"6\">");
	find_replace_templatesets("forumbit_depth1_cat", "#".preg_quote("<td class=\"tcat\" colspan=\"2\">")."#i", "<td class=\"tcat\" colspan=\"3\">");
	find_replace_templatesets("forumbit_depth2_forum", "#".preg_quote(" id=\"mark_read_{\$forum['fid']}\"></span></td>")."#i", " id=\"mark_read_{\$forum['fid']}\"></span></td><td class=\"{\$bgcolor}\" align=\"center\" valign=\"top\" width=\"1\"><img src=\"forums_icons/{\$forum['icon']}\" onerror=\"this.style.display = 'none';\" alt=\"{\$forum['name']}\" /></td>");
	find_replace_templatesets("forumdisplay_subforums", "#".preg_quote("colspan=\"5\"")."#i", "colspan=\"6\"");
	find_replace_templatesets("forumdisplay_subforums", "#".preg_quote("<td class=\"tcat\" width=\"2%\">&nbsp;</td>")."#i", "<td class=\"tcat\" width=\"2%\">&nbsp;</td><td class=\"tcat\" width=\"2%\">&nbsp;</td>");
	find_replace_templatesets("forumbit_depth2_cat", "#".preg_quote("<td class=\"{\$bgcolor}\" valign=\"top\">")."#i", "<td class=\"{\$bgcolor}\" align=\"center\" valign=\"top\" width=\"1\"><img src=\"forums_icons/{\$forum['icon']}\" onerror=\"this.style.display = 'none';\" alt=\"{\$forum['name']}\" /></td><td class=\"{\$bgcolor}\" valign=\"top\">");
	$cache->update_forums();
}

function forums_icons_deactivate()
{
	global $mybb, $db, $cache;

// Remove
	require_once MYBB_ROOT."inc/adminfunctions_templates.php"; 
	find_replace_templatesets("forumbit_depth1_cat", '#'.preg_quote('<td class="thead{$expthead}" colspan="6">').'#', '<td class="thead{$expthead}" colspan="5">',0);
	find_replace_templatesets("forumbit_depth1_cat", '#'.preg_quote('<td class="tcat" colspan="3">').'#', '<td class="tcat" colspan="2">',0);
	find_replace_templatesets("forumbit_depth2_forum", '#'.preg_quote('<td class="{$bgcolor}" align="center" valign="top" width="1"><img src="forums_icons/{$forum[\'icon\']}" onerror="this.style.display = \'none\';" alt="{$forum[\'name\']}" /></td>').'#', '',0);
	find_replace_templatesets("forumdisplay_subforums", '#'.preg_quote('colspan="6"').'#', 'colspan="5"',0);
	find_replace_templatesets("forumdisplay_subforums", '#'.preg_quote('<td class="tcat" width="2%">&nbsp;</td><td class="tcat" width="2%">&nbsp;</td>').'#', '<td class="tcat" width="2%">&nbsp;</td>',0);
	find_replace_templatesets("forumbit_depth2_cat", '#'.preg_quote('<td class="{$bgcolor}" align="center" valign="top" width="1"><img src="forums_icons/{$forum[\'icon\']}" onerror="this.style.display = \'none\';" alt="{$forum[\'name\']}" /></td>').'#', '',0);
    $db->query("ALTER TABLE ".TABLE_PREFIX."forums DROP `icon`");
	$cache->update_forums();
}

function forums_icons_admin_forum_action_handler(&$actions)
{
	global $mybb, $lang;

	$lang->load("forums_icons");

	if(is_super_admin((int)$mybb->user['uid']))
	{
		$actions['forums_icons'] = array('active' => $lang->forums_icons_url_2, 'file' => '');
	}
}

function forums_icons_admin_forum_menu(&$sub_menu)
{
	global $mybb, $lang;

    $lang->load("forums_icons");

	if(is_super_admin((int)$mybb->user['uid']))
	{
		$sub_menu['110'] = array("id" => $lang->forums_icons_url_2, "title" => $lang->forums_icons_name, "link" => "index.php?module=forum-".$lang->forums_icons_url_2);
	}
}

function forums_icons_admin_load()
{
	// add $cp_style
	global $mybb, $db, $page, $lang, $cache, $cp_style;

    $lang->load("forums_icons");

	if($page->active_action != $lang->forums_icons_url_2)
	{
		return;
	}

	$icon_dir = "forums_icons";
	$forum_cache = cache_forums();
	$img = "<img src=\"../".$icon_dir."/";
	// add $cp_style
	$img_delete = "<img src=styles/".$cp_style."/images/icons/delete.png> ";
	$img_edit = "<img src=styles/".$cp_style."/images/icons/success.png> ";
	$page->add_breadcrumb_item($lang->forums_icons_name);
	$page->output_header($lang->forums_icons_name);

if($mybb->input['action'] == "edit") {
	$form = new Form("index.php?module=forum-".$lang->forums_icons_url_2."&amp;action=save", "post", "save",1);
	echo $form->generate_hidden_field("fid", $mybb->input['fid']);
	$form_container = new FormContainer(''.$lang->forums_icons_forums_container.''.$forum_cache[$mybb->input['fid']]['name']);

	$form_container->output_row($lang->forums_icons_icon, $lang->forums_icons_icon_des, $form->generate_file_upload_box("upload_icon", array('style' => 'width: 230px;')), 'file');
	$form_container->output_row($lang->forums_icons_used_icon, $lang->forums_icons_used_des, "{$img}".$forum_cache[$mybb->input['fid']]['icon']."\" >", 'icon');
	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->forums_icons_submit);
	$form->output_submit_wrapper($buttons);
	$form->end();
	$page->output_footer();
}
if($mybb->input['action'] == "save")
{
	$dirpath = MYBB_ROOT."forums_icons";
	$file_type = $_FILES['upload_icon']['type'];
	switch(strtolower($file_type))
	{
		case "image/gif":
		case "image/jpeg":
		case "image/x-jpg":
		case "image/x-jpeg":
		case "image/pjpeg":
		case "image/jpg":
		case "image/png":
		case "image/x-png":
			$typeicon =  1;
			break;
		default:
			$typeicon = 0;
	}

	if($typeicon == 0)
	{

		flash_message($lang->forums_icons_no_file, 'error');
		admin_redirect("index.php?module=forum-".$lang->forums_icons_url_2."&amp;action=edit&amp;fid=".intval($mybb->input['fid']));

	}

        if ($_FILES['upload_icon']['error'] == '0')
        {
                $icono_image = $_FILES['upload_icon']['tmp_name'];
                $newfile = $dirpath . "/" . $_FILES['upload_icon']['name'];
                if (!copy($icono_image, $newfile))
                {

				flash_message($lang->forums_icons_no_file_again, 'error');
				admin_redirect("index.php?module=forum-".$lang->forums_icons_url_2."&amp;action=edit&amp;fid=".intval($mybb->input['fid']));	
                }

					$update = array( 
						"icon" => $_FILES['upload_icon']['name']
					); 
					$db->update_query("forums", $update, "fid='".$db->escape_string($mybb->input['fid'])."'");

					$cache->update_forums();

					flash_message($lang->forums_icons_file_success, 'success');
					admin_redirect("index.php?module=forum-".$lang->forums_icons_url_2."");

        }else{

		flash_message($lang->forums_icons_no_file_again, 'error');
		admin_redirect("index.php?module=forum-".$lang->forums_icons_url_2."&amp;action=edit&amp;fid=".intval($mybb->input['fid']));


	}


}
if($mybb->input['action'] == "delete")
	{
		$query = $db->simple_select("forums", "*", "fid='".intval($mybb->input['fid'])."'");
		$forum = $db->fetch_array($query);

		if(!$forum['fid'])
		{
			flash_message("Error", 'error');
			admin_redirect("index.php?module=forum-".$lang->forums_icons_url_2);
		}

		// User clicked no
		if($mybb->input['no'])
		{
			admin_redirect("index.php?module=forum-".$lang->forums_icons_url_2);
		}

		if($mybb->request_method == "post")
		{
			$db->query("UPDATE mybb_forums set icon='' where fid='{$forum['fid']}'");
			$cache->update_forums();
			flash_message($lang->forums_icons_deleted, 'success');
			admin_redirect("index.php?module=forum-".$lang->forums_icons_url_2);
		}
		else
		{
			$page->output_confirm_action("index.php?module=forum-".$lang->forums_icons_url_2);
		}
	}
		$table = new Table;
		$table->construct_header($lang->forums_icons_forums, array("width" => "50%"));
		$table->construct_header($lang->forums_icons_fid, array("class" => "align_center", "width" => "5%"));
		$table->construct_header($lang->forums_icons_forum_icon, array("class" => "align_center", "width" => "15%"));
		$table->construct_header($lang->controls, array("class" => "align_center", "colspan" => 2, "width" => "20%"));
		$table->construct_row();

		foreach($forum_cache as $forum)
		{
			if($forum['type'] != "c")
			{
				$table->construct_cell("<b>".$forum['name']."</b>");
				$table->construct_cell($forum['fid'], array("class" => "align_center"));
				$table->construct_cell("{$img}".$forum['icon']."\" >", array("class" => "align_center"));
				$table->construct_cell("<a href=\"index.php?module=forum-".$lang->forums_icons_url_2."&amp;action=edit&amp;fid={$forum['fid']}\">{$img_edit}".$lang->forums_icons_edit."</a>", array("class" => "align_center"));
				$table->construct_cell("<a href=\"index.php?module=forum-".$lang->forums_icons_url_2."&amp;action=delete&amp;fid={$forum['fid']}&amp;my_post_key={$mybb->post_code}\" onclick=\"return AdminCP.deleteConfirmation(this, '{$lang->forums_icons_delete_onclick}')\">{$img_delete}".$lang->forums_icons_delete."</a>", array("class" => "align_center"));
			}
		$table->construct_row();

		}
		$table->output($lang->forums_icons_name);
		$page->output_footer();
} 
?>
