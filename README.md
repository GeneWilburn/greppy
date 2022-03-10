# Greppy
A Lightweight Perl/PHP Website Search Engine Based on Grep

"Greppy" was born from the need to provide a client with a small search engine for an informational website housed in the AWS Cloud. The Cloud's Linux server is minimal: 1GB RAM and tight storage to keep costs down. The site is an Apache/PHP website with monthly updates and a considerable number of both HTML and PDF pages.

The challenge was to provide the client with a search engine without adding significantly to storage requirements or requiring more RAM. It had to be lean and mean.

## GNU Grep To The Rescue

Knowing from experience how quickly GNU Grep can rip through text files, searching for words or phrases, I elected to build a simple search engine around it.

## Dependencies

- PHP 7.2 or higher
- Perl
- GNU Grep (not BSD Grep)
- Pandoc
- pdftotext

The pdftotext utility is part of xpdf, available to Debian/Ubuntu users via 'apt install xpdf-tools'. MacOS users can obtain pdftotext via 'brew install xpdf' using Homebrew.


## Greppy Files

The working version of Greppy uses three programs:

- buildindex.pl
- searchform.php
- search.php

The buildindex.pl Perl script is a batch file that is run to create and/or recreate the contents of a *textdata* directory. It can be run as needed, manually, or scheduled to run in a cron job.

The searchform.php PHP script is an HTML form for entering a search term or phrase, and choosing whether to do whole-word or stem searches, and brief or expanded context around the results.

The search.php PHP script handles the invocation of grep and creates an HTML display of the results, with links back to the original documents. The search term or phrase found in the text database is highlighted in red.

The search.php script checks for a line-by-line *exclude.txt* file to bypass designated private directories or files.

## File Layout

By default the layout of the system is relative to the DocumentRoot of the Apache server:

DocumentRoot/buildindex.pl  
DocumentRoot/search/searchform.php  
DocumentRoot/search/search.php  
DocumentRoot/search/exclude.txt  
DocumentRoot/search/textdata/  

The *textdata/* directory is the text database that grep searches for matches to the search query.

## Performance

The downside of this system is that it is batch oriented. When pages are added, updated, or deleted, buildindex.pl must be re-run. *Note:* buildindex.pl empties the *textdata/* directory every time it is run, so that the "index" remains in sync with the website contents.

The upside of the system is that it is batch oriented. It updates the *textdata/* directory quickly (about 5 minutes on a moderately large informational site) and requires little overhead.

The text data in *textdata/* creates a very modest footprint on overall storage, and grep runs quickly in a small amount of RAM.

The speed of the searches is gratifyingly fast.

## Caveat

Although an attempt has been made to handle files with spaces in filenames, other metacharacters can cause problems with buildindex.pl. If at all possible, it is recommended that you groom a site first with [detox](https://github.com/dharple/detox) to fix filename issues before they become an issue. You can run detox in `--dry-run` mode to see which links might be affected by fixing filenames.

## Credits

The author of this system is [Gene Wilburn](https:genewilburn.com), a retired Canadian IT specialist.

The system was commissioned by Gene's colleague Mark Dornfeld, President of Cyantic Systems, a Toronto-based IT consulting firm.
