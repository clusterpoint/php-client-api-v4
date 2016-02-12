# Unit Testing using phpspec

##Reference
* [GitHub Repo](https://github.com/phpspec/phpspec)  
* [Website & Documentation](http://www.phpspec.net)  
* [Cheat Sheet](https://github.com/yvoyer/phpspec-cheat-sheet)  
  
##Quick start:  
```
composer install --dev  
alias phpspec="./vendor/phpspec/phpspec/bin/phpspec"  
phpspec run
```

#Compatability check
##phpcompatinfo

```
wget http://bartlett.laurent-laville.org/get/phpcompatinfo-5.0.0.phar  
php phpcompatinfo-5.0.0.phar analyser:run .
```

#DOC API generation
## Sami
* [Sami GitHub Repo](https://github.com/FriendsOfPHP/Sami)