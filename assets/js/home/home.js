var home =
{
	init : function()
	{
		home.events.init();
	},

	events :
	{
		config :
		{

		},

		init : function()
		{
			home.events.on_load();
			home.events.submit_links_click();
		},

		/**
		 * methods to call on page load
		 */
		on_load : function()
		{
		},

		submit_links_click : function()
		{
			$('a#submit_links').live('click', function()
			{
				home.updates.processing_links()
				var links_to_check = $('textarea#links_to_check').val();
				home.mods.process_links(links_to_check);
			})
		}
	}, //end events

	mods :
	{
		config :
		{

		},

		process_links : function(links_to_check)
		{
			$.ajax(
			{
				"dataType" : 'json',
				"type" : "post",
				"data" : {links_to_check : links_to_check},
				"url" : 'links/process_links',
				"success" : function(json)
				{
					if (json.result == 'max_links')
					{
						alert("Whoa! Looks like you've added more than 50 links there. Please reduce it to less than 50 as it takes a little time to check them all. Thanks!");
					}
					
					else if (json.result)
					{
						var source = $('#account_template').html();
						var template = Handlebars.compile(source);
						var html = template(json);
						$('#account_results').html(html);
						$('textarea#links_to_check').val('');
					}
					
					//reset the button
					home.updates.processing_links()
				}
			});
		}
	}, //end mods

	updates :
	{

		processing_links : function()
		{
			$('#submit_links').toggle();
			$('#ajax_spinner').toggle();
		},

	} //end updates

};
//end of file something.js