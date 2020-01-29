<?php
/**
 * Elgg Reported content.
 */

/**
 * Initialize the plugin
 *
 * @return void
 */
function reportedcontent_init() {

	// Extend CSS
	elgg_extend_view('admin.css', 'reportedcontent/admin_css');

	if (elgg_is_logged_in()) {
		// Extend footer with report content link
		elgg_register_menu_item('footer', [
			'name' => 'report_this',
			'href' => 'ajax/form/reportedcontent/add',
			'title' => elgg_echo('reportedcontent:this:tooltip'),
			'text' => elgg_echo('reportedcontent:this'),
			'icon' => 'exclamation-triangle',
			'priority' => 500,
			'section' => 'default',
			'link_class' => 'elgg-lightbox',
			'deps' => 'elgg/reportedcontent',
		]);
	}

	elgg_register_plugin_hook_handler('register', 'menu:user_hover', 'reportedcontent_user_hover_menu');

	elgg_register_menu_item('page', [
		'name' => 'administer_utilities:reportedcontent',
		'text' => elgg_echo('admin:administer_utilities:reportedcontent'),
		'href' => 'admin/administer_utilities/reportedcontent',
		'section' => 'administer',
		'parent_name' => 'administer_utilities',
		'context' => 'admin',
	]);
}

/**
 * Add report user link to hover menu
 *
 * @param \Elgg\Hook $hook 'register', 'menu:user_hover'
 *
 * @return void|ElggMenuItem[]
 */
function reportedcontent_user_hover_menu(\Elgg\Hook $hook) {
	
	if (!elgg_is_logged_in()) {
		return;
	}
	
	$user = $hook->getEntityParam();
	if (!$user instanceof ElggUser) {
		return;
	}
	
	if (elgg_get_logged_in_user_guid() == $user->guid) {
		return;
	}
	
	$href = elgg_http_add_url_query_elements('ajax/form/reportedcontent/add', [
		'address' => $user->getURL(),
		'title' => $user->getDisplayName(),
	]);
	
	$return = $hook->getValue();
	$return[] = \ElggMenuItem::factory([
		'name' => 'reportuser',
		'text' => elgg_echo('reportedcontent:user'),
		'icon' => 'exclamation-triangle',
		'href' => $href,
		'section' => 'action',
		'link_class' => 'elgg-lightbox',
		'deps' => 'elgg/reportedcontent',
	]);

	return $return;
}

return function() {
	elgg_register_event_handler('init', 'system', 'reportedcontent_init');
};
