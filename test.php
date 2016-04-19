<?php $title = "Regular Expressions Tester";
	require("head.php");
?>
<script>
	function readReg()
	{
		//base text and user input regex
		var input = document.getElementById("regex").value; 
		var text = "The quick brown fox jumped over the lazy dog. THE QUICK BROWN FOX " +  
				"JUMPED OVER THE LAZY DOG? The Quick Brown Fox Jumped Over The Lazy " + 
				"Dog! My phone number is (145)234-5678. Email: panda8024@potato.com " + 
				"192.168.000.100 1-800-CALLNOW #$%^&*<>-=+{}[]\|/,;\"";
		
		//resets the error flag
		document.getElementById("error").style.display = "none";
		
		//if there is input
		if(input != null && input != "")
		{	
			//array to hold match indicies and lengths
			var matches = new Array();
			var repo = document.getElementById("repo");
			
			try
			{
				//find the text that matches this expression and split it by commas
				var match = text.match(new RegExp(input, "g"));
			}
			//shows a flag if the expression throws an error
			catch(error)
			{
				document.getElementById("error").style.display = "inline-block";
			}
			
			//short circuit for no matches
			if(match == null)
			{
				repo.innerHTML = text;
				return;
			}
			
			//get rid of duplicates in match
			match = deleteDuplicates(match);
			
			//iterate through every possible match
			for(var i = 0; i < match.length; i++)
			{
				var index = 0;
				var found = text.indexOf(match[i]);
				
				//while there is still a new match, put the index and length of word in matches
				while(found != -1)
				{
					matches.push(new Array(found, match[i].length));//.length counts actual # of characters
					index = found + match[i].length;
					found = text.indexOf(match[i], index);
				}
			}//now matches has location and length of every matched part of the text
			
			//overlapping sections should take the earliest starting highlight and the longest one
			matches = sortMatches(matches);
			matches = deleteOverlap(matches);
			console.dir(matches);
			
			//necessary to keep track after string lengths
			index = 0;
			
			//cut the text into the matched areas and make spans for highlighted areas
			//takes care of unselected text before the first selection
			var previous = text.substring(index, matches[0][0]);
			var node = document.createTextNode(previous);
			repo.innerHTML = "";
			repo.appendChild(node);
			
			
			//matches is now an ordered array of pairs of ints for the start and length of selected text
			for(var i = 0; i < matches.length; i++)
			{
				//make a span with special class for selected text
				var selected = text.substr(matches[i][0], matches[i][1]);
				var selText = document.createTextNode(selected);
				var span = document.createElement("span");
				span.setAttribute("class", "selected");
				span.appendChild(selText);
				repo.appendChild(span);
				index = matches[i][0] + matches[i][1];
				
				//finds non selected text if any and makes a text node of it
				if(i == matches.length - 1)
					var after = text.substring(index, text.length);
				else
					var after = text.substring(index, matches[i + 1][0]);
				node = document.createTextNode(after);
				repo.appendChild(node);
			}
		}
		else
		{
			document.getElementById("repo").innerHTML = text;
		}
	}
	
	//deletes matches that match the same text
	function deleteDuplicates(array)
	{
		var temp = new Array();
		array.sort();
		var j = 0;
		
		for(var i = 0; i < array.length; i++)
		{
			//if the next element is not a duplicate, record this element
			if(array[i] != array[i+1])//can only be used if array is sorted
			{
				temp[j] = array[i];
				j++;
			}
		}
		return temp;
	}
	
	//assumes array contains strictly arrays of 2 number items
	function sortMatches(array)
	{
		//short circuit
		if(array.length < 2)
			return array;
		
		var temp = new Array();
		var pivot = array[Math.round(array.length / 2)];
		var small = new Array(), large = new Array();
		
		//temp array, replace old if start at same place and is longer
		//possibly do this when you're getting matches?
		for(var j = 0; j < array.length; j++)
		{
			//short circuit to get rid of exact duplicates (which shouldn't happen anyway)
			if (pivot == array[j])
				continue;
			else
			{
				//put before pivot
				if(array[j][0] < pivot[0] || (array[j][0] == pivot[0] && array[j][1] < pivot[1]))
				{
					small.push(array[j]);
				}
				else//put after pivot
				{
					large.push(array[j]);
				}
			}
		}//have two arrays of smaller and larger than pivot
		
		//recursively sort two arrays
		small = sortMatches(small);
		large = sortMatches(large);
		
		//recombine arrays
		for(var i = 0; i < small.length; i++)
			temp.push(small[i]);
		temp.push(pivot);
		for(var i = 0; i < large.length; i++)
			temp.push(large[i]);
		
		return temp;
	}
	
	//delete matches that start in the same place or the selections overlap
	function deleteOverlap(matches)
	{
		var temp = new Array();
		var end = 0;
		
		for(var i = 0; i < matches.length; i++)
		{
			//for last item in array
			if(i == matches.length - 1)
			{
				if(end < matches[i][0])
					temp.push(matches[i]);
			}
			else
			{
				//only adds the longest of matches that start at the same index
				if(matches[i + 1][0] != matches[i][0])
				{
					//make sure previous matches didn't end after this one
					if(end <= matches[i][0])
					{
						temp.push(matches[i]);
					
						//show where this match would end
						end = matches[i][0] + matches[i][1] - 1;
					}
				}
			}
		}
		return temp;
	}
</script>
	<p>/<input oninput="readReg()" id="regex" type="text" size="30" />/g<span id="ß">Error</span></p>
	
	<p id="repo">
		The quick brown fox jumped over the lazy dog. THE QUICK BROWN FOX 
		JUMPED OVER THE LAZY DOG? The Quick Brown Fox Jumped Over The Lazy 
		Dog! My phone number is (145)234-5678. Email: panda8024@potato.com 
		192.168.000.100 1-800-CALLNOW #$%^&amp;*&lt;&gt;-=+{}[]\|/,;
	</p>
</div>
<?php require("footer.php"); ?>
