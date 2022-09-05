/*
    You obviously would want to put this in a more appropriate place for your project and to transpile it.
    In this case I've put it here because I want it to be REALLY clear what code goes with what functionality.
    So keeping it together with the PHP makes it a lot easier for this demo code.
*/

(function($) {
  $.entwine('ss.preview', function($) {
    $('.cms-preview').entwine({
      CurrentURL: null,

      /**
       * Override to ensure there is always a valid state available
       * since we're dealing with a whole admin section rather than
       * a specific record.
       *
       * Also forces our preview URL into the state - without this it
       * would try to use the state's preview URL which in this case
       * would be undefined.
       *
       * NOTE: This is unlikely to work as-is in a GridField inside
       * some other CMSPreviewable object's edit form. You'd want to
       * add in a way to get back to the original preview state.
       */
      _getNavigatorStates() {
        let states = this._super();
        if (!states.length) {
          states = [{
            name: 'Unversioned',
            url: this.CurrentURL,
            active: true,
          }];
        }
        const preview = this;
        $.map(states, function(state) {
          state.url = preview.getCurrentURL();
          return state;
        });
        return states;
      },

      /**
       * Enable and expand preview panel and preview some URL
       */
      previewSomeURL(url) {
        this.setCurrentURL(url);
        this.setCurrentStateName('Unversioned');
        this.enablePreview();
        this._loadUrl(url);
        if (!this.is(':visible')) {
          this.changeMode('split', false);
        }
        if (!this.is(':visible')) {
          this.changeMode('preview', false);
        }
      },

    });

    $('.gridfield-preview-btn').entwine({
      onmatch() {
        // This will fire before the normal entwine onclick handler
        // which means we aren't competing with core gridfield handlers.
        this.on('click', this.handleClick);
      },

      handleClick(event) {
        event.preventDefault();
        event.stopPropagation();
        $('.cms-preview').previewSomeURL(this.data('preview-link'));
        return false;
      },
    });

  });
}(jQuery));
