<?php

# search.php
# G. Wilburn, Feb/Mar 2022
# gene@wilburn.ca

# v1.1

# search.php does a grep search on the data directory, e.g., textdata/, that
# contains the text content created by the companion program buildindex,pl
# 
# It can search by wholeword matches, or partial (stem) matches and the user
# is given the option of brief context surrounding the match, or a more extended
# context
#
# Found matches are displayed in most-recently created or updated order.
#
# search.php uses an embedded CSS style sheet for formatting.
#
# search.php is called from searchform.php, a simple HTML form.


# Check if query is empty
if (empty($_GET['query'])) {
    # If the field is empty, display a message to the user
    echo " <br/> Please back up to previous page and fill in the search field";
    
} else {
    $query = $_GET['query'];
	$wholeword = $_GET['wholeword'];
	$brief = $_GET['brief'];

	# Groom search term. Uppercase 1st char.
	if ($query == strtoupper($query)) {
		$query = strtolower($query);
	}
	# Remove any quotes, set to lower case
    $query = str_replace('"', '', $query);
	$query = trim($query);
	
	# Uppercase first letter of each search term
	if (strpos($query, ' ')) {
		$parts = explode(' ', $query);
		$query = '';
		foreach ($parts as $part) {
			$part = ucfirst($part);
			$query = $query . ' '. $part . '';	
		}
	} else {
		$query = ucfirst($query);
	}
	$query = trim($query);
}

# Embedded style sheet
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Search Results</title>

<style>
    h1 {
            text-align: left;
            font-family: Helvetica;
            font-size: 24px;
    }


    p {
            text-align: left;
            font-family: Helvetica;
            font-size: 18px;
    }
	blockquote {
            margin-left: 40px;
            margin-right: 40px;
            margin-top: .5em;
            margin-bottom: .5em;
            text-align: left;
            font-family: Helvetica;
            font-size: 18px;
	}
</style>

</head>
<body>

<h1>Search Results:</h1>

<?php

# Set the location of the data directory
$datadir = "textdata\/";

# If wholeword search, add word boundary metacharacter to search term
if ($wholeword == "on") {
	$query = '\b' . $query . '\b';
}

# Search for query in $datadir, with either brief or expanded context 
if ($brief == "on") {
	$raw = `grep -iHE -C 1 -o ".{0,90}$query.{0,90}" textdata/*.txt `;
} else {
	$raw = `grep -iHE -C 1 "$query" textdata/*.txt `;
}

# Check to see if search found a match. If not, prompt to try a different search term
if ($raw == "") {
    print "No results. Please back up to previous page and try a different search term.";
} else {
    # load results, extract filename extension, get document name,
    # separate content from embedded doc data, highlight search term in red
    $results = explode("\n", $raw);
	# Reverse sort the results to provide most-recently-updated first
	rsort($results);
    $lastdoc = '';

	# Iterate through results, groom, and display
	for ($i = 0; $i < count($results); $i++) {
		$value = $results[$i];
		$last_element = count($results) -1;

	   	# Strip $datadir from front of $value
	   	$value = preg_replace("/^$datadir/", "", $value);

		# Remove timestamp from string
		$value = preg_replace("/^.*dot_99_/", "../", $value);

		# Restore slashes and dots to path and filenames
		$line = preg_replace("/_99_/", "/", $value);
		$line = preg_replace("/dot/", "\.", $line);
		$line = stripslashes($line);

		# Get $doc
		# Remove colon from grep search
		if ($pos = strpos($line, "txt:", 0)) {
			$doc = substr($line, 0, $pos +3);
		}	
		# Remove hyphen from grep search
		if ($pos = strpos($doc, "txt-", 0)) {
			$doc = substr($line, 0, $pos +3);
		}
		
		# Restore original extension
		$line = preg_replace("/^.*txt:/", "", $line);
		$line = preg_replace("/^.*txt-/", "", $line);

		$content = $line;

		# Remove .txt in $doc leaving it with original file extension
		$doc = preg_replace("/.txt/", "", $doc);

		# Create hyperlink if new document
        if ($doc != $lastdoc) {
        	print "<h3><a href=\"$doc\">$doc</a></h3>";
           	$lastdoc = $doc;
       	}
		 
		# Stuff content into a single-line paragraph
		if  ($doc == $lastdoc) {
			$para = $para . " ". $content;
		}	

		# Find instances of search query and highlight in red
		# Search on all-lowercase, mixed case, and all-uppercase
		if ( ($doc == $lastdoc) || ($i == $last_element)) {
			$content  = $para;
			$para = "";
			# Remove \b from front and back of $query
			$query = preg_replace("/\\\b/", "", $query);
			$lc_query = strtolower($query);
			$uc_query = strtoupper($query);
			if ( preg_match("/$query/", $content) ) {
        		$content = str_replace($query, "<font color=\"red\">$query</font>", $content);
			}
			if ( preg_match("/$lc_query/", $content) ) {
        		$content = str_replace($lc_query, "<font color=\"red\">$lc_query</font>", $content);
			} 
			if ( preg_match("/$uc_query/", $content) ) {
        		$content = str_replace($uc_query, "<font color=\"red\">$uc_query</font>", $content);
			}

			# Display the content of the search
			if (!preg_match("/--/", $content) ) {
				print "<blockquote>$content</blockquote>";
			}
		}
		
		#### Debug statements ####
		# print "Query: $query - ". strlen($query). "</br></br>";
		# print "Content: $content</br></br>";
		# print "<p>Value = $value</p>";
		# print "<p>Line = $line</p>";	
		# print "Doc = $doc \n";
		# print "<p>Last = $lastdoc</p>";
    }
}
?>

</body>
</html>

