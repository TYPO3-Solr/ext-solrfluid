.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _conf-tx-solr-search-solrfluid-introduction:

================
Introduction
================


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

What does it do?
================

The solrfluid addon allows you to use the well known template engine fluid, together with EXT:solr.
To achieve this, solfluid ships the needed domain model classes that can be used during the rendering to access the data

