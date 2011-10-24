$(function(){

				// Accordion
				$('.accordion .head').click(function() {
					$(this).next().toggle('slow');
					return false;
				}).next().hide();
				
				$( "#accordion" )
					.accordion({
						header: "> div > h2"
					})
					.sortable({
						axis: "y",
						handle: "h2",
						stop: function() {
							stop = true;
					}
				});
				
				$( "#accordion" ).accordion({
					fillSpace: true,
					autoHeight: false,
					navigation: true,
					collapsible: true
				});
				
				// Tabs
				$('#tabs').tabs();
				
				// Toggle editor action buttons
				// will need to add check for actual state
				$('.editor .actions li a').click(function () {
				      $(this).toggleClass("enabled");
				});
			
});