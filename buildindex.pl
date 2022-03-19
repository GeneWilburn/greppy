#!/usr/bin/perl

# buildindex.pl

# G. Wilburn, Feb/Mar 2022

# v1.1

# buildindex.pl searches a site for html and pdf documents, converts them to text, and
# places all the text in a single search directory named "textdata" This provides the
# data for the companion program, search.php
#
# buildindex.pl can be run manually or set up as a recurring cron job
# It should be run after new material is added to a website
#
# buildindex.pl adds the mtime date to the beginning of each saved filename
# which allows search.php to reverse sort the output, displaying the
# most recently created or updated material at the top of the search results
#
# In order to protect private or privileged data, buildindex.pl checks for an
# exclude.txt file for directories that should excluded from the data directory.


use warnings;
use strict;


# Clear out existing files in target directory
my $ret = `rm search/textdata/*.txt`;
 
# Load any directories to be excluded
my @exclude = `cat search/exclude.txt`;
chomp @exclude;

# Get a files listing of PDFs and HTML pages and their datestamps
my @webfiles = `find . -iname "*.pdf" -printf "%T@%p\n" -o -iname "*.htm*"  -printf "%T@%p\n"`;

# Remove trailing linefeed or spaces
chomp(@webfiles);

# Sort then reverse sort the listing
my @filearray = sort(@webfiles);
@filearray = reverse(@filearray);

# Set date on error log
$ret = `date > search/textdata/build.log`;

# Iterate C-style for value AND array index number
for (my $i = 0; $i <= $#filearray; $i++) {
	my $file = $filearray[$i];
	my $outfile = $file;
	# Strip datestamp from beginning of filename
	$file =~ s/^..........\...........//;

	# perform the pdftotext or pandoc conversion
	my $allowed="yes";
	foreach my $exclude (@exclude) {
		# Check filename against excluded directories list
		if ($file =~ m/$exclude/) {
			$allowed="no";
		}
	}	
		# Substitute benign characters for slashes and dots
		$outfile =~ s/\//_99_/g;
		$outfile =~ s/\./dot/g;
		# add .txt extension
		$outfile = $outfile . "\.txt";
		# quote $outfile 
		$outfile = "\"" . $outfile . "\"";

	if ($allowed eq "yes") {
		# if this is a PDF, process accordingly, else process as HTML
		if ( $file =~ m/pdf$/i ) {
			$file =~ s/ /\\ /g;
			# perform the pdftotext conversion
			$ret = `pdftotext $file - > search/textdata/$outfile 2>>search/textdata/build.log `;
		} else {
			# perform the html to text conversion
			$ret = `pandoc -f html -t plain $file > search/textdata/$outfile 2>>search/textdata/build.log `;
		}
		$ret = `echo $file >> search/textdata/build.log`;
		print ".";
	
		}
}
		print "\n";

