#!/usr/bin/env python3
"""Pin one or more extensions to the current tip of their tracked branch.

Usage: ./update-extension.py $1 (accepts several also)
       ./update-extension.py --all (runs for all)

when update is ran, it writes the most recent commit for that branch to the YAML file

should be run locally with required git credentials etc for private repos 
@developer note: needs pip install ruamel.yaml first
"""
import os
import shutil
import subprocess
import sys
import tempfile

from ruamel.yaml import YAML

MANIFEST = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'extensions.yaml')


def sh(cmd, **kw):
    return subprocess.run(cmd, check=True, capture_output=True, text=True, **kw).stdout.strip()


def latest_sha(repo, branch):
    ref = sh(['git', 'ls-remote', repo, branch])
    if not ref:
        sys.exit(f'branch {branch} not found on {repo}')
    return ref.split()[0]


def report_changes(name, repo, old, new, branch):
    tmp = tempfile.mkdtemp()
    try:
        sh(['git', 'clone', '--quiet', '--filter=blob:none', '--no-checkout', repo, tmp])
        changed = sh(['git', '-C', tmp, 'diff', '--name-only', old, new]).splitlines()
    finally:
        shutil.rmtree(tmp, ignore_errors=True)
    sql = [f for f in changed if f.endswith('.sql') or '/sql/' in f or f.startswith('sql/')]
    print(f'{name}: {old[:12]} -> {new[:12]} (branch {branch}), {len(changed)} files changed')
    if sql:
        print('⚠️  schema/SQL files changed -- this needs a deliberate migration before deploy:')
        for s in sql:
            print(f'      {s}')


def main():
    args = sys.argv[1:]
    if not args:
        sys.exit('usage: update-extension.py <ExtensionName> [...] | --all')

    yaml = YAML()
    yaml.preserve_quotes = True
    with open(MANIFEST) as f:
        data = yaml.load(f)

    targets = list(data.keys()) if args == ['--all'] else args

    changed_any = False
    for name in targets:
        if name not in data:
            print(f'{name}: not found in manifest, skipping')
            continue
        entry = data[name]
        repo = entry['repo_url']
        branch = entry.get('branch', 'master')
        old = entry.get('commit')
        new = latest_sha(repo, branch)

        if new == old:
            print(f'{name}: already at {new[:12]} (branch {branch}) -- no change')
            continue

        if old:
            report_changes(name, repo, old, new, branch)
        else:
            print(f'{name}: pinning to {new[:12]} (branch {branch}) [first pin]')
        entry['commit'] = new
        changed_any = True

    if changed_any:
        with open(MANIFEST, 'w') as f:
            yaml.dump(data, f)
        print('\nUpdated extensions.yaml -- review the diff, commit, and rebuild.')


if __name__ == '__main__':
    main()
