<?php

/**
 * Sample Custom post type implementation
 */
$opts = array(
	'post_type_name' => 'project',
	'singular' => 'Project',
	'plural' => 'Projects',
	'slug' => 'projects',
	'supports' => array('title'),
	'labels' => array(
		'add_new' => 'Add new Project',
		'add_new_item' => 'Add new Project',
		'new_item' => 'New Project'
	)
);
$project = new CPT($opts);
$project->menu_icon('dashicons-admin-multisite');