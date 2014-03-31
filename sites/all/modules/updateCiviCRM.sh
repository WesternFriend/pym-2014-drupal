#!/bin/bash

echo 'Putting site in Maintenance Mode.'
drush vset maintenance_mode 1


echo 'Putting site in Online Mode.'
drush vset maintenance_mode 0
