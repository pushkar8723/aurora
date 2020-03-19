Web Interface Setup
===================

The docker image for this module is automatically published as [Github package](https://github.com/pushkar8723/aurora/packages/23017).

Environment Variables
---------------------

- AURORA_BASE_URL: The base path for your setup (excluding the hostname). That is if Aurora is available at http://example.com/aurora then set this variable to `/aurora`.
- AURORA_SQL_USER: Aurora's MySQL username.
- AURORA_SQL_PASS: Aurora's MySQL password.
- AURORA_SQL_DB: Aurora's MySQL database name.
- AURORA_SQL_HOST: MySQL hostname.
- AURORA_SQL_PORT: MySQL port.

Docker Secrets Variables
------------------------

In production, the following environment variables can be used with docker secrets.

- AURORA_SQL_USER_FILE
- AURORA_SQL_PASS_FILE
- AURORA_SQL_DB_FILE
- AURORA_SQL_HOST_FILE
- AURORA_SQL_PORT_FILE

Initial Configuration
---------------------

Before you run this docker image. Make sure your MySQL server is running. To quickly set up the tables and initialize the values, run all SQL files in [DB](https://github.com/pushkar8723/aurora/tree/master/DB) folder.

After a fresh installation, you should be able to login with the following credentials.
UserID: judge
Password: aurora

You should change your password in Account > Account Settings page. If you want you can change your other info on Team Settings page. You should also navigate to different pages in the Admin dropdown menu to get familiar with various options to tweak the judge.
Following are the pages and their uses in short:

1. **Team Settings**: Here you can find a list of all registered teams and all the information that has been collected about each. Team details may be edited if required. Newly registered teams are assigned the status "Normal" and may be suspended or put in waiting state by an Administrator (who needs to set their status back to "Normal", this is done to block someone with suspicious activities).
2. **Contest Settings**: Here you can add a new contest.
3. **Group Settings**: You can create various groups for users like UG, PG, students, professionals, etc.
4. **Problem Settings**: Gives you a list of all currently existing problems and the options to Edit them or Add a new one. Problem Statement, Image, Input and Output files for all problems must be according to the max size defined in system configuration. Problems that are marked Disabled are completed hidden from Normal Users.
5. **Clarifications Settings**: Here you can see/reply/edit various comments users posted on problem pages and on feedback pages.
6. **Request Logs**: Logs of all requests such as login, logout, register, submissions, etc can be found here.
7. **Broadcast**: Helps you manage the broadcast messages. *Note:* Broadcast messages are delivered only when the judge is in active mode.
8. **Judge Settings**: It helps you configure judge socket settings, mode, penalty, active duration and can also be used to update Notice displayed on the home page.

You need to create groups for easier classification of users on the various Rankings page.

How to conduct a contest
------------------------

1. Go to Contest settings page add the new contest (This can be done in advance so that the user can know about the upcoming contests).
2. Go to Problem Settings and set the status of all *past contest problems* that arent part of this competition to 'Inactive' (instead of deleting them). This will disable them from further accepting submissions.
3. Add new problems and be sure to set their group to contest code.
By default, the problems will be added in disabled mode and in the practice section. This will make problems visible only to admins and thus only admin can submit and check the problems. After all the testing is done, simply update the contest field to 'contest' and status to 'Active'. This will make problems visible and available for submission as soon as the contest starts.
4. On the sand-boxed judge / virtual machines which will judge the solutions, ensure that the connection settings in the judge.py script are accurate, and run it.
5. You may now submit your 'correct' solutions to the 'Active' problems and test them to ensure that the system is working properly.
6. Go back to the Judge Settings page and set the status to 'Active'. If you do not specify the 'End Time', a default value of 3 hours will be assumed. Normal users can now login, view problems and submit solutions.
7. When the timer expires, Judge mode will be automatically changed to 'Disabled', and submissions are no longer allowed. It may take a bit longer than that for the judgment of all submissions to take place.
8. If you wish, you may open the submission page of all problems and make certain accepted solutions 'Public' thereby allowing everyone to see the code and can even set the Display IO field in the problem settings page to 'Yes' (this will allow Normal users to see their mistakes.
