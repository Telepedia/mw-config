#!/usr/bin/env bash
# temp build of 8.2.35 ourselves since docker is taking too long
set -euo pipefail

PHP_COMMIT=e3eaff845f7a9f7259c31da54dce0320b47a3bbd
PHP_VERSION=8.2.35
PHP_SHA256=5737b98601fe5ab1b8c5db0c82ed3158acb9283ed3081ffe2faffb9ac81455e6
IMAGE=harbor.telepedia.org/mediawiki/php:${PHP_VERSION}-fpm-trixie

if docker manifest inspect "$IMAGE" >/dev/null 2>&1; then
	echo "$IMAGE already exists in Harbor, skipping build"
	exit 0
fi

workdir=$(mktemp -d)
trap 'rm -rf "$workdir"' EXIT

git -C "$workdir" init -q
git -C "$workdir" fetch -q --depth 1 https://github.com/docker-library/php.git "$PHP_COMMIT"
git -C "$workdir" checkout -q FETCH_HEAD

context="$workdir/${PHP_VERSION%.*}/trixie/fpm"

grep -q "PHP_SHA256=\"${PHP_SHA256}\"" "$context/Dockerfile" || {
	echo "Dockerfile at $PHP_COMMIT doesn't use the php.net SHA-256 for $PHP_VERSION" >&2
	exit 1
}

docker build --pull --platform linux/amd64 -t "$IMAGE" "$context"

actual=$(docker run --rm --platform linux/amd64 "$IMAGE" php -r 'echo PHP_VERSION;')
if [ "$actual" != "$PHP_VERSION" ]; then
	echo "Built image reports PHP $actual, expected $PHP_VERSION" >&2
	exit 1
fi

docker push "$IMAGE"
docker image inspect --format '{{index .RepoDigests 0}}' "$IMAGE"
