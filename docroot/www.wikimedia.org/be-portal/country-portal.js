    mainsite= '.wikipedia.org';
    infotext = 1;

    function removetext() {
	if (infotext==1) {
	    document.getElementById("fsearch").search.value = "";
	    document.getElementById("fsearch").search.style.color = "#000000";
	    infotext = 0;
	}
    }

    url= 'http://';
    function setlang( clicked)	{
	document.getElementById("fsearch").action =
                     url +clicked.lang +mainsite +'/wiki/Special:Search';

	return true;
    }
    function golang( clicked)	{
	document.location = url +clicked.lang +mainsite;

	return true;
    }


    function makefield( words) {
	document.write( '<input type="text" name="search" onfocus="removetext()"'
			+' value="' +words +'" size="26" maxlength="50" />');
    }
    function makebutton( language, color, tekst) {
	document.write('<input type="submit" name="go" onclick="setlang(this)" lang="'
			+language +'" class="' +color +' button" value="'+tekst +'" />');
    }

