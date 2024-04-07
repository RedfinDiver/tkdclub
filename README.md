# TKD-Club

is a Joomla 5 Extension to manage Taekwondo-Clubs, especially in Austria.
It has several functions built in e.g. member-, class-, promotion management and much more.

You can find more information about it (sorry, german language only) at 

[https://tkdclub.readthedocs.io](https://tkdclub.readthedocs.io)


## Usage of this repo

This repo contains the source code and is "spiced up" with the right tools to start developing and building right out of the box. You will develop on a dockerised LAMP stack. So no quirks with installing servers, databases etc. on your host machine. 

## Prerequisites
Make sure to have following software tools installed on your local machine.
Tested on linux only, pick your distro of choice. (Windows user: you will find a way too. Mac user: you too, but most likely more easily ;-)

[Docker](https://www.docker.com/) - Development on Containers, no messup on your local machine

[Lando](https://lando.dev/) - More abstract layer for spinning up the containers, tooling etc.

[Apache Ant](https://ant.apache.org/) - For building the zip files, ready for installation on a Joomla site.

Apache Ant needs a Java runtime as a dependency - your favorite package manager will do all the magic (Windows user: have fun!) For the build to complete from the build.xlm you need to have following ant extensions:

[Ant-Contrib-Tasks](https://ant-contrib.sourceforge.net/) - Task collection for Ant

[xmltask](http://www.oopsconsultancy.com/software/xmltask/) - Doing some tricks with the version information in the manifest files

Consider changing the path to this extensions in the build.xlm in the root directory of this repo according to your setup.

[Git](https://git-scm.com/) - Version control system

### Optional
[VSCode](https://code.visualstudio.com/) - My choice (and many others) as text editor

[REST Client](https://marketplace.visualstudio.com/items?itemName=humao.rest-client) - VSCode plugin to test the api


## How to get started

- Clone this repo `git clone https://github.com/RedfinDiver/tkdclub.git`.

- Spin up the the lando enviroment `lando start`.
- Install Joomla `lando install` 
- Run `lando db-import .config/settings.sql --no-wipe` 
- Run `lando db-import .config/enable.sql --no-wipe` 

This will leave you with a development setup which has

- all Joomla files in folder www
- Joomla at [tkdclub.local/administrator](tkdclub.local/amdministrator)
- phpMyadmin at [pma.local](pma.local)
- mailhog at [mailhog.local](mailhog.local)
- xdebug ready for hooking in (see .vscode/launch.json)
- superuser "admin" with password "admin" ready to go
- default language for back- and frontend set so german
- all tkdclub source files symlinked to www
- installed plugins enabled
- Joomla set to debug mode and session time of 300 minutes

## Adjusting to your needs
- Check and adjust for appropriate php and maria-db versions in .lando.yml.
- Check and adjust for urls in .lando.yml "proxy". For urls see output of initial `lando start`, in current setup you have to add to your `/etc/hosts`:
```
127.0.0.1 tkdclub.local
127.0.0.1 pma.local
127.0.0.1 mailhog.local
```

- Set download link for Joomla in ./config/defaults.env. Note: tkdclub will only work on Joomla 4/5
- By running `lando install-joomla` (after `lando start`) you will get an untouched Joomla install e.g. for testing the zip-files for proper installation.
- Other tooling is available, check with `lando`

## Building installable .zip files

Use the build.xml for automated building of the entire "tkdclub" - Package or for a single extension. I use vscode "Ant Target Runner" - Extension ([Link](https://marketplace.visualstudio.com/items?itemName=nickheap.vscode-ant)) for this purpose, works quite well for me.

The .zip files are installable in every Joomla 5 site.

Happy coding!