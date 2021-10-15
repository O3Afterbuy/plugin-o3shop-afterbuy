#!/bin/bash

if [ $1 ]; then
    git checkout -b "generated_release_branch_$1"
fi

git commit -a -m 'This branch was automatically created by a build script.';
git push --set-upstream origin "generated_release_branch_$1";