Judge
=====

The docker image for this module is automatically published as [Github package](https://github.com/pushkar8723/aurora/packages/23019).

Environment Variables
---------------------

- AURORA_SQL_USERNAME: Aurora's MySQL username.
- AURORA_SQL_PASSWORD: Aurora's MySQL password.
- AURORA_SQL_DATABASE: Aurora's MySQL database name.
- AURORA_SQL_HOSTNAME: MySQL hostname.
- AURORA_SQL_HOSTPORT: MySQL port.

Docker Secrets Variables
------------------------

In production, the following environment variables can be used with docker secrets.

- AURORA_SQL_USERNAME_FILE
- AURORA_SQL_PASSWORD_FILE
- AURORA_SQL_DATABASE_FILE
- AURORA_SQL_HOSTNAME_FILE
- AURORA_SQL_HOSTPORT_FILE
