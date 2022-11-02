const config = require("./config.js");
const http   = require("http");
const qs 	 = require("qs");

class Py{
	constructor(){
		this.resData='';
	}
	post(path, cookie, postData) {
		return new Promise((resolve, reject) => {
			this.resData = '';
			const data = qs.stringify(postData);
			const options = {
				host: 'localhost',
				port: 5000,
				path:'/'+path,
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
					'Content-Length': data.length,
					'Cookie': cookie
				}
			};
			const req = http.request(options, res => {
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
			req.write(data);
			req.end();
		})
	}
}

module.exports.call = async function (path, postData) {
	let caller = new Py();
	let res = await caller.post(path, '', postData);
	return {
		res: res,
		resData: caller.resData,
		status: res.statusCode,
	}
}
