<?php

# search.php
# G. Wilburn, Feb/Mar 2022

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
    $lastdoc = '';

	# Iterate through results, groom, and display
	for ($i = 0; $i < count($results); $i++) {
		$value = $results[$i];
		$last_element = count($results) -1;

	   	# Strip $datadir from front of $value
	   	$value = preg_replace("/^$datadir/", "", $value);

		# Get extension from metadata and remove from string
		if ( preg_match("/^html--dot_99/i", $value) ) {
        	$ext = substr($value, 0, 4);
			$value = preg_replace("/^html--dot_99_/i", "../", $value);
		} elseif ( preg_match("/^pdf--dot_99/i", $value) ) {
        	$ext = substr($value, 0, 3);
			$value = preg_replace("/^pdf--dot_99_/i", "../", $value);
		}

		# Restore slashes to path and filenames
		$line = preg_replace("/_99_/", "/", $value);
		$line = preg_replace("/dot/", "\.", $line);
		$line = stripslashes($line);

		# Get $doc
		# Remove colon from grep search
		if ($pos = strpos($line, ":", 0)) {
			$doc = substr($line, 0, $pos);
		}	
		# Remove hyphen from grep search
		if ($pos = strpos($doc, "txt-", 0)) {
			$doc = substr($line, 0, $pos +3);
		}
		
		# Remove metadata from $line and assign $line to $content
		$line = preg_replace("/^.*txt:/", "", $line);
		$line = preg_replace("/^.*txt-/", "", $line);
		$content = $line;

		# Replace .txt in $doc with original file extension
        $doc = preg_replace("/.txt/", ".$ext", $doc);

		# Create hyperlink if new document
        if (($doc != $lastdoc) && ($content != "--")) {
        	echo "<h3><a href=\"$doc\">$doc</a></h3>";
           	$lastdoc = $doc;
       	}
		 
		# Stuff content into a single-line paragraph
		if ($content != "--") {
			$para = $para . " ". $content;
		}	

		# Find instances of search query and highlight in red
		# Search on all-lowercase, mixed case, and all-uppercase
		if ( ($content == "--") || ($i == $last_element)) {
			$content = $para;
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
        	print "<blockquote>$content</blockquote>";
		}
		
		#### Debug statements ####
		# print "Query: $query - ". strlen($query). "</br></br>";
        # print "$content</br></br>";
		# print "<p>Value = $value</p>";
        # print "Ext = $ext\n";
        # print "<p>Line = $line</p>";	
		# print "Doc = $doc \n";
		# print "<p>Last = \$lastdoc</p>";
    }
}
?>

</body>
</html>
