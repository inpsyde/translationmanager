#!/usr/bin/env bash

cd `git rev-parse --show-toplevel`/.git/hooks

ln -s ../../bin/test-min.sh pre-commit
ln -s ../../bin/test-med.sh pre-push