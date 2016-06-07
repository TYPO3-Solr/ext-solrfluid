=====
Facets
=====

The goal of a good search is, that the user will find what he is looking for as fast as possible.
To support this goal you can give information from the results to the user to "drill down" or "filter" the results
up to a point where he exactly finds what he was looking for. This concept is called "faceting".

Imagine a user in an online shoe shop is searching for the term "shoe", wouldn't it be useful to allow
the user to filter by "gender", "color" and "brand" to find exactly the model where he is looking for?

In the following paragraphs we will get an overview about the different facet types that can be created on a solr field
just by adding a few lines of configuration.

Facet Types
=====

A solr field can contain different type of data, where different facets make sence. The simplest facet is an options "facet".
The "options facet" just contains a list of values and the user can choose one or many of them. A more complex type
could be a "range facet" on a price field. A facet like this needs to allow to filter on a range of a minimum and a maximum value.

In the following paragraphs we will introduce the available facet types in EXT:solrfluid and show how to configure them.

Option
----

Hierarchical
----

Date Range
----

Numeric Range
----


Rendering with fluid
=====

Default partials
----

Facet grouping
----
