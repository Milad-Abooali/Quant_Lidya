#!/bin/bash

pm2 start ./aiml/app.py --interpreter=python3 --name AIML
&&
pm2 start ./ruby/app.js --name Ruby
&&
pm2 start ./app1/app.js --name App_1