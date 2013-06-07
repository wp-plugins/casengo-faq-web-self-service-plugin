/*!
 * tpl.js
 */

// @win window reference
// @fn function reference

function contentLoaded(win, fn) {
	var done = false, top = true,

	doc = win.document, root = doc.documentElement,

	add = doc.addEventListener ? 'addEventListener' : 'attachEvent',
	rem = doc.addEventListener ? 'removeEventListener' : 'detachEvent',
	pre = doc.addEventListener ? '' : 'on',

	init = function(e) {
		if (e.type == 'readystatechange' && doc.readyState != 'complete') return;
		(e.type == 'load' ? win : doc)[rem](pre + e.type, init, false);
		if (!done && (done = true)) fn.call(win, e.type || e);
	},

	poll = function() {
		try {root.doScroll('left');} catch(e) {setTimeout(poll, 50);return;}
		init('poll');
	};

	if (doc.readyState == 'complete') fn.call(win, 'lazy');
	else {
		if (doc.createEventObject && root.doScroll) {
			try {top = !win.frameElement;} catch(e) { }
			if (top) poll();
		}
		doc[add](pre + 'DOMContentLoaded', init, false);
		doc[add](pre + 'readystatechange', init, false);
		win[add](pre + 'load', init, false);
	}

}

function checkInputValue(el, ev) {
	var val = el.getAttribute('data-value');
	if (!val || val === '') {
		if (ev === 'focus') {
			el.className = '';
			el.value = '';
		} else if (ev === 'blur') {
			el.className = 'empty';
			el.value = el.getAttribute('data-emptytext');
		}
	} else {
		el.className = '';
	}
}

//Trims special characters that would return an error if inserted in the search query

function trimSearchSpecialSymbols(string){
	var symbArray = ['+', '-', '&', '|', '!', '(',  ')', '{', '}', '[', ']', '^', '"', '~', '*', '?', ':', '\\'], 
	stringFinal = '';

	for(var i=0; i<symbArray.length; i++){
		stringFinal = string.replace(symbArray[i], '', 'gi');
		string= stringFinal;
	}

	return stringFinal.trim();
};

contentLoaded(window, function() {
	var btn = document.getElementById('askbutton'),
	articleEscalateElement = document.getElementById('articleescalate');
	if (btn) {
		btn.onclick = function() {
			openVIP();
		};
	}
	
	var searchForm = document.getElementById('search-form'),
	keyword = document.getElementById('keyword'),
	keyword_mini = document.getElementById('keyword-mini');
	
	if(searchForm) {
		searchForm.onsubmit = function(e) {
			if(keyword) {
				if(keyword.value == keyword.getAttribute('data-emptytext')) {
					keyword.value = '';
				}
				keyword.value = trimSearchSpecialSymbols(keyword.value);
			}
			if(keyword_mini) {
				keyword_mini.value = trimSearchSpecialSymbols(keyword_mini.value);
			}
		}
	}
	
	if (articleEscalateElement) {
		articleEscalateElement.onclick = function() {
			openVIP();
		};
	}
	
	var inputs = [],
		input = null;		
	inputs.push(keyword);
	inputs.push(keyword_mini);
	for (var i = 0, iLen = inputs.length; i < iLen; i++) {
		input = inputs[i];
		if (input) {
			input.onfocus = function() {
				checkInputValue(this, 'focus');
			};
			input.onblur = function() {
				checkInputValue(this, 'blur');
			};
			input.onkeyup = function() {
				this.setAttribute('data-value', this.value);
			};
		}
	}
});
