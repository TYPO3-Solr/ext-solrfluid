
function SearchController() {
    var _this = this;

    this.init = function() {
        jQuery("body").delegate("a.solr-ajaxified", "click", _this.handleClickOnAjaxifiedUri);
    },

    this.handleClickOnAjaxifiedUri = function() {
        var clickedLink = jQuery(this);
        var uri = clickedLink.uri();
        uri.addQuery("eID","tx_solrfluid_search");
        jQuery.get(
            uri.href(),
            function(data) {
                var solrContainer = clickedLink.closest(".tx_solr");
                var solrParent = solrContainer.parent();
                solrContainer = solrContainer.replaceWith(data.html);

                _this.scrollToTopOfElement(solrParent, 50);

                jQuery("body").trigger("tx_solr_updated");
            },
            'jsonp'
        );
        return false;
    },

    this.scrollToTopOfElement = function(element, deltaTop) {
        jQuery('html, body').animate({
            scrollTop: (element.offset().top - deltaTop) + 'px'
        }, 'slow');
    }
}

jQuery(document).ready(function() {
    solrSearchController = new SearchController();
    solrSearchController.init();
});
