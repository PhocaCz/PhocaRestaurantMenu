/*
 * addrow.js - an example JavaScript program for adding a row of input fields
 * to an HTML form
 *
 * This program is placed into the public domain.
 *
 * The orginal author is Dwayne C. Litzenberger.
 *
 * THIS SOFTWARE IS PROVIDED AS-IS WITHOUT WARRANTY
 * OF ANY KIND, NOT EVEN THE IMPLIED WARRANTY OF
 * MERCHANTABILITY. THE AUTHOR OF THIS SOFTWARE,
 * ASSUMES _NO_ RESPONSIBILITY FOR ANY CONSEQUENCE
 * RESULTING FROM THE USE, MODIFICATION, OR
 * REDISTRIBUTION OF THIS SOFTWARE.
 *
 * Home page: http://www.dlitz.net/software/addrow/
 *
 * History:
 *  Version 2 - 2006-01-14  dlitz@dlitz.net
 *   - Add support for MSIE 6
 *   - Separate HTML and JavaScript into separate files
 *   - Tested on:
 *      + Konqueror 3.4.2
 *      + Microsoft Internet Explorer 5.00
 *      + Microsoft Internet Explorer 6.0
 *      + Mozilla Firefox 1.0.4 (via browsershots.org)
 *      + Mozilla Firefox 1.5
 *      + Opera 8.51
 *      + Safari 2.0 (via browsershots.org)
 *  Version 1
 *   - Initial release
 * 
 * Reworked for Phoca Component by Jan Pavelka
 * http://www.phoca.cz
 * Added behaviour for categories
 * Added support for radio buttons
 * Added support for adding two rows at once
 */

function addRow(categoryId, type) {
    /* Declare variables */
    var elements, templateRow, rowCount, row, className, newRow, element;
    var i, s, t;
	var tString, tStringNew, classNameWithCategoryId, numberItemPublish, countRows;
    
    /* Get and count all "tr" elements with class="row".    The last one will
     * be serve as a template. */
    if (!document.getElementsByTagName)
        return false; /* DOM not supported */
    elements = document.getElementsByTagName("tr");
    templateRow = null;
    rowCount = 0;
    for (i = 0; i < elements.length; i++) {
        row = elements.item(i);
        
        /* Get the "class" attribute of the row. */
        className = null;
        if (row.getAttribute)
            className = row.getAttribute('class')
        if (className == null && row.attributes) {    /* MSIE 5*/
            /* getAttribute('class') always returns null on MSIE 5, and
             * row.attributes doesn't work on Firefox 1.0.    Go figure. */
            className = row.attributes['class'];
            if (className && typeof(className) == 'object' && className.value) {
                /*MSIE 6*/
                className = className.value;
            }
        } 
        
        /* This is not one of the rows we're looking for.    Move along. */
		classNameWithCategoryId = "pm-tr-row-" + categoryId;
        if (className != classNameWithCategoryId)
            continue;
        
        /* This *is* a row we're looking for. */
        templateRow = row;
        rowCount++;
    }
	
    if (templateRow == null)
        return false; /* Couldn't find a template row. */
    
    /* Make a copy of the template row */
    newRow = templateRow.cloneNode(true);

    /* Change the form variables e.g. price[x] -> price[rowCount] */
    elements 			= newRow.getElementsByTagName("input");
	numberItemPublish 	= 0;

    for (i = 0; i < elements.length; i++) {
        element = elements.item(i);
        s = null;
        s = element.getAttribute("name");

        if (s == null)
            continue;
        t = s.split("[");
        if (t.length < 2)
            continue;
			
		/* We need new name for inputs: e.g. newitem instead of item to know which inputs are new.
		 *  After we add "new" to input names, we must check if the "new" prefix exists or not.
		 * If it exists don't do add it anymore */
		tString = t[0].toString();
		if (tString.match("new")) {
			tStringNew = t[0];
		} else {
			tStringNew = "new" + t[0];
		}
		
		s = tStringNew + "[" + categoryId + "][" + rowCount.toString() + "]";
        /* element.setAttribute("style", "background: #FFF9E5;");*/
		element.setAttribute("name", s);
		
		/* Add behaviour for radio buttons
		 * We need to add value values (value="1" and value="0" for new created radio buttons 
		 * There are two values for publish (publish/unpbublish), so if there is numberItemPublish == 0
		 * it means that the first value was not added, it will be added and numberItemPublish set to = 1
		 * if there is numberItemPublish == 1 the second value should be set and the numberItemPublish will
		 * be set back to = 0 because of new row, where the first value come as next*/
		 
		if (tStringNew == "newitempublish" && numberItemPublish == 0) {
			element.value = "1";
			numberItemPublish = 1;
		} else if (tStringNew == "newitempublish" && numberItemPublish == 1) {
			element.value = "0"
			numberItemPublish = 0;
		} else {
			element.value = "";
		}
		
    }
    
    /* Add the newly-created row to the table */
	/* Security check */
	countRows = countAddedRows();
	if (countRows > 50) {
		alert("Please save your changes before continuing");
		return false;
	}
	
    templateRow.parentNode.appendChild(newRow);
	/* If type is 5 add description too*/
	/*if (type == 5) {*/
		addRowDesc(categoryId);
	/*}*/
    return true;
}


function addRowDesc(categoryId) {
    /* Declare variables */
    var elements, templateRow, rowCount, row, className, newRow, element;
    var i, s, t;
	var tString, tStringNew, classNameWithCategoryId, countRows;
    
    /* Get and count all "tr" elements with class="row".    The last one will
     * be serve as a template. */
    if (!document.getElementsByTagName)
        return false; /* DOM not supported */
    elements = document.getElementsByTagName("tr");
    templateRow = null;
    rowCount = 0;
    for (i = 0; i < elements.length; i++) {
        row = elements.item(i);
        
        /* Get the "class" attribute of the row. */
        className = null;
        if (row.getAttribute)
            className = row.getAttribute('class')
        if (className == null && row.attributes) {    /* MSIE 5 */
            /* getAttribute('class') always returns null on MSIE 5, and
             * row.attributes doesn't work on Firefox 1.0.    Go figure. */
            className = row.attributes['class'];
            if (className && typeof(className) == 'object' && className.value) {
                /*MSIE 6*/
                className = className.value;
            }
        } 
        
        /* This is not one of the rows we're looking for.    Move along. */
		classNameWithCategoryId = "pmdesctr pm-tr-row-desc-" + categoryId;
        if (className != classNameWithCategoryId)
            continue;
        
        /* This *is* a row we're looking for. */
        templateRow = row;
        rowCount++;
    }
	
    if (templateRow == null)
        return false; /* Couldn't find a template row. */
    
    /* Make a copy of the template row */
    newRow = templateRow.cloneNode(true);

    /* Change the form variables e.g. price[x] -> price[rowCount] */
    elements 			= newRow.getElementsByTagName("textarea");

    for (i = 0; i < elements.length; i++) {
        element = elements.item(i);
        s = null;
        s = element.getAttribute("name");

        if (s == null)
            continue;
        t = s.split("[");
        if (t.length < 2)
            continue;
			
		/* We need new name for inputs: e.g. newitem instead of item to know which inputs are new.
		 *  After we add "new" to input names, we must check if the "new" prefix exists or not.
		 * If it exists don't do add it anymore */
		tString = t[0].toString();
		if (tString.match("new")) {
			tStringNew = t[0];
		} else {
			tStringNew = "new" + t[0];
		}
		
		s = tStringNew + "[" + categoryId + "][" + rowCount.toString() + "]";
		element.setAttribute("name", s);
		element.value = '';

    }
	
	 /* Add the newly-created row to the table */
	/* Security check */
	countRows = countAddedRows();
	if (countRows > 50) {
		alert("Please save your changes before continuing");
		return false;
	}
	
    templateRow.parentNode.appendChild(newRow);
    return true;
}


function countAddedRows() {
    /* Check to see if the counter has been initialized*/
    if ( typeof countAddedRows.counter == 'undefined' ) {
        /* It has not... perform the initilization*/
        countAddedRows.counter = 0;
    }
	
	++countAddedRows.counter;
	
	return countAddedRows.counter;
}

/* set ts=8 sw=4 sts=4 expandtab: */
