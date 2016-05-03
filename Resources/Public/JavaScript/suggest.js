jQuery(document).ready(function () {

    // Change back to the old behavior of auto-complete
    // http://jqueryui.com/docs/Upgrade_Guide_184#Autocomplete
    jQuery.ui.autocomplete.prototype._renderItem = function (ul, item) {
        return jQuery("<li></li>")
            .data("item.autocomplete", item)
            .append("<a>" + item.label + "</a>")
            .appendTo(ul);
    };

    var req = false;

    jQuery('form[data-suggest]').each(function () {
        var $form = $(this);
        $form.find('input.js-solr-q').autocomplete({
            source: function (request, response) {
                if (req) {
                    req.abort();
                    response();
                }

                req = jQuery.ajax({
                    url: $form.data('suggest'),
                    dataType: 'json',
                    data: {
                        termLowercase: request.term.toLowerCase(),
                        termOriginal: request.term,
                        L: $form.find('input[name="L"]').val()
                    },
                    success: function (data) {
                        req = false;

                        var output = [];

                        jQuery.each(data, function (term, termIndex) {
                            output.push({
                                label: term.replace(new RegExp('(?![^&;]+;)(?!<[^<>]*)(' +
                                    jQuery.ui.autocomplete.escapeRegex(request.term) +
                                    ')(?![^<>]*>)(?![^&;]+;)', 'gi'), '<strong>$1</strong>'),
                                value: term
                            });
                        });

                        response(output);
                    }
                });
            },
            select: function (event, ui) {
                this.value = ui.item.value;
                $form.submit();
            },
            delay: 0,
            minLength: 3
        });
    })
});
