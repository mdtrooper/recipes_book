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

function show_home()
{
	$content = array();
	
	$total_recipes = (int)get_recipes(null, true);
	$total_tags = (int)db_get_count('tags');
	$total_users = (int)db_get_count('users');
	$total_ingredients = (int)db_get_count('ingredients');
	
	$temp = db_get_rows_sql("
		SELECT (SELECT tag FROM tags WHERE tags.id = id_tag) AS tag, COUNT(id_recipe) AS count
		FROM rel_tags_recipes
		GROUP BY id_tag
		ORDER BY count DESC
		LIMIT 1;
	");
	$most_tag_used = "";
	if (!empty($temp))
		$most_tag_used = $temp[0]['tag'];
	
	$temp = db_get_rows_sql("
		SELECT (SELECT ingredient FROM ingredients WHERE ingredients.id = id_ingredient) AS ingredient,
			COUNT(id_recipe) AS count
		FROM rel_ingredients_recipes
		GROUP BY id_ingredient
		ORDER BY count DESC
		LIMIT 1;
	");
	$most_ingredient_used = "";
	if (!empty($temp))
		$most_ingredient_used = $temp[0]['ingredient'];
	
	$temp = db_get_rows_sql("
		SELECT (SELECT title FROM recipes WHERE recipes.id = id_recipe) AS recipe,
			SUM(duration) AS total_duration
		FROM steps
		GROUP BY id_recipe
		ORDER BY total_duration DESC
		LIMIT 1;
	");
	$recipe_large_duration = "";
	if (!empty($temp))
	{
		$time = time_array_to_string(seconds_to_time_array($most_recipe_duration = $temp[0]['total_duration']));
		
		$recipe_large_duration = $temp[0]['recipe'] . " " . $time;
	}
	
	$temp = db_get_rows_sql("
		SELECT (SELECT title FROM recipes WHERE recipes.id = id_recipe) AS recipe,
			SUM(duration) AS total_duration
		FROM steps
		GROUP BY id_recipe
		ORDER BY total_duration ASC
		LIMIT 1;
	");
	$recipe_short_duration = "";
	if (!empty($temp))
	{
		$time = time_array_to_string(seconds_to_time_array($most_recipe_duration = $temp[0]['total_duration']));
		
		$recipe_short_duration = $temp[0]['recipe'] . " " . $time;
	}
	
	$temp = db_get_rows_sql("
		SELECT (SELECT title FROM recipes WHERE recipes.id = id_recipe) AS recipe,
			COUNT(id) AS count
		FROM steps
		GROUP BY id_recipe
		ORDER BY count DESC
		LIMIT 1;
	");
	$recipe_most_steps = "";
	if (!empty($temp))
		$recipe_most_steps = $temp[0]['recipe'] . " " . $temp[0]['count'] . " steps";
	
	$temp = db_get_rows_sql("
		SELECT (SELECT title FROM recipes WHERE recipes.id = id_recipe) AS recipe,
			COUNT(id) AS count
		FROM steps
		GROUP BY id_recipe
		ORDER BY count ASC
		LIMIT 1;
	");
	$recipe_less_steps = "";
	if (!empty($temp))
		$recipe_less_steps = $temp[0]['recipe'] . " " . $temp[0]['count'] . " steps";
	
	
	$temp = db_get_rows_sql("
		SELECT (SELECT user FROM users WHERE users.id = id_user) AS user,
			COUNT(id) AS count
		FROM recipes
		GROUP BY id_user
		ORDER BY count DESC
		LIMIT 1;
	");
	$user_most_recipes = "";
	if (!empty($temp))
		$user_most_recipes = $temp[0]['user'] . " " . $temp[0]['count'] . " recipes";
	
	ob_start();
	?>
	<div class="panel panel-default">
		<div class="panel-heading">Recipes Book</div>
		<div class="panel-body">
			Save your recipes and organize it. It is easy and useful.
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">Recipes</div>
		<div class="panel-body">
			<ul>
				<li>Total Recipes: <span class="badge"><?=$total_recipes;?></span></li>
				<li>Total tags: <span class="badge"><?=$total_tags;?></span></li>
				<li>Total users: <span class="badge"><?=$total_users;?></span></li>
				<li>Total ingredients: <span class="badge"><?=$total_ingredients;?></span></li>
				<li>Tag most used: <?=$most_tag_used;?></li>
				<li>Ingredient most used: <?=$most_ingredient_used;?></li>
				<li>Recipe most large duration: <?=$recipe_large_duration;?></li>
				<li>Recipe most short duration: <?=$recipe_short_duration;?></li>
				<li>Recipe most steps: <?=$recipe_most_steps;?></li>
				<li>Recipe less steps: <?=$recipe_less_steps;?></li>
				<li>User with most recipes: <?=$user_most_recipes;?></li>
			</ul>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">Top 5 Recipes</div>
		<div class="panel-body">
			<ul>
				<?php
				$temp = db_get_rows_sql("
					SELECT (SELECT title FROM recipes WHERE recipes.id = id_recipe) AS recipe,
						(SUM(points) / COUNT(id_user)) AS average
					FROM points
					GROUP BY id_recipe
					ORDER BY average DESC
					LIMIT 5;
				");
				if (!empty($temp))
				{
					foreach ($temp as $row)
					{
						echo "<li>" . $row['recipe'] . " <span class='badge'>" . round($row['average'], 2) . "</span></li>";
					}
				}
				?>
			</ul>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">Last 5 Recipes</div>
		<div class="panel-body">
			<ul>
				<?php
				$temp = db_get_rows_sql("
					SELECT title
					FROM recipes
					ORDER BY id DESC
					LIMIT 5;
				");
				if (!empty($temp))
				{
					foreach ($temp as $row)
					{
						echo "<li>" . $row['title'] . "</li>";
					}
				}
				?>
			</ul>
		</div>
	</div>
	<?php
	$content["body"] = ob_get_clean();
	
	page($content);
}
?>
