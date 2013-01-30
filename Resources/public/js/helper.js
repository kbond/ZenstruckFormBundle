/**
 * Helper functions
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
var ZenstruckFormHelper = {
    /**
     * Allows a standard <a> tag to become a method="POST" link
     *
     * Add the class "method-post" or "method-delete" to an <a> tag for it's href value to become a POST link.
     * Use the "method-delete" class to generate a confirmation dialog
     */
    initPostLinkHelper: function() {
        $('a.method-post,a.method-delete').on('click', function(e) {
            e.preventDefault();

            //check if delete method - show confirmation if is
            if ($(this).hasClass('method-delete')) {
                if (!confirm("Are you sure you want to delete?")) {
                    return;
                }
            }

            // create delete form and submit
            var $form = $('<form></form>').attr('method', 'POST').attr('action', $(this).attr('href'));
            $form.appendTo($('body'));
            $form.submit();
        });
    },

    /**
     * Adds Symfony2 form collection add and delete button functionality
     */
    initFormCollectionHelper: function() {
        // form collection remove button
        $('.form-collection').on('click', '.form-collection-element a.remove', function(e) {
            e.preventDefault();
            $(this).parents('.form-collection-element').remove();
        });

        // form collection prototype creation
        $('.form-collection-add').on('click', function(e) {
            e.preventDefault();

            var $this = $(this);
            var $container = $this.siblings('div[data-prototype]').first();
            var count = $('.form-collection-element', $container).length;
            var prototype = $container.data('prototype');

            // set count
            prototype = prototype.replace(/__name__/g, count);

            // create dom element
            var $newWidget = $(prototype);

            $container.children('.form-collection').removeClass('hide').append($newWidget);
        });
    },

    initialize: function() {
        this.initFormCollectionHelper();
        this.initPostLinkHelper();
    }
};
