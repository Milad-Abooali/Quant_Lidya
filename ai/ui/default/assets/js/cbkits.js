/**
 * Codebox JS Kits
 */
class cbkits {
    /**
     * Constructor
     * @param debug
     */
    constructor(debug=false) {
        this.C.success((debug) ? 'Enable console' : 'Disable console','cbkits','SET');
        this.C.debug(debug);
    }

    /*
     *  Custom Console
     */
    static customConsole = {
        DEBUG:false,
        TYPE:'log',
        holder:console,
        /**
         * Clear
         */
        clear(){
            console.clear();
        },
        /**
         * Table
         * @param title
         * @param props
         */
        table(title=null,props=null){
            if(!this.DEBUG) return;
            console.table(data, props);
        },
        /**
         * Group
         * @param title
         */
        group(title=null) {
            if(!this.DEBUG) return;
            console.group(title);
        },
        /**
         * Group End
         * @param title
         */
        gEnd(title=null) {
            if(!this.DEBUG) return;
            console.groupEnd(title);
        },
        /**
         * Info
         * @param val
         * @param title
         * @param act
         */
        info(val=null, title='',act='Info',type='log'){
            if(!this.DEBUG) return;
            let msg = '👁‍🗨‍'+' '+act.padEnd(10)+title.padEnd(20)+ val;
            console[type]("%c" + msg, "color:blue");
        },
        /**
         * Success
         * @param val
         * @param title
         * @param act
         */
        success(val=null, title='',act='Success',type='log'){
            if(!this.DEBUG) return;
            let msg = '✅'.padEnd(2)+act.padEnd(10)+title.padEnd(20)+ val;
            console[type]("%c" + msg, "color:Green");
        },
        /**
         * Warning
         * @param val
         * @param title
         * @param act
         */
        warning(val=null, title='',act='Warning',type='log'){
            if(!this.DEBUG) return;
            let msg = '⚠️'.padEnd(3)+act.padEnd(10)+title.padEnd(20)+ val;
            console[type]("%c" + msg, "color:Orange");
        },
        /**
         * Error
         * @param val
         * @param title
         * @param act
         */
        error(val=null, title='',act='Error',type='trace'){
            if(!this.DEBUG) return;
            let msg = '❌'.padEnd(2)+act.padEnd(10)+title.padEnd(20)+ val;
            console[type]("%c" + msg, "color:Red");
        },
        /**
         * Log
         * @param val
         * @param title
         * @param act
         */
        log(val=null, title='',act='Log',type='log'){
            if(!this.DEBUG) return;
            let msg = '💬'.padEnd(2)+act.padEnd(10)+title.padEnd(20)+ val;
            console[type]("%c" + msg, "color:black");
        },
        /**
         * Debugger
         * @param show
         * @returns {string}
         */
        debug(show){
            this.DEBUG=show;
            console = this.holder;
            this.success((show) ? 'Enable console' : 'Disable console','cbkits','SET');
            if(!show){
                this.holder = console;
                console = {};
                Object.keys(this.holder).forEach(function(key){
                    console[key] = function(){};
                })
            }else{
                console = this.holder;
            }
            return this.DEBUG;
        }
    }
    C = cbkits.customConsole;

    /*
     * Local Storage
     */
    static localStorage = {
        /**
         * Clear
         */
        clear(){
            window.localStorage.clear();
            cbkits.customConsole.success('localStorage is cleared.','cbkits','RUN');
        }
    }
    Storage = cbkits.localStorage;

    /*
     *  Global Functions
     */
    static globalFunctions = {
        /**
         * Bytes to readable
         * @param bytes
         * @returns {string}
         */
        readableByte(bytes) {
            if(bytes < 1024) return bytes + " bytes";
            else if(bytes < 1048576) return(bytes / 1024).toFixed(3) + " KiB";
            else if(bytes < 1073741824) return(bytes / 1048576).toFixed(3) + " MiB";
            else return(bytes / 1073741824).toFixed(3) + " GiB";
        },
        /**
         * Size Of Object
         * @param obj
         * @param readable
         * @returns {number}
         */
        sizeOf(obj, readable=false) {
            let bytes = 0;
            if(obj !== null && obj !== undefined) {
                switch(typeof obj) {
                    case 'number':
                        bytes += 8;
                        break;
                    case 'string':
                        bytes += obj.length * 2;
                        break;
                    case 'boolean':
                        bytes += 4;
                        break;
                    case 'object':
                        var objClass = Object.prototype.toString.call(obj).slice(8, -1);
                        if(objClass === 'Object' || objClass === 'Array') {
                            for(var key in obj) {
                                if(!obj.hasOwnProperty(key)) continue;
                                sizeOf(obj[key]);
                            }
                        } else bytes += obj.toString().length * 2;
                        break;
                }
            }
            return (readable) ?  this.readableByte(bytes) : bytes;
        }
    }
    GFunc = cbkits.globalFunctions;

}


