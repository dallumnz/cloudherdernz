#!/bin/bash
# Architecture Versioning Tool
# Snapshots ARCHITECTURE.md if changed, then clears the working copy
# Called as a post-step after code-reviewer

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ARCHITECTURE_FILE="$SCRIPT_DIR/../../ARCHITECTURE.md"
ARCHITECTURE_DIR="$SCRIPT_DIR/../../documentation/architecture"

# Ensure directory exists
mkdir -p "$ARCHITECTURE_DIR"

# Check if file exists and has content
if [ ! -f "$ARCHITECTURE_FILE" ] || [ ! -s "$ARCHITECTURE_FILE" ]; then
    echo "ARCHITECTURE.md not found or empty - nothing to snapshot"
    exit 0
fi

# Get latest snapshot
LATEST_SNAPSHOT=$(ls -t "$ARCHITECTURE_DIR"/ARCHITECTURE_*.md 2>/dev/null | head -1)

# Compare with current
if [ -n "$LATEST_SNAPSHOT" ] && diff -q "$ARCHITECTURE_FILE" "$LATEST_SNAPSHOT" > /dev/null 2>&1; then
    echo "ARCHITECTURE.md unchanged - skipping snapshot"
    exit 0
fi

# Create snapshot with today's UTC date
DATE_UTC=$(date -u +%Y-%m-%d)
SNAPSHOT_PATH="$ARCHITECTURE_DIR/ARCHITECTURE_${DATE_UTC}.md"

cp "$ARCHITECTURE_FILE" "$SNAPSHOT_PATH"

# Remove the working copy - ensures fresh ARCHITECTURE.md for next feature
rm -f "$ARCHITECTURE_FILE"

echo "✓ Snapshotted to $(basename "$SNAPSHOT_PATH") - ARCHITECTURE.md removed"
