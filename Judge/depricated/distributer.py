# This script helps in distributing load on judge.
# This is done by running several copies of judge script on different systems
# and connecting the web interface to distributer (this script) rather than a
# judge. The distributer, then assigns different run id to different judge.

import socket, SocketServer
# List of servers running judge script
servers = [("127.0.0.1", 8724),("127.0.0.1", 8725)]
i = 0
e = 0
class MyTCPHandler(SocketServer.StreamRequestHandler):

    def handle(self):
        global servers, i, e
        self.data = self.rfile.readline().strip()
        if(self.data[0:3] == 'del'):
            for j in range(servers.__len__()):
                s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                s.connect(servers[j])
                s.send(self.data)
                s.close
		print servers[j], "->", self.data
        elif (len(self.data) > 0):
            while(True):
                try:
                    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                    s.connect(servers[i])
                    s.send(self.data)
                    s.close
                    print servers[i], "->", self.data
                    i = (i + 1)%servers.__len__()
                    e = 0
                except Exception, err:
                    print str(err)
                    i = (i + 1)%servers.__len__()
                    e = e + 1
                if(e == 0):
                    break;
                elif(e == servers.__len__()):
                    print "=== FATAL ERROR : ALL SERVERS DOWN ==="
                    break;


if __name__ == "__main__":
	# Distributer's IP and Port
	# Note : Judge's socket setting on web interface should point to distributer
        HOST, PORT = "127.0.0.1", 8723
        server = SocketServer.TCPServer((HOST, PORT), MyTCPHandler)
        server.request_queue_size = 100
        print 'Queue Size : ', server.request_queue_size
        try:
                server.serve_forever()
        except KeyboardInterrupt, e:
                print "Keyboard Interrupt Detected.\n"
        except Exception, e:
                print "Exception : "+str(e)+"\n"

