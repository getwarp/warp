#!/usr/bin/env bash
set -e

composer update --prefer-lowest

composer suggests \
	spaceonfire/collection \
	spaceonfire/command-bus \
	spaceonfire/common \
	spaceonfire/container \
	spaceonfire/criteria \
	spaceonfire/data-source \
	spaceonfire/type \
	spaceonfire/value-object |
	xargs -I '{}' echo '{}'
