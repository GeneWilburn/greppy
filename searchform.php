<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search Form</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
<center>
<h1>Search Form</h1>

 <form method="GET" action="search.php">
<div>
   Search term or phrase: <input type="text" name="query"><br/>
   <php $query = htmlspecialcharacters(trim($query); ?></br>
</div>

<div>
  <input type="checkbox" id="wholeword" name="wholeword" checked>
  <label for="wholeword">Wholeword Search (uncheck for stemword search)</label>
</div>

<div>
  <input type="checkbox" id="brief" name="brief" checked>
  <label for="brief">Brief context (uncheck for expanded context)&nbsp;&nbsp;</label>
</div>

<br/><input type="submit" value="submit" >
</form>

</center>
</body>
</html>

