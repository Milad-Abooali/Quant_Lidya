#!/bin/bash
pm2 delete all;
cd ./aiml && pm2 start ./app.py --watch --interpreter=python3  --name LidyaAIML;
cd .. && cd ./ruby && pm2 start ./app.js --watch --name LidyaRuby;
cd .. && cd ./app1 && pm2 start ./app.js --watch --name LidyaApp