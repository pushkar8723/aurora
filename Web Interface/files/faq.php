<center><h1>Frequently Asked Questions</h1></center>
<center><h3>Aurora v2</h3></center>
<table class='faq'><tr class="info"><th>Previous version was working fine, why this upgrade?</th></tr><tr><td>
	<p>At the end of Code Jam Finals, Aurora went into a state where no one can open any page or make submissions for several minutes.
        And at that time there were not even 50 participants! When I read the source code I found that various changes can be made to enhance its performance.
        Also some change can be made so that the judge can run 24/7</p>
</td></tr></table><br/>
<table class='faq'><tr class="info"><th>What are the changes in this version?</th></tr><tr><td>
	<p>There are following changes :</p>
        <ol>
            <li>There are two sections of problems now, Practice Problems and Contest Problems. One can make submissions to practice problems when there is no contest live, i.e. judge is in Passive mode. 
                Contest problems excepts submissions only when judge is in Active mode.</li>
            <li>Two type of problems are there now, 'Active' and 'Inactive', both will be visible to users but submissions can be made only on 'Active' Problems.</li>
            <li>Ranking is done only on Contest Problems. Although there is score on each Practice Problem but its score is calculated separately.</li>
            <li>There is a team page for each team. You can see which practice problems you / your team-mates solved, and problems that are yet to be solved.</li>
            <li>Each contest has its own main page where problems will appear when contest starts also it will contain various Announcements/Notifications related to the contest.</li>
            <li>Judge now runs in a sandbox environment on DC Hub server rather than on virtual machine enabling it to run 24/7, also sockets are used to further improve the performance of judge.</li>
            <li>Some changes to judge and web interface made it possible to set fractional time limit like 0.5 second</li>
        </ol>
</td></tr></table><br/>
<!--table class='faq'><tr class="info"><th>What is the platform configuration?</th></tr><tr><td>
	<p>Your programs will be running on following configurations.</p>
</td></tr></table><br/-->
<table class='faq'><tr class="info"><th>What are the limitations of this version?</th></tr><tr><td>
	<p>Java programmers need to define their main function inside class named 'Main' (like other online judges) or they will get compilation error.
        </p>
</td></tr></table><br/>
<table class='faq'><tr class="info"><th>I liked few features of old judge which are now missing.</th></tr><tr><td>
	<p>Post description of features you liked on Contact Us page and I will try and add them as soon as I can.</p>
</td></tr></table><br/>
<hr/>
<center><h3>Aurora</h3></center>
<br><table class='faq'><tr class="info"><th>What is the Aurora Online Judge?</th></tr><tr><td>
	<p>The Aurora Online Judge is a Programming Contest Control System. It acts as an interface between the judges and the participants of a Computer Programming Contest.</p>
	<p>A Computer Programming Contest is a competition where teams submit (computer program) solutions to judges. The teams are given a set of computer problems to solve in a limited amount of time (for example 3 hours). The judges then give a pass/fail judgement to the submitted solution which is sent back to the teams. The team rankings are computed based on the solutions, when the solutions were submitted and how many attempts were made to solve the problem. The judges testing is a Black box testing where the teams do not have access to the judges' test data.</p>
</td></tr></table>


<br><table class='faq'><tr class="info"><th>How does this website actually work?</th></tr><tr><td>
	<p>The Aurora Online Judge System has three main parts : the SQL Database (which stores all information), the User Interface (the website that you are currently using) and the Execution Protocol (the scripts that actually run the programs you submit). The website essentially just takes information from the Database, formats it to make it look nice, add options to manipulate it, and presents it to the user.</p>
	<p>The data displayed on both sides of the webpage is refreshed a few times per minute (using Ajax) in order to provide you with the latest information conveniently. The User Account system is implemented by Cookies (which are used to save information about whether or not you are currently logged in, and if so, more details about your team).</p>
</td></tr></table>

<br><table class='faq'><tr class="info"><th>What exactly is the Execution Protocol?</th></tr><tr><td>
	<p>Execution Protocol, as mentioned before, is a script that actually runs submitted programs and judges their correctness. The basic functioning of the Execution Protocol can be described as follows :<br></p>
	<ul>
		<li>Select a solution submitted from the database that has not yet been evalutated, and for which a compiler/interpreter is available.</li>
		<li>Compile the source code, if required. If compilation fails, return "Compilation Error".</li>
		<li>Run the program, connecting the Stardard Input, Output and Error Streams to appropriate files.</li>
		<li>Wait for an appropriate amount of time (as specified by the time limit of the problem).</li>
		<li>If the program has not already terminated, kill it and return "Time Limit Exceeded".</li>
		<li>If the STDERR stream (directed to a temporary file) is not empty, return "Run Time Error".</li>
		<li>Now that the program has terminated with the time limit, compare the output with the correct output associated with the problem. If there is a total match, return "Accepted".</li>
		<li>Remove all whitespace characters in the program output and correct output and compare again. If there is a match, return "Presentation Error", or else return "Wrong Answer".
	</ul>
</td></tr></table>
	
<br><table class='faq'><tr class="info"><th>How do I participate here?</th></tr><tr><td>
	<p>The first thing you need to do it register your team using form given on the <a target='new' href='?display=register'>Registration Page</a>. Once you choose a unique team name and give details about the (1-3) members of your team, you need to wait until an Administrator authorizes your account (after verifying its authenticity). Details (with the exception being your Password) provided during registration cannot be changed unless you request an Administrator to do it for you (which means you'll need a good reason). Once that is done, you may login.</p>
	<p>Once logged in, you may the view information you provided during registeration and change your password from the <a target='new' href='?display=account'>Account Settings</a> page. You can access and search through the list of all currently available problems from the <a href='?display=problem' target=new>Problems Index</a> (solved problems will automatically be marked green). If the contest is in Active or Passive Mode, you may also submit solutions to problems by selecting a file to upload or by copy-pasting your code in the area provided, provided the language you have used is supported and allowed for that particular problem. Your code must read from the Stanard Input and print to the Standard Output, and must be efficient (fast) enough to finish within the time limit of the problem. You can see the results of the program run on the <a href='?display=submissions' target=new>Submission Status</a> Page.</p>
	<p>If you have any questions that havent already been answered here, or any ambiguity regarding the problems themselves during the contests, you may use the <a target='new' href='?display=clarifications'>Clarifications</a> Feature to ask Administrators or other teams your question. Usually, clarifications can only been seen by the Admistrators and team that requested it. However, if an Administrator thinks it is appropriate, he may make your question and his reply 'Public', thus allowing all teams to see it.</p>
</td></tr></table>	

	
<br><table class='faq'><tr class="info"><th>What type of platform shall my codes be run on?</th></tr><tr><td>
	<p>To prevent malicious codes from harming the Execution Environment or the Server itself, submitted programs are executed on Virtual Machines. The configuration of the Virtual Machine being used right now is given below :</p>
	<ul>
		<li>Operating System : Ubuntu 10.10 (Maverick); Harddisk : 20GB ; RAM : 512MB</li>
		<li>Brainf**k Interpreter : bf (version 20041219)</li>
		<li>C Compiler : gcc 4.4.5</li>
		<li>C++ Compiler : g++ 4.4.5</li>
		<li>C# Compiler : Mono Compiler Version 2.6.7 (gmcs)</li>
		<li>Java Compiler : javac 1.6.0_20, java 1.6.0_20</li>
		<li>JavaScript Interpreter : rhino 1.7</li>
		<li>Pascal Interpreter : gpc version 20070904</li>
		<li>Perl Interpreter : perl v5.10.1</li>
		<li>PHP Interpreter : PHP 5.3.3</li>
		<li>Python Interpreter : python 2.6.6</li>
		<li>Ruby Interpreter : ruby 1.8.7</li>
	</ul>
	<p>Please contact an Administrator to request support for additional languages.</p>
</td></tr></table>

<br><table class='faq'><tr class="info"><th>Can you give an example of kind of the programs we can submit here?</th></tr><tr><td>
	<p>Please refer to and use the <a target='new' href='?display=problem&pid=1'>Squares</a> Problem to test your choice of programming language. Accepted solutions to this problem have also been made Public for educational reasons, and are available in the following languages : <a target='new' href='?display=code&rid=1'>C</a>, <a target='new' href='?display=code&rid=2'>C++</a>, <a target='new' href='?display=code&rid=3'>C#</a>, <a target='new' href='?display=code&rid=4'>Java</a>, <a target='new' href='?display=code&rid=5'>JavaScript</a>, <a target='new' href='?display=code&rid=6'>Pascal</a>, <a target='new' href='?display=code&rid=7'>Perl</a>, <a target='new' href='?display=code&rid=8'>PHP</a>, <a target='new' href='?display=code&rid=9'>Python</a> and <a target='new' href='?display=code&rid=10'>Ruby</a>. Please remember that there is a 100KB limit on the size of the code you can submit.</p>
	<p>Please do bother not submitting malicious programs that might harm the Execution Environment or the Server itself. As the execution takes place on Virtual Machines, this will only result in a minor inconvenience to the Administrators and the suspension of your account. Also, programs that try to communicate with machines other than this server (in an attempt to send information like the input given to the program) will not work, given that the Virtual Machines are on an small isolated private network. Sumbission of programs that do anything other than try to solve problems will result in severe consequences.</p>
</td></tr></table>
	
<br><table class='faq'><tr class="info"><th>Why is my program not being Accepted?</th></tr><tr><td>
	<p>The programs are judged by the Execution Protocol as described above. However, there exist cases that havent been dealt with, and some of which are mentioned below along with some common errors :</p>
	<ul>
		<li>No provision has been made to detect Run Time Errors in case of languages which need to be compiled. Consequently, if one occurs, it may cause the process to hang (returning TLE, Time Limit Exceeded) or to abort (returning WA, Wrong Answer).</li>
		<li>Java code files must have the same name as the class which contains the main function. If you are uploading *.java files, this should not be a concern, but in case you are submitting text, please ensure that you specify the class name correctly when asked for it.</li>
		<li>Ensure that your program is not printing anything other that what is asked. Ensure that the print operations that you used for debugging your code are removed or commented out. Also ensure that your program is reading from the Standard Input only, and not a file as during debugging.</li>
	</ul>
	<p>If you are sure that none of the reasons described above are applicable in your case, please reconsider the virtual impossibity that logic of your program is flawed, and reexamine your code. If you are absolutely sure that your program is correct in every way, but is still not being Accepted, you may contact an Administrator (via the Clarifications feature) to rejudge or manually run your program (if it does come to that, please quote the Run ID). Note that a particular clarification can only be deleted by the team that requested them provided it not been replied to by an Administrator.</p>
</td></tr></table>	

<br><table class='faq'><tr class="info"><th>How is the ranking done here?</th></tr><tr><td>
	<p>The primary basis for ranking teams is their score. In case the score of two teams are equal, then the team whose solution got accepted first is ranked higher. Note that every incorrect submission (submitted before the first correct solution) results in a <?php global $admin; echo $admin["penalty"]; ?> minute penalty on the time of your submission. Therefore, please avoid submiting programs unless you are reasonably sure they will work.</p>
	<!--
		<p>An important point that must be explained is that there are two separate ranklists available on this site. The <a href='?display=rankings' target='new'>Current Rankings</a> are updated every 10 seconds and reflect the current ranks of the various teams, independent of their past performance. In contrast, the <a href='?display=scoreboard' target='new'>Main Scoreboard</a> (updated far more infrequently) contains the results of the various competitions conducted till now, and uses them to generate long term rankings.</p>
	-->
</td></tr></table>

<br><table class='faq'><tr class="info"><th>What are the different Contest modes you mentioned before?</th></tr><tr><td>
	<p>The different Contest Modes mentioned earlier are described below :</p>
	<ul>
		<li>Active Mode : Submissions are allowed, problem types are hidden, and the Timer is On.</li>
		<li>Passive Mode : Submissions are allowed, problem types are visible, and the Timer is Off.</li>
		<li>Disabled Mode : Submissions are not allowed, problem types are visible, and the Timer is Off.</li>
		<li>Lockdown Mode : All features (except FAQ, Main Scoreboard & Clarifications) are disabled for normal users.</li>
	</ul>
	<p>The Lockdown Mode is used immediately prior to (Active Mode) contests, during which Administrators (who arent affected by the Lockdown Mode once they log in) are uploading and testing new problems.</p>
</td></tr></table>

<br><table class='faq'><tr class="info"><th>One last thing ... why did you make this?</th></tr><tr><td>
	<p>Ah ... I was jobless.</p>
</td></tr></table>
