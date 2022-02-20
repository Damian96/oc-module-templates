#!/bin/bash

PROJECT='dwhite-oc3';

echo ">>> Watching for file changes..."
inotifywait -q -m -e close_write -r ./module_templates.ocmod |
while read -r directory event filename; do
    TARGET=${directory#./module_templates.ocmod/upload/}
    cp "$directory$filename" -t "/home/damian/workspace/$PROJECT/htdocs/$TARGET"
    echo -e "\t\e[92mCopied $directory$filename\e[39m"
done
echo "Done!"

exit 0