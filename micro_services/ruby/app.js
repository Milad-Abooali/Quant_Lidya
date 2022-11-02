// process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";
const cluster = require('cluster');
const ajax = require("./ajax.js");
const vConsole = require("./vconsole.js");
const python = require("./python.js");


if (cluster.isMaster) {
	cluster.fork();
	cluster.on('exit', function(worker, code, signal) {
		const timeOut = (config.isDevEnv) ? 15 : 3
		setTimeout(function(){
			console.log('cluster Error...');
			if(config.isDevEnv){
				console.log('worker',worker);
				console.log('code',code);
				console.log('signal',signal);
			}
			else {
				cluster.fork();
			}
		}, timeOut*1000);
	});
}

if (cluster.isWorker) { // Worker

	// Modules
	const 		config   	= require('./config.js');
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
	const  		fs 			= require('fs');
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

	// Online Clients
	let clientsList = {};
	// Online Agents
	let agentsList = {};
	// Online Sessions
	let onlineSockets = {};
	// Get Session By Socket Id
	function getSessionBySocketId(socketId){
		if(onlineSockets[socketId]){
			let type = onlineSockets[socketId].type;
			let sessionId = onlineSockets[socketId].sessionId;
			if (type==='agent'){
				return agentsList[sessionId];
			}
			else if (type==='client'){
				return clientsList[sessionId];
			}
		}
	}

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
				path:'/ai/ajax/',
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
			appSession: cookieString,
			onlineClients: Object.keys(clientsList).length,
			onlineAgents: Object.keys(agentsList).length,
			crmStatus: crmStatus,
		//	redisStatus: redisStatus,
			redisStatus: 'Online',
			aiml: aimlStatus,
			ml:1,
			hi:1,
			engines:15,
			addons:2
		};
		socket.emit('monitor', monitorData);
		socket.emit('onlineList', {
			users: clientsList,
			agents: agentsList
		});
		socket.broadcast.emit('monitor', monitorData);
		console.clear();
		console.log("= = = = = Status = = = = =\n", monitorData);
 		vConsole.print();
		console.log("= = = = = Online Sockets = = = = =\n", onlineSockets);
//		console.log("= = = = = Online Clients = = = = =\n", clientsList);
//		console.log("= = = = = Online Agents = = = = =\n", agentsList);
	}

	// e - Initial
	function initial(socket) {
		watchdogApp(socket);
	}
	// e - Connect
	io.on('connection', function(socket){

		setInterval(function(){
			socket.emit('onlineList', {
				users: clientsList,
				agents: agentsList
			});
		}, 15000);

		// e - As Soon As Client Connect
		setTimeout(()=>initial(socket) , 100);
		// e - On Any Call
		socket.onAny((eventName, ...args) => {
			setTimeout(()=>watchdogApp(socket) , 350);
		});
		// e - Login
		socket.on('login', function(data) {
			vConsole.add('User connected | '+ data.email);
			onlineSockets [socket.id] = {
				type:'client',
				sessionId : data.session,
				data:data
			};
			data.socketId = socket.id;
			clientsList[data.session] = data;
		});
		// e - Login Agent
		socket.on('loginAgent', function(data){
			vConsole.add('Agent connected | '+ data.email);
			onlineSockets [socket.id] = {
				type:'agent',
				sessionId: data.session,
				data:data
			};
			data.socketId = socket.id;
			agentsList[data.session] = data;
		});
		// e - Disconnect
		socket.on('disconnect', function(){
			vConsole.add('User disconnect | '+ onlineSockets[socket.id].data.email);
			if(onlineSockets[socket.id].type==='client'){
				delete clientsList[onlineSockets[socket.id].sessionId];
			} else if(onlineSockets[socket.id].type==='agent'){
				delete agentsList[onlineSockets[socket.id].sessionId];
			}
			delete onlineSockets[socket.id];
			setTimeout(()=>watchdogApp(socket) , 5);
		});
		// e - Watchdog
		socket.on('watchdogApp', function(data){
			onlineSockets[socket.id].data = data;
			if(onlineSockets[socket.id].type==='client'){
				clientsList[onlineSockets[socket.id].sessionId] = data;
			} else if(onlineSockets[socket.id].type==='agent'){
				agentsList[onlineSockets[socket.id].sessionId] = data;
			}
			vConsole.add('watchdogApp | '+ data.email);
		});
		// e - Message
		socket.on('clientMessage', function(data){
			if(onlineSockets[socket.id].type==='client') {
				data['sessionId'] = onlineSockets[socket.id].sessionId;
				ajax.call('handler', cookieString, data).then(call => {
					if (call.status == 200) {
						try {
							const msg = JSON.parse(call.resData);
							if (msg.MSG_ID > 0) {
								vConsole.add(`clientMessage | ${onlineSockets[socket.id].data.email} > ${msg.UUID} > ${msg.MSG_ID}`);
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
									}, 1000*60);
									// clearTimeout(clientMessagesQueue[msg.MSG_ID]);
								} else {
									io.to(clientsList[data['sessionId']].socketId).emit('handlerMessage',call.resData);
								}
							} else {
								vConsole.add('No response from brain!');
								vConsole.add(call.resData);
								socket.emit('watchdogApp');
							}
						} catch (e) {
							vConsole.add(call.resData);
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
					try {
						const resData = JSON.parse(call.resData);
						if (resData.pid > 0) {
							vConsole.add(`API AIML | ${onlineSockets[socket.id].data.email} < ${postData.MSG_ID} < ${resData.response}`);
							socket.emit('watchdogApp');
							resData['MSG_ID'] = postData.MSG_ID;
							resData['AIE'] = 'AIML';
							socket.emit('resAIML', resData);
						} else {
							vConsole.add('No response from AIML!');
							vConsole.add(resData);
							socket.emit('watchdogApp');
						}
					} catch (e) {
						vConsole.add(call.resData);
					}
				} else {
					vConsole.add(call.res);
				}
			});
		});
		// e - Archive Messages
		socket.on('archiveMessages', function(data){
			data.sessionId = onlineSockets[socket.id].sessionId;
			ajax.call('archiveMsg', cookieString, data).then(call => {
				if (call.status == 200) {
					vConsole.add(`Archive History | ${onlineSockets[socket.id].data.email} < ${data.msg_id}`);
				} else {
					vConsole.add(call.res);
				}
			});
		});
		// e - Feedback
		socket.on('feedBack', (postData) => {
			if(onlineSockets[socket.id].type==='client'){
				ajax.call('uFeedback', cookieString, postData).then(call => {
					if (call.status == 200) {
						vConsole.add(`Feedback | ${onlineSockets[socket.id].data.email} < ${postData.i} < ${postData.t}`);
						try {
							const jsonData = JSON.parse(call.resData);
							socket.emit('uFeedbackAdd', jsonData.res);
							socket.broadcast.emit('uFeedbackAdd', jsonData.res);
						} catch (e) {
							vConsole.add(call.resData);
						}
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
					try {
						const jsonData = JSON.parse(call.resData);
						vConsole.add(`load Chats | ${clientsList[data.sessionId].email} < ${data.datetime}`);
						if(jsonData.res)
							socket.emit('historyMessages', jsonData.res);
					} catch (e) {
						vConsole.add(call.resData);
					}
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
	wServer.listen(3000, function(){
	   console.clear();
	   console.log('listening on '+config.appUrl+':3000');
	});

}
