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

function show_login()
{
	$content = array();
	$content["title"] = "Recipes book - Login";
	$content["section"] = "login";
	
	$user = get_parameter('user', '');
	
	ob_start();
	
	if (get_message() === "error_login")
	{
		?>
		<div class="alert alert-danger" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<strong>Error!</strong> A problem has been occurred in login process.
		</div>
		<?php
		set_message(null);
	}
	?>
	<div class="panel panel-default">
		<form id="login_form" method="post" action="index.php">
			<input type="hidden" name="action" value="login" />
			<div class="panel-heading">Login</div>
			<div class="panel-body">
				<div class="input-group">
					<span class="input-group-addon glyphicon glyphicon-user"></span> 
					<input type="text" class="form-control" placeholder="User" name="user" value="<?=$user;?>">
				</div>
				<div class="input-group">
					<span class="input-group-addon glyphicon glyphicon-lock"></span>
					<input type="password" class="form-control" placeholder="Password" name="password">
				</div>
				<button type="button" class="btn btn-default btn-lg btn-block" onclick="$('#login_form').submit();">
					Login
					<span class="glyphicon glyphicon-chevron-right"></span>
				</button>
			</div>
		</form>
	</div>
	<?php
	$content["body"] = ob_get_clean();
	
	page($content);
}
?>