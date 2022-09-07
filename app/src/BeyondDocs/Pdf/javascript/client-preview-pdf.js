/*
  You obviously would want to put this in a more appropriate place for your project and to transpile it.
  In this case I've put it here because I want it to be REALLY clear what code goes with what functionality.
  So keeping it together with the PHP makes it a lot easier for this demo code.
*/

(function bootGridfieldPreview($) {
  $.entwine('myapp', ($) => {
    /**
     * When we see this hidden field, create a new select element in the preview panel
     */
    $('.js-preview-data').entwine({
      onmatch() {
        const dropdown = $('<select id="client-pdf-selector"><option disabled selected>-- select a pdf --</option></select>');
        const pdfs = this.data('ids');
        for (id in pdfs) {
          dropdown.append(`<option value="${id}">${pdfs[id]}</option>`);
        }
        $('.cms-navigator').prepend(dropdown);
      },
    });

    /**
     * Enable funky functionality via the preview panel dropdown
     */
    $('select#client-pdf-selector').entwine({
      /**
       * Combine parts of a URL without having to know if there's a trailing slash
       */
      combinePaths(one, two) {
        if (!one.endsWith('/')) {
          one += '/';
        }
        return one + two;
      },

      /**
       * Show the PDF in the preview panel whenever the dropdown selection changes
       */
      onchange() {
        const id = this.val();
        const preview = $('.cms-preview').entwine('.ss.preview');
        preview._unblock();
        const baseUrl = $('.js-preview-data').data('preview-url');
        preview._loadUrl(this.combinePaths(baseUrl, id));
      }
    });

  });
}(jQuery));
