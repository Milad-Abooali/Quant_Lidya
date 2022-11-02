const moment = require("moment");

let virtualConsole = {};

module.exports.add = function(text) {
	virtualConsole[Math.random()] = [moment().format("Y-m-d HH:mm:ss.SS"), text];
}

module.exports.print = function(text) {
	if(Object.keys(virtualConsole).length){
		console.log("= = = = = Logs = = = = =\n");
		let i = 0;
		let time;
		Object.keys(virtualConsole).reverse().forEach(function (key){
			if(i++ > 5) return;
			if(time!==virtualConsole[key][0])
				console.log('\x1b[43m%s\x1b[0m','████████████████████  '+virtualConsole[key][0]+'  ████████████████████');
			console.log(virtualConsole[key][1]);
			time = virtualConsole[key][0];
		});
	}
}