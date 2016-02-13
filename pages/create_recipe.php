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
	
	ob_start();
	
	// --- Ini Template row for ingredient -----------------------------
	?>
	<div class="ingredient_template row" style="display: none;" data-index_ingredient="0">
		<div class="col-md-4">
			<select name="template_ingredient" class="ingredient" placeholder="Ingredient">
			</select>
		</div>
		<div class="col-md-1">
			<input type="text" class="form-control" placeholder="Amount" name="template_amount" value="">
		</div>
		<div class="col-md-2">
			<select name="template_measure_type" class="measure_type" placeholder="Measure type" >
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
			<input type="text" class="form-control" placeholder="Note" name="template_note" value="">
		</div>
		<div class="col-md-1">
			<button type="button" class="remove_row_button btn btn-default col-md-12" onclick="remove_ingredient();">
			<span class="glyphicon glyphicon-trash"></span>
		</button>
		</div>
	</div>
	<?php
	// --- End Template row for ingredient -----------------------------
	?>
	
	
	
	<div class="panel panel-default">
		<div class="panel-heading">Recipe</div>
		<form id="create_recipe_form" method="post" action="index.php">
			<input type="hidden" name="action" value="create_recipe" />
			<input type="text" class="form-control" placeholder="Title" name="title" value="<?=$title;?>">
			<textarea type="text" class="form-control" placeholder="Description" name="description" value="<?=$description;?>"></textarea>
			
			
			<div class="panel panel-default">
				<div class="panel-heading">Duration</div>
				<div class="col-md-6">
					<input class="form-control" type="text" value="0" name="duration_hours">
				</div>
<!--
				</div>
				<div class="input-group">
-->
				<div class="col-md-6">
					<input class="form-control" type="text" value="0" name="duration_minutes">
				</div>
			</div>
			
			<div class="input-group">
				<span class="input-group-addon">Servings</span>
				<input class="form-control" type="text" value="1" name="servings">
			</div>
			<div class="input-group">
				<span class="input-group-addon">Tags</span>
				<input name="tags" class="form-control" type="text" value="" />
			</div>
		</form>
	</div>
	
	<div class="panel panel-default">
		<div class="ingredients_title panel-heading">Ingredients</div>
		
		<div class="row">
			<button type="button" class="btn btn-default col-md-12" onclick="add_ingredient();">
				Add ingredient
				<span class="glyphicon glyphicon-plus"></span>
			</button>
		</div>
		
		
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">Steps</div>
	</div>
	
	<script type="text/javascript">
		function remove_ingredient(index)
		{
			$(".ingredient_row")
				.filter(function() { return $(this).data("index_ingredient") === index;})
				.remove();
		}
		
		function add_ingredient()
		{
			var $cloned_row = $(".ingredient_template")
				.clone()
				.removeClass("ingredient_template")
				.addClass("ingredient_row");
			
			var index_ingredient = $(".ingredient_template").data("index_ingredient");
			index_ingredient++;
			
			$cloned_row.data("index_ingredient", index_ingredient);
			$(".ingredient_template").data("index_ingredient", index_ingredient);
			
			$cloned_row.find(".remove_row_button")
				.attr("onclick", "javascript: remove_ingredient(" + index_ingredient + ");");
			
			$('.ingredient', $cloned_row).selectize({
				create: true,
				sortField: 'text'
			});
			$('.measure_type', $cloned_row).selectize({
				create: true,
				sortField: 'text'
			});
			
			$cloned_row
				.insertAfter(".ingredients_title")
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
						caca = $.getJSON('index.php?ajax=1&action=get_tags&query=' + query);
						
						console.log(caca);
						
						return caca;
					}
				}
			}
		);
	</script>
	<?php
	$content["body"] = ob_get_clean();
	
	page($content);
}
?>