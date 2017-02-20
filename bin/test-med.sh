#!/usr/bin/env bash

exitCode=0

function assert() {
    echo "$@"
    echo ""
    "$@"

    local status=$?

    if [ $status -ne 0 ]; then
        echo "error with $1" >&2
    fi

    exitCode+=$status
}

assert bin/test-min.sh

vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs

# Hook PHPCompatibility in PHPCS.
# Need to be hard link due to class loading with relative path inside PHPCompatibility.
phpCompPath="vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/PHPCompatibility"
[[ -d ${phpCompPath} ]] && rm -r ${phpCompPath}
cp -av vendor/wimg/php-compatibility/. ${phpCompPath} > /dev/null
assert vendor/bin/phpcs -s --standard=phpcs.xml includes

exit $exitCode