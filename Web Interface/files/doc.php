<h2>Documentation</h2>
<h3>How to start using this software</h3>
<ul>
    <li>Extract the contents of the archive into your apache's web site root directory (or a subdirectory thereof).</li>
    <li>Edit the file /path/to/aurora/config.php, and set the SITE_URL, SQL_USER, SQL_PASS, SQL_DB, SQL_HOST, SQL_PORT variables appropriately.</li>
    <li>Run the sql file present in /path/to/aurora/db/auroa.sql on the mysql server to setup various tables and procedures.</li>
    <li>Open the website via your browser. If the site is loaded perfectly then this means that a proper connection to the database could be established.</li>
    <li>This software is now ready to use. (You may need to set up Judge Script if you haven't done it yet)</li>
    <li>You may check FAQ sections for further information.</li>
</ul>
<h3>Administration</h3>
<ul>
    <li>Login as an Administrator (initial credentials : UID => judge, PASS => aurora) and go through the various settings page in Admin menu. Here you will find various option like judge mode (initially set to disabled), notice, problems setting, contest settings etc.</li>
    <li>You should change your password in Account > Account Settings page. If you want you can change your other info on Team Settings page.</li>
    <li>Team Settings : Gives you a list of all team that have registered.</li>
    <li>Contest Settings : Here you can add new contest.</li>
    <li>Group Settings : You can create various groups for users like UG, PG, students, professional etc.</li>
    <li>Problem Settings : Gives you a list of all currently existing problems and the options to Edit them or Add a new one. Problem Statement, Image, Input and Output files for all problems must be less than 2MB. Problems that are marked Disabled are completed hidden from Normal Users.</li>
    <li>Team Settings : Here you can find a list of all registered teams, and all the information that has been collected about each. Team details may be edited if required. Newly registered teams are assigned the status "Normal", and may be suspended or put in waiting state by an Administrator (who needs to set their status back to "Normal", this is done to block someone with suspicious activities).
    <li>Clarifications Settings : Here you can see/reply/edit various comments user posted on problem pages and on feedback pages.</li>
    <li>Request Logs : Logs of all requests such as login, logout, register, submissions etc can be found here.</li>
</ul>

<h3>How to conduct a contest</h3>
<ul>
    <li>Login as an Administrator, go to the Judge Settings Page, and set the contest mode to Lockdown. This will forcefully log out all currently logged in non-admin users and shut off access to the Problems, Submission Status, Contact Us and Rankings Pages, thereby hiding anything you do from the users.</li>
    <li>Go to Contest settings page add the new contest (This can be done in advance so that the user can know about the upcoming contests).</li>
    <li>Go to Problem Settings and set the status of all past contest problems that arent part of this competition to 'Inactive' (instead of deleting them). This will effectively disable them from further accepting submissions.</li>
    <li>Add new problems and be sure to set their pgroup to contest code. Set the status of the new problems to 'Active'.</li>
    <li>On the sand-boxed judge / virtual machines which will judge the solutions, ensure that the connection settings in the judge.py script are accurate, and run it.</li>
    <li>You may now submit your 'correct' solutions to the 'Active' problems and test them to ensure that the system is working properly.</li>
    <li>Go back to the Judge Settings page and set the status to 'Active'. If you do not specify the 'End Time', a default value of 3 hours will be assumed. Normal users can now login, view problems and submit solutions.</li>
    <li>When the timer expires, the contest status is automatically set to 'Disabled', and submissions are no longer allowed. It may take a bit longer than that for the judgement of all submissions to take place.</li>
    <li>If you wish, you may open the submission statistics of all problems and make certain accepted solutions 'Public' thereby allowing everyone to see the code and can even set the Display IO field in problem settings page to 'Yes' (this will allow Normal users to see their mistakes, Note : This is not recommended for problems with large input / output file). The general format of the links to these codes will be "http://[server-address]/[path]/viewsolution/[Run ID]".</li>
</ul>