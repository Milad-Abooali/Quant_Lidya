#!/usr/bin/env python
# -*- coding: utf-8 -*-
# coding=utf8
# pip3 install python-aiml
# pip3 install flask
# pip3 install waitress

from flask import Flask, request
import json
import os
import sys
import aiml
import time
import datetime
# Time
time.clock = time.time
def UtcNow():
    now = datetime.datetime.utcnow()
    return int(now.strftime("%s"))

# Get Path
def GetScriptPath():
    return os.path.abspath( os.path.dirname( __file__ ) )

# Path
print(f'Call Path: {os.getcwd()}')
os.chdir( GetScriptPath() )
print(f'Run Path: {os.getcwd()}')

# Brain
brain = aiml.Kernel()
brain.learn("list.xml")
brain.respond("INITIAL AIML")
    
# PID
pid = os.getpid()
print(f'process {pid} Started.')

# Setup flask server
app = Flask(__name__)

@app.route("/", methods = ['GET','POST'])
def root():
    timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    return json.dumps(
        {
        "pid":pid,
        "time":timestamp
        }
    )
    
@app.route("/aiml", methods = ['POST'])
def aiml():
    timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    inputget = request.values.get('input')
    if inputget:
        response = brain.respond(inputget)
    else:
        response = brain.respond('hi')
    return json.dumps(
        {
        "pid":pid,
        "time":timestamp,
        "input":inputget,
        "response":response
        }
    )
    
if __name__ == "__main__":
    from waitress import serve
    serve(app, host="127.0.0.1", port=5000)

