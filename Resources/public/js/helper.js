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

    /**
     * Initializes the AjaxEntity Select2 widget
     */
    initSelect2Helper: function() {
        if(!jQuery().select2) {
            return;
        }

        $('.zenstruck-ajax-entity').each(function() {
            var $this = $(this);
            var required = $this.attr('required');
            var multiple = $this.hasClass('multiple');

            var options = {
                minimumInputLength: 1,
                allowClear: !required,
                multiple: multiple,
                placeholder: function(element) {
                    return $(element).data('placeholder');
                },
                initSelection : function (element, callback) {
                    var initialData = $(element).data('initial');

                    if (initialData) {
                        callback(initialData);
                    }
                },
                ajax: {
                    dataType: 'json',
                    data: function (term) {
                        return {
                            q: term
                        }
                    },
                    results: function (data) {
                        return { results: data }
                    }
                }
            };

            $(this).select2(options);

            if (multiple) {
                $(this).on('change', function(e) {
                    if (e.removed) {
                        var re = new RegExp(e.removed.id, 'g');
                        $this.val($this.val().replace(re, ''));
                    }
                });
            }
        });
    },

    initTunnelHelper: function() {
        $('.zenstruck-tunnel-select[data-callback]').click(function(e) {
            var $this = $(this);

            // create full function name (see http://stackoverflow.com/questions/9228292/javascript-callback-from-form-attribute)
            var callback = $this.data('callback');
            var parts = callback.split('.');

            callback = window;

            $(parts).each(function(){
                callback = callback[this];
            });

            if (typeof callback === 'function') {
                var $element = $this.siblings('.zenstruck-tunnel-id');
                var id = $element.val();

                callback(id, $element);
            }

            e.preventDefault();
        });

        $('.zenstruck-tunnel-clear').click(function(e) {
            $(this)
                .siblings('.zenstruck-tunnel-id').val('')
                .siblings('.zenstruck-tunnel-title').html('')
            ;

            e.preventDefault();
        });
    },

    initialize: function() {
        this.initFormCollectionHelper();
        this.initPostLinkHelper();
        this.initSelect2Helper();
        this.initTunnelHelper();
    }
};
