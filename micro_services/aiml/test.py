import os

def GetScriptPath():
    return os.path.abspath( os.path.dirname( __file__ ) )

print(GetScriptPath())