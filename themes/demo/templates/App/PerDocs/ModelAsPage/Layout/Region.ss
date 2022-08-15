<% include SideBar %>
<div class="content-container unit size3of4 lastUnit">
	<article>
		<h1>$Title</h1>
		<div class="content">
            $URLSegment
        </div>
	</article>
</div>

<% if $HasPerm('CMS_ACCESS') %>$SilverStripeNavigator<% end_if %>
