# Symlinked Extensions [![Build Status](https://travis-ci.org/GooseStudio/symlinked-extensions.svg?branch=master)](https://travis-ci.org/GooseStudio/symlinked-extensions)
Simple script that replaces themes, plugins and mu-plugins with symlinks to development versions

Add below config to composer.json to run the script.
```
    "post-update-cmd": ["php vendor/bin/linkit"]
```


### Example linkit.json

```
{
  "mu-plugins": {
    "target": "/web/app/mu-plugins/",
    "src": [
        "/home/projects/wordpress/my-plugin"    
    ]
  },
  "plugins": {
    "target": "/web/app/plugins/",
    "src": [
        "/home/projects/wordpress/my-plugin",
        "/home/projects/wordpress/my-plugin2"
    ]
  },
  "themes": {
    "target": "/web/app/themes/",
    "src": [
        "/home/projects/wordpress/my-theme"    
    ]
  }
}
```

### Arguments

* **--test** runs the script but without removing directories or symlinking
* **--no-dev** excludes the script from running if "--no-dev" param is used in composer install/update.
* **--linkit=full_path_to_json_file** use specified file as config.
* **--hide** Do not print commands
* **--keep** Do not remove existing folders, postfixes with number of folders.