const moment = require("moment");
const fs = require('fs');

let virtualConsole = {};

module.exports.add = function(text) {
	virtualConsole[Math.random()] = [moment().format("Y-m-d HH:mm:ss.SS"), text];
}

module.exports.print = function(text) {
	if(Object.keys(virtualConsole).length){

		try {
			fs.writeFileSync(`./logs/${process.pid}_P.json`, JSON.stringify(virtualConsole));
			fs.writeFileSync(`./logs/live_P.json`, JSON.stringify(virtualConsole));
		} catch (err) {
			console.error(err);
		}

		console.log("= = = = = Logs = = = = =\n");
		let i = 0;
		let time;
		Object.keys(virtualConsole).reverse().forEach(function (key){
			if(i++ > 10) return;
			if(time!==virtualConsole[key][0])
				console.log('\x1b[43m%s\x1b[0m','████████████████████  '+virtualConsole[key][0]+'  ████████████████████');
			console.log(virtualConsole[key][1]);
			time = virtualConsole[key][0];
		});
	}
}