# venta-css
![](https://cdn.shopify.com/s/files/1/0560/7466/6159/files/venta-logo-2.jpg?v=1648643584)

## About 
Copy-pasting CSS Rules from the browser developer tools? I call that lazy CSS writing! When you don't want to use CSS frameworks, but you're worried that your CSS file turns into one gigantic mess, then this package is for you! 

## Example 
Sometimes, it's hard to remember predefined class names, and you certainly don't want to make your site look like the same with others using the same framework. Building CSS from scratch isn't easy though, especially when you are lazy! You just don't notice that you are using the same rules that can be optimized. 

### **BEFORE** `build` command:
```
<!--HTML-->
<div class="container"></div>
<div class="flex"></div>
<div class="all-centered"></div>
```
```
/**CSS**/
.container {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}
.flex {display: flex;}
.all-centered {
    align-items: center;
    justify-content: center;
}
```
### **AFTER** `build` command:

With Venta CSS, your CSS class selectors will be 
* **Broken down** - Selectors are broken down into invidual tokens
* **Re-grouped** - Tokens are grouped together according to the rules 
* **Minified** - Each selector names will be minifed to 3 characters 
* **Removed Unused CSS** - Re-grouping process creates unused tokens

![](https://cdn.shopify.com/s/files/1/0560/7466/6159/files/Desktop_2022-03-30_21-17-43.png?v=1648646330)

```
<!--HTML-->
<div class="xCM e7Y f3N"></div>
<div class="xCM"></div>
<div class="e7Y"></div>
```
```
/**CSS**/
.xCM {display:flex;} 
.e7Y {align-items:center;justify-content:center;} 
.f3N {flex-direction:column;} 
```

## Installation
The package can be installed using Composer.
```
composer require kenjiefx/venta-css
```
## Venta CLI 
To use Venta CLI, create a file named `venta` in your root directory: 
```
#!/usr/bin/php
<?php

if (php_sapi_name() !== 'cli') exit;
define('ROOT',__DIR__);

require_once ROOT.'/vendor/autoload.php';
new \Kenjiefx\VentaCss\VentaCli($argv);
```

## app.css 
All your CSS has to be writen in the `/venta/app.css` file in your public directory. 
```
<link rel="stylesheet" href="/venta/app.css">
```

## venta.config.json 
To create a config file, run the following command: 
``` 
php venta hook $YOUR_PUBLIC_DIR 
```
Note that this command will create `venta.config.json` on your root directory, as well as `/vnt` directory which contains a back-up copy of your public directory. 

## Build Command 
```
php venta build
```
This command will parse all the HTML and HTM files in your public directory to inject the minified CSS. 

## Revert Command 
You can revert to the original state (non-minified) state of your project by running the revert command: 
```
php venta revert
```
