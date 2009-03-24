/*
 * sortTable (for jQuery)
 * version: 9.3.23
 * @requires jQuery v1.2 or later
 *
 * Usage:
 *  
 *  $(document).ready(function() {
 *    $("table.sortable").sortTable();
 *  })
 *
 * <table class="sortable">
 *		<thead>
 *			<tr>
 *				<th class="sort-date">Date</th>
 *				<th class="sort-alpha">Name</th>
 *				<th class="sort-numeric">Distance</th>
 *			</tr>
 *		</thead>
 *		<tbody>
 * 		</tbody>
 *	</table>
 */
(function($){
	$.fn.sortTable = function(options){
		var settings = $.extend({}, $.fn.sortTable.defaults, options);
			
		var $table = $(this);
		$('th', $table).each(function(column) {
			var findSortKey;
			if ($(this).is('.sort-alpha')) {
				findSortKey = function($cell) {
					return $cell.text().toUpperCase();
				};
			}
			else if ($(this).is('.sort-numeric')) {
				findSortKey = function($cell) {
					var key = parseFloat($cell.text().replace(/^[^\d.]*/, ''));
					return isNaN(key) ? 0 : key;
				};
			}
			else if ($(this).is('.sort-date')) {
				findSortKey = function($cell) {
					return Date.parse($cell.text());
				};
			}
			if (findSortKey) {
				$(this).click(function() {
					if(settings.sort_field == $(this).text()){
						settings.sort_desc = -settings.sort_desc;
					}
					else{
						settings.sort_field = $(this).text();
						settings.sort_desc = 1;
					}
					
					var rows = $table.find('tbody > tr').get();
					$.each(rows, function(index, row) {
						row.sortKey = findSortKey($(row).children('td').eq(column));
					});
					rows.sort(function(a, b) {
						if (a.sortKey < b.sortKey) return -settings.sort_desc;
						if (a.sortKey > b.sortKey) return settings.sort_desc;
						return 0
					});
					$.each(rows, function(index, row) {
						$table.children('tbody').append(row);
						row.sortKey = null;
					});
					if(settings.should_restripe){
						$table.restripe();
					}
				});
			}
		});
	}
	$.fn.restripe = function(){
		$("tbody tr:odd", this).removeClass("even").addClass("odd");
		$("tbody tr:even", this).removeClass("odd").addClass("even");
		return this;
	}
	$.fn.sortTable.defaults = {
		sort_field: null,
		sort_desc: -1,
		should_restripe: true,
	}
	
})(jQuery);