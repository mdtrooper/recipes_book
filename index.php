<?php
/*
Copyright (C) 2016 Miguel de Dios
--
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
higher any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software Foundation,
Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
*/

////////////////////////////////////////////////////////////////////////
$config = array();
$config['db_host'] = '127.0.0.1';
$config['db_name'] = 'recipes_book';
$config['db_user'] = 'root';
$config['db_password'] = 'unodostres';
////////////////////////////////////////////////////////////////////////

require_once("include/functions.php");
require_once("include/html.php");
require_once("include/ajax.php");

$config['user'] = get_sesion_var('user', null);
$config['id_user'] = get_sesion_var('id_user', null);

db_connect();

$ajax = (bool)get_parameter('ajax');

$action = get_parameter("action");

if ($ajax)
{
	if (user_logged())
	{
		switch ($action)
		{
			case "get_tags":
				ajax_get_tags();
				break;
			case "get_ingredients":
				ajax_get_ingredients();
				break;
		}
	}
	
	return;
}


$page = get_parameter("page", "home");

switch ($action)
{
	case "save_recipe":
		$correct = false;
		if (user_logged())
			$correct = save_recipe();
		if ($correct)
			set_message("correct_save_recipe");
		else
			set_message("error_save_recipe");
		break;
	case "logout":
		logout();
		$page = "home";
		break;
	case "login":
		$correct = login();
		if ($correct)
		{
			set_message("correct_login");
			$page = "home";
		}
		else
		{
			set_message("error_login");
			$page = "login";
		}
		break;
}

if (($page === "login" && user_logged()) ||
	($page === "recipe_form" && !user_logged()) ||
	($page === "user" && !user_logged()))
{
	$page = "home";
}

switch ($page)
{
	default:
	case 'home':
		require_once("pages/home.php");
		show_home();
		break;
	case 'about':
		require_once("pages/about.php");
		show_about();
		break;
	case 'login':
		require_once("pages/login.php");
		show_login();
		break;
	case 'recipe_form':
		require_once("pages/recipe_form.php");
		show_recipe_form();
		break;
	case 'user':
		require_once("pages/user.php");
		show_user();
		break;
	case 'recipe':
		break;
}
?>