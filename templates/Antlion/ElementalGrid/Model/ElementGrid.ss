<% if $Title && $ShowTitle %>
    <% with $HeadingTag %>
		<div class="cell">
        	<{$Me} class="element-title">$Up.Title.XML</{$Me}>
		</div>
    <% end_with %>
<% end_if %>
<div class="cell" data-listelement-count="$Elements.Elements.Count">
    <% if $Elements %>
		<div class="grid-x grid-padding-x grid-padding-y {$Top.VerticalAlignClass}" data-listelement-count="$Elements.Elements.Count">
			<% loop $Elements.Elements %>
				<div class="cell {$WidthClass}">{$Me}</div>
			<% end_loop %>
		</div>
	<% end_if %>
</div>