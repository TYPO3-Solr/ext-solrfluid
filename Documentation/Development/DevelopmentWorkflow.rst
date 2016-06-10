====================
Development Workflow
====================

For the development of EXT:solrfluid we use our internal git repository. For the git structure we are using **"git flow"**. Phabricator & Arcanist can be used for code reviews.

The following steps are required to work on a task in solrfluid:

* Install *git flow*
    * See https://github.com/nvie/gitflow and https://github.com/nvie/gitflow/wiki/Installation
* Install [arcanist](https://secure.phabricator.com/book/phabricator/article/arcanist/)
* Checkout origin develop branch (``git checkout --track -b develop origin/develop``)
* Git flow initialze ``git flow init -d``
* Create new feature branch (``git flow feature start my-new-feature``)
* Run tests (See CI Chapter of this document)
* Commit your changes (``git commit -am 'Add some feature'``)
* Send changes to code review (``arc diff``)
* Once the review is complete, you will run (``arc land [branch]``) in the review branch, which will merge
  its contents into the deploy branch you branched off of, and then delete the review branch.
  for help run (``arc help land``)
