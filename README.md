# Quellwerke Bugtrack Bundle for Pimcor v10.* & v11.*

## Adding a button to the left menu of the admin panel with the functionality to send debug messages.
After installing the bundle, a support contact button will appear in the left vertical menu in the admin panel.

After clicking this button, you will be able to describe the issue and send a request to the support team.

After submitting the request, you will receive a JSON-file with debug configurations (the download will start automatically). You can also forward this file to the support team.

## Installation:
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


### 4. Run the composer install
```
composer install
```

The bundle is installed in "vendor/quellwerke/quellwerke-bugtrack-bundle".

## Additional settings:
### Sending email notifications.
Make sure that in the file "config/config.yaml" you have specified the sender email details required for sending data via email:
```
pimcore:
    email:
        sender:
            name: 'Example Customer'
            email: example@mail.com
        return:
            name: ''
            email: ''
```
Also specify the support email address in the Pimcore admin panel under “Settings -> System Settings -> Debug -> Debug Email Addresses (CSV)”.

--
#Example of a JSON file received after a request:
```
{
  "status": "ok",
  "message": "Test 11",
  "fileName": "bug_message__23-04-2026_12-55-15.json",
  "time": "23-04-2026_12-55-15",
  "result": "The request has been received and processed. A notification has been sent to the support email address.",
  "backErrorLog": [
    "[2026-04-23T11:00:22.008348+02:00] request.ERROR: ...",
    "[2026-04-23T11:00:22.293564+02:00] request.CRITICAL: ..."
  ],
  "frontLog": {
    "activeTab": {
      "itemType": "Object",
      "id": 81,
      "tab": "Edit",
      "subTab": "Media"
    },
    "originalError": {
      "errorLog": "xhr request to /admin/portal/dashboard-list failed"
    }
  }
}
```

* `"fileName"`: file name
* `"time"`: request time
* `"result"`: server response
* `"backErrorLog"`: backend logs for the last 2 hours
* `"frontLog"`: frontend logs at the moment of the request
* `"originalError"`: some errors captured in the browser
* `"itemType"`: data of the opened tab (only "Data Objects", "Assets", "Document" are processed)
* `"id"`: ID of the opened object
* `"tab"`: active main tab
* `"subTab"`: active sub-tab

