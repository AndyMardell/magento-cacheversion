# Magento 1 Cache Version
A little tiny Magento 1 module that adds a version string to CSS and JS files

Before installation:

`<link rel="stylesheet" type="text/css" href="http://yourwebsite.com/skin/frontend/base/default/css/style.css" />`

After installation:

`<link rel="stylesheet" type="text/css" href="http://yourwebsite.com/skin/frontend/base/default/css/style.css?v=1.2" />`

Config:

![Magento 1 Cache Version](https://i.imgur.com/FcodxV6.png)

## Installation
Install like any other Magento module.

#### With Composer

Add this repository to composer.json

```
"repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:AndyMardell/magento-cacheversion.git"
    }
]
```
And run
```bash
composer require mardell/cacheversion
```

#### Without Composer

If you don't use composer or modman, you can use the following:

```bash
cp -r src/ /path/to/your/magento/installation
```

## Usage

To enable the module, log in to Magento and go to `System > Configuration > CACHE VERSION > Configuration`

If you release new CSS/JS and want to make sure it's not cached on return visitor's browsers, you can change the version numbers in this section and hit save.
