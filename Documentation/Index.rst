.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: Includes.txt


.. _start:

=======================================
Apache Solr for TYPO3 - Solrfluid
=======================================

.. only:: html

	:Classification:
		solr

	:Version:
		|release|

	:Language:
		en

	:Description:
		Apache Solr for TYPO3 - Fluid Templating. Allows you to render your search results with fluid.

	:Keywords:
		search, full text, index, solr, lucene, fast, query, fluid, templating

	:Copyright:
		2009-2015

	:Author:
		Frans Saris & Timo Schmidt

	:Email:
		timo.schmidt@dkd.de

	:License:
		This document is published under the Open Content License
		available from http://www.opencontent.org/opl.shtml

	:Rendered:
		|today|

	The content of this document is related to TYPO3,
	a GNU/GPL CMS/Framework available from `typo3.org <http://www.typo3.org/>`_.


	**Table of Contents**

.. toctree::
	:maxdepth: 3
	:titlesonly:
	:glob:

	Configuration/Reference

What does it do?
================

The solrfluid addon allows you to use the well known template engine fluid, together with EXT:solr.

Before you start
================

Make sure your solr extension is configured to index everything you need

* EXT:solr is installed
* TypoScript template is included and solr endpoint is configured
* TYPO3 domain record exists
* Solr sites are initialized through "Initialize Solr connections"
* Solr checks in the reports module are green

If you run into any issues with setting up the base EXT:solr extension, please
consult the `documentation <https://forge.typo3.org/projects/extension-solr/wiki>`_.
Also please don't hesitate to ask for help on the
`TYPO3 Solr Slack channel <https://typo3.slack.com/messages/ext-solr/>`_

How to migrate
================

To simplify the migration from the old template engine to fluid, both template engines a supported side by side
until version ####TOBEDEFINED### of EXT:solr.

The following steps are needed after installing solrfluid:

* Include the TypoScript: "Template Search - Fluid rendering (include after Default Configuration)s"

* Replace the EXT:solr plugin instance with the EXT:solrfluid replacement:

You can used the fluid rendering instead the normal rendering by using the plugins that are postfixed with "solrfluid" instead the normal pi based plugins.

Example:

Instead using "Search: Form, Result, Additional Components" use "Search: Form, Result, Additional Components (SolrFluid)".
After these steps solrfluid is usable and using the default templates. If you want to use your own once, you can change the template location.

Use custom fluid templates
================

After these steps solrfluid is usable and using the default Templates, Layouts and Partials. If you want to overwrite them,
you can change the TypoScript configuration:

plugin.tx_solrfluid {
	view {
		layoutRootPaths.10 = EXT:yourpath/Layouts/
		partialRootPaths.10 = EXT:yourpath/Partials/
		templateRootPaths.10 = EXT:yourpath/Templates/
    }
}

Migrating facets
================

By default, the rendering of facets is done with the old template engine. If you want to use the fluid rendering, you
can use the type "fluid" to render the facets with fluid. The option "fluid.template", allows you to configure a custom template that
should be used to render this facet.

Example:

plugin.tx_solr.search.faceting.facets {
   type {
        label = Content Type
        field = type
        type = fluid
        fluid {
            template = MyCustomTemplate
        }
   }
}

