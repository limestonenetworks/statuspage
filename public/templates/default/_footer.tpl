
		<div id="footer">
			<div class="wrapper">
				{if file_exists('templates/default/images/logo_square.jpg')}
				<img src="templates/default/images/logo_square.jpg" />
				{/if}
				<div class="text">
					<ul>
						{foreach from=$footer_links item=link}
						<li><a href="{$link.url}">{$link.title}</a></li>
						{/foreach}
						<li><iframe src="github-buttons/github-btn.html?user=limestonenetworks&repo=statuspage&type=watch"  allowtransparency="true" frameborder="0" scrolling="0" width="62px" height="20px"></iframe></li>
					</ul>
				</div>
			</div>
		</div>


		<script type="text/javascript">
			{literal}
			!function(d,s,id) {
				var js,fjs=d.getElementsByTagName(s)[0];
				if(!d.getElementById(id)){
					js=d.createElement(s);
					js.id=id;
					js.src="//platform.twitter.com/widgets.js";
					fjs.parentNode.insertBefore(js,fjs);
				}
			}
			(document,"script","twitter-wjs");
			{/literal}

			{if $smarty.get.refresh}setRefresh();{/if}
		</script>
	</body>
</html>
