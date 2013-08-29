import MySQLdb as sql
import platform,re, os, shutil, sys, thread, time, urllib, SocketServer, subprocess

# Ubuntu 10.10
# sudo apt-get update
# sudo apt-get install bf g++ fpc mono-gmcs openjdk-6-jdk perl php5 python python-mysqldb rhino ruby

if "-judge" not in sys.argv:
	print "\nArgus Online Judge : Execution Protocol (Linux Version 1.0)";
	print "\nCommand Line Options :"
	print "    -judge    : Connect to the server and start judging submissions."
	print "    -unsafe   : Do not skip unsafe programs whose execution may compromise this system."
	print "    -cache    : Use IO Files in Current Directory instead of downloading them."
	print
	sys.exit(0);

timeoffset = 0
# Initialize Database and judge Constants
sql_hostname = '127.0.0.1'
sql_hostport = 3306
sql_username = ''
sql_password = ''
sql_database = ''
HOST, PORT = "127.0.0.1", 8724
#timeoffset = 19800

# Initialize Language Constants
extension = {"Brain":"b","C":"c","C++":"cpp","C#":"cs","Java":"java","JavaScript":"js","Pascal":"pas","Perl":"pl","PHP":"php","Python":"py","Ruby":"rb","Text":"txt"}
php_prefix = "<?php ini_set('log_errors',1); ini_set('error_log','env/error.txt'); ?>";
ioeredirect = " 0<env/input.txt 1>env/output.txt 2>env/error.txt"

# Define useful variables

running = 0
mypid = int(os.getpid())
timediff = 0
languages = []

# File Read/Write Functions
def file_read(filename):
	if not os.path.exists(filename): return "";
	f = open(filename,"r"); d = f.read(); f.close(); return d.replace("\r","")
def file_write(filename,data):
	f = open(filename,"w"); f.write(data.replace("\r","")); f.close();

# Systems Check
def system():
	global languages
	if not os.path.isdir("env"): os.mkdir("env");
	if "-unsafe" in sys.argv: i=1
	else: i=0
	if(i and os.popen("find /usr/bin/ -name bf").read()!=""): languages.append("Brain"); # Brainf**k
	if(os.popen("find /usr/bin/ -name gcc").read()!=""): languages.append("C");
	if(os.popen("find /usr/bin/ -name g++").read()!=""): languages.append("C++");
	if(i and os.popen("find /usr/bin/ -name javac").read()!=""): languages.append("Java");
	if(i and os.popen("find /usr/bin/ -name js").read()!=""): languages.append("JavaScript");
	if(i and os.popen("find /usr/bin/ -name gmcs").read()!=""): languages.append("C#");
	if(i and os.popen("find /usr/bin/ -name fpc").read()!=""): languages.append("Pascal");
	if(i and os.popen("find /usr/bin/ -name perl").read()!=""): languages.append("Perl");
	if(i and os.popen("find /usr/bin/ -name php").read()!=""): languages.append("PHP");
	if(i and os.popen("find /usr/bin/ -name python").read()!=""): languages.append("Python");
	if(i and os.popen("find /usr/bin/ -name ruby").read()!=""): languages.append("Ruby");

# Program Compilation
def create(codefilename,language):
	if(language not in ('C','C++','C#','Java','Pascal')): return
	print "Compiling Code File ..."
	result = None
	if language=="C":
		os.system("gcc env/"+codefilename+".c -lm -lcrypt -O2 -pipe -ansi -DONLINE_JUDGE -w -o env/"+codefilename+ioeredirect);
		if not os.path.exists("env/"+codefilename): result="CE"
	elif language=="C++":
		os.system("g++ env/"+codefilename+".cpp -lm -lcrypt -O2 -pipe -DONLINE_JUDGE -o env/"+codefilename+ioeredirect);
		if not os.path.exists("env/"+codefilename): result="CE"
	elif language=="C#":
		os.system("gmcs env/"+codefilename+".cs -out:env/"+codefilename+".exe"+ioeredirect);
		if not os.path.exists("env/"+codefilename+".exe"): result="CE"
	elif language=="Java":
		os.system("javac -g:none -Xlint -d env env/"+codefilename+".java"+ioeredirect);
		if ((not os.path.exists("env/"+codefilename+".class")) and (not os.path.exists("env/main/"+codefilename+".class"))): result="CE"
	elif language=="Pascal":
		#os.system("gpc env/"+codefilename+".pas -O -march=pentiumpro -fno-asm -Wall -lm -static -dONLINE_JUDGE -o env/"+codefilename+ioeredirect);
		os.system("fpc env/"+codefilename+".pas -oenv/"+codefilename+ioeredirect);
		if not os.path.exists("env/"+codefilename): result="CE"
	if result==None: print "Code File Compiled to Executable."
	else: print "Compilation Error"
	return result

# Program Execution
def execute(exename,language, timelimit):
	global running, timediff
	inputfile = " <env/input.txt"
	if language == "Brain" : cmd = "timeout "+str(timelimit)+" bf env/"+exename+".b"+inputfile
	elif language=="C": cmd = "timeout "+str(timelimit)+" env/"+exename+inputfile
	elif language=="C++": cmd = "timeout "+str(timelimit)+" env/"+exename+inputfile
	elif language=="C#": cmd = "timeout "+str(timelimit)+" mono env/"+exename+".exe"+inputfile
	elif language=="Java":
		if(os.path.exists("env/"+exename+".class")): 
			cmd = "timeout "+str(timelimit)+" java -client -classpath env "+exename+inputfile
		else:
			cmd = "timeout "+str(timelimit)+" java -client -classpath env main/"+exename+inputfile
	elif language=="JavaScript": cmd = "timeout "+str(timelimit)+" rhino -f env/"+exename+".js"+inputfile
	elif language=="Pascal": cmd = "timeout "+str(timelimit)+" env/"+exename+inputfile
	elif language=="Perl": cmd = "timeout "+str(timelimit)+" perl env/"+exename+".pl"+inputfile
	elif language=="PHP": cmd = "timeout "+str(timelimit)+" php -f env/"+exename+".php"+inputfile
	elif language=="Python": cmd = "timeout "+str(timelimit)+" python env/"+exename+".py"+inputfile
	elif language=="Ruby": cmd = "timeout "+str(timelimit)+" ruby env/"+exename+".rb"+inputfile
	os.system("chmod 550 .")
	starttime = time.time()
	p = subprocess.Popen([cmd], shell=True,stdout=subprocess.PIPE, stderr=subprocess.PIPE)
	(stdout, stderr)= p.communicate()
	endtime = time.time()
	timediff = endtime - starttime

	if p.returncode != 0 and p.returncode != 124:
		if language == "Brain" : cmd = "bf env/"+exename+".b"+inputfile
		elif language=="C": cmd = "env/"+exename+inputfile
		elif language=="C++": cmd = "env/"+exename+inputfile
		elif language=="C#": cmd = "mono env/"+exename+".exe"+inputfile
		elif language=="Java":
			if(os.path.exists("env/"+exename+".class")): 
				cmd = "java -client -classpath env "+exename+inputfile
			else:
				cmd = "java -client -classpath env main/"+exename+inputfile
		elif language=="JavaScript": cmd = "rhino -f env/"+exename+".js"+inputfile
		elif language=="Pascal": cmd = "env/"+exename+inputfile
		elif language=="Perl": cmd = "perl env/"+exename+".pl"+inputfile
		elif language=="PHP": cmd = "php -f env/"+exename+".php"+inputfile
		elif language=="Python": cmd = "python env/"+exename+".py"+inputfile
		elif language=="Ruby": cmd = "ruby env/"+exename+".rb"+inputfile
		p = subprocess.Popen([cmd], shell=True,stdout=subprocess.PIPE, stderr=subprocess.PIPE)
		(stdout, stderr)= p.communicate()
	
	os.system("chmod 750 .")
	file_write('env/output.txt', stdout);
	file_write('env/error.txt', stderr);
	t = p.returncode
	return t

# Perform system checks
if(platform.system()!='Linux'):
	print "Error : This script can only be run on Linux."
	sys.exit(0);

# Print Heading
os.system("clear")
print "\nArgus Online Judge : Execution Protocol\n";

# Obtain lock
if(os.path.exists("lock.txt")):
	print "Error : Could not obtain lock on Execution Protocol\n"
	print "This problem usually occurs if you are trying to run two instances of this script on the same machine at the same time. However, if this is not the case, the solution to this problem would be to shut down all instances of this script, manually delete the 'lock.txt' file (which shall be in the same directory as this) and restart a single instance of it. The latter is usually due to an improper termination the last time this was run, or an error in the script itself.\n";
	sys.exit(1);
else:
	lock = open("lock.txt","w");
	print "Obtained lock on Execution Protocol\n";

# System Check
system()
if len(languages)==0:
	print "Error : No Languages supported on this System."
	sys.exit(1);
else: languages.append('Text');
print "Supported Languages : "+str(languages)+"\n"

def runjudge(runid):
        try:
                # Connect to Database
                print runid
                print "Connecting to Server ..."
                link = sql.connect(host=sql_hostname,port=sql_hostport,user=sql_username,passwd=sql_password,db=sql_database);
                cursor = link.cursor(sql.cursors.DictCursor)
                print "Connected to Server ..."
                print        

                if "-cache" not in sys.argv: cursor.execute("SELECT runs.rid as rid,runs.pid as pid,tid,runs.language,subs_code.name as name,subs_code.code as code,error,input,problems.output as output,timelimit FROM runs,problems, subs_code WHERE problems.pid=runs.pid and runs.access!='deleted' and runs.rid = subs_code.rid and runs.rid = '"+str(runid)+"' and runs.language in "+str(tuple(languages))+" ORDER BY runs.rid ASC LIMIT 0,1")
		# else: cursor.execute("SELECT rid,runs.pid as pid,tid,language,runs.name,runs.code as code,error,timelimit,options FROM runs x,problems WHERE problems.pid=runs.pid and (tid=1 AND runs.rid = (SELECT max(rid) FROM runs WHERE x.tid=tid and x.pid=pid)) and runs.access!='deleted' and runs.result is NULL and runs.language in "+str(tuple(languages))+" ORDER BY rid ASC LIMIT 0,1")
                else: cursor.execute("SELECT runs1.rid as rid,runs1.pid as pid,tid,runs1.language,subs_code.name as name,subs_code.code as code,error,timelimit FROM runs AS runs1,problems, subs_code WHERE problems.pid=runs1.pid and runs1.rid = subs_code.rid and runs1.access!='deleted' and runs1.rid = '"+str(runid)+"' and runs1.language in "+str(tuple(languages))+" ORDER BY runs1.rid ASC LIMIT 0,1")	
		# Select an Unjudged Submission

                run = cursor.fetchone()
                cursor.execute("UPDATE runs SET result='...' WHERE rid='%d'" % (run["rid"]));
                print "Selected Run ID %d for Evaluation." % (run["rid"]);
                
                # Clear Environment
                #for f in os.listdir("env"):
                #        if re.search("\["+str(runid)+"\]", f):
                #                os.unlink("env/"+f)
                os.system("rm -r env/*");
                print "Cleared Environment for Program Execution." ;
                
                # Initialize Variables
                result = None; timetaken = 0; running = 0
                
                # Check for "#include<CON>" in case of C/C++
                if result==None and (run["language"]=="C" or run["language"]=="C++") and re.match(r"#include\s*['\"<]\s*[cC][oO][nN]\s*['\">]",run["code"]):
                        print "Language C/C++ : #include<CON> detected."
                        file_write("env/error.txt","Error : Including CON is not allowed.");
                        result = "CE"; timetaken = 0

                if result==None and run["language"]=="C":
                        code = run["code"].split("\n");
                        newcode = ""
                        for line in code:
                                newcode+=re.sub(r"//(.*)$","",line)+"\n"
                        run["code"]=newcode

		# Check for malicious codes
		if result==None and (run["code"].find(":(){ :|:& };:") != -1 or run["code"].find("HTTP") != -1):
			print "Suspicious Code."
			file_write("env/error.txt", "Error : Suspicious code.");
			result = "SC";
			timetaken = 0;

                # Check for malicious codes in Python
                if result==None and run["language"]=="Python" and (
                        run["code"].find("subprocess") != -1 or run["code"].find("os.") != -1) or run["code"].find("os as") != -1:
                        print "Suspicious Code."
                        file_write("env/error.txt","Error : Suspicious code.");
                        result = "SC"; timetaken = 0

                # Write Code & Input File
                if result==None:
                        if run["language"]=="Java": codefilename = run["name"]
                        elif run["language"]=="Text": codefilename = "output"
                        else: codefilename = "code";
                        codefile = open("env/"+codefilename+"."+extension[run["language"]],"w")
                        if(run["language"]=="PHP"): codefile.write(php_prefix); # append prefix for PHP
                        codefile.write(run["code"].replace("\r","")); codefile.close();
                        if "-cache" not in sys.argv: file_write("env/input.txt",run["input"]);
                        else:
                                try:
                                        with open("io_cache/Aurora Online Judge - Problem ID "+str(run["pid"])+" - Input.txt"): pass
                                except IOError:
                                        cursor.execute("Select input, output from problems where pid ="+str(run["pid"]))
                                        filecreate = cursor.fetchone()
                                        file_write("io_cache/Aurora Online Judge - Problem ID "+str(run["pid"])+" - Input.txt", filecreate['input'])
                                        file_write("io_cache/Aurora Online Judge - Problem ID "+str(run["pid"])+" - Output.txt", filecreate['output'])
                                shutil.copyfile("io_cache/Aurora Online Judge - Problem ID "+str(run["pid"])+" - Input.txt","env/input.txt")
                        print "Code & Input File Created."

		# Compile, if required
                if result==None:
                        result = create(codefilename,run["language"]); # Compile

		# Increase Time Limit in case of JavaScript & PHP
                if run["language"] in ('JavaScript','PHP'):
                        run["timelimit"]+=1

		# Run the program through a new thread, and kill it after some time
                if result==None and run["language"]!="Text":
                        running = 0
                        print "Spawning process ..."
                        t = execute(codefilename,run["language"], run['timelimit'])
                        #while running==0: pass # Wait till process begins
                        print "Process Complete!"
                        if t == 124:
                                result = "TLE"
                                timetaken = run["timelimit"]
                                #kill(codefilename,run["language"])
                                file_write('env/error.txt', "Time Limit Exceeded - Process killed.")
                        else:
                                timetaken = timediff

                # Compare the output
                output = ""
                if result==None and run["language"]!="Text" and file_read("env/error.txt")!="":
                        output = file_read("env/output.txt")
                        result = "RTE"
                if result==None:
                        output = file_read("env/output.txt")
                        if "-cache" in sys.argv:
                                run["output"] = file_read("io_cache/Aurora Online Judge - Problem ID "+str(run["pid"])+" - Output.txt")
                        correct = run["output"].replace("\r","")
                        file_write("env/correct.txt",run["output"])
                        if run["output"] is None: run["output"] = ""
                        if(output==correct): 
                                result="AC"
                        elif "S" in run["output"] and re.sub(" +"," ",re.sub("\n *","\n",re.sub(" *\n","\n",output)))==re.sub(" +"," ",re.sub("\n *","\n",re.sub(" *\n","\n",correct))): result = "AC"
                        elif(re.sub(r"\s","",output)==re.sub(r"\s","",correct)): result = "AC" if "P" in run["output"] else "PE"
                        else: result = "WA"
                print "Output Judgement Complete."

		# Write results to database
                error = file_read("env/error.txt")
                if result=="AC": output = ""
                # if "-cache" in sys.argv: output = "/// Output not saved. ///"
                cursor.execute("UPDATE runs SET time='%.3f',result='%s' WHERE rid=%d" % (float(timetaken),result,int(run["rid"])));
		cursor.execute("UPDATE subs_code SET error='%s',output='%s' WHERE rid=%d" %(re.escape(error), re.escape(output),int(run["rid"])));
                #link.commit();
                print "Result (%s,%.3f) updated on Server.\n" % (result,timetaken)

                # Update admin.lastjudge time on server
                cursor.execute("SELECT * FROM admin WHERE variable='lastjudge'");
                #print "cur : "+ str(cursor.rowcount) + " " + str(int(time.time())+timeoffset)
                if cursor.rowcount>0: cursor.execute("UPDATE admin SET value='"+str(int(time.time())+timeoffset)+"' WHERE variable='lastjudge'");
                else: cursor.execute("INSERT INTO admin VALUES ('lastjudge','"+str(int(time.time())+timeoffset)+"')");
                link.commit();

                # Disconnect from Server
                try: cursor.close();
                except: pass
                try: link.close();
                except: pass
                print "Disconnected from Server.\n"
		sys.stdout.flush();
        except sql.Error, e:
            print "MySQL Error %d : %s\n" % (e.args[0],e.args[1])

class MyTCPHandler(SocketServer.StreamRequestHandler):

    def handle(self):
        # self.rfile is a file-like object created by the handler;
        # we can now use e.g. readline() instead of raw recv() calls
        self.data = self.rfile.readline().strip()
        # Likewise, self.wfile is a file-like object used to write back
        # to the client
        if(self.data == 'rejudge'):
                print (("{} wrote:").format(self.client_address[0]))
                print self.data
                link = sql.connect(host=sql_hostname,port=sql_hostport,user=sql_username,passwd=sql_password,db=sql_database);
                cursor = link.cursor(sql.cursors.DictCursor)
                cursor.execute("SELECT rid FROM runs WHERE result is NULL and access != 'deleted'")
                link.close()
                i = 0
                for i in range(cursor.rowcount):
                        run = cursor.fetchone()
                        runjudge(run['rid'])
                        i = i + 1 
                cursor.close()
        elif (len(self.data) > 0):
                print (("{} wrote:").format(self.client_address[0]))
                runjudge(int(self.data))
                
        


if __name__ == "__main__":
	sql_username = raw_input("SQL Username : ")
	sql_password = raw_input("SQL Password : ")
	sql_database = raw_input("SQL Database : ")
    # Create the server, binding to localhost on port 8723
        server = SocketServer.TCPServer((HOST, PORT), MyTCPHandler)
	server.request_queue_size = 100
	print 'Queue Size : ', server.request_queue_size
    # Activate the server; this will keep running until you
    # interrupt the program with Ctrl-C
        print "Supported Languages : " + str(languages) + '\n'
	print ("Waiting for submissions... ")
        try:
		server.serve_forever()
        except KeyboardInterrupt, e:
                print " Keyboard Interrupt Detected.\n"
        except Exception, e:
                print "Exception : "+str(e)+"\n"
        # Release lock
        try:
                lock.close();
                os.unlink("lock.txt");
        except: pass
        print "Released lock on Execution Protocol.\n"

        # Terminate
        print "Argus Online Judge : Execution Protocol Terminated.\n";
