<% include SideBar %>
<div class="content-container unit size3of4 lastUnit">
	<article>
		<h1>$Title</h1>
		<div class="content">
            $ElementalArea
        </div>
        <div>
            <% loop $Properties %>
                $ME
            <% end_loop %>
        </div>
	</article>
		$Form
		$CommentsForm
</div>

<% if $HasPerm('CMS_ACCESS') %>$SilverStripeNavigator<% end_if %>