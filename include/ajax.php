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

function ajax_get_tags()
{
	$tags = db_get_rows('tags', 'tag');
	$return = array();
	foreach ($tags as $tag)
		$return[] = $tag['tag'];
	
	echo json_encode($return);
}

function ajax_get_ingredients()
{
	$query = get_parameter("query", "");
	
	$ingredients = db_get_rows('ingredients',
		array('id', 'ingredient'),
		array('ingredient' => array('like' => $query . "%" )));
	
	echo json_encode($ingredients);
}
?>