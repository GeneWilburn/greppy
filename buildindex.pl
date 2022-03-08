#!/usr/bin/perl

## buildindex.pl
## search site for all PDFs and HTML pages
## excluding any directories in "exclude.txt"
## and convert to text files in "search/texdata/" directory

## G. Wilburn Feb/Mar 2022
## gene@wilburn.ca


use warnings;
use strict;


# Clear out existing files in target directory
my $ret = `rm search/textdata/*.txt`;
 

my @exclude = `cat search/exclude.txt`;
chomp @exclude;

# Get a files listing of PDFs and HTML pages
my @filearray  = `find . -iname "*.pdf" -o -iname "*.htm*" `;

# Remove trailing linefeed or spaces
chomp @filearray;

# Set date on error log
$ret = `date > search/textdata/build.log`;

# Iterate C-style for value AND array index number
for (my $i = 0; $i <= $#filearray; $i++) {
	my $file = $filearray[$i];

	
	# Preserve a copy of original extension
	my $ext = "";
	if ($file =~ m/.*.(pdf)$/) { $ext = "pdf--"; }
	elsif ($file =~ m/.*.(PDF)$/) { $ext = "PDF--"; }
	elsif ($file =~ m/.*.(html)$/) { $ext = "html--"; }
	elsif ($file =~ m/.*.(htm)$/) { $ext = "htm--"; }
	elsif ($file =~ m/.*.(HTML)$/) { $ext = "HTML--"; }


	# create output file name with .txt extension
	my $outfile = $file;
	if ( $outfile =~ m/\.pdf$/) {
		$outfile =~ s/\.pdf$/.txt/;
	} elsif ( $outfile =~ m/\.PDF$/) {
		$outfile =~ s/\.PDF$/.txt/;
	} elsif ( $outfile =~ m/\.html/) {
		$outfile =~ s/\.html$/.txt/;
	} elsif ( $outfile =~ m/\.htm/) {
		$outfile =~ s/\.htm$/.txt/;
	} elsif ( $outfile =~ m/\.HTML/) {
		$outfile =~ s/\.HTML$/.txt/;
	}

	# add backslash to all literal spaces in filename
	$outfile =~ s/ /\\ /g;	
	
	# Add original extension to outfile name
	$outfile = "\"" . $ext. $outfile . "\"";

	# Perform the pdftotext conversion
	my $yesno="yes";
	foreach my $exclude (@exclude) {
		if ($file =~ m/$exclude/) { $yesno="no"; }
	}	
		$outfile =~ s/\//_99_/g;
		$outfile =~ s/\./dot/;

	if ($yesno eq "yes") {
		my $prefix = lc(substr($outfile,1,3));
		# print "$prefix ";
		if ( $prefix eq "pdf" ) {
			#$file =~ s/^\.\///;
			$file =~ s/ /\\ /g;
			# perform the pdftotext conversion
			$ret = `pdftotext $file - > search/textdata/$outfile 2>>search/textdata/build.log `;
		} else {
			# perform the html to text conversion
			$ret = `pandoc -f html -t plain $file > search/textdata/$outfile 2>>search/textdata/build.log `;
		}
		$ret = `echo $file >> search/textdata/build.log`;
		# Show activity on monitor
		print ".";
	
		}
}
		print "\n";

exit;
