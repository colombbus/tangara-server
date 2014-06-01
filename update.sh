#!/bin/bash

git pull
chmod -R 777 app/cache app/logs
chown -R apps:users *

