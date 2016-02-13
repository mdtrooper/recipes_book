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

function show_about()
{
	$content = array();
	$content["title"] = "Recipes book - About";
	$content["section"] = "about";
	
	ob_start();
	?>
	<div class="panel panel-default">
		<div class="panel-heading">What is "Recipes book"?</div>
		<div class="panel-body">
			<p>It is a website, not a Laundry. Well, the website/project is trying to be a recipes book.</p>
		</div>
	</div>
	<?php
	$content["body"] = ob_get_clean();
	
	page($content);
}
?>