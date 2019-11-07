# Phabricator statistics utility

## About

This tool parses phabricator web directly.
Phabricator APIs does not allow to take more detailed information, 
like when changes were requested or revision approved.

This tool is to be used to calculate review SLAs.

## Installing

`composer install`

`cp config.dist.php config.php`

Change values in `config.php`.

## Running

Change date restriction inside `run.php`, if needed.

Run to analyse diffs from-to (only IDs):

```bash
php run.php 31000 32000
```

You can run several in parallel, merge afterwards:

```bash
cd results
cat stats* > main.csv
```
