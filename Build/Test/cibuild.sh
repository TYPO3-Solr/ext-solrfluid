#!/usr/bin/env bash

echo "PWD: $(pwd)"

export TYPO3_PATH_WEB="$(pwd)/.Build/Web"
export TYPO3_PATH_PACKAGES="$(pwd)/.Build/vendor/"

if [ $TRAVIS ]; then
    # Travis does not have composer's bin dir in $PATH
    export PATH="$PATH:$HOME/.composer/vendor/bin"
fi

php-cs-fixer --version > /dev/null 2>&1
if [ $? -eq "0" ]; then
    echo "Check PSR-2 compliance"
    php-cs-fixer fix -v --level=psr2 --dry-run Classes

    if [ $? -ne "0" ]; then
        echo "Some files are not PSR-2 compliant"
        echo "Please fix the files listed above"
        exit 1
    fi
fi

echo "Run unit tests"
UNIT_BOOTSTRAP=".Build/vendor/typo3/cms/typo3/sysext/core/Build/UnitTestsBootstrap.php"
if [[ $TYPO3_VERSION == "^8.7" ]]; then
    UNIT_BOOTSTRAP=".Build/vendor/typo3/testing-framework/Resources/Core/Build/UnitTestsBootstrap.php"
fi

.Build/bin/phpunit --colors -c Build/Test/UnitTests.xml --coverage-html=../../../coverage-unit-solrfluid/ --bootstrap=$UNIT_BOOTSTRAP

echo "Run integration tests"

#
# Map the travis and shell variale names to the expected
# casing of the TYPO3 core.
#
if [ -n $TYPO3_DATABASE_NAME ]; then
	export typo3DatabaseName=$TYPO3_DATABASE_NAME
else
	echo "No environment variable TYPO3_DATABASE_NAME set. Please set it to run the integration tests."
	exit 1
fi

if [ -n $TYPO3_DATABASE_HOST ]; then
	export typo3DatabaseHost=$TYPO3_DATABASE_HOST
else
	echo "No environment variable TYPO3_DATABASE_HOST set. Please set it to run the integration tests."
	exit 1
fi

if [ -n $TYPO3_DATABASE_USERNAME ]; then
	export typo3DatabaseUsername=$TYPO3_DATABASE_USERNAME
else
	echo "No environment variable TYPO3_DATABASE_USERNAME set. Please set it to run the integration tests."
	exit 1
fi

if [ -n $TYPO3_DATABASE_PASSWORD ]; then
	export typo3DatabasePassword=$TYPO3_DATABASE_PASSWORD
else
	echo "No environment variable TYPO3_DATABASE_PASSWORD set. Please set it to run the integration tests."
	exit 1
fi

INTEGRATION_BOOTSTRAP=".Build/vendor/typo3/cms/typo3/sysext/core/Build/FunctionalTestsBootstrap.php"
if [[ $TYPO3_VERSION == "^8.7" ]]; then
    INTEGRATION_BOOTSTRAP=".Build/vendor/typo3/testing-framework/Resources/Core/Build/FunctionalTestsBootstrap.php"
fi

.Build/bin/phpunit --colors -c Build/Test/IntegrationTests.xml --coverage-html=../../../coverage-integration-solrfluid/ --bootstrap=$INTEGRATION_BOOTSTRAP
