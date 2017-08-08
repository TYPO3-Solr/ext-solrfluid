#!/usr/bin/env bash

SCRIPTPATH=$( cd $(dirname $0) ; pwd -P )
EXTENSION_ROOTPATH="$SCRIPTPATH/../../"

if [[ $* == *--local* ]]; then
    echo -n "Choose a TYPO3 Version (e.g. ^8.7,~6.2.17,~7.6.5): "
    read typo3Version
    export TYPO3_VERSION=$typo3Version

    echo -n "Choose a EXT:solr Version (e.g. dev-release-6.1.x,~3.1.1): "
    read extSolrVersion
    export EXT_SOLR_VERSION=$extSolrVersion

    echo -n "Choose a database hostname: "
    read typo3DbHost
    export TYPO3_DATABASE_HOST=$typo3DbHost

    echo -n "Choose a database name: "
    read typo3DbName
    export TYPO3_DATABASE_NAME=$typo3DbName

    echo -n "Choose a database user: "
    read typo3DbUser
    export TYPO3_DATABASE_USERNAME=$typo3DbUser

    echo -n "Choose a database password: "
    read typo3DbPassword
    export TYPO3_DATABASE_PASSWORD=$typo3DbPassword
fi

if [ -z $TYPO3_VERSION ]; then
	echo "Must set env var TYPO3_VERSION (e.g. ^8.7 or ~7.6.0)"
	exit 1
fi

wget --version > /dev/null 2>&1
if [ $? -ne "0" ]; then
	echo "Couldn't find wget."
	exit 1
fi

export TYPO3_PATH_PACKAGES="${EXTENSION_ROOTPATH}.Build/vendor/"
export TYPO3_PATH_WEB="${EXTENSION_ROOTPATH}.Build/Web/"

echo "Using extension path $EXTENSION_ROOTPATH"
echo "Using package path $TYPO3_PATH_PACKAGES"
echo "Using web path $TYPO3_PATH_WEB"

if [[ $TYPO3_VERSION == "^8.7" ]]; then
    # For ^8.7 we need to use the new testing framework
    # after dropping 7.x support we need to change this in the patched files
    composer require --dev typo3/cms="$TYPO3_VERSION"
    composer require --dev --prefer-source typo3/testing-framework="1.0.1"

    sed  -i 's/Core\Tests\FunctionalTestCase as TYPO3IntegrationTest/TYPO3\TestingFramework\Core\FunctionalTestCase as TYPO3IntegrationTest/g' .Build/Web/typo3conf/ext/solr/Tests/Integration/IntegrationTest.php
    sed  -i 's/Core\Tests\UnitTestCase as TYPO3UnitTest/TYPO3\TestingFramework\Core\UnitTestCase as TYPO3UnitTest/g' .Build/Web/typo3conf/ext/solr/Tests/Unit/UnitTest.php
else
    composer require --dev --prefer-source typo3/cms="$TYPO3_VERSION"
fi

composer require --dev apache-solr-for-typo3/solr="$EXT_SOLR_VERSION"

# Restore composer.json
git checkout composer.json

mkdir -p $TYPO3_PATH_WEB/uploads $TYPO3_PATH_WEB/typo3temp

# Setup Solr using install script
chmod 775 $EXTENSION_ROOTPATH/.Build/Web/typo3conf/ext/solr/Resources/Private/Install/*.sh
$EXTENSION_ROOTPATH/.Build/Web/typo3conf/ext/solr/Resources/Private/Install/install-solr.sh -d "$HOME/solr" -t