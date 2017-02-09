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

function show_user()
{
	global $config;
	
	$content = array();
	$content["title"] = "Recipes book - User profile";
	$content["section"] = "user";
	
	$user = db_get_rows('users',
		null,
		array('id' => array('=' => $config['id_user'])));
	$user = $user[0];
	
	
	ob_start();
	
	switch (get_message())
	{
		case "error_update_user":
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<strong>Error!</strong> A problem has been occurred in the update user.
			</div>
			<?php
			set_message(null);
			break;
		case "correct_update_user":
			?>
			<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<strong>Correct!</strong> Update user.
			</div>
			<?php
			set_message(null);
			break;
	}
	?>
	<div class="panel panel-default">
		<div class="panel-heading">User Profile</div>
		<form id="user_profile" method="post" action="index.php?page=user">
			<input type="hidden" name="action" value="update_user" />
			<input type="text" class="form-control" placeholder="User" name="user" value="<?=$config['user'];?>" readonly="readonly" />
			<input type="text" class="form-control" placeholder="Email" name="email" value="<?=$user['email'];?>" />
			<input type="password" class="form-control" placeholder="Password" name="password" value="" />
			<input type="password" class="form-control" placeholder="Repeat password" name="repeat_password" value="" />
			<button type="button" class="btn btn-default btn-lg btn-block" onclick="$('#user_profile').submit();">
				Update
				<span class="glyphicon glyphicon-save"></span>
			</button>
		</form>
	</div>
	<div class="panel panel-default">
		<?php
		$count_recipes = get_recipes(array('id_user' => array('=' => $config['id_user'])), true);
		$pagination_values = pagination_get_values($count_recipes);
		$recipes = get_recipes(array('id_user' => array('=' => $config['id_user'])), false, $pagination_values);
		?>
		<div class="panel-heading">User recipes <span class="badge"><?=$count_recipes;?></span></div>
		<div class="panel-body">
			<?php
			
			
			if (empty($recipes))
			{
			?>
			<div class="alert alert-info" role="alert">
				<strong>Empty list</strong> You have any recipes.
			</div>
			<?php
			}
			else
			{
				print_pagination($pagination_values, "index.php?page=user");
			?>
				<div class="list-group">
				<?php
				foreach ($recipes as $recipe)
				{
				?>
					<div class="list-group-item">
						<div class="input-group-btn" >
							<a class="btn btn-default col-md-10" style="text-align: left;" href="index.php?page=show_recipe&id_recipe=<?=$recipe['id'];?>">
								<?php
								echo $recipe['title'] . " - " . truncate_string($recipe['description']);
								?>
							</a>
							<a class="btn btn-default col-md-1" href="index.php?page=recipe_form&action=edit_recipe&id_recipe=<?=$recipe['id'];?>"><span class="glyphicon glyphicon-edit"></span></a>
							<a class="btn btn-default col-md-1" href="index.php?page=user&action=delete_recipe&id_recipe=<?=$recipe['id'];?>"><span class="glyphicon glyphicon-trash"></span></a>
						</div>
					</div>
				<?php
				}
				?>
				</div>
			<?php
			}
			?>
		</div>
	</div>
	<?php
	$content["body"] = ob_get_clean();
	
	page($content);
}
?>