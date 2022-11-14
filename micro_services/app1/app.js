const config = require('./config.js');

// process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";
const cluster = require('cluster');
const ajax = require("./ajax.js");
const vConsole = require("./vconsole.js");
const python = require("./python.js");
const countryLib = require("country-flags-dial-code");
const fs = require('fs');

if (cluster.isMaster) {
	cluster.fork();
	cluster.on('exit', function(worker, code, signal) {
		const timeOut = (config.isDevEnv) ? 15 : 1
		setTimeout(function(){
			console.log('cluster Error...');
			const wcs = {
				'worker':worker,
				'code':code,
				'signal':signal
			}
			try {
				fs.writeFileSync(`./logs/${process.pid}_E.json`, JSON.stringify(wcs));
			} catch(err) {
				console.error(err);
			}
			if(config.isDevEnv)
				cluster.fork();
		}, timeOut*1000);
	});
}

if (cluster.isWorker) { // Worker

	// Modules
	const		vConsole 	= require('./vconsole.js');
	const		ajax	 	= require('./ajax.js');
	const		python	 	= require('./python.js');
	const 		moment 		= require("moment");
	const 		http	 	= require("http");
	const 		https 		= require('https');
	const		qs		 	= require('qs');
	const	 	express  	= require('express');
	var			app 		= express();
	// const 		Redis 		= require('ioredis');
	const 		MD5      	= require("crypto-js/md5");
	const 		getJSON  	= require('get-json'); 		 // Need Remove
	const    	parser  	= require('groan');			 // Need Remove

	let 	    cookieString=' ';

	// Web Server
	let wServer;
	if(config.ssl===true) {
		const options = {
			cert: fs.readFileSync(config.appPath+'/../ssl/certificate.crt'),	// CERT
			key: fs.readFileSync(config.appPath+'/../ssl/private.key'),		    // KEY
			ca: fs.readFileSync(config.appPath+'/../ssl/bundle'),			    // CA BUNDLE
			requestCert: false,
			rejectUnauthorized: false
		}
		wServer = https.createServer(options, app);
	}
	else {
		wServer = http.createServer(app);
	}
	// Socket IO
	const io = require('socket.io')(wServer,  { cors: { origin: '*' } });
	// Web JSON Response
	app.get('/', function(req, res){
		res.json(
		{
				pid: 	process.pid,
				time:	moment().format("Y-m-d HH:mm:ss.SS")
			  }
		);
	});

	// Online [role]
	let onlineRoleList = {
		guest:0,
		user:0,
		agent:0,
		admin:0
	};
	// Online [session_id]
	let onlineSessionList = {};
	// Online [socket_id]
	let onlineSocketList = {};
	// Get Session By Socket Id
	function getSessionBySocket(socketId){
		if(onlineSocketList[socketId]){
			const sessionId = onlineSocketList[socketId].sessionId;
			return onlineSessionList[sessionId];
		}
	}
	// Get Socket By Session Id
	function getSocketBySession(sessionId){
		
		if(onlineSessionList[sessionId]){
			const socketId = onlineSessionList[sessionId].socketId;
			return onlineSessionList[socketId];
		}
	}
	// Sum Object Values
	const sumValues = obj => Object.values(obj).reduce((a, b) => a + b);

	// Messages Queue Timeout
	let clientMessagesQueue = {};

	// Watch Dog - Redis
	/*
	let redis;
	let redisStatus = 'stopped';
	function watchdogRedis() {
		if(redisStatus==='stopped'){
			try {
				redis = new Redis();
				redisStatus='connecting...';
			} catch (e){
				redisStatus='stopped';
			}
		}
		redis.on('connect',  () => redisStatus='connected');
		redis.on("ready", (err) => redisStatus='ready');
		redis.on("idle",  (err) => redisStatus='idle');
		redis.on("end",   (err) => redisStatus='stopped');
		redis.on("error", function(err) {
			redisStatus='stopped';
			redis.quit();
		});
	}
	*/
	// Watch Dog - Brain
	function checkCRM() {
		return new Promise((resolve, reject) => {
			let options = {
				host: config.appHost,
				path:'/lib/ajax.php',
				agent: new https.Agent({
					rejectUnauthorized: false,
				}),
				headers: {
					'Cookie': cookieString
				}
			};
			https
				.get(options, function(res) {
					if(res.headers['set-cookie']) cookieString = res.headers['set-cookie'][0];
					// console.log('BrainStatus', res.statusCode, cookieString);
					resolve(res.statusCode === 200);
				})
				.on("error", function(e) {
					// console.log('BrainStatus', e);
					resolve(false);
				});
		})
	}
	let crmStatus;
	async function watchdogBrain() {
		crmStatus = await checkCRM();
	}
	// Watch Dog - AIML
	function checkAIML() {
		return new Promise((resolve, reject) => {
			let options = {
				host: 'localhost',
				port:5000,
				path:'/'
			};
			http
				.get(options, function(res) {
					// console.log('BrainStatus', res.statusCode, cookieString);
					resolve(res.statusCode === 200);
				})
				.on("error", function(e) {
					// console.log('BrainStatus', e);
					resolve(false);
				});
		})
	}
	let aimlStatus;
	async function watchdogAIML() {
		aimlStatus = await checkAIML();
	}
	// Watch Dog - Monitor
	let monitorData = {};
	function watchdogApp(socket)  {
		watchdogBrain();
		watchdogAIML();
		// watchdogRedis();
		monitorData = {
			devEnv: config.isDevEnv,
			appSession: cookieString,
			onlineClient: Object.keys(onlineSocketList).length,
			onlineRoleList: onlineRoleList,
			crmStatus: crmStatus,
		//	redisStatus: redisStatus,
			redisStatus: 'Online',
			aiml: aimlStatus,
			ml:1,
			hi:1,
			engines:14,
			addons:2
		};
		socket.emit('eMonitor', monitorData);
		socket.broadcast.emit('eMonitor', monitorData);
		console.clear();
		console.log("= = = = = Monitor Status = = = = =\n", monitorData);
		console.log("= = = = = Online Sockets = = = = =\n", onlineSocketList);
		//console.log("= = = = = Online Sessions = = = = =\n", onlineSessionList);
		vConsole.print();
	}

	// e - eLogin
	function eLogin(socket, data) {
		vConsole.add('User connected | '+ socket.id);
		// Logout First
		if(onlineSocketList[socket.id]){
			const oldRole = onlineSocketList[socket.id].role;
			onlineRoleList[oldRole]--;
		}
		onlineSocketList[socket.id] = {
			sessionId:data.sess
		};
		const postData = {
			sessionId	: data.sess
		};
		ajax.call(data.token, 'app', 'getSession', cookieString, postData).then(call => {
			if (call.status == 200) {
				try{
					const ajaxRes = JSON.parse(call.resData);
					if(ajaxRes.e){
						vConsole.add(ajaxRes);
					} else {
						const role = (ajaxRes.res.app) ? ajaxRes.res.app.role : 'guest';
						onlineRoleList[role]++;
						onlineSessionList[data.sess] = ajaxRes.res;
						onlineSocketList[socket.id].role = role;
					}
				}
				catch (e){
					vConsole.add(call.resData);
				}

			} else {
				vConsole.add(call.res);
			}
			setTimeout(()=>watchdogApp(socket) , 5);
		});
	}

	// e - Initial
	function initial(socket) {
		watchdogApp(socket);
	}

	// e - Connect
	io.on('connection', function(socket){

		// Update Loop
		setInterval(function(){
			socket.emit('eMonitor', monitorData);
		}, 15000);
		// e - As Soon As Client Connect
		setTimeout(()=>initial(socket) , 100);

		// e - On Any Call
		socket.onAny((eventName, ...args) => {
			setTimeout(()=>watchdogApp(socket) , 350);
		});
		// e - Login
		socket.on('eLogin', function(data){
			eLogin(socket, data);
		});
		// e - Disconnect
		socket.on('disconnect', function(){
			vConsole.add('User disconnect | '+ socket.id);
			try{
				onlineRoleList[onlineSocketList[socket.id]];
				onlineRoleList[onlineSocketList[socket.id].role]--;
				delete onlineSessionList[onlineSocketList[socket.id].sessionId];
				delete onlineSocketList[socket.id];
			} catch (e) {
				vConsole.add('User disconnect | Socket Id not Found!');
			} finally {
				setTimeout(()=>watchdogApp(socket) , 5);
			}
		});
		// CRM - Login
		socket.on('crmLogin', (data, callback) => {
			vConsole.add('CRM Login | '+ socket.id);
			let postData = {};
			try{
				postData = {
					sessionId	: onlineSocketList[socket.id].sessionId,
					username	: data.u,
					password	: data.p
				};
			} catch ($e) {
				eLogin(socket, data);
				vConsole.add('re eLogin');
				postData = {
					sessionId	: onlineSocketList[socket.id].sessionId,
					username	: data.u,
					password	: data.p
				};
			}finally {
				ajax.call(data.token, 'app', 'crmLogin', cookieString, postData).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							if(ajaxRes.session) onlineSessionList[ onlineSocketList[socket.id].sessionId ] = ajaxRes.session;
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}

		});
		// CRM - Logout
		socket.on('crmLogout', (data, callback) => {
			vConsole.add('CRM Logout | '+ socket.id);
			const postData = {
				sessionId	: onlineSocketList[socket.id].sessionId
			};
			ajax.call(data.token, 'app', 'crmLogout', cookieString, postData).then(call => {
				if(call.status == 200) {
					try {
						const ajaxRes = JSON.parse(call.resData);
						callback(ajaxRes);
					} catch (e) {
						vConsole.add(call.resData);
					}
				} else {
					vConsole.add(call.res);
				}
				setTimeout(()=>watchdogApp(socket) , 5);
			});
		});
		// CRM - Password Recovery
		socket.on('crmRecovery', (data, callback) => {
			vConsole.add('CRM Password Recovery | '+ socket.id);
			const postData = {
				sessionId	: onlineSocketList[socket.id].sessionId,
				username	: data.u
			};
			ajax.call(data.token, 'app', 'crmRecovery', cookieString, postData).then(call => {
				if(call.status == 200) {
					try {
						const ajaxRes = JSON.parse(call.resData);
						callback(ajaxRes);
					} catch (e) {
						vConsole.add(call.resData);
					}
				} else {
					vConsole.add(call.res);
				}
				setTimeout(()=>watchdogApp(socket) , 5);
			});
		});
		// List Countries
		socket.on('listCountries', (callback) => {
			callback( countryLib.getCountryListMap() );
		});
		// CRM - Register
		socket.on('crmRegister', (data, callback) => {
			vConsole.add('CRM Register | '+ socket.id);
			const postData = {
				sessionId	: onlineSocketList[socket.id].sessionId,
				fname	: data.fname,
				lname	: data.lname,
				email	: data.email,
				phone	: data.phone,
				country	: data.country,
				source		: 'APP',
				campaign	: '',
				affiliate	: '',
				unit_id		: '1',
			};
			ajax.call(data.token, 'app', 'crmRegister', cookieString, postData).then(call => {
				if(call.status == 200) {
					try {
						const ajaxRes = JSON.parse(call.resData);
						callback(ajaxRes);
					} catch (e) {
						vConsole.add(call.resData);
					}
				} else {
					vConsole.add(call.res);
				}
				setTimeout(()=>watchdogApp(socket) , 5);
			});
		});
		// Check Permit
		socket.on('checkPermit', (data, callback) => {
			vConsole.add('Check Permit | '+ socket.id);
			const postData = {
				sessionId	: onlineSocketList[socket.id].sessionId,
				target	: data.target,
				act	 : data.act,
				echo : data.echo
			};
			ajax.call(data.token, 'app', 'checkPermit', cookieString, postData).then(call => {
				if(call.status == 200) {
					try {
						const ajaxRes = JSON.parse(call.resData);
						callback(ajaxRes);
					} catch (e) {
						vConsole.add(call.resData);
					}
				} else {
					vConsole.add(call.res);
				}
				setTimeout(()=>watchdogApp(socket) , 5);
			});
		});
		// Get Screen
		socket.on('getScreen', (data, callback) => {
			vConsole.add('Get Screen | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'getScreen', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}
		});
		// Get Form
		socket.on('getForm', (data, callback) => {
			vConsole.add('Get Form | '+ socket.id);
			vConsole.add(data);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				const postData = {
					sessionId	: onlineSocketList[socket.id].sessionId,
					name	: data.name,
					params	: data.params
				};
				ajax.call(data.token, 'app', 'getForm', cookieString, postData).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}
		});
		// Get Wizard
		socket.on('getWizard', (data, callback) => {
			vConsole.add('Get Wizard | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				const postData = {
					sessionId	: onlineSocketList[socket.id].sessionId,
					name	: data.name
				};
				ajax.call(data.token, 'app', 'getWizard', cookieString, postData).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}
		});
		// CRM - Get Profile
		socket.on('crmGetProfile', (data, callback) => {
			vConsole.add('CRM Get Profile | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				const postData = {
					sessionId: onlineSocketList[socket.id].sessionId,
					screen: data.screen
				};
				ajax.call(data.token, 'app', 'crmGetProfile', cookieString, postData).then(call => {
					if (call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(() => watchdogApp(socket), 5);
				});
			}
		});
		// CRM - Update Profile General
		socket.on('crmUpdateProfileG', (data, callback) => {
			vConsole.add('CRM Update Profile | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'crmUpdateProfileG', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}
		});
		// CRM - Update Profile Extra
		socket.on('crmUpdateProfileE', (data, callback) => {
			vConsole.add('CRM Update Profile | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'crmUpdateProfileE', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}
		});
		// CRM - Update Profile Agreement
		socket.on('crmUpdateProfileAgreement', (data, callback) => {
			vConsole.add('CRM Update Profile | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'crmUpdateProfileAgreement', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}
		});
		// CRM - Get Platform Groups
		socket.on('crmGetPlatformGroups', (data, callback) => {
			vConsole.add('Get Platform Groups | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'crmGetPlatformGroups', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}

		});
		// CRM - Meta Open TP
		socket.on('crmMetaOpenTP', (data, callback) => {
			vConsole.add('Meta Open TP | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'crmMetaOpenTP', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}

		});
		// CRM - Meta Update Login Password
		socket.on('crmUpdateLoginPassword', (data, callback) => {
			vConsole.add('Update TP Password | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'crmUpdateLoginPassword', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}

		});
		// Meta - Get Login Positions
		socket.on('getLoginPositions', (data, callback) => {
			vConsole.add('Get TP Position | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'getLoginPositions', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}

		});
		// Meta - Get Login Statistics
		socket.on('getLoginStatistics', (data, callback) => {
			vConsole.add('Get TP Statistics | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'getLoginStatistics', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}

		});
		// Meta - Close Position
		socket.on('closePosition', (data, callback) => {
			vConsole.add('Close Position | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'closePosition', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}

		});
		// Meta - Get Market Prices
		socket.on('getMarketPrices', (data, callback) => {
			vConsole.add('Get Market Prices | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'getMarketPrices', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}

		});
		// Meta - Get Symbol Chart
		socket.on('getSymbolChart', (data, callback) => {
			vConsole.add('Get Symbol Prices | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'getSymbolChart', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}

		});



		// Meta - Simple Order
		socket.on('simpleOrder', (data, callback) => {
			vConsole.add('Simple Order | '+ socket.id);
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call(data.token, 'app', 'simpleOrder', cookieString, data).then(call => {
					if(call.status == 200) {
						try {
							const ajaxRes = JSON.parse(call.resData);
							callback(ajaxRes);
						} catch (e) {
							vConsole.add(call.resData);
						}
					} else {
						vConsole.add(call.res);
					}
					setTimeout(()=>watchdogApp(socket) , 5);
				});
			}

		});


		// e - Watchdog
		socket.on('eWatchdogApp', function(data){
			// @TODO - Update my data
			vConsole.add('watchdogApp | '+ data.email);
		});
		// e - Update Client
		socket.on('eUpdateClient', (data, callback) => {
			vConsole.add('Update Client | '+ socket.id);
			onlineSocketList[socket.id] = {
				sessionId:data.sess
			};
			const postData = {
				sessionId	: data.sess
			};
			ajax.call(data.token, 'app', 'getSession', cookieString, postData).then(call => {
				if (call.status == 200) {
					try{
						const ajaxRes = JSON.parse(call.resData);
						if(ajaxRes.e){
							vConsole.add(ajaxRes);
						} else {
							const role = (ajaxRes.res.app) ? ajaxRes.res.app.role : 'guest';
							onlineSessionList[data.sess] = ajaxRes.res;
							onlineSocketList[socket.id].role = role;
							callback({
								id		: ajaxRes.res.id,
								role	: role
							});
						}
					}
					catch (e){
						vConsole.add(call.resData);
					}
				} else {
					vConsole.add(call.res);
				}
				setTimeout(()=>watchdogApp(socket) , 5);
			});
		});
		// e - Message
		socket.on('clientMessage', function(data){
			if(onlineSocketList[socket.id].type==='client') {
				data['sessionId'] = onlineSocketList[socket.id].sessionId;
				ajax.call('handler', cookieString, data).then(call => {
					if (call.status == 200) {
						const msg = JSON.parse(call.resData);
						if (msg.MSG_ID > 0) {
							vConsole.add(`clientMessage | ${onlineSocketList[socket.id].data.email} > ${msg.UUID} > ${msg.MSG_ID}`);
							socket.emit('watchdogApp');
							if(Object.keys(agentsList).length>0){
								socket.broadcast.emit('newClientMessage', {
									SESSION_ID: data['sessionId'],
									MSG_ID: msg.MSG_ID,
									handler: call.resData
								});
								clientMessagesQueue[msg.MSG_ID] = setTimeout(function(){
									io.to(clientsList[data['sessionId']].socketId).emit('handlerMessage',call.resData);
									socket.broadcast.emit('newClientMessageEnd', {
										SESSION_ID: data['sessionId'],
										MSG_ID: msg.MSG_ID,
										handler: call.resData
									});
								}, 1000*60*15);
								// clearTimeout(clientMessagesQueue[msg.MSG_ID]);
							} else {
								io.to(clientsList[data['sessionId']].socketId).emit('handlerMessage',call.resData);
							}
						} else {
							vConsole.add('No response from brain!');
							vConsole.add(call.resData);
							socket.emit('watchdogApp');
						}
					} else {
						vConsole.add(call.res);
					}
				});
			} else {
				vConsole.add('Error | Wrong side try to send message!');
			}
		});
		// e - Skip Auto Response
		socket.on('skipAutoRes', function(data){
			clearTimeout(clientMessagesQueue[data.MSG_ID]);
			vConsole.add('Skip Auto Respons for '+data.MSG_ID);
			socket.emit('SkippedAutoRes',data.MSG_ID);
		});
		// e - AIML
		socket.on('apiAIML', function(data){
			let postData = JSON.parse(data);
			python.call('aiml', postData).then(call => {
				if (call.status == 200) {
					const resData = JSON.parse(call.resData);
					if (resData.pid > 0) {
						vConsole.add(`API AIML | ${onlineSocketList[socket.id].data.email} < ${postData.MSG_ID} < ${resData.response}`);
						socket.emit('watchdogApp');
						resData['MSG_ID'] = postData.MSG_ID;
						resData['AIE'] = 'AIML';
						socket.emit('resAIML', resData);
					} else {
						vConsole.add('No response from AIML!');
						vConsole.add(resData);
						socket.emit('watchdogApp');
					}
				} else {
					vConsole.add(call.res);
				}
			});
		});
		// e - Archive Messages
		socket.on('archiveMessages', function(data){
			try {
				onlineSocketList[socket.id].sessionId;
			} catch (e) {
				eLogin(socket, data.client);
			} finally {
				data.sessionId = onlineSocketList[socket.id].sessionId;
				ajax.call('archiveMsg', cookieString, data).then(call => {
					if (call.status == 200) {
						vConsole.add(`Archive History | ${onlineSocketList[socket.id].data.email} < ${data.msg_id}`);
					} else {
						vConsole.add(call.res);
					}
				});
			}

		});
		// e - Feedback
		socket.on('feedBack', (postData) => {
			if(onlineSocketList[socket.id].type==='client'){
				ajax.call('uFeedback', cookieString, postData).then(call => {
					if (call.status == 200) {
						vConsole.add(`Feedback | ${onlineSocketList[socket.id].data.email} < ${postData.i} < ${postData.t}`);
						const jsonData = JSON.parse(call.resData);
						socket.emit('uFeedbackAdd', jsonData.res);
						socket.broadcast.emit('uFeedbackAdd', jsonData.res);
					} else {
						vConsole.add(call.res);
					}
				});
			} else {
				vConsole.add('Error | Wrong side try to send feedback!');
			}
		});
		// e - Load Client Messages By Date
		socket.on('loadMessages', function(data){
			let postData = {
				date : data.datetime,
				userId : clientsList[data.sessionId].server.isLogin,
				sessionId : data.sessionId,
				socketId : clientsList[data.sessionId].socketId
			};
			ajax.call('loadMsg', cookieString, postData).then(call => {
				if (call.status == 200) {
					const jsonData = JSON.parse(call.resData);
					vConsole.add(`load Chats | ${clientsList[data.sessionId].email} < ${data.datetime}`);
					if(jsonData.res)
						socket.emit('historyMessages', jsonData.res);
				} else {
					vConsole.add(call.res);
				}
			});

		});
		// e - AgentRes
		socket.on('agentRes', function(data){
			clearTimeout(clientMessagesQueue[data.MSG_ID]);
			socket.emit('SkippedAutoRes',data.MSG_ID);
			io.to(clientsList[data['sessionId']].socketId).emit('handlerMessage', data.res);
			vConsole.add('AI | Message Sent');
			socket.emit('clearNewMsg', {
					MSG_ID: data.MSG_ID,
					SESSION_ID: data.sessionId
			});
		});

	});

	// Web Server Start
	wServer.listen(3500, function(){
	   console.clear();
	   console.log('listening on '+config.appUrl+':3500');
	});

}
