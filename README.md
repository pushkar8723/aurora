Aurora v2
=========

SPOJ like judge to automatically judge the submitted solution. Derived from its initial version [Aurora-Online-Judge](https://github.com/kaustubh-karkare/aurora-online-judge). This version automates many processes like ranking, problem judging etc. and also improves exsisting features by removing many redundant procedures and using sockets.
Its web interface is redesigned from scratch to make it look more SPOJ like so that users find it to comfortable to adjust and can also serve as a platform for practice as well as compete with other teams. Few features are added so that the administrators find it easy to handle.
Its database structure hasn't changed much and is compatible with initial version. It is designed to meet the requirement specific to CQM matches hosted at BIT Mesra but its registration process can be easily changed to meet requirement of any other organisation.

Setup Judge Python Script
-------------------------

Python script for judge can be found in judge folder. To protect rest of the machine from potenial treat it is recommended to run the judge in a sandboxed environment to trap it inside a folder or run it on a virtual machine. Both processes are described below.

### How to Setup Sandbox Environment
In this method Judge is tarped inside an area in machine by using chroot command in linux and detailed steps for seting up judge can be found in 'set up judge' file inside judge folder.

### How to Setup Judge in Virtual Machine 

Follow following steps.

1. Install Ubuntu 12.04 on a virtual machine.
2. Install required compliers/interpreters (one time process).
<code>
sudo apt-get update
sudo apt-get install bf g++ fpc mono-gmcs openjdk-6-jdk perl php5 python python-mysqldb rhino ruby
</code>
** Note **
You may need to add following two lines in '/etc/apt/sources.list' file
deb http://in.archive.ubuntu.com/ubuntu/ precise main universe 
deb-src http://in.archive.ubuntu.com/ubuntu/ precise main universe

3. Edit <code>sql_hostname, sql_hostport, sql_username, sql_password, sql_database, HOST, PORT</code> appropriately. Run the python script.
<code>
python judge.py -judge -unsafe -cache
</code>
	* <code>-judge</code> option turn on the judgement. Without this judgement will not begin only short description of options available will be shown
	* <code>-unsafe</code> option set the judgement for all languages (some which can used to perform suspicious operations)
	* <code>-cache</code> option tells the judge to cache input and correct output first time the solution for a problem is submitted and later use this cache rather than fetching data from database each time. Judge can be shutdown by keyboard intrupt i.e, CTRL+C

How to start using this software
--------------------------------

1. Extract the contents of the archive into your apache's web site root directory (or a subdirectory thereof).
2. Edit the file /path/to/aurora/config.php, and set the <code>SITE_URL, SQL_USER, SQL_PASS, SQL_DB, SQL_HOST, SQL_PORT</code> variables appropriately.
3. Run the sql file present in /path/to/aurora/db/auroa.sql on the mysql server to setup various tables and procedures.
4. Open the website via your browser. If the site is loaded perfectly then this means that a proper connection to the database could be established.
5. This software is now ready to use. (You may need to set up Judge Script if you haven't done it yet)
6. You may check FAQ sections for further information.

Administration
--------------

1. Login as an Administrator (initial credentials : UID => judge, PASS => aurora) and go through the various settings page in Admin menu. Here you will find various option like judge mode (initially set to disabled), notice, problems setting, contest settings etc.
2. You should change your password in Account > Account Settings page. If you want you can change your other info on Team Settings page.
3. Team Settings : Gives you a list of all team that have registered.
4. Contest Settings : Here you can add new contest.
5. Group Settings : You can create various groups for users like UG, PG, students, professional etc.
6. Problem Settings : Gives you a list of all currently existing problems and the options to Edit them or Add a new one. Problem Statement, Image, Input and Output files for all problems must be less than 2MB. Problems that are marked Disabled are completed hidden from Normal Users.
7. Team Settings : Here you can find a list of all registered teams, and all the information that has been collected about each. Team details may be edited if required. Newly registered teams are assigned the status "Normal", and may be suspended or put in waiting state by an Administrator (who needs to set their status back to "Normal", this is done to block someone with suspicious activities).
8. Clarifications Settings : Here you can see/reply/edit various comments user posted on problem pages and on feedback pages.
9. Request Logs : Logs of all requests such as login, logout, register, submissions etc can be found here.

### How to conduct a contest

1. Login as an Administrator, go to the Judge Settings Page, and set the contest mode to Lockdown. This will forcefully log out all currently logged in non-admin users and shut off access to the Problems, Submission Status, Contact Us and Rankings Pages, thereby hiding anything you do from the users.
2. Go to Contest settings page add the new contest (This can be done in advance so that the user can know about the upcoming contests).
3. Go to Problem Settings and set the status of all past contest problems that arent part of this competition to 'Inactive' (instead of deleting them). This will effectively disable them from further accepting submissions.
4. Add new problems and be sure to set their pgroup to contest code. Set the status of the new problems to 'Active'.
5. On the sand-boxed judge / virtual machines which will judge the solutions, ensure that the connection settings in the judge.py script are accurate, and run it.
6. You may now submit your 'correct' solutions to the 'Active' problems and test them to ensure that the system is working properly.
7. Go back to the Judge Settings page and set the status to 'Active'. If you do not specify the 'End Time', a default value of 3 hours will be assumed. Normal users can now login, view problems and submit solutions.
8. When the timer expires, the contest status is automatically set to 'Disabled', and submissions are no longer allowed. It may take a bit longer than that for the judgement of all submissions to take place.
9. If you wish, you may open the submission statistics of all problems and make certain accepted solutions 'Public' thereby allowing everyone to see the code and can even set the Display IO field in problem settings page to 'Yes' (this will allow Normal users to see their mistakes, Note : This is not recommended for problems with large input / output file). The general format of the links to these codes will be "http://[server-address]/[path]/viewsolution/[Run ID]".

Acknowledgements
----------------

* Kaustubh Karkare, creator of Aurora Online Judge from which this version was derived.
* Siddhartha Sahu, created functions.php which is extensively used for database interaction.

License
-------

Released under the [MIT License](http://opensource.org/licenses/MIT).
