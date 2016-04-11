Apache Solr for TYPO3 - Fluid template rendering
============================================================

This extension is an addon for the TYPO3 "solr" extension

How to run the UnitTests
------------------------

First you need to set some environment variables and boostrap the system with the bootstrap script (you only need to do this once),
the bootstrapper will ask you for some variables (TYPO3 version, EXT:solr version, db host, db user and db passwort) that are needed
for the integration tests:

```bash
chmod u+x ./Tests/Build/*.sh
source ./Tests/Build/bootstrap.sh --local
```

Now you can run the complete test suite:

```bash
./Tests/Build/cibuild.sh
```
