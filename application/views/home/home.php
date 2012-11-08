<script>
	$(document).ready(function()
	{
		home.init();
	}); 
</script>

<div id="link_input" class="jumbotron">
	<h1>Got Links? Want Twitter Usernames?</h1>
	<p class="lead">
		You're in luck. Just paste those links one per line below and TweetFinder will work it's magic.
	</p>
	<a href="https://twitter.com/share" class="twitter-share-button" data-via="jetrank">Tweet</a>

<p>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</p>
	<textarea id="links_to_check" name="links" class="span7" style="height: 230px;"></textarea>
	<br />
	<br />
	<a id="submit_links" class="btn btn-large btn-success">Fetch Me Twitter Accounts! </a>
	<span id="ajax_spinner" style="display: none; "><img src="http://mozcheck.com/img/ajax-loader.gif" alt="Loading" title="Loading... "> <em>We're checking your links now! This might take a minute or two...</em></span>
</div>

<hr>
<div class="row-fluid marketing">
	<div id="account_results" class="span12">
	    <!--table goes here from template -->
	</div>
</div>
<script id="account_template" type="text/template">
    <h4>Your Results Are In!</h4>
    <br />
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Link</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		{{#result}}
		<tr>
			<td>
			    <a target="_blank" href="{{original_link}}">
			    {{original_link}}
		        </a>
		    </td>
			<td class="center">
			    {{#if twitter_account}}
			     <img src="https://api.twitter.com/1/users/profile_image?screen_name={{twitter_account}}&amp;size=normal" class="avatar hidden-phone">
			     <br />
			     <a target="_blank" title="Open in New Window" href="https://twitter.com/{{twitter_account}}">@{{twitter_account}}</a>
			    {{/if}}
			    
			    {{#unless twitter_account}}
			     -
                {{/unless}}
		    </td>
		</tr>
		{{/result}}
	</tbody>
</table>

</script>

