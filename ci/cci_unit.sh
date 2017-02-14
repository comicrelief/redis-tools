#!/usr/bin/env bash

set -e

[ -d redis-tools ] && cd redis-tools
[ -f ~/.bashrc ] && . ~/.bashrc

mkdir ~/.ssh
touch ~/.ssh/known_hosts
ssh-keyscan github.com >> ~/.ssh/known_hosts

composer install -o

composer test

true
