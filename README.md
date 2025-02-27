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
- Install development enviroment with `lando install` 

This will leave you with a development setup which has

- all Joomla files in folder www
- Joomla at [tkdclub.local/administrator](tkdclub.local/amdministrator)
- phpMyadmin at [pma.local](pma.local)
- mailhog at [mailhog.local](mailhog.local)
- xdebug ready for hooking in (see .vscode/launch.json)
- superuser "admin" with password "admin" ready to go
- default language for back- and frontend set so german
- all tkdclub source files symlinked to www
- all tkdclub components and plugins discovered/enabled
- Joomla set to debug mode and session time of 300 minutes

## Adjusting to your needs
- Check and adjust for appropriate php and maria-db versions in `.lando.yml`.
- Check and adjust for urls in `.lando.yml` "proxy". For urls see output of initial `lando start`, in current setup you have to add to your `/etc/hosts`:
```
127.0.0.1 tkdclub.local
127.0.0.1 pma.local
127.0.0.1 mailhog.local
```

- Set download link for Joomla in `./config/install.sh` on line 4. Note: tkdclub will only work on Joomla 4/5

## Other tooling

The lando app is configured with more tooling. Check with `lando` for a quick overview. 
Make sure to have issued `lando start` before using any other tooling command!
Let`s discover the most useful commands during development:

### `lando install`

As mentioned in the ["How to get started"- section](#how-to-get-started), this is your
command of choice when you start development on tkdclub. Nothing can go wrong with it.
Under the hood, `lando install` does following commands after another:

1. `lando install-joomla`
2. `lando tkdclub-link`
3. `lando tkdclub-install`

### `lando install-joomla`

This command will wipe the current Joomla installation and will make a brand new one by downloading the Joomla installation file configured in `.config/install.sh`.
But no part of the tkdclub-app will be installed (which means symlinking and discovering) in this. For what reason? Well, this will make it easy to test bundled zip-files for installing into it. This is the right thing to do before releasing a new version, you know...

### `lando tkdclub-link`

Will symlink all tkdclub extensions (back- and frontend, all plugins) to the Joomla installation. You could do this after `lando install-joomla` e.g. as you have checked the installation and deinstallation of zips worked fine. Don\`t forget to invoke also `lando tkdclub-install` otherwise Joomla will not recognise the new extensions.

### `lando tkdclub-unlink`

Will unlink all symlinks of tkdclub app. This means that Jooml still has the tkdclub extensions in its database and will give an error at trying to click a menu item linked with tkdclub extensions.

### `lando tkdclub-install`

Will discover the symlinked tkdclub-extensions in the Joomla application. You need this command when starting with `lando install-joomla`, followed by `lando tkdclub-ìnstall` to reach the same status of your app as after `lando install`.
 
## Building installable .zip files

Use the build.xml for automated building of the entire "tkdclub" - Package or for a single extension. I use vscode "Ant Target Runner" - Extension ([Link](https://marketplace.visualstudio.com/items?itemName=nickheap.vscode-ant)) for this purpose, works quite well for me.

The .zip files are installable in every Joomla 5 site.

Happy coding!