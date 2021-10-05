# PHP Laravel environment
Docker environment required to run Laravel (based on official php and mysql docker hub repositories).

## Requirements
* Docker version 18.06 or later
* Docker compose version 1.22 or later
* An editor or IDE
* MySQL Workbench

Note: OS recommendation - Linux Ubuntu based.

## Components:
1. Apache 2.4
2. PHP 8.0 (Apache handler)
3. MySQL 8
4. Laravel 8

## Setting up DEV environment
1.Clone this repository from GitHub.

Note: Delete storage/mysql-data folder if it is exist.

2.Add domain to local 'hosts' file:
```bash
127.0.0.1    localhost
```

3.Install make (for Windows user only):

- Install chocolatey for windows from [here](https://chocolatey.org/install)

- Run ```choco install make```

4.Build and start the image from your terminal:
```bash
make build
make start
make composer-install
```

5.Make sure that you have installed migrations/seeds:
```bash
make migrate
make seed
```

6.In order to use this application, please open in your browser next url: [http://localhost](http://localhost).
