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

assert composer validate --strict

assert vendor/sourcerer-mike/wp-readme-validator/bin/validate.php README.md

exit $exitCode