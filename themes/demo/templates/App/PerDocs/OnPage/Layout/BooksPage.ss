<% include SideBar %>
<div class="content-container unit size3of4 lastUnit">
	<article>
		<h1>$Title AWOO</h1>
		<div class="content">
            $ElementalArea
        </div>
        <div>
            <% loop $Books %>
                <div id="$Anchor">
                    <h2>$Title</h2>
                    <strong>$Author</strong><br>
                    <em>$Genre</em>
                </div>
            <% end_loop %>
        </div>
	</article>
		$Form
		$CommentsForm
</div>

<% if $HasPerm('CMS_ACCESS') %>$SilverStripeNavigator<% end_if %>
