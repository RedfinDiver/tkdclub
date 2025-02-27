### helper script for installing joomla ###

### Give the link to Joomla install file ###
### Please don't use .zst type as I wasn't able to get this archive type decompressed in lando :-( ###
JOOMLA_INSTALL_URL="https://github.com/joomlagerman/joomla/releases/download/5.2.4v1/Joomla_5.2.4-Stable-Full_Package_German.tar.gz"

### Extraction filename of given url ###
JOOMLA_INSTALL_FILE_NAME="$(basename "$JOOMLA_INSTALL_URL")"
JOOMLA_INSTALL_FILE_PATH="/app/zips/$JOOMLA_INSTALL_FILE_NAME"


# flag -j
joomla()
{
    echo "Starting complete Joomla/TkdClub enviroment.."
    sleep 1
    echo "Wiping and creating new \"$LANDO_WEBROOT\" directory..."
    rm -rf /app/www
    mkdir -p /app/www
    sleep 1

    download
    decompress

    echo "Installing Joomla via CLI installation..."
    php /app/www/installation/joomla.php install \
        --site-name=tkdclub \
        --admin-user=admin \
        --admin-username=admin \
        --admin-password=test!1234567 \
        --admin-email=test@test.org \
        --db-type=mysqli \
        --db-host=database \
        --db-user=admin \
        --db-pass=admin \
        --db-name=tkdclub \
        --db-prefix=dev_ \
        --db-encryption=0 \
        --public-folder= -v
    sleep 1

    echo "Setting debug variables..."
    php /app/www/cli/joomla.php config:set debug=true error_reporting=maximum lifetime=300 -v
    sleep 1

    echo "Clean Joomla installation is done!"
    sleep 1
}

download()
{   
    echo "Checking for zips folder..."
    if [ ! -d "/app/zips" ]; then
        mkdir -p "/app/zips"
        echo "zips folder created!"
    else
        echo "Zips folder already present!"
    fi

    echo "Checking for Joomla file..."
    if [ -f "$JOOMLA_INSTALL_FILE_PATH" ]; then
        echo "Joomla install file '"$JOOMLA_INSTALL_FILE_NAME"' already exists, no download necessary!"
    else
        echo "Joomla file '"$JOOMLA_INSTALL_FILE_NAME"' is downloading..."
        wget -nv -O "$JOOMLA_INSTALL_FILE_PATH" "$JOOMLA_INSTALL_URL"
    fi
}

decompress()
{
    echo "Unzipping joomla install files..."
    TYPE=$(file --mime-type -b "$JOOMLA_INSTALL_FILE_PATH")

    echo "Type is $TYPE"
    
    case "$TYPE" in
        application/zip)
            unzip "$JOOMLA_INSTALL_FILE_PATH" -d $LANDO_WEBROOT
            echo "Joomla install files unzipped!"
            ;;
        application/gzip)
            tar -xzf "$JOOMLA_INSTALL_FILE_PATH" -C $LANDO_WEBROOT
            echo "Joomla install files unzipped!"
            ;;
        *)
            echo "Unknown or unsupported archive type: $TYPE"
            echo "Please provide a .zip or .gz file"
            exit 1
            ;;
    esac
}



# flag -l
link()
{
    if [ -e www/configuration.php ] # linking only when files are present
        then
        # linking component
        ln -snf /app/source/component/backend /app/www/administrator/components/com_tkdclub
        ln -snf /app/source/component/frontend /app/www/components/com_tkdclub
        ln -snf /app/source/component/media /app/www/media/com_tkdclub

        # linking api
        ln -snf /app/source/component/api /app/www/api/components/com_tkdclub

        # linking plugins
        mkdir -p /app/www/plugins/console && ln -snf /app/source/plugins/bdreminder_cli /app/www/plugins/console/bdreminder
        ln -snf /app/source/plugins/gradeupdate /app/www/plugins/content/gradeupdate
        ln -snf /app/source/plugins/tkdclubmember /app/www/plugins/user/tkdclubmember
        ln -snf /app/source/plugins/tkdclubmember/media /app/www/media/plg_user_tkdclubmember
        ln -snf /app/source/plugins/bdreminder_task /app/www/plugins/task/bdreminder
        ln -snf /app/source/plugins/webservice /app/www/plugins/webservices/tkdclub
    fi
}

# flag -u
unlink()
{
    # using rm instead of unlink because of using also for zip-installs
    # removing component
    rm -rf /app/www/components/com_tkdclub
    rm -rf /app/www/administrator/components/com_tkdclub
    rm -rf /app/www/media/com_tkdclub

    # removing api
    rm -rf /app/www/api/components/com_tkdclub

    # removing plugins
    rm -rf /app/www/plugins/console/bdreminder
    rm -rf /app/www/plugins/content/gradeupdate
    rm -rf /app/www/plugins/user/tkdclubmember
    rm -rf /app/www/media/plg_user_tkdclubmember
    rm -rf /app/www/plugins/task/bdreminder
    rm -rf /app/www/plugins/webservices/tkdclub
}

# flag -t
tkdclub()
{
    echo "Discover linked extensions by Joomla..."
    sleep 1
    php /app/www/cli/joomla.php extension:discover -v
    
    echo "Installing discovered extensions..."
    sleep 1
    php /app/www/cli/joomla.php extension:discover:install -v
}

while getopts ":jluta" option; do
   case $option in
    j) # install untouched joomla
        joomla
        exit;;
    l) # link source files into Joomla
        link
        exit;;
    u) # unlink source files from Joomla
        unlink
        exit;;
    t) # discover and install tkdclub in Joomla
        tkdclub
        exit;;
    a) # full service: install Joomla, symlink, discover and install tkdclub
        joomla
        link
        tkdclub
        exit;;
   esac
done
