(function($) {
  $.entwine('ss.preview', function($) {
    $('.cms-preview').entwine({
      CurrentURL: null,

      /**
       * Override to ensure there is always a valid state available
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
