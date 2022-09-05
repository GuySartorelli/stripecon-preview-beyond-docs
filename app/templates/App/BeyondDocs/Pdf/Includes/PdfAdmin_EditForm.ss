<div class="flexbox-area-grow">
    <% include SilverStripe/Forms/Form %>
</div>
<% if $hasExtraClass('cms-previewable') %>
    <div class="toolbar--south">
        <% include SilverStripe\\Admin\\LeftAndMain_ViewModeSelector SelectID="preview-mode-dropdown-in-content" %>
    </div>
<% end_if %>
