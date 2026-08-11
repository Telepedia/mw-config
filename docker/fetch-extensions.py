#!/usr/bin/env python3
"""Pulls all of our MediaWiki extensions and skins from the extension.yaml file into the image

If a specific commit is needed, pass commit: in extensions.yaml otherwise, branch: will be used.
"""
import os
import shutil
import subprocess
import yaml

MW_DIR = os.environ.get('MW_DIR', '/srv/mediawiki')


def run(cmd, cwd=None):
    subprocess.run(cmd, cwd=cwd, check=True)


with open('extensions.yaml') as f:
    manifest = yaml.safe_load(f)

for name, d in manifest.items():
    path = os.path.join(MW_DIR, d['path'])
    repo = d['repo_url']
    branch = d.get('branch', 'master')
    commit = d.get('commit')

    if os.path.isdir(path) and not os.path.isdir(os.path.join(path, '.git')):
        shutil.rmtree(path)
    if os.path.exists(path):
        continue

    if commit:
        os.makedirs(path, exist_ok=True)
        run(['git', 'init', '-q', path])
        run(['git', '-C', path, 'remote', 'add', 'origin', repo])
        run(['git', '-C', path, 'fetch', '-q', '--depth', '1', 'origin', commit])
        run(['git', '-C', path, 'checkout', '-q', 'FETCH_HEAD'])
    else:
        run(['git', 'clone', '-q', '--depth', '1', '--branch', branch, repo, path])

    if d.get('shallow_submodules'):
        run(['git', '-C', path, 'submodule', 'update', '--init', '--recursive'])

    if d.get('composer'):
        run(['composer', 'install', '--no-dev', '--no-interaction', '--prefer-dist', '--ignore-platform-reqs'], cwd=path)

    shutil.rmtree(os.path.join(path, '.git'), ignore_errors=True)

print(f'Fetched {len(manifest)} extensions/skins into {MW_DIR}')
