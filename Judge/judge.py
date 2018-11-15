import pymysql as sql
import platform,re, os, shutil, signal, sys, _thread as thread, time, urllib, socketserver as SocketServer, subprocess, codecs

if "-judge" not in sys.argv:
	print("\nArgus Online Judge : Execution Protocol (Linux Version 1.0)");
	print("\nCommand Line Options :")
	print("    -judge    : Connect to the server and start judging submissions.")
	print("    -cache    : Use IO Files in Current Directory instead of downloading them.")
	print()
	sys.exit(0);

timeoffset = 0

def getEnv(key, default):
	return os.environ[key] if key in os.environ else default

# Initialize Database and judge Constants
sql_hostname = getEnv('AURORA_SQL_HOSTNAME', '127.0.0.1')
sql_hostport = getEnv('AURORA_SQL_HOSTPORT', 3306)
sql_username = getEnv('AURORA_SQL_USERNAME', 'aurora')
sql_password = getEnv('AURORA_SQL_PASSWORD', 'aurora')
sql_database = getEnv('AURORA_SQL_DATABASE', 'aurora_main')
HOST, PORT = "0.0.0.0", 8723
#timeoffset = 19800

# Initialize Language Constants
php_prefix = "<?php ini_set('log_errors',1); ini_set('error_log','env/error.txt'); ?>";
ioeredirect = " 0<env/input.txt 1>env/output.txt 2>env/error.txt"

# Addition of new Language requires change below 
# NOTE : You may need to add few lines in 'create' function too on addtion of new language.
langarr = {
"AWK": {"extension": "awk", "system":"find /usr/bin/ -name awk", "execute":"awk -f env/[exename].awk[inputfile]"},
"Bash": {"extension": "sh", "system":"find /bin/ -name bash", "execute":"bash env/[exename].sh[inputfile]"},
"Brain" : {"extension": "b", "system":"find /usr/bin/ -name bf", "execute":"bf env/[exename].b[inputfile]"},
"C" : {"extension":"c", "system":"find /usr/bin/ -name cc", "compile":"cc env/[codefilename].c -O2 -fomit-frame-pointer -o env/[codefilename] -lm"+ioeredirect, "execute":"env/[exename][inputfile]"},
"C++": {"extension": "cpp", "system": "find /usr/bin/ -name g++", "compile": "g++ env/[codefilename].cpp -O2 -fomit-frame-pointer -o env/[codefilename]"+ioeredirect, "execute": "env/[exename][inputfile]"},
"C#" : {"extension": "cs", "system":"find /usr/bin/ -name mcs", "compile":"mcs env/[codefilename].cs -out:env/[codefilename].exe"+ioeredirect, "execute":"mono env/[exename].exe[inputfile]"},
"Java" : {"extension" : "java", "system":"find /usr/bin/ -name javac", "compile":"javac -g:none -Xlint -d env env/[codefilename].java"+ioeredirect, "execute":"java -client -classpath env [exename][inputfile]"},
"JavaScript": {"extension":"js", "system": "find /usr/bin/ -name rhino", "execute":"rhino -f env/[exename].js[inputfile]"},
"Pascal": {"extension":"pas", "system":"find /usr/bin/ -name fpc", "compile":"fpc env/[codefilename].pas -O2 -oenv/[codefilename]"+ioeredirect, "execute":"env/[exename][inputfile]"},
"Perl": {"extension":"pl", "system":"find /usr/bin/ -name perl", "execute":"perl env/[exename].pl[inputfile]"},
"PHP": {"extension":"php", "system":"find /usr/bin/ -name php", "execute":"php -f env/[exename].php[inputfile]"},
"Python": {"extension":"py", "system":"find /usr/bin/ -name python2", "execute":"python2 env/[exename].py[inputfile]"},
"Python3": {"extension":"py", "system":"find /usr/bin/ -name python3", "execute":"python3 env/[exename].py[inputfile]"},
"Ruby": {"extension":"rb", "system":"find /usr/bin/ -name ruby", "execute":"ruby env/[exename].rb[inputfile]"},
"Text": {"extension":"txt"}
}

# Define useful variables

running = 0
mypid = int(os.getpid())
timediff = 0
languages = []

# File Read/Write Functions
def file_read(filename):
	if not os.path.exists(filename): return "";
	f = codecs.open(filename,"r","utf-8"); d = f.read(); f.close(); return d.replace("\r","")
def file_write(filename,data):
	f = codecs.open(filename,"w","utf-8"); f.write(data.replace("\r","")); f.close();

# Systems Check
def system():
	global languages
	if not os.path.isdir("env"): os.mkdir("env");
	for lang in langarr:
		if(lang != "Text" and os.popen(langarr[lang]["system"]).read()!=""): languages.append(lang);

# Program Compilation
def create(codefilename,language):
	if(language not in ('C','C++','C#','Java','Pascal')): return
	print("Compiling Code File ...")
	result = None
	compilecmd = langarr[language]["compile"]
	compilecmd = compilecmd.replace("[codefilename]", codefilename)
	print(compilecmd)
	if language=="Java":
		os.system(compilecmd)
		if ((not os.path.exists("env/"+codefilename+".class")) and (not os.path.exists("env/main/"+codefilename+".class"))):
			result="CE"
	elif language=="C#":
		os.system(compilecmd)
		if not os.path.exists("env/"+codefilename+".exe"): 
			result="CE"
	else:
		os.system(compilecmd)
		if not os.path.exists("env/"+codefilename):
			result="CE"

	if result==None: print("Code File Compiled to Executable.")
	else: print("Compilation Error")
	return result

# Program Execution
def execute(exename,language, timelimit):
	global running, timediff
	inputfile = " <env/input.txt 1>env/output.txt 2>env/error.txt"
	if language == "Java" and not(os.path.exists("env/"+exename+".class")): 
			exename = "main/"+exename
	cmd = 'ulimit -p 100; su judge -c \"'+langarr[language]["execute"]+"; exit;\""
	cmd = cmd.replace("[exename]", exename)
	cmd = cmd.replace("[inputfile]", inputfile)

	os.system("chmod 100 .")
	if(os.path.exists("env/input.txt")): os.system("chmod 777 env/input.txt")
	if(os.path.exists("env/error.txt")): os.system("chmod 777 env/error.txt")
	if(os.path.exists("env/output.txt")): os.system("chmod 777 env/output.txt")

	starttime = time.time()
	proc = subprocess.Popen([cmd], shell=True, preexec_fn=os.setsid)
	try:
		print(proc.communicate(timeout=timelimit))
		t = proc.returncode
	except subprocess.TimeoutExpired:
		t = 124
	endtime = time.time()
	timediff = endtime - starttime
	
	os.system("chmod 750 .")
	os.system("pkill -u judge")
	print("Return Code : "+str(t))
	return t

# Perform system checks
if(platform.system()!='Linux'):
	print("Error : This script can only be run on Linux.")
	sys.exit(0);

# Print Heading
os.system("clear")
print("\nArgus Online Judge : Execution Protocol\n");

# System Check
system()
if len(languages)==0:
	print("Error : No Languages supported on this System.")
	sys.exit(1);
else: languages.append('Text');
print("Supported Languages : "+str(languages)+"\n")
sys.stdout.flush();
def runjudge(runid):
	try:
		# Connect to Database
		print(runid)
		print("Connecting to Server ...")
		link = sql.connect(host=sql_hostname,port=sql_hostport,user=sql_username,passwd=sql_password,db=sql_database,charset='utf8');
		cursor = link.cursor(sql.cursors.DictCursor)
		print("Connected to Server ...")
		print()       

		if "-cache" not in sys.argv: 
			cursor.execute("SELECT runs.rid as rid,runs.pid as pid,tid,runs.language,subs_code.name as name,subs_code.code as code,error,input,problems.output as output,timelimit FROM runs,problems, subs_code WHERE problems.pid=runs.pid and runs.access!='deleted' and runs.rid = subs_code.rid and runs.rid = '"+str(runid)+"' and runs.language in "+str(tuple(languages))+" ORDER BY runs.rid ASC LIMIT 0,1")
		else:
			cursor.execute("SELECT runs1.rid as rid,runs1.pid as pid,tid,runs1.language,subs_code.name as name,subs_code.code as code,error,timelimit FROM runs AS runs1,problems, subs_code WHERE problems.pid=runs1.pid and runs1.rid = subs_code.rid and runs1.access!='deleted' and runs1.rid = '"+str(runid)+"' and runs1.language in "+str(tuple(languages))+" ORDER BY runs1.rid ASC LIMIT 0,1")
		# Select an Unjudged Submission
		run = cursor.fetchone()
		cursor.execute("UPDATE runs SET result='...' WHERE rid='%d'" % (run["rid"]));
		print("Selected Run ID %d for Evaluation." % (run["rid"]));
		
		os.system("rm -r env/*");
		print("Cleared Environment for Program Execution.");
		
		# Initialize Variables
		result = None; timetaken = 0; running = 0
		sys.stdout.flush();
		# Write Code & Input File
		if result==None:
			if run["language"]=="Java": codefilename = run["name"]
			elif run["language"]=="Text": codefilename = "output"
			else: codefilename = "code";
			codefile = codecs.open("env/"+codefilename+"."+langarr[run["language"]]["extension"],"w","utf-8")
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
			print("Code & Input File Created.")

		# Compile, if required
		if result==None:
			result = create(codefilename,run["language"]); # Compile
		sys.stdout.flush();
		# Increase Time Limit in case some languages
		if run["language"] in ('Java', 'Python', 'Python3', 'Ruby', 'PHP', 'C#', 'JavaScript'):
			if run["language"] in ("Java", "C#", "JavaScript"):
				run['timelimit'] *= 2;
			elif run["language"] in ("Python", "Ruby", "PHP", "Python3"):
				run['timelimit'] *= 3;

		# Run the program through a new thread, and kill it after some time
		if result==None and run["language"]!="Text":
			running = 0
			print("Spawning process ...")
			t = execute(codefilename,run["language"], run['timelimit'])
			#while running==0: pass # Wait till process begins
			print("Process Complete!")
			if t == 124:
				result = "TLE"
				timetaken = run["timelimit"]
				#kill(codefilename,run["language"])
				file_write('env/error.txt', "Time Limit Exceeded - Process killed.")
			elif t == 139:
				file_write('env/error.txt', 'SIGSEGV||Segmentation fault (core dumped)\n'+file_read("env/error.txt"))
				timetaken = timediff
			elif t == 136:
				file_write('env/error.txt', 'SIGFPE||Floating point exception\n'+file_read("env/error.txt"))
				timetaken = timediff
			elif t == 134:
				file_write('env/error.txt', 'SIGABRT||Aborted\n'+file_read("env/error.txt"))
				timetaken = timediff
			elif t != 0:
				file_write('env/error.txt', 'NZEC||return code : '+str(t)+"\n"+file_read("env/error.txt"))
				timetaken = timediff
			else:
				timetaken = timediff
		sys.stdout.flush();
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
			if run["output"] is None: run["output"] = ""
			if(output==correct): 
				result="AC"
			elif "S" in run["output"] and re.sub(" +"," ",re.sub("\n *","\n",re.sub(" *\n","\n",output)))==re.sub(" +"," ",re.sub("\n *","\n",re.sub(" *\n","\n",correct))): result = "AC"
			elif(re.sub(r"\s","",output)==re.sub(r"\s","",correct)): result = "AC" if "P" in run["output"] else "PE"
			else: result = "WA"
		print("Output Judgement Complete.")

		# Write results to database
		error = file_read("env/error.txt")
		cursor.execute("UPDATE runs SET time='%.3f',result='%s' WHERE rid=%d" % (float(timetaken),result,int(run["rid"])));
		cursor.execute("UPDATE subs_code SET error=\"%s\",output=\"%s\" WHERE rid=%d" %(re.escape(error), re.escape(output),int(run["rid"])));
		print("Result (%s,%.3f) updated on Server.\n" % (result,timetaken))
		sys.stdout.flush();
		# Commit changes
		link.commit();

		# Disconnect from Server
		try: cursor.close();
		except: pass
		try: link.close();
		except: pass
		print("Disconnected from Server.\n")
		sys.stdout.flush();
	except sql.Error as e:
	    print("MySQL Error %d : %s\n" % (e.args[0],e.args[1]))

class MyTCPHandler(SocketServer.StreamRequestHandler):

	def handle(self):
		# self.rfile is a file-like object created by the handler;
		# we can now use e.g. readline() instead of raw recv() calls
		self.data = self.rfile.readline()
		self.data = self.data.decode('utf-8')
		# Likewise, self.wfile is a file-like object used to write back
		# to the client
		if(self.data == 'rejudge'):
			print((("{} wrote:").format(self.client_address[0])))
			print(self.data)
			link = sql.connect(host=sql_hostname,port=sql_hostport,user=sql_username,passwd=sql_password,db=sql_database);
			cursor = link.cursor(sql.cursors.DictCursor)
			cursor.execute("SELECT rid FROM runs WHERE result is NULL and access != 'deleted'")
			link.close()
			i = 0
			for i in range(cursor.rowcount):
				try:
					run = cursor.fetchone()
					runjudge(run['rid'])
					i = i + 1
				except Exception as e:
					print("Exception in RID : "+str(run['rid'])+"\n"+str(e)+"\n") 
			cursor.close()
		elif(self.data[0:3] == 'del'):
			print((("{} wrote:").format(self.client_address[0])))
			print(self.data)
			print("Deleting io files for pid - "+self.data[3:])
			filename = "io_cache/Aurora Online Judge - Problem ID "+self.data[3:]+" - Input.txt"
			if (os.path.exists(filename)):
				os.remove(filename)
			filename = "io_cache/Aurora Online Judge - Problem ID "+self.data[3:]+" - Output.txt"
			if (os.path.exists(filename)):
				os.remove(filename)
		elif (len(self.data) > 0):
			print((("{} wrote:").format(self.client_address[0])))
			runjudge(int(self.data))
		
	


if __name__ == "__main__":
    # Create the server, binding to localhost on port 8723
	server = SocketServer.TCPServer((HOST, PORT), MyTCPHandler)
	server.request_queue_size = 100
	print('Queue Size : ', server.request_queue_size)
    # Activate the server; this will keep running until you
    # interrupt the program with Ctrl-C
	print("Waiting for submissions... ")
	try:
		server.serve_forever()
	except KeyboardInterrupt as e:
		print(" Keyboard Interrupt Detected.\n")
	except Exception as e:
		print("Exception : "+str(e)+"\n")
	# Release lock
	try:
		lock.close();
		os.unlink("lock.txt");
	except: pass
	print("Released lock on Execution Protocol.\n")

	# Terminate
	print("Argus Online Judge : Execution Protocol Terminated.\n");
