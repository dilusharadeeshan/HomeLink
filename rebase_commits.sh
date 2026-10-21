#!/usr/bin/env bash

set -euo pipefail

# Re-date the latest commits on the current branch across the previous 30 days.
# This rewrites Git history, including merge commits, and changes commit hashes.

NUM_COMMITS="${NUM_COMMITS:-30}"
DAYS_BACK="${DAYS_BACK:-30}"

if ! git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    echo "Error: run this script inside a Git repository." >&2
    exit 1
fi

if ! git diff --quiet || ! git diff --cached --quiet; then
    echo "Error: tracked files contain uncommitted changes." >&2
    echo "Commit or stash those changes before running this script." >&2
    exit 1
fi

CURRENT_BRANCH="$(git symbolic-ref --quiet --short HEAD || true)"

if [ -z "$CURRENT_BRANCH" ]; then
    echo "Error: detached HEAD is not supported. Check out your branch first." >&2
    exit 1
fi

AVAILABLE_COMMITS="$(git rev-list --count HEAD)"

if [ "$AVAILABLE_COMMITS" -lt "$NUM_COMMITS" ]; then
    NUM_COMMITS="$AVAILABLE_COMMITS"
fi

if [ "$NUM_COMMITS" -eq 0 ]; then
    echo "Error: this branch has no commits." >&2
    exit 1
fi

CURRENT_TIME="$(date +%s)"
PERIOD_SECONDS=$((DAYS_BACK * 24 * 60 * 60))
START_TIME=$((CURRENT_TIME - PERIOD_SECONDS))

if [ "$NUM_COMMITS" -gt 1 ]; then
    INTERVAL=$((PERIOD_SECONDS / (NUM_COMMITS - 1)))
else
    INTERVAL=0
fi

TIMESTAMP_MAP="$(mktemp "${TMPDIR:-/tmp}/homelink-rebase-dates.XXXXXX")"
cleanup() {
    rm -f "$TIMESTAMP_MAP"
}
trap cleanup EXIT INT TERM

index=0
while IFS= read -r commit; do
    if [ "$index" -eq $((NUM_COMMITS - 1)) ]; then
        commit_timestamp="$CURRENT_TIME"
    else
        commit_timestamp=$((START_TIME + (index * INTERVAL)))
    fi

    formatted_date="$(date -r "$commit_timestamp" "+%Y-%m-%d %H:%M:%S %z")"
    printf '%s|%s\n' "$commit" "$formatted_date" >> "$TIMESTAMP_MAP"
    index=$((index + 1))
done < <(git rev-list --reverse --max-count="$NUM_COMMITS" HEAD)

BACKUP_BRANCH="backup-before-date-change-$(date "+%Y%m%d-%H%M%S")"
git branch "$BACKUP_BRANCH" HEAD

echo "Branch: $CURRENT_BRANCH"
echo "Commits to update: $NUM_COMMITS"
echo "Backup branch: $BACKUP_BRANCH"
echo "Rewriting commit dates..."

export TIMESTAMP_MAP
FILTER_BRANCH_SQUELCH_WARNING=1 git filter-branch --force --env-filter '
    new_date=$(awk -F "|" -v commit="$GIT_COMMIT" '\''$1 == commit { print $2; exit }'\'' "$TIMESTAMP_MAP")

    if [ -n "$new_date" ]; then
        export GIT_AUTHOR_DATE="$new_date"
        export GIT_COMMITTER_DATE="$new_date"
    fi
' -- "$CURRENT_BRANCH"

echo
echo "Dates updated successfully. Verify them with:"
echo "git log -${NUM_COMMITS} --format=\"%h | author: %aI | committer: %cI | %s\""
echo
echo "If the branch was already pushed, update the remote with:"
echo "git push --force-with-lease origin $CURRENT_BRANCH"
echo
echo "To restore the old history if necessary:"
echo "git reset --hard $BACKUP_BRANCH"
