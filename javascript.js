var result = [];

function createEle(tagName, className, content) {
    var tag = document.createElement(tagName);
    tag.className = className;
    if (content.length != 0) {
        tag.textContent = content;
    }
    return tag;
}

function rvOldResult(wikigo) {
    var nodes = wikigo.childNodes;
    for (var i = nodes.length - 1; i > 0; i--)
        wikigo.removeChild(nodes[i]);
}

function displayResult() {
    var wikigo = document.getElementsByClassName('wikigo')[0];
    rvOldResult(wikigo);

    console.log(wikigo);
    result.forEach(function (element) {
        var res = createEle('div', 'searchResult', '');

        var title = createEle('a', 'title', element.title);
        title.href = element.link;
        var link = createEle('p', 'link', element.link);
        var desc = createEle('p', 'desc', element.desc);

        res.appendChild(title);
        res.appendChild(link);
        res.appendChild(desc);
        wikigo.appendChild(res);
    }, this);
}

function sendRequest() {
    var query = document.getElementById('search').value;
    if (query.length != 0) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                result = JSON.parse(this.responseText);
                displayResult();
                //console.log(this.responseText);
            }
        };
        xhttp.open("POST", "wikigo.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("query=" + query);
    }
}