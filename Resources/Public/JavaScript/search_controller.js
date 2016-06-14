
function SearchController() {
    var _this = this;

    _this.ajaxType = 7383;

    this.init = function() {
        jQuery("body").delegate("a.solr-ajaxified", "click", _this.handleClickOnAjaxifiedUri);
    },

    this.handleClickOnAjaxifiedUri = function() {
        var clickedLink = jQuery(this);
        var uri = clickedLink.uri();
        uri.addQuery("type", _this.ajaxType);
        jQuery.get(
            uri.href(),
            function(data) {
                var solrContainer = clickedLink.closest(".tx_solr");
                var solrParent = solrContainer.parent();
                solrContainer = solrContainer.replaceWith(data);

                _this.scrollToTopOfElement(solrParent, 50);

                jQuery("body").trigger("tx_solr_updated");
            }
        );
        return false;
    },

    this.scrollToTopOfElement = function(element, deltaTop) {
        jQuery('html, body').animate({
            scrollTop: (element.offset().top - deltaTop) + 'px'
        }, 'slow');
    },

    this.setAjaxType = function(ajaxType) {
        _this.ajaxType = ajaxType;
    }
}

jQuery(document).ready(function() {
    solrSearchController = new SearchController();
    solrSearchController.init();

    if(typeof solrSearchAjaxType !== "undefined") {
        solrSearchController.setAjaxType(solrSearchAjaxType);
    }
});
