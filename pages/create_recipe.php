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

function show_create_recipe()
{
	$content = array();
	$content["title"] = "Recipes book - Create Recipe";
	$content["section"] = "create_recipe";
	
	$data_json = get_parameter('data', null);
	$data = json_decode($data_json, true);
	
	$title = "";
	if (isset($data['title']))
		$title = $data['title'];
	
	$description = "";
	if (isset($data['description']))
		$description = $data['description'];
	
	$duration_hours = 0;
	$duration_minutes = 0;
	$duration_seconds = 0;
	if (isset($data['duration']))
	{
		$duration = seconds_to_time_array($data['duration']);
		$duration_hours = $duration['hours'];
		$duration_minutes = $duration['minutes'];
		$duration_seconds = $duration['seconds'];
	}
	
	$servings = 0;
	if (isset($data['servings']))
		$servings = $data['servings'];
	
	$tags = "";
	if (isset($data['tags']))
		$tags = implode(",", $data['tags']);
	
	$ingredients = array();
	if (isset($data['ingredients']))
		$ingredients = $data['ingredients'];
	
	
	foreach ($ingredients as $i => $ingredient)
	{
		if (!is_int($ingredient['id']))
		{
			$ingredients[$i]['name'] = $ingredient['id'];
			$ingredients[$i]['id'] =
				db_get_value('ingredients', 'ingredient',
					array('ingredient' => $ingredient['id']));
		}
		else
		{
			$ingredients[$i]['name'] =
				db_get_value('ingredients', 'ingredient',
					array('id' => $ingredient['id']));
		}
		
		if (!is_int($ingredient['measure_type']))
		{
			$ingredients[$i]['measure_type'] =
				db_get_value('measure_types', 'id',
					array('measure_type' => $ingredient['measure_type']));
		}
	}
	
	$steps = array();
	if (isset($data['steps']))
		$steps = $data['steps'];
	
	debug($steps, true);
	
	foreach ($steps as $i => $step)
	{
		$duration = seconds_to_time_array($step['duration']);
		$steps[$i]['duration_hours'] = $duration['hours'];
		$steps[$i]['duration_minutes'] = $duration['minutes'];
		$steps[$i]['duration_seconds'] = $duration['seconds'];
	}
	
	ob_start();
	
	// --- Ini Template row for ingredient -----------------------------
	?>
	<div class="ingredient_template row" style="display: none;" data-index_ingredient="0">
		<div class="col-md-4">
			<select name="ingredient" class="ingredient" placeholder="Ingredient">
			</select>
		</div>
		<div class="col-md-1">
			<input type="text" class="form-control" placeholder="Amount" name="amount" value="">
		</div>
		<div class="col-md-2">
			<select name="measure_type" class="measure_type" placeholder="Measure type" >
				<?php
				foreach (get_measure_types() as $id_measure => $measure) {
					?>
					<option value="<?=$id_measure;?>"><?=$measure;?></option>
					<?php
				}
				?>
			</select>
		</div>
		<div class="col-md-4">
			<input type="text" class="form-control" placeholder="Note" name="note" value="">
		</div>
		<div class="col-md-1">
			<button type="button" class="remove_row_button btn btn-default col-md-12" onclick="remove_ingredient();">
			<span class="glyphicon glyphicon-trash"></span>
		</button>
		</div>
	</div>
	<?php
	// --- End Template row for ingredient -----------------------------
	
	// --- Ini Template row for step -----------------------------------
	?>
	<div class="step_template" style="display: none; margin-bottom: 10px;" data-index_step="0">
		<div class="row">
<!--
			<div class="panel-heading">Duration</div>
-->
			<div class="col-md-4">
				<input class="form-control" type="text" value="0" name="step_duration_hours">
			</div>
			<div class="col-md-4">
				<input class="form-control" type="text" value="0" name="step_duration_minutes">
			</div>
			<div class="col-md-4">
				<input class="form-control" type="text" value="0" name="step_duration_seconds">
			</div>
		</div>
		<div class="row">
			<div class="col-md-11">
				<textarea type="text" class="form-control col-md-11" placeholder="Step" name="step"></textarea>
			</div>
			<div class="col-md-1">
				<button type="button" class="remove_row_button btn btn-default btn-lg btn-block" onclick="remove_step();">
					<span class="glyphicon glyphicon-trash"></span>
				</button>
			</div>
		</div>
	</div>
	<?php
	// --- End Template row for step -----------------------------------
	?>
	
	
	
	<div class="panel panel-default">
		<div class="panel-heading">Recipe</div>
		<form id="create_recipe_form" method="post" action="index.php">
			<input type="hidden" name="action" value="create_recipe" />
			<input type="text" class="form-control" placeholder="Title" name="title" value="<?=$title;?>">
			<textarea type="text" class="form-control" placeholder="Description" name="description"><?=$description;?></textarea>
			
			
			<div class="panel panel-default">
				<div class="panel-heading">Duration</div>
				<div class="row">
					<div class="col-md-4">
						<input class="form-control" type="text" name="duration_hours" value="<?=$duration_hours;?>">
					</div>
					<div class="col-md-4">
						<input class="form-control" type="text" name="duration_minutes" value="<?=$duration_minutes;?>">
					</div>
					<div class="col-md-4">
						<input class="form-control" type="text" name="duration_seconds" value="<?=$duration_seconds;?>">
					</div>
				</div>
			</div>
			
			<div class="input-group">
				<span class="input-group-addon">Servings</span>
				<input class="form-control" type="text" name="servings" value="<?=$servings;?>">
			</div>
			<div class="input-group">
				<span class="input-group-addon">Tags</span>
				<input name="tags" class="form-control" type="text" value="<?=$tags;?>" />
			</div>
		</form>
	</div>
	
	<div class="panel panel-default">
		<div class="ingredients_title panel-heading">Ingredients</div>
		
		<button type="button" class="btn btn-default btn-block add_ingredient_row" onclick="add_ingredient();">
			Add ingredient
			<span class="glyphicon glyphicon-plus"></span>
		</button>
	</div>
	
	<div class="panel panel-default">
		<div class="steps_title panel-heading">Steps</div>
		
		<button type="button" class="btn btn-default btn-block add_step_row" onclick="add_step();">
			Add step
			<span class="glyphicon glyphicon-plus"></span>
		</button>
	</div>
	
	<div class="panel panel-default">
		<button type="button" class="btn btn-default btn-lg btn-block save_recipe_row" onclick="save_recipe();">
			Save recipe
			<span class="glyphicon glyphicon-save"></span>
		</button>
	</div>
	<form id="form_data_to_send" method="post" action="index.php?action=save_recipe&page=create_recipe">
		<input type="hidden" name="data" value="" />
	</form>
	
	<script type="text/javascript">
		var ingredients = <?=json_encode($ingredients);?>;
		var steps = <?=json_encode($steps);?>;
		
		function save_recipe()
		{
			var recipe = {};
			recipe['title'] = $("input[name='title']").val();
			recipe['description'] = $("textarea[name='description']").val();
			recipe['duration'] = <?=SECONDS_1_HOUR;?> * parseInt($("input[name='duration_hours']").val());
			recipe['duration'] += <?=SECONDS_1_MINUTE;?> * parseInt($("input[name='duration_minutes']").val());
			recipe['duration'] += parseInt($("input[name='duration_seconds']").val());
			recipe['servings'] = $("input[name='servings']").val();
			recipe['tags'] = $.map($("input[name='tags']").val().split(","), $.trim);
			
			recipe['ingredients'] = [];
			$.each(
				$(".ingredient_row"),
				function(i, ingredient)
				{
					var temp = {};
					
					temp["id"] =
						$("select[name='ingredient']", ingredient).val();
					temp["amount"] =
						$("input[name='amount']", ingredient).val();
					temp["measure_type"] =
						$("select[name='measure_type']", ingredient).val();
					temp["note"] =
						$("input[name='note']", ingredient).val();
					
					recipe['ingredients'][$(ingredient).data('index_ingredient')] = temp;
				}
			);
			
			recipe['ingredients'] =
				$.grep(recipe['ingredients'], function(elem) { return typeof(elem) != 'undefined'; });
			
			recipe['steps'] = [];
			
			$.each(
				$(".step_row"),
				function(i, step)
				{
					var temp = {};
					
					temp['duration'] = <?=SECONDS_1_HOUR;?> *
						parseInt($("input[name='step_duration_hours']", step).val());
					temp['duration'] += <?=SECONDS_1_MINUTE;?> *
						parseInt($("input[name='step_duration_minutes']", step).val());
					temp['duration'] +=
						parseInt($("input[name='step_duration_seconds']", step).val());
					temp['step'] =
						$("textarea[name='step']", step).val();
					
					recipe['steps'][$(step).data('index_step')] = temp;
				}
			);
			
			recipe['steps'] =
				$.grep(recipe['steps'], function(elem) { return typeof(elem) != 'undefined'; });
			
			json_recipe = JSON.stringify(recipe);
			$("input[name='data']").val(json_recipe);
			$("#form_data_to_send").submit();
		}
		
		function remove_step(index)
		{
			$(".step_row")
				.filter(function() { return $(this).data("index_step") === index;})
				.remove();
		}
		
		function remove_ingredient(index)
		{
			$(".ingredient_row")
				.filter(function() { return $(this).data("index_ingredient") === index;})
				.remove();
		}
		
		function add_step(step)
		{
			var duration_hours = 0;
			var duration_minutes = 0;
			var duration_seconds = 0;
			var step_textarea = "";
			
			
			if (typeof(step) != "undefined")
			{
				duration_hours = step['duration_hours'];
				duration_hours = step['duration_hours'];
				duration_hours = step['duration_hours'];
				step_textarea = step['step'];
			}
			
			
			var $cloned_row = $(".step_template")
				.clone()
				.removeClass("step_template")
				.addClass("step_row");
			
			var index_step = $(".step_template").data("index_step");
			index_step++;
			
			$cloned_row.data("index_step", index_step);
			$(".step_template").data("index_step", index_step);
			
			$cloned_row.find(".remove_row_button")
				.attr("onclick", "javascript: remove_step(" + index_step + ");");
			
			
			$("input[name='step_duration_hours']", $cloned_row).TouchSpin
			(
				{
					min: 0,
					boostat: 5,
					maxboostedstep: 10,
					postfix: 'hours'
				}
			).val(duration_hours);
			$("input[name='step_duration_minutes']", $cloned_row).TouchSpin
			(
				{
					min: 0,
					boostat: 5,
					maxboostedstep: 10,
					postfix: 'minutes'
				}
			).val(duration_minutes);
			$("input[name='step_duration_seconds']", $cloned_row).TouchSpin
			(
				{
					min: 0,
					boostat: 5,
					maxboostedstep: 10,
					postfix: 'seconds'
				}
			).val(duration_hours);
			
			$("textarea[name='step']", $cloned_row).val(step_textarea);
			
			
			$cloned_row
				.insertBefore(".add_step_row")
				.show();
		}
		
		function add_ingredient(ingredient)
		{
			var id = null;
			var name = null;
			var measure_type = null;
			var amount = "";
			var note = "";
			if (typeof(ingredient) != "undefined")
			{
				id = ingredient['id'];
				name = ingredient['name'];
				measure_type = ingredient['measure_type'];
				amount = ingredient['amount'];
				note = ingredient['note'];
			}
			
			var $cloned_row = $(".ingredient_template")
				.clone()
				.removeClass("ingredient_template")
				.addClass("ingredient_row");
			
			var index_ingredient = $(".ingredient_template")
				.data("index_ingredient");
			index_ingredient++;
			
			$cloned_row.data("index_ingredient", index_ingredient);
			$(".ingredient_template").data("index_ingredient", index_ingredient);
			
			$cloned_row.find(".remove_row_button")
				.attr("onclick", "javascript: remove_ingredient(" + index_ingredient + ");");
			
			$('.ingredient', $cloned_row).selectize
			(
				{
					create: true,
					sortField: 'ingredient',
					valueField: 'id',
					labelField: 'ingredient',
					searchField: 'ingredient',
					load:
						function(query, callback)
						{
							if (!query.length) return callback();
							$.ajax
							(
								{
									url: 'index.php?ajax=1&action=get_ingredients&query=' + encodeURIComponent(query),
									type: 'GET',
									dataType: 'json',
									error:
									function()
									{
										callback();
									},
									success:
									function(res)
									{
										callback(res.slice(0, 10));
									}
								}
							);
						}
				}
			);
			$('.measure_type', $cloned_row).selectize
			(
				{
					create: true,
					sortField: 'text'
				}
			);
			
			if (id != null)
			{
				$(".ingredient", $cloned_row)[0]
					.selectize.addOption({'id': id, 'ingredient': name});
				$(".ingredient", $cloned_row)[0].selectize.setValue(id);
			}
			$('input[name="amount"]', $cloned_row).val(amount);
			$(".measure_type", $cloned_row)[0].selectize.setValue(measure_type);
			$('input[name="note"]', $cloned_row).val(note);
			
			$cloned_row
				.insertBefore(".add_ingredient_row")
				.show();
		}
		
		
		$("input[name='duration_hours']").TouchSpin
		(
			{
				min: 0,
				boostat: 5,
				maxboostedstep: 10,
				postfix: 'hours'
			}
		);
		$("input[name='duration_minutes']").TouchSpin
		(
			{
				min: 0,
				boostat: 5,
				maxboostedstep: 10,
				postfix: 'minutes'
			}
		);
		$("input[name='duration_seconds']").TouchSpin
		(
			{
				min: 0,
				boostat: 5,
				maxboostedstep: 10,
				postfix: 'seconds'
			}
		);
		$("input[name='servings']").TouchSpin
		(
			{
				min: 1,
				boostat: 5,
				maxboostedstep: 10,
				postfix: '#'
			}
		);
		
		$("input[name='tags']").tagsinput
		(
			{
				typeahead:
				{
					source:  function(query)
					{
						source = $.getJSON('index.php?ajax=1&action=get_tags&query=' + query);
						
						return source;
					}
				}
			}
		);
		
		$(
			function()
			{
				if (ingredients.length > 0)
				{
					$.each
					(
						ingredients,
						function(i, ingredient)
						{
							if (ingredient != null)
							{
								add_ingredient(ingredient);
							}
						}
					);
				}
				
				if (steps.length > 0)
				{
					$.each
					(
						steps,
						function(i, step)
						{
							if (step != null)
							{
								add_step(step);
							}
						}
					);
				}
			}
		);
	</script>
	<?php
	$content["body"] = ob_get_clean();
	
	page($content);
}
?>