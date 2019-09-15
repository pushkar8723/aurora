Aurora v2
=========

Aurora is an open source OJ inspired by SPOJ. Derived from its initial version [Aurora-Online-Judge](https://github.com/kaustubh-karkare/aurora-online-judge). This version improves the exsisting features by removing many redundant procedures, using sockets, easy load distribution, increases security and scalibily to meet the need of a small scale organization.
Its web interface is redesigned from scratch to make it look more SPOJ like. So that users find it to comfortable to adjust. It can also serve as a platform to practice as well as compete with other teams. Few features were added so that the administrators find it easy to handle.
It is designed to meet the requirement specific to CQM matches hosted at BIT Mesra, but most of its components can be easily changed to meet requirement of any other organisation.

Setup Judge
-----------

Aurora is divided into two parts

1. Web Interface
2. Judge written in python, which compiles, executes and tests the correctiveness of the solution.

Both these components communicates with each other and interacts with the database. 
Images for both of these components are available [here](https://github.com/pushkar8723/aurora/packages).
[Docker Compose file for reference](https://github.com/pushkar8723/aurora/blob/master/docker-compose.yml)

Having issue with Aurora?
-------------------------

See if our [FAQ Page](https://github.com/pushkar8723/Aurora/wiki/FAQ) has solution to your problem.

Still have problems? Then raise an issue [here](https://github.com/pushkar8723/Aurora/issues). I will try my best to solve it as soon as possible (no promises though).

Want to contribute?
-------------------

Just create a PR on a seprate branch with appropriate name and describe the changes thoroughly.
Also make sure you have full ownership of the code you submit.

Acknowledgement
---------------

* Kaustubh Karkare, creator of Aurora Online Judge from which this version was derived.
* Siddhartha Sahu, created functions.php which is extensively used for database interaction.

License
-------

Released under the [MIT License](http://opensource.org/licenses/MIT).
