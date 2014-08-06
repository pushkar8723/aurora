Web Interface Setup
===================
Aurora's web interface requries url rewirte functionality. If you are shown 404 error for various pages then most probably it is disabled. To enable it add following line in **httpd.conf**
```
LoadModule rewrite_module modules/mod_rewrite.so
```

You also need to enable following extention in **php.ini**. Just uncomment the lines.
```
extension=openssl.so
extension=pdo_mysql.so
```
To set limit of maxmium file size that can be uploaded you need to change following in **php.ini** according to your prefference.
```
post_max_size = 20M
upload_max_filesize = 20M
```


Initial Configuration
---------------------

After all server configuration and database are in place, it is time to edit config.php
You need to edit following line in config.php
```
define("SITE_URL", "http://" . $_SERVER['HTTP_HOST'] . "/aurora");      // path to directory
define("SQL_USER", "aurora");           // Database username    
define("SQL_PASS", "aurora");           // Database password
define("SQL_DB", "aurora_main");     	// Database name  
define("SQL_HOST", "127.0.0.1");        // Database host
define("SQL_PORT", "3306");             // Database port
```
First line specifies the path of the directory. Delete ```. "/aurora"``` in case it is the root of web server otherwise update it accordingly. Other options are for MySQL Database settings. 

If you have successfully imported the database then you should be able to login using 
UserID    : judge
Password  : aurora

You should change your password in Account > Account Settings page. If you want you can change your other info on Team Settings page. You should also navigate to different pages in Admin dropdown menu to get familiar with various options to tweak the judge.
Following are the pages and their uses in short:

1. **Team Settings** : Here you can find a list of all registered teams, and all the information that has been collected about each. Team details may be edited if required. Newly registered teams are assigned the status "Normal", and may be suspended or put in waiting state by an Administrator (who needs to set their status back to "Normal", this is done to block someone with suspicious activities).
2. **Contest Settings** : Here you can add new contest.
3. **Group Settings** : You can create various groups for users like UG, PG, students, professional etc.
4. **Problem Settings** : Gives you a list of all currently existing problems and the options to Edit them or Add a new one. Problem Statement, Image, Input and Output files for all problems must be according to max size defined in system configuration. Problems that are marked Disabled are completed hidden from Normal Users.
5. **Clarifications Settings** : Here you can see/reply/edit various comments user posted on problem pages and on feedback pages.
6. **Request Logs** : Logs of all requests such as login, logout, register, submissions etc can be found here.
7. **Broadcast** : Helps you manage broadcast message. *Note:* Broadcast messages are delivered only when judge is in active mode.
8. **Judge Settings** : It help you configure judge socket settings, mode, penalty, active duration and can also be used to update Notice displayed on home page.

You need to create groups for easier classification of users on various Rankings page.

How to conduct a contest
------------------------

1. Go to Contest settings page add the new contest (This can be done in advance so that the user can know about the upcoming contests).
2. Go to Problem Settings and set the status of all *past contest problems* that arent part of this competition to 'Inactive' (instead of deleting them). This will disable them from further accepting submissions.
3. Add new problems and be sure to set their pgroup to contest code.
By default the problems will be added in disabled mode and in practice section. This will make problems visible only to admins and thus only admin can submit and check the problems. After all the testing is done, simply update the contest field to 'contest' and status to 'Active'. This will make problems visible and available for submission as soon as contest starts.
4. On the sand-boxed judge / virtual machines which will judge the solutions, ensure that the connection settings in the judge.py script are accurate, and run it.
5. You may now submit your 'correct' solutions to the 'Active' problems and test them to ensure that the system is working properly.
6. Go back to the Judge Settings page and set the status to 'Active'. If you do not specify the 'End Time', a default value of 3 hours will be assumed. Normal users can now login, view problems and submit solutions.
7. When the timer expires, Judge mode will be automatically changed to 'Disabled', and submissions are no longer allowed. It may take a bit longer than that for the judgement of all submissions to take place.
8. If you wish, you may open the submission page of all problems and make certain accepted solutions 'Public' thereby allowing everyone to see the code and can even set the Display IO field in problem settings page to 'Yes' (this will allow Normal users to see their mistakes. The general format of the links to these codes will be "http://[server-address]/[path]/viewsolution/[Run ID]".
