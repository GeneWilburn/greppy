<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search Form</title>
<style>
    h1 {
            text-align: center;
            font-family: Helvetica;
            font-size: 30px;
    }   


    p { 
            text-align: left;
            font-family: Helvetica;
            font-size: 24px;
    }   
    }   
	button, input, select, textarea {
  			font-family: Helvetica;
  			font-size: 24px;;
	}

</style>
</head>
<body>
<center>
<h1>Search Form</h1>

<font size="5">
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
</font>
</center>
</body>
</html>

