var app = require('http').createServer(handler),
  io = require('socket.io').listen(app),
  parser = new require('xml2json'),
  fs = require('fs');

// creating the server ( localhost:8000 )
app.listen(8000);

console.log('server listening on 79.143.180.199:8000');

// on server started we can load our monitorWarningClient.html page
function handler(req, res) {
  fs.readFile(__dirname + '/monitorWarningClient.html', function(err, data) {
    if (err) {
      console.log(err);
      res.writeHead(500);     
return res.end('Error loading monitorWarningClient.html');
    }
    res.writeHead(200);
   
res.end(data);
  });
}

// creating a new websocket to keep the content updated without any AJAX request
io.sockets.on('connection', function(socket) {
  console.log(__dirname);
  // watching the xml file
  fs.watchFile(__dirname + '/WeatherWarningBulletin.xml', function(curr, prev) {
    // on file change we can read the new xml
    fs.readFile(__dirname + '/WeatherWarningBulletin.xml', function(err, data) {
    if (err) throw err;
    //using regular expression to process xml file
	var strRegex = new RegExp("<title>(.*?)<\/title>","g");
    var match;
    var res;
	if(match = strRegex.exec(data)){;
		json=match[1];
	};
	if(match = strRegex.exec(data)){;
	};
	if(match = strRegex.exec(data)){;
		json=match[1];
		json= json.replace("<![CDATA[", "");
		json = json.replace("]]>", "");
	};

      // send the new data to the monitorWarningClient
      socket.volatile.emit('notification', json);
    });
  });

});