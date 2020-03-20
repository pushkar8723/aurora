Aurora v2
=========

Aurora is an open-source OJ inspired by SPOJ. Derived from [Aurora-Online-Judge](https://github.com/kaustubh-karkare/aurora-online-judge), this version improves the scalability and security of the application to meet the requirements for a small scale organization. Its web interface is redesigned from scratch to make it look more SPOJ like. So that users find it comfortable to adjust. It can also serve as a platform to practice as well as compete with other teams. Few features were added so that the administrators find it easy to maintain.
It was designed to meet the requirement specific to CQM matches hosted at BIT Mesra, but most of its components can be easily updated to meet the requirement of any other educational organization.

Getting Started
---------------

Aurora uses docker for development as well as for running the application. Images for each module is automatically generated and uploaded [here](https://github.com/pushkar8723/aurora/packages).
There are `docker-compose` files sepecific to following usecases.

<details>
<summary>Run Locally</summary>

> **[docker-compose.yml](https://github.com/pushkar8723/aurora/blob/master/docker-compose.yml)** can be used to simple run and test out Aurora on a local environment.
>
> Simply run `docker-compose up` to pull all the images and run it.
>
> Then visit [localhost](http://localhost) to test the application.
>
> **_Note:_ Since we are using Github packages, you would need to [configure docker for use with Github token](https://help.github.com/en/packages/using-github-packages-with-your-projects-ecosystem/configuring-docker-for-use-with-github-packages) or docker won't be able to pull the images and docker may start building the image instead. You can follow our [Docker Setup Guide](https://github.com/pushkar8723/aurora/wiki/Docker-Setup) to mitigate this build step.**
</details>

<details>
<summary>Dev Setup</summary>

> **[docker-compose.dev.yml](https://github.com/pushkar8723/aurora/blob/master/docker-compose.dev.yml)** can be used to build, run and test out Aurora on a local environment.
>
> Simply run `docker-compose -f docker-compose.dev.yml build` to build.
>
> Run `docker-compose -f docker-compose.dev.yml up` to run it on [localhost](http://localhost).
</details>

<details>
<summary>Prod Setup</summary>

> **[docker-compose.prod.yml](https://github.com/pushkar8723/aurora/blob/master/docker-compose.prod.yml)** is meant to be used as template for prod configuration.
>
> Check our [guidelines](https://github.com/pushkar8723/aurora/wiki/Using-Docker-in-Production) on how to use this template.
</details>

Facing an issue
---------------

See if our [FAQ Page](https://github.com/pushkar8723/Aurora/wiki/FAQ) has solution to your problem.

Still have problems? Then raise an issue [here](https://github.com/pushkar8723/Aurora/issues). I will try my best to solve it as soon as possible (no promises though).

Contribution Guilelines
-----------------------

Just create a PR on a seprate branch with appropriate name and describe the changes thoroughly.
Also make sure you have full ownership of the code you submit.

Acknowledgement
---------------

* [Kaustubh Karkare](https://github.com/kaustubh-karkare), creator of Aurora Online Judge from which this version was derived.
* [Siddhartha Sahu](https://github.com/sdht0), created functions.php which is extensively used for database interaction.

License
-------

Released under the [MIT License](http://opensource.org/licenses/MIT).
