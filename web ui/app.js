var availableTags = [];
var suggest = [[]];
var term = "";
var suggestCounter = 0;
var stopWords = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','so','solr'];
function getAutoSuggestions(){
	var queryTerm = $("#q")[0].value;
	if(queryTerm.length == 0){
		return false;
	}
	availableTags = [];
	var terms = queryTerm.trim().split(' ');
	var oldQuery = $("#q")[0].value.split(' ');
	
	console.log(terms);
	for(var k = 0 ; k < terms.length ; k++){
		// var autoCompleteServerURL = "http://localhost:8983/solr/SCrawler/suggest?q="
		// autoCompleteServerURL += queryTerm + "&wt=json&indent=true";
		term = terms[k];
		suggestCounter = k;
		$.ajax({
			//url: autoCompleteServerURL,
			url: 'SuggestCORS.php',
			type: "GET",
			// dataType: 'JSONP',
			data:{
				"query": term
			},
			crossDomain: true,
			async: false,
			// contentType : 'application/x-www-form-urlencoded; charset=UTF-8',
			success: function(res){
				var data = JSON.parse(res);
				//get the returned term value
				// var baseString = res.substring(87);
				// var subTermEnd = baseString.indexOf('":');
				// var baseTerm   = res.substring(88,87+subTermEnd);
				// console.log(baseTerm);
				var baseTerm = "";
				$.each(data.suggest.suggest, function(key, value){
				    baseTerm = key;
				});
				var out = data.suggest.suggest[baseTerm];
				var suggestions = out.suggestions;
				suggestions.sort(function(a, b) {
				    return parseFloat(b.weight) - parseFloat(a.weight);
				});
				availableTags = [];
				suggest[suggestCounter] = new Array();
				var count = 0;
				re = /^[a-zA-Z]+$/i;
				for(var i = 0 ; i < suggestions.length && count < 5 ; i ++){

					console.log(suggestions[i].term+"--");
					if(re.exec(suggestions[i].term)){
						suggest[suggestCounter][count] = suggestions[i].term;
						count++;
						console.log(suggest[suggestCounter][count-1]);
					}
				}
				suggestCounter++;
				console.log(suggest);
				
				
			}

		});

	}
	var ql = $("#q")[0].value.trim().split(' ').length;
	var prefix = "";
	for(var l = 0 ; l < ql - 1; l++)
		prefix += suggest[l][0] + " ";
	var availCounter = 0;
	for(var j = 0 ; j < suggest[ql-1].length ; j++){
		if(ql > 1){
			if(stopWords.indexOf(suggest[ql-1][j].toLowerCase()) != -1 && suggest[ql-1][j].length < 3)
				continue;
			availableTags[availCounter] = prefix + suggest[ql-1][j];
		}else{
			if(stopWords.indexOf(suggest[0][j].toLowerCase()) != -1 && suggest[0][j].length < 3)
				continue;
			availableTags[availCounter] = suggest[0][j];
		}
		availCounter++;
	}
	$( "#q" ).autocomplete({
      minLength: 0,
      source: function(request, response) {          
        // var data = $.grep(availableTags, function(value) {
        	
        	
        // });            
        if(availableTags.length == 0)
    		return false;
    	var suggetionList = [];
    	var counter = 0;
    	var stemList = [];
        var updatedList = [];
        var updateCounter = 0;
        var finalList = [];
        for(var k = 0 ; k < availableTags.length ; k++){
        	stemList[k] = stemmer(availableTags[k]);
        }
        for(var r = 0 ; r < stemList.length ; r++){

        	if(updatedList.length == 0){
        		updatedList[updateCounter] = stemList[r];
        		finalList[updateCounter] = availableTags[r];
        		updateCounter++;
        	}
        	else if(updatedList.length > 0 && updatedList.indexOf(stemList[r]) == -1){
        		updatedList[updateCounter] = stemList[r];
        		finalList[updateCounter] = availableTags[r];
        		updateCounter++;
        	}
        }
        console.log(stemList);
        console.log(updatedList);
        //return availableTags;
        response(finalList);
      },
      minLength : 4
    });

}
function triggerQuery(){
	
}
$(function() {
    $.getScript("PorterStemmer.js", function(){

	   console.log("Stemmer algorithm loaded");
	});
    

    $("#q").onkeyup = getAutoSuggestions();
});
