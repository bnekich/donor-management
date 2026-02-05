#!/bin/bash

# Production cleanup script
# Removes test files and development-only files from the deployment

echo "Starting production cleanup..."

# Remove test directory
if [ -d "tests" ]; then
    rm -rf tests
    echo "Removed tests directory"
fi

# Remove test-related files
rm -f phpunit.xml
rm -rf .phpunit.cache
rm -f .phpunit.result.cache

# Remove development documentation
rm -f AGENTS.md CLAUDE.md GEMINI.md

# Remove development tools and config
rm -rf .cursor .github .editorconfig .gitattributes .gitignore .nvmrc
rm -rf .fleet .idea .vscode .zed
rm -f .phpactor.json

# Remove Postman collections
rm -f *_postman_collection.json

echo "Production cleanup complete."
