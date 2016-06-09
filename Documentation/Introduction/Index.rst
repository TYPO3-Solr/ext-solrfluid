.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _conf-tx-solr-search-solrfluid-introduction:

================
Introduction
================

Welcome to the manual of EXT:solrfluid. In this document we want to document the features of solrfluid and help to configure, use and adapt it to your needs.

Thanks
===============

Thanks to all partners and contributors who support the development around Apache Solr & TYPO3.

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


How to get it
================

EXT:solrfluid is available for dkd partners only. If you want to get it go to http://www.typo3-solr.com or call dkd +49 (0)69 - 247 52 18-0

What does it do?
================

The solrfluid addon allows you to use the well known template engine fluid, together with EXT:solr.
To achieve this, solfluid ships the needed domain model classes that can be used during the rendering to access the data

