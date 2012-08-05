#!/bin/sh

# find and deletes dead symlinks
cd /var/www/magento1701/htdocs && find -L . -type l -print0 | xargs -0 rm

lns -afFr /var/www/Mgt_Graylog/htdocs /var/www/magento1701/htdocs