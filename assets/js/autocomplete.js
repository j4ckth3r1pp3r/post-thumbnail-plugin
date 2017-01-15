(function( $ ) {
	$(function() {
    function j4ckAutocomplete () {
      var url = j4ckAutocompleteSearch.url + "?action=autocomplete_search";
  		$( ".j4ck-post-autocomplete" ).autocomplete({
  			source: url,
  			delay: 500,
  			minLength: 3,
        select: function (e, ui) {
          $(this).parents('form').find('.j4ck-post-id').val(ui.item.id);
        }
  		});
    }
    j4ckAutocomplete();
    $(document).on('panelsopen widget-updated widget-added', function(e) {
      var dialog = $(e.target);

      if( !dialog.has('.j4ck-post-autocomplete').length ) return;
      j4ckAutocomplete();

      $('.ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content').css('z-index', '999999');
    });
	});
})( jQuery );
