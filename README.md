# Quellwerke Bugtrack Bundle for Pimcor v10.* & v11.*

## Initial example of a bundle for further development.
Adding a button to the left menu of the admin panel with a message-sending function.  

## Installation
(If you are using DDEV, first run: ddev ssh)
### 1. run the command
```
composer config repositories.quellwerke-bugtrack-bundle vcs https://github.com/Shtarev/quellwerke-bugtrack-bundle
```

### 2. In the composer.json file, add "Quellwerke\\QuellwerkeBugtrackBundle\\Composer\\InitHandler::init" like this:
```
"scripts": {
  "post-install-cmd": [
    "Quellwerke\\QuellwerkeBugtrackBundle\\Composer\\InitHandler::init",
    "Pimcore\\Composer::installAssets"
  ],
```

### 3. Install the bundle by selecting the desired version, for example:
```
composer require quellwerke/quellwerke-bugtrack-bundle:dev-master
```


### 4. run the composer install
```
composer install
composer dump-autoload
```

The bundle is installed in "vendor/quellwerke/quellwerke-bugtrack-bundle".

### Check command:
```
php bin/console quellwerke:bugs:command
```

### Check rout:
```
www.yousite.com/admin/bugtrack/bugs
```
