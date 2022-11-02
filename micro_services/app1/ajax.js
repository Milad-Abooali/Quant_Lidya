const config = require("./config.js");
const https  = require("https");
const qs 	 = require("qs");

class Ajax{
	constructor(){
		this.resData='';
	}
	post(token, classFile, func, cookie, postData) {
		return new Promise((resolve, reject) => {
			this.resData = '';
			const qsData = qs.stringify(postData);
			const options = {
				host: config.appHost,
				path:`/lib/ajax.php?c=${classFile}&f=${func}&t=${token}&TOKEN=${token}`,
				method: 'POST',
				agent: new https.Agent({
					rejectUnauthorized: false,
				}),
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
					'Content-Length': Buffer.byteLength(qsData),
					'Cookie': cookie
				}
			};
			const req = https.request(options, res => {
				res.on('data', d => {
					this.resData += d
				});
				res.on('end', () => {
					resolve(res);
				});
			});
			req.on('error', error => {
				reject(error);
			});
			req.write(qsData);
			req.end();
		})
	}
}

module.exports.call = async function (token, classFile, func, cookie, postData) {
	let caller = new Ajax();
	let res = await caller.post(token, classFile, func, cookie, postData);
	return {
		res: res,
		resData: caller.resData,
		status: res.statusCode,
	}
}
