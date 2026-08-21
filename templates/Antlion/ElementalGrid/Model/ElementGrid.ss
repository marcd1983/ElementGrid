<% cached $ID, $LastEdited, $Elements.Elements.Count, $Elements.Elements.Max('LastEdited') %>
<% if $Title && $ShowTitle %>
    <% with $HeadingTag %>
		<{$Me} class="element-title">$Up.Title.XML</{$Me}>
    <% end_with %>
<% end_if %>
	<% if $Content %>
			<div class="element-content">
				$Content
			</div>
	<% end_if %>

    <% if $Elements %>
		<div class="element-grid grid-x <% if not $Top.NoGridSpace %>grid-margin-x grid-margin-y<% end_if %> {$Top.VerticalAlignClass} {$Top.HorizontalAlignClass}" data-listelement-count="$Elements.Elements.Count">
			<% loop $Elements.Elements %>
				{$Me}
			<% end_loop %>
		</div>
	<% end_if %>
<% end_cached %>
